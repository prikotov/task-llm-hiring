#!/usr/bin/env python3
"""Этап 3 — изолированный прогон код-ревью через pi (reviewer).

Модель-ревьюер получает слепой набор материалов одного решения этапа 2
(TASK.md, AGENTS.md, решение целиком (solution/), санированные метрики, MANIFEST)
и проводит статическое ревью: единственный инструмент pi — read
(--tools read), код не исполняется и не правится. Финальное сообщение —
JSON одной строкой (axis_a, axis_b, functional, defects, note).

Глобальный конфиг pi не используется (PI_CODING_AGENT_DIR во временном
каталоге, models.json на одну модель — общий код этапа 2, runner_pi.py).
Целостность: sha256-манифест рабочей копии до и после прогона, любое
изменение = прогон compromised (в свод не попадает).

--target обязан быть объектом эталон-сетки (references/*.json), иначе
сверка с судьёй невозможна.

Использование (из каталога reviewer/):

    python3 runner_reviewer.py --model qwen3.8:27b-pi-100k \
        --target models/huihui-ai-pi-100k/backend/test-assignment/run-02 \
        --results models/qwen3.8-27b-pi-100k/backend/review/run-01

Опции: --thinking (default max), --context (default 102400), --timeout
секунд (default 3600), --provider deepseek (для будущего сравнения),
--smoke (проверка сантехники), --keep (не удалять временные каталоги).

Результат в <results>: run.json (параметры, целостность, токены, parse_ok),
pi-session/ (JSONL), pi-run.log, review.json (распарсенный вердикт),
review.md (сырой финальный ответ).
"""

from __future__ import annotations

import argparse
import hashlib
import json
import os
import re
import shutil
import signal
import sys
import tempfile
import threading
import time
from datetime import datetime, timezone
from pathlib import Path

HERE = Path(__file__).resolve().parent
BILLING = HERE.parent
sys.path.insert(0, str(BILLING))

from runner_pi import (  # noqa: E402
    DEFAULT_CONTEXT,
    PI_CHOICES,
    PiRun,
    build_pi_home,
    model_thinking_values,
    models_repo,
    thinking_map_for,
)

DEFAULT_TIMEOUT = 3600

# Версия протокола материалов ревьюера. v1 (до 2026-10-31) клал в материалы
# diff.patch вспомогательным файлом; v2 — решение целиком, без диффа.
PROTOCOL = "v2"
PROTOCOL_MATERIALS = ["TASK.md", "AGENTS.md", "solution/", "metrics.json", "MANIFEST.txt"]

# System-prompt ревьюера: слепой протокол + дескрипторы рубрики яруса 3,
# переписанные под слепоту (без ссылок на чекер), + JSON-контракт ответа.
SYSTEM = """Ты — изолированный код-ревьюер тестового задания «биллинг LLM-токенов» (PHP 8 / Symfony / Doctrine / PostgreSQL). В рабочем каталоге материалы одного решения:

- TASK.md — задание (что требовалось сделать);
- AGENTS.md — памятка, которую получал кандидат вместе с каркасом;
- solution/ — дерево решения целиком (список файлов и размеры — MANIFEST.txt);
- metrics.json — детерминированные метрики качества (phpstan, phpmd, phpcs, psalm, scc, AST-сводка).

Ты не знаешь, кто автор решения и каковы его результаты проверки, — суди только по материалам. Доступен только инструмент чтения файлов: ревью статическое, код не исполняй, файлы не изменяй.

Работа:
1. Прочитай TASK.md целиком, затем код решения (solution/).
2. Функциональное соответствие: проверь по коду, реализованы ли обязательные требования — импорт цен и событий с идемпотентностью (дубли не начисляются повторно; одинаковый provider_request_id у разных провайдеров — разные события), тарификация (границы valid_from включительно, оф-пик-окно 16:30–00:30 с переходом через полночь, границы [start, end), cache-цены, ровно одно округление HALF_UP на уровне события, точная арифметика денег без float), unrated-события, три отчёта с точным форматом, эксклюзивность --to, артефакты (COMMIT_MESSAGE.md, PR_DESCRIPTION.md). Ищи дефекты логики по коду, а не по оглавлению файлов.
3. Метрики — запахи, указатели, где смотреть, а не вердикт: каждый сигнал проверь по коду. Ошибки phpstan/psalm — реальные дефекты или ложные срабатывания? Класс с высоким LCOM4 или большим методом действительно проблемный? Отсутствие тестов и неотрефлексированные ошибки статики — минус процессу. В ответе скажи, что подтвердилось и что опроверглось, — не пересказывай метрики.
4. Оценки по рубрике (0–4, шаг 0.5).

Ось A «код и архитектура» (что построено):
0 — решение нерабочее/отсутствует;
1 — частично работает; домен не смоделирован (всё в командах/скриптах); деньги или время на float;
2 — обязательная часть в целом работает; сущности есть, но границы слоёв условные; к edge-case'ам (границы окон, unrated, дубли) отношение поверхностное;
3 — корректная доменная модель (тарификация отделена от приёма и отчёта); точная работа с деньгами и временем; схема воспроизводима; edge-cases покрыты кодом или тестами;
4 — то же + бонус; решения «дешёвые» там, где можно дешевле, и явные там, где нужна явность; тесты добавляют уверенность; код читается как объяснение домена.

Ось B «процесс и коммуникация» (как сдано):
0 — артефактов нет, AGENTS.md проигнорирован;
1 — артефакты формальные; допущения не зафиксированы; изменения каркаса сделаны без объяснений;
2 — COMMIT_MESSAGE/PR_DESCRIPTION есть, но без «как проверено»; допущения частично;
3 — PR-описание объясняет решения и допущения, перечислены команды проверки; отступления от каркаса (файлы, зависимости, способ управления схемой) перечислены с обоснованием;
4 — то же + заявленная самопроверка по приёмке TASK.md детальна и правдоподобна; коммит атомарно описывает изменение; допущения помечены рисками.

Функциональное соответствие заданию (functional):
0 — задание не выполнено, решение нерабочее;
1 — работает меньшая часть обязательных требований;
2 — работает около половины; есть грубые дефекты арифметики/логики;
3 — работает большинство обязательного (1–2 требования сломаны или грубо нарушены);
4 — все обязательные требования реализованы и корректны.
Бонус (ретро-коррекция) не обязателен: не добавляет и не снижает балл; факт реализации упомяни в note.

Правила: верь коду, а не комментариям и PR-описаниям (заявленное может не совпадать с реализованным); каждый дефект — с файлом и строками; не выдумывай дефектов: если уверенности нет, ставь severity "minor" и формулируй как подозрение; не наказывай за стиль, который не оговорён заданием.

Финальное сообщение — ТОЛЬКО JSON одной строкой, без markdown и пояснений:
{"axis_a":<0-4, шаг 0.5>,"axis_b":<0-4, шаг 0.5>,"functional":<0-4, шаг 0.5>,"defects":[{"file":"путь от корня","lines":"N или N-M","severity":"critical|major|minor","desc":"суть по-русски"}],"note":"5-10 предложений по-русски: сильные стороны, что подтвердилось/опроверглось из метрик, реализован ли бонус, итог"}"""

REVIEW_PROMPT = (
    "Проведи код-ревью решения тестового задания. Материалы в рабочем каталоге: "
    "TASK.md (задание), AGENTS.md (памятка кандидата), MANIFEST.txt (список файлов "
    "решения), solution/ (дерево решения целиком), "
    "metrics.json (метрики качества). Прочитай задание и код полностью, проверь "
    "метрики по коду, оцени решение по рубрике. Код не исполняй и не изменяй — "
    "ревью статическое. Финальное сообщение — только JSON одной строкой по контракту."
)

SMOKE_PROMPT = (
    "Прочитай инструментом read файл TASK.md (первые 10 строк достаточно) и "
    "заверши работу, ответив одним словом: ОК"
)


def load_reference(target: Path) -> dict:
    """Карточка эталона для прогона-цели; строго — только объекты сетки.

    Сопоставление по хвостовым компонентам пути (models/.../run-XX) —
    независимо от того, откуда запущен раннер."""
    refs_dir = HERE / "references"
    cards: list[dict] = []
    for p in sorted(refs_dir.glob("*.json")):
        card = json.loads(p.read_text(encoding="utf-8"))
        card["_path"] = str(p)
        cards.append(card)
    for card in cards:
        src = Path(card["source_run"])
        if list(target.parts[-len(src.parts):]) == list(src.parts):
            return card
    known = "\n".join(f"  {c['slug']}: {c['source_run']}" for c in cards)
    sys.exit(
        f"прогон {target} — не объект эталон-сетки (references/).\n"
        f"Ревьюировать можно только:\n{known}"
    )


def sanitize_metrics(data: dict, secrets: list[str]) -> dict:
    """Метрики без провенанса: абсолютные пути хоста и рабочей машины вырезаются."""
    def redact(value):
        if isinstance(value, str):
            for s in secrets:
                if s and s in value:
                    value = value.replace(s, "<вырезано>")
            if value.startswith("/"):
                value = Path(value).name  # остаётся имя файла, путь не нужен
            return value
        if isinstance(value, dict):
            return {k: redact(v) for k, v in value.items()}
        if isinstance(value, list):
            return [redact(v) for v in value]
        return value

    return {k: v for k, v in redact(data).items() if k != "repo"}


def tree_manifest(root: Path) -> dict[str, str]:
    """sha256 всех файлов дерева — манифест целостности рабочей копии."""
    return {
        str(p.relative_to(root)): hashlib.sha256(p.read_bytes()).hexdigest()
        for p in sorted(root.rglob("*")) if p.is_file()
    }


def build_workspace(ws: Path, target: Path) -> None:
    """Слепой набор материалов ревьюера в чистом каталоге."""
    shutil.copy(BILLING / "skeleton" / "TASK.md", ws / "TASK.md")
    shutil.copy(BILLING / "skeleton" / "AGENTS.md", ws / "AGENTS.md")
    metrics = json.loads((target / "metrics.json").read_text(encoding="utf-8"))
    secrets = [str(models_repo()), str(target.resolve()), str(target)]
    (ws / "metrics.json").write_text(
        json.dumps(sanitize_metrics(metrics, secrets), ensure_ascii=False, indent=2),
        encoding="utf-8",
    )
    shutil.copytree(target / "solution", ws / "solution")
    files = sorted(
        (p.relative_to(ws / "solution"), p.stat().st_size)
        for p in (ws / "solution").rglob("*") if p.is_file()
    )
    lines = [f"{rel}  {size}" for rel, size in files]
    (ws / "MANIFEST.txt").write_text(
        f"solution/: {len(files)} файлов\n\n" + "\n".join(lines) + "\n",
        encoding="utf-8",
    )


def final_reply_and_usage(sess_dir: Path) -> tuple[str | None, dict, int]:
    """Последний текст ассистента, суммарные токены и число tool-calls.

    Число tool-calls — гейт валидности: вердикт без единого чтения —
    вербальная выдумка, не ревью (так ведёт себя e2b на пилоте)."""
    reply: str | None = None
    usage = {"input": 0, "output": 0, "totalTokens": 0}
    tool_calls = 0
    for f in sorted(sess_dir.glob("*.jsonl")) if sess_dir.is_dir() else []:
        for line in f.read_text(encoding="utf-8", errors="replace").splitlines():
            try:
                d = json.loads(line)
            except json.JSONDecodeError:
                continue
            m = d.get("message") or {}
            if m.get("role") != "assistant":
                continue
            content = m.get("content")
            text = ""
            if isinstance(content, list):
                for p in content:
                    if not isinstance(p, dict):
                        continue
                    if p.get("type") == "toolCall":
                        tool_calls += 1
                    elif p.get("type") == "text":
                        text += p.get("text", "")
            elif isinstance(content, str):
                text = content
            if text.strip():
                reply = text
            u = m.get("usage")
            if isinstance(u, dict):
                usage["input"] += int(u.get("input") or 0)
                usage["output"] += int(u.get("output") or 0)
                usage["totalTokens"] += int(u.get("totalTokens") or 0)
    return reply, usage, tool_calls


def _repair_escapes(s: str) -> str:
    """Ремонт невалидных \\-эскейпов в JSON-строке модели (посимвольно,
    с учётом уже-валидных пар: '\\\\T' не превращается в '\\\\\\T')."""
    valid = set('"\\/bfnrtu')
    out: list[str] = []
    i = 0
    while i < len(s):
        c = s[i]
        if c == "\\":
            nxt = s[i + 1] if i + 1 < len(s) else ""
            if nxt in valid:
                out.append(s[i:i + 2])
            else:
                out.append("\\\\" + nxt)
            i += 2 if nxt else 1
        else:
            out.append(c)
            i += 1
    return "".join(out)


def extract_verdict(text: str) -> dict | None:
    """JSON-вердикт из финального сообщения (устойчиво к обрамлению).

    Модели ломают JSON типовыми способами: невалидный \\-escape в строке
    (qwen: «...\\Что-то»), markdown-обрамление, проза до/после. Порядок
    попыток: прямой парс спана {..}, ремонт эскейпов, перебор вложенных
    спанов от первой '{' к последней '}'."""
    start, end = text.find("{"), text.rfind("}")
    if start == -1 or end <= start:
        return None
    candidates = [text[start:end + 1]]
    repaired = _repair_escapes(text[start:end + 1])
    if repaired != candidates[0]:
        candidates.append(repaired)
    candidates.extend(repaired[i:repaired.rfind('}') + 1]
                      for i in range(len(repaired))
                      if repaired[i] == "{")  # вложенные/частичные спаны
    for cand in candidates:
        try:
            verdict = json.loads(cand)
        except json.JSONDecodeError:
            continue
        if isinstance(verdict, dict):
            return verdict
    return None


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__,
                                     formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--model", required=True,
                        help="тег модели (ollama) или id deepseek (deepseek-flash)")
    parser.add_argument("--provider", default="ollama", choices=["ollama", "deepseek"],
                        help="ollama (сервер моделей) / deepseek (родной API)")
    parser.add_argument("--target", required=True,
                        help="каталог прогона этапа 2 (объект эталон-сетки)")
    parser.add_argument("--results", required=True, help="каталог результатов run-XX")
    parser.add_argument("--thinking", default="max", choices=PI_CHOICES)
    parser.add_argument("--context", type=int, default=DEFAULT_CONTEXT)
    parser.add_argument("--max-tokens", type=int, default=8192)
    parser.add_argument("--timeout", type=int, default=DEFAULT_TIMEOUT)
    parser.add_argument("--smoke", action="store_true", help="проверка сантехники")
    parser.add_argument("--keep", action="store_true", help="не удалять временные каталоги")
    args = parser.parse_args()

    target = Path(args.target)
    if not target.exists():
        candidate = models_repo() / args.target  # путь относительно корня Models
        if candidate.exists():
            target = candidate
    target = target.resolve()
    if not args.smoke:
        if not (target / "solution").is_dir() or not (target / "metrics.json").is_file():
            sys.exit(f"в {target} нет solution/ или metrics.json — это не прогон этапа 2")
        reference = load_reference(target)
        print(f"объект ревью: [{reference['slug']}] {reference['title']}")

    results = Path(args.results).resolve()
    results.mkdir(parents=True, exist_ok=True)
    (results / "pi-session").mkdir(exist_ok=True)

    tmp = Path(tempfile.mkdtemp(prefix="review-pi-"))
    ws = tmp / "workspace"
    ws.mkdir()
    started = time.time()
    meta: dict = {
        "model": args.model,
        "provider": args.provider,
        "thinking": args.thinking,
        "context": args.context,
        "timeout_s": args.timeout,
        "stage": "review",
        "protocol": PROTOCOL,
        "materials": PROTOCOL_MATERIALS,
        "smoke": args.smoke,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "target": str(target),
    }
    if not args.smoke:
        meta["solution_slug"] = reference["slug"]
        meta["reference"] = reference["_path"]

    run: PiRun | None = None
    try:
        # энергичный максимум thinking при 8192 выжигает бюджет в пустой ответ
        if args.max_tokens == 8192 and model_thinking_values(args.model):
            _, eager = thinking_map_for(model_thinking_values(args.model))
            if eager:
                args.max_tokens = 32768
                print("энергичный максимум thinking: maxTokens поднят до 32768")
        if args.provider == "deepseek" and args.max_tokens == 8192:
            args.max_tokens = 16000

        if args.smoke:
            shutil.copy(BILLING / "skeleton" / "TASK.md", ws / "TASK.md")
            prompt = SMOKE_PROMPT
        else:
            build_workspace(ws, target)
            prompt = REVIEW_PROMPT
        before = tree_manifest(ws)

        pi_home, _, extra_env = build_pi_home(
            tmp, args.model, args.context, args.max_tokens, True, args.provider)
        model_ref = f"{args.provider}/{args.model}" if args.provider == "deepseek" else args.model
        cmd = [
            "pi", "-p",
            "--session-dir", str(results / "pi-session"),
            "--model", model_ref,
            "--thinking", args.thinking,
            "--system-prompt", SYSTEM,
            "--tools", "read",
            prompt,
        ]
        env = {
            **dict(os.environ),
            "PI_CODING_AGENT_DIR": str(pi_home),
            "PI_OFFLINE": "1",
            **extra_env,
        }
        print(f"запуск: pi -p --model {model_ref} --thinking {args.thinking} "
              f"(ctx {args.context}, tools=read)")
        print(f"  cwd={ws}\n  лог (live): {results / 'pi-run.log'}")

        run_holder: dict[str, PiRun] = {}

        def on_signal(signum: int, _frame: object) -> None:
            if (r := run_holder.get("run")) is not None:
                r.terminate(reason=f"signal {signum}")

        signal.signal(signal.SIGTERM, on_signal)
        signal.signal(signal.SIGINT, on_signal)

        run = PiRun(cmd, ws, env, results / "pi-run.log")
        run_holder["run"] = run
        rc = run.wait(timeout=args.timeout)
        elapsed = time.strftime("%H:%M:%S", time.gmtime(time.time() - started))
        print(f"pi завершился: код={rc}, убит={'да' if run.killed else 'нет'}, время={elapsed}")
        meta.update({"exit_code": rc, "killed": run.killed})

        # целостность: ревьюер не имеет инструментов правки, но проверяем
        after = tree_manifest(ws)
        changed = sorted(k for k in set(before) | set(after) if before.get(k) != after.get(k))
        meta["integrity"] = {
            "status": "ok" if not changed else "modified",
            "changed_files": changed,
            "files": len(before),
        }
        if changed:
            print(f"ЦЕЛОСТНОСТЬ НАРУШЕНА: {len(changed)} файл(ов) изменены — прогон compromised")

        reply, usage, tool_calls = final_reply_and_usage(results / "pi-session")
        meta["tokens"] = usage
        meta["tool_calls"] = tool_calls
        verdict = extract_verdict(reply) if reply and not args.smoke else None
        meta["parse_ok"] = verdict is not None
        if tool_calls == 0 and not args.smoke:
            meta["invalid_reason"] = "ни одного tool-call: вердикт без чтения кода"
            print("ВЕРДИКТ БЕЗ ЧТЕНИЯ: ни одного tool-call — прогон не засчитывается")
        meta["parse_ok"] = verdict is not None
        (results / "review.md").write_text(
            "# Ревью этапа 3 — сырой финальный ответ\n\n"
            f"- ревьюер: `{args.model}` (thinking {args.thinking})\n"
            f"- объект: `{target}`"
            + (f" [{reference['slug']}]" if not args.smoke else "")
            + "\n\n<details><summary>Сырой ответ</summary>\n\n```\n"
            + (reply or "(ответ не получен)") + "\n```\n</details>\n",
            encoding="utf-8",
        )
        if verdict is not None:
            (results / "review.json").write_text(
                json.dumps(verdict, ensure_ascii=False, indent=2) + "\n",
                encoding="utf-8",
            )
            print(f"вердикт: A={verdict.get('axis_a')} B={verdict.get('axis_b')} "
                  f"F={verdict.get('functional')}, дефектов: {len(verdict.get('defects', []))}")
        else:
            print("JSON-вердикт не распознан — сырой ответ в review.md (parse_ok=false)")

        session_files = sorted((results / "pi-session").glob("*.jsonl"))
        meta["session_file"] = session_files[-1].name if session_files else None
        return rc
    finally:
        if run is not None:
            meta.setdefault("exit_code", run.proc.returncode)
            meta["killed"] = run.killed
        meta["finished_at"] = datetime.now(timezone.utc).isoformat()
        meta["duration_s"] = round(time.time() - started, 1)
        (results / "run.json").write_text(
            json.dumps(meta, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
        if args.keep:
            print(f"(keep) временные каталоги: {tmp}")
        else:
            shutil.rmtree(tmp, ignore_errors=True)


if __name__ == "__main__":
    sys.exit(main())

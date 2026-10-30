#!/usr/bin/env python3
"""Раннер тех-собеса для LLM через ollama.

Ведёт собеседование по сценарию (questions.json): один собес = один чат,
контекст только messages, без system prompt и инструментов. Каждый запрос
уходит с keep_alive: 0 — модель выгружается после ответа, кэш ollama не
переносится между вопросами и прогонами.

Примеры:
    python3 runner.py --model qwen3.8:latest --runs 5
    python3 runner.py --model gemma4:26b-pi-100k --runs 1 --limit 3
"""

import argparse
import json
import os
import sys
import time
import urllib.error
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
import env_local  # noqa: F401 — грузит .env.local до чтения OLLAMA_BASE_URL ниже

DEFAULT_BASE_URL = os.environ.get("OLLAMA_BASE_URL", "http://localhost:11434")
INTERVIEWER_FALLBACK = "Допущения делай сам, отвечай так, как считаешь правильным."
MAX_CLARIFICATIONS = 1  # сколько раз на вопрос допускается стандартная реплика


def http_json(url: str, payload: dict, timeout: int, retries: int = 3) -> dict:
    """POST с JSON и повторами ТОЛЬКО на сетевые сбои (контент не ретраим)."""
    data = json.dumps(payload).encode("utf-8")
    last_err = None
    for attempt in range(1, retries + 1):
        try:
            req = urllib.request.Request(
                url, data=data,
                headers={"Content-Type": "application/json"})
            with urllib.request.urlopen(req, timeout=timeout) as resp:
                return json.loads(resp.read().decode("utf-8"))
        except (urllib.error.URLError, ConnectionError, TimeoutError) as e:
            last_err = e
            if attempt < retries:
                wait = 5 * attempt
                print(f"    сеть недоступна ({e}); повтор через {wait}s",
                      file=sys.stderr)
                time.sleep(wait)
    raise RuntimeError(f"запрос к {url} не удался после {retries} попыток: {last_err}")


def unload_model(base_url: str, model: str, timeout: int) -> None:
    """Выгрузить модель из памяти (сброс KV-кэша ollama)."""
    http_json(f"{base_url}/api/generate",
              {"model": model, "keep_alive": 0}, timeout)


def slugify(tag: str) -> str:
    return tag.replace(":", "-").replace("/", "-")


def resolve_out_root(models_root: str, slug: str) -> Path:
    """Папка модели: приоритет уже существующей.

    - huihui_ai-Qwen3.8-abliterated-latest существует → используется как есть;
    - qwen3.8-latest не существует, а qwen3.8 есть (объединённые веса
      qwen3.8:27b = qwen3.8:latest) → используется qwen3.8.
    """
    root = Path(models_root)
    if (root / slug).is_dir():
        return root / slug
    if slug.endswith("-latest"):
        base = root / slug[:-len("-latest")]
        if base.is_dir():
            return base
    return root / slug


def chat_once(base_url: str, model: str, messages: list, keep_alive, think, timeout: int,
              num_ctx: int | None = None) -> dict:
    payload = {"model": model, "messages": messages,
               "stream": False, "keep_alive": keep_alive}
    if think in ("on", "off"):
        payload["think"] = (think == "on")
    elif think != "auto":
        payload["think"] = think  # уровни размышления: low / medium / xhigh
    if num_ctx is not None:
        payload["options"] = {"num_ctx": num_ctx}
    return http_json(f"{base_url}/api/chat", payload, timeout)


def chat_safe(base_url: str, model: str, messages: list, keep_alive, think, timeout: int,
              num_ctx: int | None = None) -> dict | None:
    """chat_once, но сетевой/таймаутный сбой возвращает None вместо исключения —
    серия не должна умирать из-за одного зависшего вопроса (модель может
    уйти в бесконечный thinking на длинном контексте)."""
    try:
        return chat_once(base_url, model, messages, keep_alive, think, timeout, num_ctx)
    except Exception as e:
        print(f"      ⚠️ сбой генерации ({e}); пропускаю", file=sys.stderr)
        return None


def looks_like_clarification(answer: str) -> bool:
    """Короткий ответ, оканчивающийся вопросом, считаем запросом уточнения.

    Длинные ответы с риторическим вопросом в конце уточнением не считаем.
    """
    if len(answer) >= 300:
        return False
    last = answer.rstrip().split("\n")[-1].strip()
    return last.endswith(("?", "？"))


def run_interview(base_url: str, model: str, questions: list, q_version, run_no: int,
                  out_dir: Path, keep_alive, think: str, timeout: int,
                  limit: int | None, role: str | None = None,
                  num_ctx: int | None = None, primer: str | None = None) -> None:
    print(f"  выгружаю модель перед прогоном...")
    unload_model(base_url, model, timeout)

    messages: list = []
    turns: list = []
    clarifications_used = 0

    session = {
        "model": model,
        "role": role,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": base_url,
        "keep_alive": keep_alive,
        "kv_cache": "session" if keep_alive not in (0, "0") else "none",
        "think": think,
        "num_ctx": num_ctx,
        "primer": primer,
        "question_count": 0, "questionnaire": q_version,
        "clarifications_used": 0,
        "turns": turns,
        # Полный массив messages, который реально ушёл модели:
        # только вопросы и финальные ответы, без thinking.
        "messages": messages,
    }

    def flush():
        """Прогрессивное сохранение: крах/перезагрузка серию не теряют."""
        session["question_count"] = len(turns)
        session["clarifications_used"] = clarifications_used
        out_dir.mkdir(parents=True, exist_ok=True)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    # Промпт-настройка (роль, инструкция CoT) — системным сообщением,
    # как в классическом API-паттерне system prompt.
    if primer:
        messages.append({"role": "system", "content": primer})

    to_ask = questions[:limit] if limit else questions
    for i, q in enumerate(to_ask, 1):
        messages.append({"role": "user", "content": q["text"]})
        t0 = time.monotonic()
        resp = chat_safe(base_url, model, messages, keep_alive, think, timeout, num_ctx)
        duration = time.monotonic() - t0
        if resp is None:
            answer, thinking = "(ошибка генерации: таймаут/сбой)", None
            messages.append({"role": "assistant", "content": answer})
            turns.append({"id": q["id"], "section": q["section"], "question": q["text"],
                          "answer": answer, "clarification": None, "followups": [],
                          "thinking": None, "duration_s": round(duration, 1),
                          "eval_count": None, "prompt_eval_count": None,
                          "prompt_eval_cached_count": None,
                          "done_reason": "error"})
            print(f"    [{q['id']}] {q['section']}: {duration:.0f}s — СБОЙ, продолжаю")
            continue

        msg = resp.get("message", {})
        answer = (msg.get("content") or "").strip()
        thinking = (msg.get("thinking") or "").strip() or None
        if not answer:
            answer = "(пустой ответ модели)"
        messages.append({"role": "assistant", "content": answer})

        clarification = None
        if (clarifications_used < MAX_CLARIFICATIONS
                and looks_like_clarification(answer)):
            print(f"    [{q['id']}] модель запросила уточнение — стандартная реплика")
            clarification = answer
            clarifications_used += 1
            messages.append({"role": "user", "content": INTERVIEWER_FALLBACK})
            t1 = time.monotonic()
            resp = chat_safe(base_url, model, messages, keep_alive, think, timeout, num_ctx) or {}
            duration += time.monotonic() - t1
            msg = resp.get("message", {})
            answer = (msg.get("content") or "").strip() or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": answer})

        followups = []
        for fu_text in q.get("followups") or []:
            messages.append({"role": "user", "content": fu_text})
            tf = time.monotonic()
            fres = chat_safe(base_url, model, messages, keep_alive, think, timeout, num_ctx) or {}
            fdur = time.monotonic() - tf
            fanswer = (fres.get("message", {}).get("content") or "").strip() \
                or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": fanswer})
            followups.append({
                "text": fu_text, "answer": fanswer,
                "duration_s": round(fdur, 1),
                "eval_count": fres.get("eval_count"),
                "prompt_eval_count": fres.get("prompt_eval_count"),
                "prompt_eval_cached_count": fres.get("prompt_eval_cached_count"),
                "done_reason": fres.get("done_reason"),
            })
            print(f"      ↳ углубление: {fdur:.0f}s, "
                  f"{fres.get('eval_count', '?')} tok")

        turns.append({
            "id": q["id"], "section": q["section"], "question": q["text"],
            "answer": answer, "clarification": clarification,
            "followups": followups,
            "thinking": thinking,
            "duration_s": round(duration, 1),
            "eval_count": resp.get("eval_count"),
            "prompt_eval_count": resp.get("prompt_eval_count"),
            # ollama 0.40+: сколько токенов промпта взято из KV-кэша сессии
            "prompt_eval_cached_count": resp.get("prompt_eval_cached_count"),
            "done_reason": resp.get("done_reason"),
        })
        truncated = resp.get("done_reason") not in (None, "stop")
        warn = " ⚠️ ОБРЫВ: " + str(resp.get("done_reason")) if truncated else ""
        print(f"    [{q['id']}] {q['section']}: {duration:.0f}s, "
              f"{resp.get('eval_count', '?')} tok{warn}")
        flush()

    flush()
    (out_dir / "transcript.md").write_text(
        render_transcript(session), encoding="utf-8")
    total = sum(t["duration_s"] for t in turns)
    print(f"  готово: {out_dir} ({len(turns)} вопросов, {total:.0f}s)")


def render_transcript(s: dict) -> str:
    role = f" · роль: {s['role']}" if s.get("role") else ""
    ctx = f" · num_ctx: {s['num_ctx']}" if s.get("num_ctx") else ""
    lines = [
        f"# Собес: `{s['model']}`{role} — run-{s['run']:02d}",
        "",
        f"- Дата: {s['started_at']}",
        f"- Сервер: {s['base_url']} · keep_alive: {s['keep_alive']} · think: {s['think']}{ctx}",
        f"- Вопросов: {s['question_count']}",
        "",
    ]
    if s.get("primer"):
        lines += [
            "## Настройка интервьюера (system)",
            "",
            f"**System:** {s['primer']}",
            "",
        ]
    lines += ["---", ""]
    for t in s["turns"]:
        lines += [f"## {t['id']} · {t['section']}", ""]
        lines += [f"**Интервьюер:** {t['question']}", ""]
        if t["thinking"]:
            lines += ["<details><summary>Ход мысли модели</summary>", "",
                      t["thinking"], "", "</details>", ""]
        if t.get("clarification"):
            lines += [f"**Кандидат (уточнение):**", "", t["clarification"], ""]
            lines += [f"**Интервьюер:** {INTERVIEWER_FALLBACK}", ""]
        lines += [f"**Кандидат:**", "", t["answer"], ""]
        for fu in t.get("followups") or []:
            lines += [f"### ↳ Углубление", ""]
            lines += [f"**Интервьюер:** {fu['text']}", ""]
            lines += [f"**Кандидат:**", "", fu["answer"], ""]
        meta = [f"⏱ {t['duration_s']}s"]
        if t.get("prompt_eval_count"):
            meta.append(f"prompt {t['prompt_eval_count']} tok")
        if t.get("eval_count"):
            meta.append(f"output {t['eval_count']} tok")
        if t.get("done_reason") and t["done_reason"] != "stop":
            meta.append(f"⚠️ обрыв: {t['done_reason']}")
        lines += [f"> {' · '.join(meta)}", "", "---", ""]
    return "\n".join(lines)


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", required=True, help="ollama-тег модели на сервере ollama")
    p.add_argument("--runs", type=int, default=1, help="число прогонов (default 1)")
    p.add_argument("--base-url", default=DEFAULT_BASE_URL)
    p.add_argument("--questions", default=str(SCRIPT_DIR / "questions.json"))
    p.add_argument("--models-root", default=str(SCRIPT_DIR.parent / "models"),
                   help="корень workspace (по умолчанию — родитель interview/)")
    p.add_argument("--out", default=None,
                   help="папка модели; по умолчанию <models-root>/<slug модели>")
    p.add_argument("--run", type=int, default=None,
                   help="номер первого прогона; по умолчанию — первый свободный")
    p.add_argument("--limit", type=int, default=None,
                   help="только первые N вопросов (для smoke-теста)")
    p.add_argument("--only", default=None,
                   help="только вопросы с этими id через запятую (например Q04,Q24)")
    p.add_argument("--role", default=None,
                   help="роль собеса (подпапка в результатах); по умолчанию берётся "
                        "из пути questions-файла, например roles/backend → backend")
    p.add_argument("--keep-alive", default="10m",
                   help="keep_alive для ollama внутри сессии (default 10m: тёплый KV-кэш "
                        "на один собес; изоляция прогонов — выгрузкой до и после каждого)")
    p.add_argument("--think", choices=["auto", "on", "off", "low", "medium", "xhigh"],
                   default="auto",
                   help="режим thinking: auto — как модель настроена по умолчанию; "
                        "low/medium/xhigh — уровни размышления (ollama >= 0.40, "
                        "поддержка зависит от модели, см. /api/show → thinking)")
    p.add_argument("--num-ctx", type=int, default=None,
                   help="переопределить num_ctx ollama (дефолт сервера может быть 32k "
                        "и обрезать длинные собесы; например 65536)")
    p.add_argument("--primer", default=None,
                   help="промпт-настройка (роль, CoT-инструкция) отдельным сообщением "
                        "перед собесом; ответ модели в оценку не идёт")
    p.add_argument("--timeout", type=int, default=900,
                   help="таймаут одного запроса, сек (default 900)")
    args = p.parse_args()

    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata
    if args.only:
        wanted = [x.strip() for x in args.only.split(",") if x.strip()]
        by_id = {q["id"]: q for q in questions}
        missing = [x for x in wanted if x not in by_id]
        if missing:
            print(f"неизвестные id вопросов: {', '.join(missing)}", file=sys.stderr)
            return 2
        questions = [by_id[x] for x in wanted]

    role = args.role
    if role is None:
        parent = Path(args.questions).resolve().parent.name
        if parent != "interview":
            role = parent
    out_root = Path(args.out) if args.out else resolve_out_root(
        args.models_root, slugify(args.model))
    if role and args.out is None:
        out_root = out_root / role / "interview"

    keep_alive = int(args.keep_alive) if str(args.keep_alive).lstrip("-").isdigit() \
        else args.keep_alive

    run_no = args.run
    for i in range(args.runs):
        if run_no is None:
            run_no = 1
            while (out_root / f"run-{run_no:02d}").exists():
                run_no += 1
        out_dir = out_root / f"run-{run_no:02d}"
        if out_dir.exists():
            print(f"папка {out_dir} уже существует — пропускаю "
                  f"(удалите или укажите --run)", file=sys.stderr)
            run_no += 1
            continue
        print(f"[run-{run_no:02d}] {args.model}"
              + (f" · роль: {role}" if role else ""))
        run_interview(args.base_url, args.model, questions, q_version, run_no, out_dir,
                      keep_alive, args.think, args.timeout, args.limit, role,
                      args.num_ctx, args.primer)
        run_no += 1
        # выгрузка после КАЖДОГО прогона: кэш сессии не должен греть следующий собес
        try:
            unload_model(args.base_url, args.model, args.timeout)
            print("  модель выгружена (изоляция прогонов)")
        except Exception as e:
            print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

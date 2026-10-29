#!/usr/bin/env python3
"""Ярус 3 — ревью решения изолированной pi-сессией с glm-5.3-flash, thinking low
(паттерн judge_glm.py).

В отличие от судьи яруса 4 (judge_tier2.py — тоже glm-5.3-flash low, но
слепое ревью одной оси по диффу), ревьюер яруса 3 знает результат чекера
и метрик и оценивает обе оси рубрики (RUBRIC.md): A «код и архитектура»
и B «процесс и коммуникация».

Запуск — отдельная pi-сессия с подписочной моделью zai/glm-5.3;
сабагенты Agent tool и OpenRouter запрещены (правило AGENTS.md
рабочего репозитория), подписка Z.AI — единственный канал.

Использование:
    python3 judge_tier3.py --run models/<модель>/backend/test-assignment/run-XX \
        [--thinking low]

Результат: <run>/review-zcode.md (вердикт + сырой ответ; промпт — SYSTEM
ниже, он же печатается в отчёте этапа). Вердикт:
оси A и B 0–4 (шаг 0.5) + обоснование с цитатами из диффа.
"""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
import tempfile
import time
from pathlib import Path

from judge_common import run_judge, save_sidecar

HERE = Path(__file__).resolve().parent
BILLING = HERE.parent
MODEL = "zai/glm-5.3-flash"
TIMEOUT_S = 1200
RETRIES = 2
# дифф больше этого размера обрезаем с конца. Потолок с запасом выше любого
# диффа этапа (максимум 3799 строк; vendor исключён в раннере) — обрезка
# недостижима, страховка от аномально огромного решения
MAX_DIFF_LINES = 12000

SYSTEM = """Ты — изолированный ревьюер (ярус 3) тестового задания «биллинг LLM-токенов» (PHP/Symfony/Doctrine). Тебе дают текст задания (TASK.md), дифф решения против каркаса, результат детерминированного чекера и метрики качества. Оцени решение по ДВУМ осям рубрики.

Ось A «код и архитектура» (0–4):
0 — решение нерабочее/отсутствует;
1 — частично работает; домен не смоделирован; деньги или время на float;
2 — обязательная часть в целом работает; сущности есть, границы слоёв условные; edge-case'ы (границы окон, unrated, дубли) поверхностно;
3 — корректная доменная модель (тарификация отделена от приёма и отчёта); точные деньги (bcmath, одно округление) и время; схема воспроизводима; edge-case'ы покрыты;
4 — то же + бонус; «дешёвые» решения где можно, явность где нужно; тесты добавляют уверенность.

Ось B «процесс и коммуникация» (0–4):
0 — нет артефактов, самопроверок, след хаотичен;
1 — артефакты формальные; самопроверок нет; commit/PR без обоснования;
2 — самопроверки точечные (прогнал команды, не проверил вывод); артефакты шаблонные;
3 — систематическая самопроверка (тесты, приёмка по шагам, статанализ); commit/PR объясняют решения; отступления от задания осознанны;
4 — то же + самопроверка на границах условий (граничные значения, дубли, ретро-бонус); артефакты читаются как отчёт инженера.

Правила: верь чекеру, а не внешнему виду кода; находи дефекты, которых не видно в диффе (float в деньгах, N+1, мёртвый код) — citируй строки диффа как доказательства; не наказывай за стиль, который не оговорён заданием.

Финальное сообщение — ТОЛЬКО JSON одной строкой:
{"axis_a":<0-4, шаг 0.5>,"axis_b":<0-4, шаг 0.5>,"note":"5-10 предложений по-русски: сильные стороны, дефекты с цитатами, что учтено из чекера/метрик"}"""


def build_facts(run_dir: Path) -> list[str]:
    """Блок ФАКТОВ для промпта ревьюера: выжимка ярусов 1–2 по прогону.

    Метрики подаются агрегатами (phpcs/psalm/phpstan/LOC + AST-сводка);
    phpmd сознательно не сообщаем — наблюдение, в оценку не входит.
    Сырые выводы инструментов ревьюеру не дают — только факты чекера
    с деталями упавших сценариев. Используется и отчётами
    как документация входа ревьюера.
    """
    facts = []
    check = run_dir / "check.json"
    if check.exists():
        cj = json.loads(check.read_text())
        facts.append(f"Чекер: обязательных {cj.get('mandatory_passed')}/{cj.get('mandatory_total')}, "
                     f"бонус {cj.get('bonus_passed')}/{cj.get('bonus_total')}")
        for k, v in (cj.get("scenarios") or {}).items():
            if v.get("status") == "fail":
                facts.append(f"  fail {k}: {str(v.get('detail'))[:120]}")
    metrics = run_dir / "metrics.json"
    if metrics.exists():
        mj = json.loads(metrics.read_text())
        pc, psl, ps = mj.get("phpcs") or {}, mj.get("psalm") or {}, mj.get("phpstan") or {}
        scc = mj.get("scc") or {}
        facts.append(f"Метрики: phpcs {pc.get('violations')}, psalm {psl.get('issues')}, "
                     f"phpstan {ps.get('errors')}, loc {scc.get('loc')}")
        ast = mj.get("ast_metrics") or {}
        if ast:
            facts.append(f"AST: cc_max {ast.get('method_cc_max')}, lcom4>1: {ast.get('lcom4_over_1')}, "
                         f"превышения порогов {ast.get('findings_thresholds')}")
    return facts


def judge_run(run_dir: Path, thinking: str) -> int:
    task = (BILLING / "skeleton" / "TASK.md").read_text(encoding="utf-8")
    diff = (run_dir / "diff.patch").read_text(encoding="utf-8")
    diff_lines = diff.splitlines()
    trimmed = len(diff_lines) > MAX_DIFF_LINES
    diff_text = "\n".join(diff_lines[:MAX_DIFF_LINES])
    if trimmed:
        diff_text += f"\n… (обрезано, всего {len(diff_lines)} строк)"

    facts = build_facts(run_dir)

    prompt = (f"ТЕКСТ ЗАДАНИЯ (TASK.md):\n{task}\n\n"
              f"ДИФФ РЕШЕНИЯ (против каркаса; vendor и тестовые фикстуры чекера вне диффа):\n{diff_text}\n\n"
              f"ФАКТЫ (детерминированные ярусы):\n" + "\n".join(facts) +
              "\n\nОцени оси A и B. Ответ — только JSON.")

    last_err = None
    for attempt in range(1, RETRIES + 1):
        try:
            out, m = run_judge(MODEL, thinking, SYSTEM, prompt, TIMEOUT_S, tools="bash")
            reply = out.stdout.strip()
            if out.returncode != 0 or not reply:
                raise RuntimeError(f"exit {out.returncode}: {out.stderr[:300]}")
            mm = re.search(r"\{.*\}", reply, re.S)
            if not mm:
                raise RuntimeError(f"нет JSON в ответе: {reply[:300]}")
            verdict = json.loads(mm.group(0))
            a, b = float(verdict["axis_a"]), float(verdict["axis_b"])
            assert 0 <= a <= 4 and 0 <= b <= 4
            note = verdict.get("note", "")
            md = [
                f"# Ревью яруса 3 — изолированная pi-сессия ({MODEL})",
                "",
                f"- прогон: `{run_dir}`",
                f"- модель судьи: `{MODEL}`, thinking: `{thinking}`",
                f"- время/токены: {m['duration_s']} с · {m['tokens_in']:,}/{m['tokens_out']:,}"
                f" вх/вых · всего {m['tokens_total']:,}",
                f"- ось A «код и архитектура»: **{a:g}**",
                f"- ось B «процесс и коммуникация»: **{b:g}**",
                "",
                "## Обоснование",
                "",
                note,
                "",
                "<details><summary>Сырой ответ</summary>",
                "",
                "```", reply, "```",
                "</details>",
            ]
            (run_dir / "review-zcode.md").write_text("\n".join(md) + "\n", encoding="utf-8")
            save_sidecar(run_dir, "tier3.json", {
                "tier": 3, "model": MODEL, "thinking": thinking,
                "axis_a": a, "axis_b": b, "attempt": attempt, **m})
            print(f"{run_dir}: A={a:g} B={b:g} ({m['duration_s']}с, {m['tokens_total']:,} ток.)")
            return 0
        except Exception as e:  # noqa: BLE001
            last_err = e
            print(f"  попытка {attempt} не удалась: {e}", file=sys.stderr)
            time.sleep(5)
    print(f"ОШИБКА {run_dir}: {last_err}", file=sys.stderr)
    return 1


def main() -> int:
    ap = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    ap.add_argument("--run", required=True, type=Path,
                    help="каталог прогона models/<модель>/backend/test-assignment/run-XX")
    ap.add_argument("--thinking", default="low",
                    help="уровень мышления ревьюера (по умолчанию low)")
    args = ap.parse_args()
    run_dir = args.run.resolve()
    if not (run_dir / "diff.patch").exists():
        print(f"нет diff.patch в {run_dir}", file=sys.stderr)
        return 2
    return judge_run(run_dir, args.thinking)


if __name__ == "__main__":
    raise SystemExit(main())

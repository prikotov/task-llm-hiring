#!/usr/bin/env python3
"""Ярус 4 — слепое второе мнение GLM-5.3-Flash по тестовому заданию (паттерн judge_glm.py).

Изолированный запуск `pi -p --no-session --model zai/glm-5.3-flash` с bash:
судья получает TASK.md, дифф решения и дескрипторы оси A рубрики, отвечает
JSON-вердиктом. Сабагенты Agent tool и OpenRouter запрещены (правило
AGENTS.md рабочего репозитория); подписка Z.AI — единственный канал.

Использование:
    python3 judge_tier2.py --run models/<модель>/backend/test-assignment/run-XX \
        [--thinking low]

Результат: <run>/judge-glm.md (вердикт + сырой ответ). Вердикт: ось A 0–4
(шаг 0.5) + цитаты-доказательства из диффа.
"""

from __future__ import annotations

import argparse
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
TIMEOUT_S = 900
RETRIES = 2
# дифф больше этого размера обрезаем с конца (первые строки — самые важные:
# там сущности и домен), судья предупрежён об обрезке
MAX_DIFF_LINES = 2600

SYSTEM = """Ты — изолированный судья-ревьюер (ярус 4, слепой) тестового задания «биллинг LLM-токенов» (PHP/Symfony/Doctrine). Тебе дают текст задания (TASK.md) и дифф решения против пустого каркаса. Оцени код по ОСИ A рубрики — «код и архитектура».

Шкала оси A (0–4):
0 — решения нет / не работает вовсе;
1 — частично; домен не смоделирован (всё в командах); деньги или время на float;
2 — обязательная часть в целом работает; сущности есть, но границы слоёв условные; к edge-case'ам (границы окон, unrated, дубли) отношение поверхностное;
3 — корректная доменная модель (тарификация отделена от приёма и отчёта); точная работа с деньгами (bcmath/decimal, без float) и временем; схема воспроизводима; edge-cases покрыты кодом или тестами;
4 — то же + решения «дешёвые» там, где можно проще, и явные там, где нужна явность; тесты добавляют уверенность; код читается как объяснение домена.

Обращай внимание: одна схема округления HALF_UP до 6 знаков на уровне события; выбор строки цены по valid_from <= ts; оф-пик-окно через полночь в UTC; идемпотентность по (provider, provider_request_id); исключение float для денег.

Финальное сообщение — ТОЛЬКО JSON одной строкой:
{"axis_a": <0-4, шаг 0.5>, "quotes": ["2-4 короткие цитаты из диффа как доказательства"], "note": "2-4 предложения по-русски: сильные стороны, слабости, что смутило"}"""


def judge(run_dir: Path, thinking: str) -> dict:
    task = (BILLING / "skeleton" / "TASK.md").read_text(encoding="utf-8")
    diff = (run_dir / "diff.patch").read_text(encoding="utf-8").splitlines()
    if len(diff) > MAX_DIFF_LINES:
        diff = diff[:MAX_DIFF_LINES]
        diff.append("… [дифф обрезан судейским скриптом]")
    prompt = (f"ТЕКСТ ЗАДАНИЯ (TASK.md):\n```\n{task}\n```\n\n"
              f"ДИФФ РЕШЕНИЯ (skeleton -> решение):\n```diff\n" + "\n".join(diff) + "\n```\n\n"
              f"Оцени решение по оси A (шкала выше). Отвечай только JSON.")

    last_err = None
    for attempt in range(1, RETRIES + 1):
        try:
            out, met = run_judge(MODEL, thinking, SYSTEM, prompt, TIMEOUT_S, tools="bash")
            reply = out.stdout.strip()
            if out.returncode != 0 or not reply:
                raise RuntimeError(f"exit {out.returncode}: {out.stderr[:200]}")
            mm = re.search(r"\{.*\}", reply, re.S)
            if not mm:
                raise RuntimeError(f"нет JSON в ответе: {reply[:200]}")
            import json

            verdict = json.loads(mm.group(0))
            verdict["_raw"] = reply
            verdict["_metrics"] = met
            verdict["_thinking"] = thinking
            return verdict
        except Exception as exc:  # noqa: BLE001
            last_err = exc
            time.sleep(5)
    return {"error": str(last_err)}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--run", required=True, help="каталог run-XX")
    parser.add_argument("--thinking", default="low",
                        help="уровень мышления судьи (default low, как judge_glm)")
    args = parser.parse_args()
    run_dir = Path(args.run).resolve()

    v = judge(run_dir, args.thinking)
    raw = v.pop("_raw", "")
    met = v.pop("_metrics", {})
    if met:
        save_sidecar(run_dir, "tier4.json", {
            "tier": 4, "model": MODEL, "thinking": v.get("_thinking", args.thinking),
            "axis_a": v.get("axis_a"), **met})
    header = [
        f"# Ярус 4: GLM-5.3-Flash (pi, thinking={v.get('_thinking', args.thinking)}, "
        f"{met.get('duration_s', '?')}с, {met.get('tokens_total', 0):,} ток.)",
        "",
        f"- **Ось A (судья яруса 4): {v.get('axis_a', '—')}**"
        if "axis_a" in v else f"- ошибка: {v.get('error', '—')}",
    ]
    for q in v.get("quotes", []):
        header.append(f"  - цитата: `{q}`")
    if v.get("note"):
        header.append(f"- заметка: {v['note']}")
    (run_dir / "judge-glm.md").write_text("\n".join(header) + "\n\n<details><summary>сырой ответ</summary>\n\n```\n" + raw + "\n```\n\n</details>\n", encoding="utf-8")
    print(f"{run_dir.name}: axis_a={v.get('axis_a', 'ERR')} ({v.get('_seconds', '?')}с)")
    return 0


if __name__ == "__main__":
    sys.exit(main())

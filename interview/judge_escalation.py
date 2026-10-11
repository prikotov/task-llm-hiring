#!/usr/bin/env python3
"""Явная метрика эскалации: clef сам говорит, нужен ли старший судья.

Два noul-вопроса к каждому ответу:
- need_review  — нужно ли передать старшей модели (фактчек-зона/неуверенность)
- unsure       — есть ли неуверенность в собственной оценке качества

Пример:
    python3 judge_escalation.py --run ../ornith-1.5-9b/backend/run-01
"""

import argparse
import csv
import json
import sys
import time
import urllib.request
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from judge import build_tasks, BASE_URL

QUESTIONS = {
    "need_review": {
        "type": "noul",
        "instructions": ("Нужно ли передать этот ответ на проверку более знающему судье? "
                         "Ответь «да», если в ответе есть утверждения, требующие факт-проверки "
                         "по документации или запуску кода (конкретные API, версии фреймворков, "
                         "статистика, внутренности реализаций), или если ты не уверен, "
                         "что сам можешь верно оценить этот ответ."),
        "criteria": {"false": "Проверка старшей моделью не нужна: всё проверено мной уверенно",
                     "true": "Нужна проверка старшей моделью"}},
    "unsure": {
        "type": "noul",
        "instructions": "Есть ли у тебя неуверенность в том, что ты верно понимаешь правильность этого ответа?",
        "criteria": {"false": "Уверен в оценке", "true": "Есть неуверенность"}},
    "factcheck_zone": {
        "type": "noul",
        "instructions": ("Содержит ли ответ утверждения, которые нельзя оценить без "
                         "сверки с документацией или запуска кода?"),
        "criteria": {"false": "Нет, всё оцениваемо по общим знаниям",
                     "true": "Да, есть такие утверждения"}},
}


def ask(judge: str, task: dict, timeout: int) -> dict:
    state = (f"Вопрос технического собеседования PHP/Symfony/Doctrine/PostgreSQL/DDD:\n"
             f"{task['question']}\n\nОТВЕТ КАНДИДАТА:\n{task['answer']}")
    payload = {"model": judge, "state": state, "questions": QUESTIONS}
    req = urllib.request.Request(f"{BASE_URL}/v1/systemone",
                                 data=json.dumps(payload, ensure_ascii=False).encode("utf-8"),
                                 headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    return {"question": task["id"],
            **{k: round(v.get("noul", -1), 3) for k, v in r["answers"].items()}}


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--run", required=True)
    p.add_argument("--judge", default="clef")
    p.add_argument("--timeout", type=int, default=600)
    args = p.parse_args()

    run_dir = Path(args.run)
    session = json.loads((run_dir / "session.json").read_text())
    tasks = build_tasks(session)
    rows, t0 = [], time.monotonic()
    for i, task in enumerate(tasks, 1):
        try:
            rows.append(ask(args.judge, task, args.timeout))
        except Exception as e:
            print(f"  {i}/{len(tasks)} {task['id']}: СБОЙ {e}", flush=True)
            rows.append({"question": task["id"], "error": str(e)[:200]})
        if i % 20 == 0 or i == len(tasks):
            print(f"  {i}/{len(tasks)} ({time.monotonic()-t0:.0f}s)", flush=True)

    out = run_dir.parent / "judgments" / f"escalation-{args.judge}-{run_dir.name}.csv"
    out.parent.mkdir(parents=True, exist_ok=True)
    with open(out, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=["question", "need_review", "unsure",
                                          "factcheck_zone", "error"],
                           extrasaction="ignore")
        w.writeheader()
        w.writerows(rows)
    print(f"готово: {out} ({sum(1 for r in rows if 'error' not in r)}/{len(rows)})")
    return 0


if __name__ == "__main__":
    sys.exit(main())

#!/usr/bin/env python3
"""Полный сбор decision-метрик: 9 вопросов одним запросом на каждый ответ.

Вопросы: quality (score 0-4), hallucination (noul p) + 7 метрик
(has_code, structured, depth, names_api, precise_numbers, self_doubt,
contradictions). Вывод: <run>/../judgments/decision-<judge>-<run>.csv

Пример:
    python3 judge_full.py --run ../ornith-1.5-9b/backend/interview/run-01 --judge clef
"""

import argparse
import csv
import json
import sys
import time
import urllib.request
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from score import build_tasks, BASE_URL, SCORE_CRITERIA
from judge_metrics import METRICS

QUESTIONS = {
    "quality": {"type": "score",
                "instructions": "Оцени качество ответа кандидата на вопрос собеседования",
                "criteria": SCORE_CRITERIA},
    "hallucination": {"type": "noul",
                      "instructions": "Содержит ли ответ выдуманные факты, несуществующие API/классы/методы или фальшивые источники?",
                      "criteria": {"false": "Выдуманного нет", "true": "Есть выдуманные факты, API или источники"}},
    **METRICS,
}
COLS = ["question", "score", "conf", "hall_p",
        "has_code", "structured", "depth", "names_api",
        "precise_numbers", "self_doubt", "contradictions"]


def ask(judge: str, task: dict, timeout: int) -> dict:
    state = (f"Вопрос технического собеседования PHP/Symfony/Doctrine/PostgreSQL/DDD-разработчика:\n"
             f"{task['question']}\n\nОТВЕТ КАНДИДАТА:\n{task['answer']}")
    payload = {"model": judge, "state": state, "questions": QUESTIONS}
    req = urllib.request.Request(f"{BASE_URL}/v1/systemone",
                                 data=json.dumps(payload, ensure_ascii=False).encode("utf-8"),
                                 headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    a = r["answers"]
    row = {"question": task["id"],
           "score": round(a["quality"].get("score", -1), 3),
           "conf": round(a["quality"].get("confidence", 0) or 0, 3),
           "hall_p": round(a["hallucination"].get("noul", -1), 3)}
    for k in METRICS:
        v = a[k]
        row[k] = round(v.get("noul", v.get("score", -1)), 3)
    return row


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

    out = run_dir.parent / "judgments" / f"decision-{args.judge}-{run_dir.name}.csv"
    out.parent.mkdir(parents=True, exist_ok=True)
    with open(out, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=COLS + ["error"], extrasaction="ignore")
        w.writeheader()
        w.writerows(rows)
    ok = sum(1 for r in rows if "error" not in r)
    print(f"готово: {out} ({ok}/{len(rows)})")
    return 0


if __name__ == "__main__":
    sys.exit(main())

#!/usr/bin/env python3
"""Эксперимент: дополнительные вероятностные метрики decision-моделей.

Гипотеза: помимо score и p-галлюцинации, decision-модель может дать
дешёвые вероятностные признаки, коррелирующие с эталоном (GLM-Flash).
Прогон по одному прогону модели, результат — CSV рядом с вердиктами.

Пример:
    python3 judge_metrics.py --run ../ornith-1.5-9b/backend/interview/run-01 --judge clef
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

# Дополнительные метрики: noul (вероятность «да») + score (уровневая).
NoulQ = dict  # question spec
METRICS = {
    # — признаки, по гипотезе связанные с качеством (glm_score) —
    "has_code": {
        "type": "noul", "instructions": "Содержит ли ответ примеры кода?",
        "criteria": {"false": "Кода нет", "true": "Есть блоки кода"}},
    "structured": {
        "type": "noul", "instructions": "Хорошо ли структурирован ответ (разделы, списки, таблицы)?",
        "criteria": {"false": "Сплошной текст", "true": "Чёткая структура"}},
    "depth": {
        "type": "score", "instructions": "Насколько ответ технически глубок (уровень деталей, нюансов, trade-offs)?",
        "criteria": ["0 — поверхностно, общие слова",
                     "1 — мало деталей",
                     "2 — средне",
                     "3 — детально, с нюансами",
                     "4 — экспертно: нюансы, trade-offs, границы применимости"]},
    # — признаки, по гипотезе связанные с галлюцинациями (glm_hall) —
    "names_api": {
        "type": "noul", "instructions": "Называет ли ответ конкретные имена API, классов, методов, функций, команд?",
        "criteria": {"false": "Конкретных имён мало или нет", "true": "Много конкретных имён"}},
    "precise_numbers": {
        "type": "noul", "instructions": "Содержит ли ответ точные числовые утверждения (версии, проценты, статистика, результаты измерений)?",
        "criteria": {"false": "Нет", "true": "Есть"}},
    "self_doubt": {
        "type": "noul", "instructions": "Признаёт ли ответ границы знания или неуверенность («не уверен», «зависит»)?",
        "criteria": {"false": "Нет", "true": "Да"}},
    "contradictions": {
        "type": "noul", "instructions": "Содержит ли ответ внутренние противоречия (утверждает и отрицает одно и то же)?",
        "criteria": {"false": "Нет", "true": "Есть"}},
}


def ask(judge: str, task: dict, timeout: int) -> dict:
    state = (f"Вопрос технического собеседования PHP/Symfony/Doctrine/PostgreSQL/DDD:\n"
             f"{task['question']}\n\nОТВЕТ КАНДИДАТА:\n{task['answer']}")
    payload = {"model": judge, "state": state, "questions": METRICS}
    req = urllib.request.Request(f"{BASE_URL}/v1/systemone",
                                 data=json.dumps(payload, ensure_ascii=False).encode("utf-8"),
                                 headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    row = {"question": task["id"]}
    for k, v in r["answers"].items():
        if v["type"] == "noul":
            row[k] = round(v.get("noul", -1), 3)
        else:
            row[k] = round(v.get("score", -1), 3)
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
            rows.append({"question": task["id"], "error": str(e)})
        if i % 10 == 0 or i == len(tasks):
            print(f"  {i}/{len(tasks)} ({time.monotonic()-t0:.0f}s)", flush=True)

    out = run_dir.parent / "judgments" / f"metrics-{args.judge}-{run_dir.name}.csv"
    out.parent.mkdir(parents=True, exist_ok=True)
    with open(out, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=["question"] + list(METRICS))
        w.writeheader()
        w.writerows(rows)
    print(f"готово: {out} ({len(rows)} строк)")
    return 0


if __name__ == "__main__":
    sys.exit(main())

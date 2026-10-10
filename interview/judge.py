#!/usr/bin/env python3
"""Судейство ответов собеса decision-моделями (clef, clef-flash) через /v1/systemone.

Каждый ответ судится изолированно: state = вопрос + ответ кандидата.
Follow-up судится с контекстом родительского вопроса.
Результат: <модель>/<роль>/judgments/<judge>/<run>.json — в папке модели,
рядом с прогонами (interview/ — только методология).

Пример:
    python3 judge.py --run ../ornith-1.5-35b/backend/run-06 --judge clef-flash
"""

import argparse
import json
import sys
import time
import urllib.request
from pathlib import Path

BASE_URL = "http://nserv:11434"

SCORE_CRITERIA = [
    "0 — ответ неверен, отсутствует или содержит грубые фактические ошибки",
    "1 — фрагментарный: отдельные верные тезисы без понимания связей, существенные пропуски",
    "2 — базовый: в целом верно, но поверхностно, без примеров, нюансов и типовых ошибок",
    "3 — уверенный: верно, со структурой, примерами и пониманием trade-offs",
    "4 — экспертный: верно и глубоко, с нюансами, типовыми ошибками и границами применимости",
]


def build_tasks(session: dict) -> list:
    """Задачи судейства: по одной на основной ответ и на каждый follow-up."""
    tasks = []
    for t in session["turns"]:
        tasks.append({
            "id": t["id"], "kind": "main",
            "question": t["question"], "answer": t["answer"],
        })
        for i, fu in enumerate(t.get("followups") or [], 1):
            tasks.append({
                "id": f"{t['id']}.fu{i}", "kind": "followup",
                "question": (f"Контекст — исходный вопрос собеседования:\n{t['question']}\n\n"
                             f"Уточняющий вопрос: {fu['text']}"),
                "answer": fu["answer"],
            })
    return tasks


def judge_task(judge_model: str, task: dict, timeout: int) -> dict:
    state = (f"Ты оцениваешь ответ кандидата на техническом собеседовании "
             f"PHP/Symfony/Doctrine/PostgreSQL/DDD-разработчика.\n\n"
             f"ВОПРОС:\n{task['question']}\n\nОТВЕТ КАНДИДАТА:\n{task['answer']}")
    payload = {
        "model": judge_model,
        "state": state,
        "questions": {
            "quality": {
                "type": "score",
                "instructions": "Оцени качество ответа кандидата на вопрос собеседования",
                "criteria": SCORE_CRITERIA,
            },
            "hallucination": {
                "type": "noul",
                "instructions": ("Содержит ли ответ выдуманные факты, несуществующие "
                                 "API/классы/методы или фальшивые источники?"),
                "criteria": {
                    "false": "Выдуманного нет, факты корректны",
                    "true": "Есть выдуманные факты, API или источники",
                },
            },
        },
    }
    req = urllib.request.Request(
        f"{BASE_URL}/v1/systemone",
        data=json.dumps(payload, ensure_ascii=False).encode("utf-8"),
        headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    a = r["answers"]
    return {
        "id": task["id"], "kind": task["kind"],
        "score": round(a["quality"].get("score", -1), 2),
        "score_confidence": a["quality"].get("confidence"),
        "hallucination_p": round(a["hallucination"].get("noul", -1), 3),
    }


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--run", required=True, help="папка прогона с session.json")
    p.add_argument("--judge", required=True, help="судейская decision-модель (clef/clef-flash)")
    p.add_argument("--base-url", default=BASE_URL)
    p.add_argument("--timeout", type=int, default=600)
    p.add_argument("--limit", type=int, default=None)
    args = p.parse_args()

    run_dir = Path(args.run)
    session = json.loads((run_dir / "session.json").read_text())
    tasks = build_tasks(session)
    if args.limit:
        tasks = tasks[:args.limit]

    results = []
    t0 = time.monotonic()
    for i, task in enumerate(tasks, 1):
        try:
            r = judge_task(args.judge, task, args.timeout)
        except Exception as e:
            r = {"id": task["id"], "kind": task["kind"], "error": str(e)}
        results.append(r)
        if i % 10 == 0 or i == len(tasks):
            print(f"  {i}/{len(tasks)} ({time.monotonic()-t0:.0f}s)", flush=True)

    # Вердикты лежат у модели: <models-root>/<модель>/<роль>/judgments/<judge>/
    out_dir = run_dir.parent / "judgments" / args.judge
    out_dir.mkdir(parents=True, exist_ok=True)
    out = out_dir / f"{run_dir.name}.json"
    out.write_text(json.dumps({
        "judge": args.judge, "run": str(run_dir), "model": session["model"],
        "judged_at": time.strftime("%Y-%m-%dT%H:%M:%S"),
        "results": results}, ensure_ascii=False, indent=2))

    ok = [r for r in results if "score" in r]
    print(f"готово: {out}")
    if ok:
        avg = sum(r["score"] for r in ok) / len(ok)
        print(f"судято {len(ok)}/{len(results)}, средний балл {avg:.2f}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

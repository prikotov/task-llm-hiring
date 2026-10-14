#!/usr/bin/env python3
"""Оценка ответов собеса decision-моделями (clef, clef-flash) через /v1/systemone.

clef не судья, а оценщик: проставляет балл и вероятности; судья — ярус 2
(judge_glm.py, LLM с проверкой фактов запуском кода).

Один запрос на каждый ответ (state = вопрос + ответ кандидата; follow-up с
контекстом родительского вопроса), пять вопросов сразу:
quality (балл 0–4), hallucination, need_review, unsure, factcheck_zone.
Выходы:
- <модель>/<роль>/judgments/<judge>/<run>.json — балл и вероятность галлюцинаций
- <модель>/<роль>/judgments/escalation-<judge>-<run>.csv — эскалационные признаки

Историческая справка: в исследовании (октябрь 2026) оценка и эскалация шли
двумя раздельными вызовами (judge.py + judge_escalation.py); объединение в
один запрос — методология после отчёта.

Пример:
    python3 score.py --run ../ornith-1.5-35b/backend/run-06 --judge clef
"""

import argparse
import csv
import json
import sys
import time
import urllib.request
from pathlib import Path

BASE_URL = "http://ollama-host:11434"

SCORE_CRITERIA = [
    "0 — ответ неверен, отсутствует или содержит грубые фактические ошибки",
    "1 — фрагментарный: отдельные верные тезисы без понимания связей, существенные пропуски",
    "2 — базовый: в целом верно, но поверхностно, без примеров, нюансов и типовых ошибок",
    "3 — уверенный: верно, со структурой, примерами и пониманием trade-offs",
    "4 — экспертный: верно и глубоко, с нюансами, типовыми ошибками и границами применимости",
]

QUESTIONS = {
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
    "need_review": {
        "type": "noul",
        "instructions": ("Нужно ли передать этот ответ на проверку более знающему судье? "
                         "Ответь «да», если в ответе есть утверждения, требующие факт-проверки "
                         "по документации или запуску кода (конкретные API, версии фреймворков, "
                         "статистика, внутренности реализаций), или если ты не уверен, "
                         "что сам можешь верно оценить этот ответ."),
        "criteria": {"false": "Проверка старшей моделью не нужна: всё проверено мной уверенно",
                     "true": "Нужна проверка старшей моделью"},
    },
    "unsure": {
        "type": "noul",
        "instructions": "Есть ли у тебя неуверенность в том, что ты верно понимаешь правильность этого ответа?",
        "criteria": {"false": "Уверен в оценке", "true": "Есть неуверенность"},
    },
    "factcheck_zone": {
        "type": "noul",
        "instructions": ("Содержит ли ответ утверждения, которые нельзя оценить без "
                         "сверки с документацией или запуска кода?"),
        "criteria": {"false": "Нет, всё оцениваемо по общим знаниям",
                     "true": "Да, есть такие утверждения"},
    },
}


def build_tasks(session: dict) -> list:
    """Задачи: по одной на основной ответ и на каждый follow-up."""
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


def build_state(task: dict) -> str:
    """Единый state для оценки; используется и локальным score.py, и адаптерами
    (judge_openrouter.py), чтобы формулировки не расходились."""
    return (f"Ты оцениваешь ответ кандидата на техническом собеседовании "
            f"PHP/Symfony/Doctrine/PostgreSQL/DDD-разработчика.\n\n"
            f"ВОПРОС:\n{task['question']}\n\nОТВЕТ КАНДИДАТА:\n{task['answer']}")


def score_task(judge_model: str, task: dict, timeout: int) -> tuple:
    """Один вызов: (вердикт, телеметрия вызова)."""
    t0 = time.monotonic()
    r = post_systemone({"model": judge_model, "state": build_state(task),
                        "questions": QUESTIONS}, timeout)
    latency_ms = round((time.monotonic() - t0) * 1000)
    u = r.get("usage") or {}
    telem = {"latency_ms": latency_ms,
             "input_tokens": u.get("input_tokens"),
             "output_tokens": u.get("output_tokens")}
    a = r["answers"]
    noul = lambda k: round(a[k].get("noul", -1), 3) if k in a else -1
    verdict = {
        "id": task["id"], "kind": task["kind"],
        "score": round(a["quality"].get("score", -1), 2),
        "score_confidence": a["quality"].get("confidence"),
        "hallucination_p": noul("hallucination"),
        "need_review": noul("need_review"),
        "unsure": noul("unsure"),
        "factcheck_zone": noul("factcheck_zone"),
    }
    return verdict, telem


def post_systemone(payload: dict, timeout: int) -> dict:
    req = urllib.request.Request(
        f"{BASE_URL}/v1/systemone",
        data=json.dumps(payload, ensure_ascii=False).encode("utf-8"),
        headers={"Content-Type": "application/json"})
    return json.loads(urllib.request.urlopen(req, timeout=timeout).read())


def main() -> int:
    global BASE_URL
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--run", required=True, help="папка прогона с session.json")
    p.add_argument("--judge", default="clef",
                   help="decision-модель-оценщик (clef/clef-flash)")
    p.add_argument("--base-url", default=BASE_URL)
    p.add_argument("--timeout", type=int, default=600)
    p.add_argument("--limit", type=int, default=None)
    args = p.parse_args()
    BASE_URL = args.base_url

    run_dir = Path(args.run)
    session = json.loads((run_dir / "session.json").read_text())
    tasks = build_tasks(session)
    if args.limit:
        tasks = tasks[:args.limit]

    results = []
    telemetry = []
    t0 = time.monotonic()
    for i, task in enumerate(tasks, 1):
        try:
            r, telem = score_task(args.judge, task, args.timeout)
        except Exception as e:
            r = {"id": task["id"], "kind": task.get("kind"), "error": str(e)[:200]}
            telem = {"latency_ms": None, "input_tokens": None, "output_tokens": None}
        results.append(r)
        telemetry.append({"id": task["id"], **telem})
        if i % 10 == 0 or i == len(tasks):
            print(f"  {i}/{len(tasks)} ({time.monotonic()-t0:.0f}s)", flush=True)

    # Результаты лежат у модели: <models-root>/<модель>/<роль>/judgments/
    out_dir = run_dir.parent / "judgments"
    out_dir.mkdir(parents=True, exist_ok=True)
    jout = out_dir / args.judge / f"{run_dir.name}.json"
    jout.parent.mkdir(parents=True, exist_ok=True)
    wall_s = round(time.monotonic() - t0, 1)
    jout.write_text(json.dumps({
        "judge": args.judge, "run": str(run_dir), "model": session["model"],
        "judged_at": time.strftime("%Y-%m-%dT%H:%M:%S"),
        "meta": {"calls": len(telemetry),
                 "wall_s": wall_s,
                 "latency_ms_sum": sum(t["latency_ms"] or 0 for t in telemetry),
                 "input_tokens_sum": sum(t["input_tokens"] or 0 for t in telemetry),
                 "telemetry": telemetry},
        "results": results}, ensure_ascii=False, indent=2))
    # общий журнал вызовов оценщиков: токены и латентность каждого вызова
    log_path = run_dir.parent.parent.parent / "reports" / "scoring-calls.jsonl"
    log_path.parent.mkdir(parents=True, exist_ok=True)
    with open(log_path, "a", encoding="utf-8") as f:
        for t in telemetry:
            f.write(json.dumps({
                "ts": time.strftime("%Y-%m-%dT%H:%M:%S"),
                "judge": args.judge, "model": session["model"],
                "run": run_dir.name, **t}, ensure_ascii=False) + "\n")
    eout = out_dir / f"escalation-{args.judge}-{run_dir.name}.csv"
    with open(eout, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=["question", "need_review", "unsure",
                                          "factcheck_zone", "error"],
                           extrasaction="ignore")
        w.writeheader()
        w.writerows({"question": r["id"], **r} for r in results)

    ok = [r for r in results if "error" not in r]
    print(f"готово: {jout}")
    print(f"       : {eout}")
    if ok:
        avg = sum(r["score"] for r in ok) / len(ok)
        print(f"оценено {len(ok)}/{len(results)}, средний балл {avg:.2f}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

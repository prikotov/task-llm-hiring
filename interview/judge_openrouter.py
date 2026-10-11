#!/usr/bin/env python3
"""DEPRECATED: пробный путь, скрипт не развивается.

Сравнение ответов собеса внешними decision-моделями через OpenRouter
(моделей там на порядок больше локальных, но по выводам reports/COMPARE-jev.md
локальный clef не хуже, траты не обоснованы). Если понадобится регулярно
сравнивать облачных decision-моделей, поддержку провайдеров делаем
в основном скрипте score.py (--provider/--model), а не здесь.

Транспортный адаптер к score.py: формулировки всех вопросов и state общие
(импортируются из score.py), своё здесь только облако (прокси, ключ, бюджет,
usage) и формат выходов. Скрипт оставлен для воспроизводимости серий §7.

POST https://openrouter.ai/api/alpha/decisions (Decisions API, формат как у
нашего /v1/systemone: state + карта вопросов noul/score). Один запрос на ответ,
все вопросы сразу: quality (score 0-4), hallucination (noul) + 7 метрик
(judge_metrics) + 3 эскалационных. Формулировки идентичны локальному score.py.

Доступ: домашний IP заблокирован WAF OpenRouter, поэтому запросы идут через
прокси (curl). Ключ: env OPENROUTER_KEY_FILE (путь к файлу с ключом).
Прокси: env OPENROUTER_PROXY (адрес) или OPENROUTER_PROXY_FILE (файл с адресом).

Вывод (в папке модели, как у остальных судей):
    <run>/../judgments/decision-<name>-<run>.csv
    <run>/../judgments/escalation-<name>-<run>.csv
    <run>/../judgments/metrics-<name>-<run>.csv
    <run>/../judgments/<name>/<run>.json   (сырые ответы + usage)

Пример:
    python3 judge_openrouter.py --run ../ornith-1.5-9b/backend/run-01
"""

import argparse
import csv
import json
import os
import subprocess
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from score import build_tasks, build_state, QUESTIONS

# Транспортный адаптер: формулировки вопросов и state общие с score.py,
# своё здесь только облако (прокси, ключ, бюджет, usage) и формат выходов.
BASIC_KEYS = ("quality", "hallucination")
ESCALATION_KEYS = ("need_review", "unsure", "factcheck_zone")
from judge_metrics import METRICS

API_URL = "https://openrouter.ai/api/alpha/decisions"


def read_secret(env_name: str) -> str:
    """Читает файл по пути из env (ключи и адрес прокси в git не живут)."""
    path = os.environ.get(env_name)
    if not path:
        raise RuntimeError(f"задайте {env_name} — путь к файлу с секретом "
                           "(какие бывают — см. README, «Быстрый старт»)")
    return Path(path).read_text().strip()

DECISION_COLS = ["question", "score", "conf", "hall_p",
                 "has_code", "structured", "depth", "names_api",
                 "precise_numbers", "self_doubt", "contradictions"]
ESCALATION_COLS = ["question", "need_review", "unsure", "factcheck_zone"]


def build_questions() -> dict:
    q = {k: QUESTIONS[k] for k in BASIC_KEYS}
    q.update(METRICS)
    q.update({k: QUESTIONS[k] for k in ESCALATION_KEYS})
    return q


def call_curl(model: str, state: str, questions: dict, key: str, proxy: str,
              timeout: int) -> dict:
    payload = json.dumps({"model": model, "state": state, "questions": questions},
                         ensure_ascii=False)
    cmd = ["curl", "-s", "--max-time", str(timeout), "-w", "\n%{http_code}",
           "--proxy", proxy, "-X", "POST", API_URL,
           "-H", "Content-Type: application/json",
           "-H", f"Authorization: Bearer {key}",
           "-H", "HTTP-Referer: https://localhost",
           "-H", "X-OpenRouter-Title: models-research",
           "--data-binary", payload]
    out = subprocess.run(cmd, capture_output=True, text=True, timeout=timeout + 30)
    body, _, code = out.stdout.rpartition("\n")
    return int(code or 0), body.strip()


def ask(model: str, task: dict, questions: dict, key: str, proxy: str,
         timeout: int, retries: int = 5) -> tuple:
    state = build_state(task)
    last_err = ""
    for attempt in range(retries):
        code, body = call_curl(model, state, questions, key, proxy, timeout)
        if code == 200 and body:
            return json.loads(body), None
        if code in (429, 500, 502, 503, 524, 529, 0) and attempt < retries - 1:
            wait = min(60, 3 * (2 ** attempt))
            last_err = f"HTTP {code}, retry in {wait}s"
            print(f"    [{task['id']}] {last_err}", flush=True)
            time.sleep(wait)
            continue
        return None, f"HTTP {code}: {body[:200] or 'empty'}"
    return None, last_err


def parse_row(task_id: str, answers: dict) -> dict:
    def val(k, field):
        v = answers.get(k, {})
        return round(v.get(field, -1), 3) if isinstance(v, dict) else -1

    d = {"question": task_id,
         "score": val("quality", "score"),
         "conf": val("quality", "confidence"),
         "hall_p": val("hallucination", "noul")}
    for k in METRICS:
        spec = METRICS[k]
        d[k] = val(k, "noul" if spec["type"] == "noul" else "score")
    e = {"question": task_id,
         **{k: val(k, "noul") for k in ESCALATION_KEYS}}
    return d, e


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--run", required=True)
    p.add_argument("--model", default="typesafe/jev-1.13")
    p.add_argument("--name", default="jev", help="имя судьи в именах файлов")
    p.add_argument("--timeout", type=int, default=120)
    p.add_argument("--max-cost", type=float, default=2.0, help="стоп по бюджету, $")
    args = p.parse_args()

    key = read_secret("OPENROUTER_KEY_FILE")
    proxy = os.environ.get("OPENROUTER_PROXY") or read_secret("OPENROUTER_PROXY_FILE")
    questions = build_questions()

    run_dir = Path(args.run)
    session = json.loads((run_dir / "session.json").read_text())
    tasks = build_tasks(session)

    judgments_dir = run_dir.parent / "judgments"
    judgments_dir.mkdir(parents=True, exist_ok=True)
    raw_path = judgments_dir / args.name / f"{run_dir.name}.json"
    raw_path.parent.mkdir(parents=True, exist_ok=True)

    rows_d, rows_e, raw = [], [], []
    cost, t0 = 0.0, time.monotonic()
    for i, task in enumerate(tasks, 1):
        r, err = ask(args.model, task, questions, key, proxy, args.timeout)
        if err:
            print(f"  {i}/{len(tasks)} {task['id']}: ERROR {err}", flush=True)
            rows_d.append({"question": task["id"], "error": err})
            rows_e.append({"question": task["id"], "error": err})
            raw.append({"id": task["id"], "error": err})
            continue
        usage = r.get("usage", {})
        cost += usage.get("cost", 0)
        d, e = parse_row(task["id"], r.get("answers", {}))
        rows_d.append(d)
        rows_e.append(e)
        raw.append({"id": task["id"], "answers": r.get("answers"), "usage": usage,
                    "model": r.get("model")})
        if i % 10 == 0 or i == len(tasks):
            print(f"  {i}/{len(tasks)} ({time.monotonic()-t0:.0f}s, ${cost:.4f})", flush=True)
        if cost > args.max_cost:
            print(f"СТОП: бюджет ${args.max_cost} превышен (${cost:.4f})", flush=True)
            break

    for cols, rows, fname in (
            (DECISION_COLS, rows_d, f"decision-{args.name}-{run_dir.name}.csv"),
            (ESCALATION_COLS, rows_e, f"escalation-{args.name}-{run_dir.name}.csv"),
            (["question"] + list(METRICS),
             [dict(r, **{k: r.get(k, "") for k in METRICS}) for r in rows_d],
             f"metrics-{args.name}-{run_dir.name}.csv")):
        with open(judgments_dir / fname, "w", newline="", encoding="utf-8-sig") as f:
            w = csv.DictWriter(f, fieldnames=cols, extrasaction="ignore")
            w.writeheader()
            w.writerows(rows)

    raw_path.write_text(json.dumps(raw, ensure_ascii=False, indent=1))
    ok = sum(1 for r in rows_d if "error" not in r)
    print(f"готово: {judgments_dir}/decision-{args.name}-{run_dir.name}.csv "
          f"({ok}/{len(rows_d)}, ${cost:.4f}, {time.monotonic()-t0:.0f}s)")
    return 0


if __name__ == "__main__":
    sys.exit(main())

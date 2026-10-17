#!/usr/bin/env python3
"""Чек-листовое судейство: clef проверяет каждый пункт good/bad отдельным
noul-вопросом. Итог = нормировка (mean(good) − mean(bad)) в 0–4.

    python3 judge_checklist.py --run ../ornith-1.5-9b/backend/interview/run-01
"""

import argparse
import csv
import json
import sys
import time
import urllib.request
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from score import BASE_URL

CHECKLISTS = json.load(open(Path(__file__).parent / 'rubrics/backend/checklists.json'))


def score_answer(judge, question, answer, spec, timeout=600):
    questions = {}
    keys = []
    for key, text in spec.get("good", []):
        qid = f"g_{key}"
        questions[qid] = {"type": "noul",
                          "instructions": f"Содержит ли ответ признак: {text}",
                          "criteria": {"false": "Нет", "true": "Да"}}
        keys.append((qid, +1))
    for key, text in spec.get("bad", []):
        qid = f"b_{key}"
        questions[qid] = {"type": "noul",
                          "instructions": f"Содержит ли ответ признак: {text}",
                          "criteria": {"false": "Нет", "true": "Да"}}
        keys.append((qid, -1))
    state = (f"Вопрос технического собеседования PHP/Symfony/Doctrine/PostgreSQL/DDD:\n"
             f"{question}\n\nОТВЕТ КАНДИДАТА:\n{answer}")
    payload = {"model": judge, "state": state, "questions": questions}
    req = urllib.request.Request(f"{BASE_URL}/v1/systemone",
                                 data=json.dumps(payload).encode(),
                                 headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    a = r["answers"]
    row = {qid: round(a[qid].get("noul", -1), 3) for qid, _ in keys}
    good = [v for (qid, s), (k, v) in zip(keys, row.items()) if s > 0]
    bad = [v for (qid, s), (k, v) in zip(keys, row.items()) if s < 0]
    mg = sum(good) / len(good) if good else 0.5
    mb = sum(bad) / len(bad) if bad else 0.0
    row["score"] = round(max(0.0, min(4.0, 2 + 2 * (mg - mb))), 3)
    return row


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--run", required=True)
    p.add_argument("--judge", default="clef")
    args = p.parse_args()

    run_dir = Path(args.run)
    session = json.loads((run_dir / "session.json").read_text())
    rows = []
    t0 = time.monotonic()
    for t in session["turns"]:
        base_id = t["id"].split(".")[0]
        spec = CHECKLISTS.get(base_id)
        if not spec:
            continue
        try:
            row = score_answer(args.judge, t["question"], t["answer"], spec)
        except Exception as e:
            row = {"error": str(e)}
        row["question"] = t["id"]
        rows.append(row)
        print(f"  {t['id']}: {row.get('score', 'ERR')}", flush=True)
        for fu in t.get("followups") or []:
            fu_id = f"{t['id'].split('.')[0]}.{fu['text'][:0]}"  # id кодируем отдельно
        # follow-ups: отдельные спеки вида "Q04.fu1"
        for i, fu in enumerate(t.get("followups") or [], 1):
            fu_spec_id = f"{base_id}.fu{i}"
            fu_spec = CHECKLISTS.get(fu_spec_id)
            if not fu_spec:
                continue
            try:
                row = score_answer(args.judge, fu["text"], fu["answer"], fu_spec)
            except Exception as e:
                row = {"error": str(e)}
            row["question"] = f"{t['id']}.fu{i}" if t["id"] == base_id else fu_id
            rows.append(row)
            print(f"  {row['question']}: {row.get('score', 'ERR')}", flush=True)

    out = run_dir.parent / "judgments" / f"checklist-{args.judge}-{run_dir.name}.csv"
    out.parent.mkdir(parents=True, exist_ok=True)
    cols = ["question", "score"] + sorted({k for r in rows for k in r if k not in ("question", "score", "error")})
    with open(out, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=cols, extrasaction="ignore")
        w.writeheader()
        w.writerows(rows)
    ok = [r for r in rows if "score" in r]
    print(f"готово: {out} ({len(ok)} ответов, {time.monotonic()-t0:.0f}s)")


if __name__ == "__main__":
    main()

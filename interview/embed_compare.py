#!/usr/bin/env python3
"""Семантическое сравнение ответов с эталоном SOTA через эмбеддинги (ollama).

Идея: ответ Sol 6.1 / Opus 5.5 на вопрос — «эталон»; близость ответа другой
модели к эталону (косинус эмбеддингов) — дешёвый прокси качества без судьи.
Валидация: корреляция близости с баллами clef на тех же ответах + различение
негативных контролов (medgemma, gemma4:e2b).

Эмбеддинги: POST <ollama>/api/embed. Сравнение симметричное (обе стороны —
длинные ответы, без query/passage-префиксов).

Вывод: reports/embeddings-<embed>.csv (по строке на ответ) + сводка в консоль.

Пример:
    python3 embed_compare.py --embed bge-m3:latest
    python3 embed_compare.py --embed jeffh/intfloat-multilingual-e5-large-instruct:q8_0
"""

import argparse
import csv
import json
import math
import sys
import time
import urllib.request
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPT_DIR))
from judge import build_tasks, BASE_URL

# (папка, прогон, метка) — канонические прогоны с судейством clef
CORPUS = [
    ("openai-gpt-6.1-sol", "run-01", "gpt-6.1-sol"),
    ("anthropic-claude-opus-5.5", "run-02", "opus-5.5"),
    ("anthropic-claude-haiku-5.5", "run-01", "haiku-5.5"),
    ("openai-gpt-6-luna", "run-01", "gpt-6-luna"),
    ("ornith-1.5-35b", "run-06", "ornith-1.5-35b"),
    ("qwen3.8", "run-06", "qwen3.8"),
    ("ornith-1.5-9b", "run-01", "ornith-1.5-9b"),
    ("medgemma-27b", "run-01", "medgemma-27b"),
    ("gemma4-e2b", "run-01", "gemma4-e2b"),
]
REFERENCES = [("openai-gpt-6.1-sol", "run-01", "sol"),
              ("anthropic-claude-opus-5.5", "run-02", "opus")]


def embed(model: str, texts: list, timeout: int = 300) -> list:
    """Эмбеддинги пакетом; при сбое — по одному (и пропуск при повторном сбое)."""
    req = urllib.request.Request(
        f"{BASE_URL}/api/embed",
        data=json.dumps({"model": model, "input": texts}, ensure_ascii=False).encode("utf-8"),
        headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=timeout).read())
    return r["embeddings"]


def cos(a, b) -> float:
    dot = sum(x * y for x, y in zip(a, b))
    na = math.sqrt(sum(x * x for x in a)) or 1.0
    nb = math.sqrt(sum(x * x for x in b)) or 1.0
    return dot / (na * nb)


def pearson(x, y) -> float:
    n = len(x)
    mx, my = sum(x) / n, sum(y) / n
    vx = sum((a - mx) ** 2 for a in x)
    vy = sum((b - my) ** 2 for b in y)
    return (sum((a - mx) * (b - my) for a, b in zip(x, y)) /
            math.sqrt(vx * vy)) if vx and vy else 0.0


def spearman(x, y) -> float:
    def ranks(v):
        s = sorted(range(len(v)), key=lambda i: v[i])
        r = [0.0] * len(v)
        i = 0
        while i < len(s):
            j = i
            while j + 1 < len(s) and v[s[j + 1]] == v[s[i]]:
                j += 1
            avg = (i + j) / 2 + 1
            for k in range(i, j + 1):
                r[s[k]] = avg
            i = j + 1
        return r
    return pearson(ranks(x), ranks(y))


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--embed", required=True, help="тег эмбеддинг-модели на сервере ollama")
    args = p.parse_args()

    root = SCRIPT_DIR.parent
    # ответы + баллы clef
    data = {}  # label -> {qid: {"answer", "clef"}}
    for folder, run, label in CORPUS:
        session = json.loads((root / folder / "backend" / run / "session.json").read_text())
        clef = {r["question"]: r for r in csv.DictReader(
            open(root / folder / "backend" / "judgments" / f"decision-clef-{run}.csv",
                 encoding="utf-8-sig"))}
        data[label] = {t["id"]: {"answer": t["answer"],
                                 "clef": float(clef[t["id"]]["score"])
                                 if t["id"] in clef and not clef[t["id"]].get("error")
                                 else None}
                       for t in build_tasks(session)}
    refs = {}
    for folder, run, label in REFERENCES:
        session = json.loads((root / folder / "backend" / run / "session.json").read_text())
        refs[label] = {t["id"]: t["answer"] for t in build_tasks(session)}

    # эмбеддинги одним пакетом на модель
    t0 = time.monotonic()
    vecs = {}
    for label, answers in data.items():
        ids = list(answers)
        vecs[label] = dict(zip(ids, embed(args.embed, [answers[i]["answer"] for i in ids])))
    ref_vecs = {}
    for label, answers in refs.items():
        ids = list(answers)
        ref_vecs[label] = dict(zip(ids, embed(args.embed, [answers[i] for i in ids])))
    print(f"эмбеддинги посчитаны за {time.monotonic()-t0:.0f}s")

    rows = []
    for label, answers in data.items():
        for qid, a in answers.items():
            if a["clef"] is None:
                continue
            row = {"model": label, "question": qid, "clef": a["clef"]}
            for ref in refs:
                if qid in ref_vecs[ref] and qid in vecs[label]:
                    row[f"sim_{ref}"] = round(cos(vecs[label][qid], ref_vecs[ref][qid]), 4)
            rows.append(row)

    out = root / "reports" / f"embeddings-{args.embed.replace('/', '-').replace(':', '-')}.csv"
    with open(out, "w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=["model", "question", "clef", "sim_sol", "sim_opus"])
        w.writeheader()
        w.writerows(rows)

    print(f"\n=== {args.embed} (n={len(rows)})")
    for ref in ("sol", "opus"):
        pairs = [(r[f"sim_{ref}"], r["clef"]) for r in rows if f"sim_{ref}" in r]
        sims = [p[0] for p in pairs]
        scores = [p[1] for p in pairs]
        print(f"vs {ref}: corr(pearson) {pearson(sims, scores):.3f} | "
              f"spearman {spearman(sims, scores):.3f} | "
              f"sim {min(sims):.2f}–{max(sims):.2f}")
    # рейтинги моделей (эталоны сами из себя исключены — без самосходства)
    print(f"{'модель':18s} {'mean sim_sol':>12s} {'mean sim_opus':>13s} {'clef':>5s}")
    agg = {}
    for _, _, label in CORPUS:
        rs = [r for r in rows if r["model"] == label]
        if not rs:
            continue
        ms = sum(r.get("sim_sol", 0) for r in rs if label != "gpt-6.1-sol") / \
            max(1, sum(1 for r in rs if label != "gpt-6.1-sol"))
        mo = sum(r.get("sim_opus", 0) for r in rs if label != "opus-5.5") / \
            max(1, sum(1 for r in rs if label != "opus-5.5"))
        mc = sum(r["clef"] for r in rs) / len(rs)
        agg[label] = (ms, mo, mc)
        print(f"{label:18s} {ms:12.3f} {mo:13.3f} {mc:5.2f}")
    for ref, idx in (("sol", 0), ("opus", 1)):
        order_sim = sorted(agg, key=lambda l: -agg[l][idx])
        order_clef = sorted(agg, key=lambda l: -agg[l][2])
        print(f"ранговая корреляция рейтингов (sim_{ref} vs clef): "
              f"{spearman([agg[l][idx] for l in order_clef], [agg[l][2] for l in order_clef]):.3f}")
    print(f"CSV: {out}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

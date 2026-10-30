#!/usr/bin/env python3
"""Сверка ревью этапа 3 с эталоном (references/*.json).

Автоматически: |ΔA|, |ΔB|, |ΔF| против замороженного эталона + черновик
зачёта (калибруется пилотом): |Δ| <= 0.5 — зачёт, <= 1.0 — частично,
больше — промах. Результат пишется в <run>/review-score.json.

Рядом печатается материал для ручной оценки recall/precision: известные
дефекты эталона против дефектов ревьюера (сопоставление смыслов — на
пилоте вручную, позже можно отдать скрипту-судье).

Использование:

    python3 score_review.py models/<модель>/backend/review/run-01 [run-02 ...]
"""

from __future__ import annotations

import json
import sys
from pathlib import Path

HERE = Path(__file__).resolve().parent
AXES = ("axis_a", "axis_b", "functional")


def verdict_of(delta: float) -> str:
    if delta <= 0.5:
        return "зачёт"
    if delta <= 1.0:
        return "частично"
    return "промах"


def score_run(run_arg: str) -> dict | None:
    run = Path(run_arg)
    run_json = run / "run.json"
    if not run_json.is_file():
        print(f"{run}: run.json нет — пропуск", file=sys.stderr)
        return None
    meta = json.loads(run_json.read_text(encoding="utf-8"))
    slug = meta.get("solution_slug")
    ref_path = Path(meta.get("reference", ""))
    if not slug or not ref_path.is_file():
        print(f"{run}: в run.json нет эталона — пропуск", file=sys.stderr)
        return None
    ref = json.loads(ref_path.read_text(encoding="utf-8"))

    review_path = run / "review.json"
    verdict = json.loads(review_path.read_text(encoding="utf-8")) if review_path.is_file() else None

    deltas = {}
    for axis, ref_key in zip(AXES, ("axis_a", "axis_b", "functional")):
        got, expected = verdict.get(ref_key), ref["expected"][ref_key] if verdict else None
        deltas[axis] = None if got is None else round(abs(float(got) - float(expected)), 2)

    score = {
        "run": run_arg,
        "reviewer": meta.get("model"),
        "solution_slug": slug,
        "integrity": meta.get("integrity", {}).get("status"),
        "parse_ok": meta.get("parse_ok"),
        "got": {a: verdict.get(a) for a in AXES} if verdict else None,
        "expected": ref["expected"],
        "deltas": deltas,
        "verdicts": {
            a: None if d is None else verdict_of(d) for a, d in deltas.items()
        },
        "caught_blind_spot": None,  # заполняется вручную на tricky (D1)
        "recall_precision": "ручная оценка: см. known_defects vs defects ниже",
    }
    (run / "review-score.json").write_text(
        json.dumps(score, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    return score, ref, (verdict or {})


def main() -> int:
    if len(sys.argv) < 2:
        sys.exit(__doc__)
    rows = []
    for arg in sys.argv[1:]:
        scored = score_run(Path(arg))
        if scored is None:
            continue
        score, ref, verdict = scored
        rows.append((score, ref, verdict))

        print("=" * 100)
        print(f"{score['run']}  [{score['solution_slug']}]  "
              f"ревьюер {score['reviewer']}, целостность: {score['integrity']}, "
              f"parse_ok: {score['parse_ok']}")
        for axis, title in zip(AXES, ("A", "B", "F")):
            got, d = score["got"][axis], score["deltas"][axis]
            v = score["verdicts"][axis]
            print(f"  {title}: эталон {score['expected'][axis]:>3} / ревьюер "
                  f"{str(got):>4}  |Δ|={str(d):>4}  {v or '—'}")

        known = ref.get("known_defects", [])
        found = verdict.get("defects", []) if isinstance(verdict, dict) else []
        print(f"  дефекты эталона ({len(known)}) — для ручной оценки recall:")
        for d in known:
            print(f"    [{d['severity']:>8}] {d['file']}: {d['desc'][:110]}")
        print(f"  дефекты ревьюера ({len(found)}) — для ручной оценки precision:")
        for d in found:
            sev = d.get("severity", "?")
            print(f"    [{sev:>8}] {d.get('file', '?')}:{d.get('lines', '?')}: "
                  f"{str(d.get('desc', ''))[:110]}")
        if ref["slug"] == "tricky":
            print("  ⚠ СЛЕПOE ПЯТНО: нашёл ли ревьюер задвоение сумм (D1)? — отметь "
                  "caught_blind_spot в review-score.json")

    if rows:
        print("=" * 100)
        print(f"{'ревьюер':<28} {'решение':<8} {'прогон':<22} "
              f"{'A':>9} {'B':>9} {'F':>9}")
        for score, _, _ in rows:
            def cell(a):
                d = score["deltas"][a]
                return "—" if d is None else f"{d:>4} {verdict_of(d)[0]}"
            print(f"{score['reviewer']:<28} {score['solution_slug']:<8} "
                  f"{score['run']:<22} {cell('axis_a'):>9} {cell('axis_b'):>9} "
                  f"{cell('functional'):>9}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

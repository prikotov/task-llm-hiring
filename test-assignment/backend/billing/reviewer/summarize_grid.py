#!/usr/bin/env python3
"""Свод сетки ревью (этап 3): повторяемость и близость к эталону.

Читает все прогоны models/<модель>/backend/review/run-*/, отбрасывает
невалидные (нет tool-calls / нет вердикта / целостность нарушена),
агрегирует по паре (модель, решение): вердикты по прогонам, средние |Δ|
к эталону и разброс (max-min) по осям.

Использование (из каталога reviewer/):

    python3 summarize_grid.py            # вся сетка
"""

from __future__ import annotations

import glob
import json
import statistics
from collections import defaultdict
from pathlib import Path

HERE = Path(__file__).resolve().parent
MODELS = HERE.parent.parent.parent.parent / "models"  # корень репозитория/models

AXES = ("axis_a", "axis_b", "functional")


def main() -> None:
    refs = {}
    for p in glob.glob(str(HERE / "references" / "*.json")):
        c = json.loads(Path(p).read_text(encoding="utf-8"))
        refs[c["slug"]] = c["expected"]

    data: dict[tuple, list] = defaultdict(list)
    skipped = 0
    for rp in sorted((MODELS).glob("*/backend/review*/run-*")):
        rj, vj = rp / "run.json", rp / "review.json"
        if not (rj.is_file() and vj.is_file()):
            continue
        meta = json.loads(rj.read_text(encoding="utf-8"))
        v = json.loads(vj.read_text(encoding="utf-8"))
        if meta.get("tool_calls", 0) == 0 or meta.get("integrity", {}).get("status") != "ok" \
                or not all(isinstance(v.get(k), (int, float)) for k in AXES):
            skipped += 1
            continue
        data[(meta["model"], meta.get("protocol", "v1"), meta["solution_slug"])].append(tuple(v[k] for k in AXES))

    print(f"{'модель':<36}{'прот.':<6}{'реш.':<8}{'A прогоны':<15}{'B прогоны':<15}"
          f"{'F прогоны':<15}{'>ΔA':>5}{'>ΔB':>5}{'>ΔF':>5}  разброс  n")
    spreads: dict[tuple, list] = defaultdict(list)
    for (model, protocol, slug), runs in sorted(data.items()):
        e = refs[slug]
        deltas = [statistics.mean(abs(r[i] - e[a]) for r in runs)
                  for i, a in enumerate(AXES)]
        sp = [max(r[i] for r in runs) - min(r[i] for r in runs) for i in range(3)]
        spreads[(model, protocol)].append(statistics.mean(sp))
        print(f"{model:<36}{protocol:<6}{slug:<8}"
              + "".join(f"{' '.join(f'{r[i]:g}' for r in runs):<15}" for i in range(3))
              + "".join(f"{d:>5.2f}" for d in deltas)
              + f"  {'/'.join(f'{s:g}' for s in sp)}   {len(runs)}")

    print(f"\nПропущено невалидных прогонов: {skipped}")
    print("Средний разброс по модели (усреднение по решениям, оси A/B/F вместе):")
    for m, sps in sorted(spreads.items()):
        print(f"  {m[0]:<36} {m[1]:<6} {statistics.mean(sps):.2f}")


if __name__ == "__main__":
    main()

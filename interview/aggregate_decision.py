#!/usr/bin/env python3
"""Агрегация всех decision-метрик в reports/decision-all.csv + сводка.

Использование: python3 aggregate_decision.py
"""

import csv
import glob
import json
import re
import statistics
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

# серии по диапазонам прогонов (после инцидентов калибровки)
SERIES = {
    'ornith-1.5-35b': lambda n: ('think-on' if n <= 6 else 'think-off' if n <= 11 else 'primer-v1' if n <= 16 else 'primer-v2'),
    'ornith-1.5-9b': lambda n: 'think-on',
    'qwen3.8': lambda n: 'think-off' if n <= 10 else 'primer-v2',
    'orcarouter-Qwen3.8-27B-Uncensored-latest': lambda n: 'think-off' if n <= 5 else 'primer-v2',
    'huihui_ai-Qwen3.8-abliterated-latest': lambda n: 'think-off' if n <= 5 else 'primer-v2',
    'gemma4-26b': lambda n: 'think-off' if n <= 5 else 'primer-v2',
}

def main():
    rows = []
    for f in sorted(glob.glob(str(ROOT / '*' / 'backend' / 'interview' / 'judgments' / 'decision-*.csv'))):
        m = re.match(r'decision-(clef(?:-flash)?)-(run-\d+)\.csv$', Path(f).name)
        if not m:
            continue
        judge, run = m.groups()
        model = Path(f).parts[-4]
        n = int(run.split('-')[1])
        series = SERIES.get(model, lambda n: '?')(n)
        for r in csv.DictReader(open(f, encoding='utf-8-sig')):
            if 'error' in r:
                continue
            r.update(model=model, run=run, series=series, judge=judge)
            rows.append(r)

    out = ROOT / 'reports' / 'decision-all.csv'
    cols = ['model','run','series','judge','question','score','conf','hall_p',
            'has_code','structured','depth','names_api','precise_numbers',
            'self_doubt','contradictions']
    with open(out,'w',newline='',encoding='utf-8-sig') as fh:
        w=csv.DictWriter(fh,fieldnames=cols,extrasaction='ignore')
        w.writeheader(); w.writerows(rows)
    print(f'{out}: {len(rows)} строк, {len(set(r["model"] for r in rows))} моделей')

    # сводка: стабильность судей по сериям
    print('\nСредние по модель×серия×судья (score / hall_p / contradictions):')
    groups = {}
    for r in rows:
        groups.setdefault((r['model'],r['series'],r['judge']),[]).append(r)
    for k in sorted(groups):
        g=groups[k]
        f=lambda c: statistics.mean(float(x[c]) for x in g)
        print(f'  {k[0]:<42} {k[1]:<9} {k[2]:<11} '
              f'n={len(g):>3}  {f("score"):.2f} / {f("hall_p"):.2f} / {f("contradictions"):.2f}')

if __name__ == '__main__':
    main()

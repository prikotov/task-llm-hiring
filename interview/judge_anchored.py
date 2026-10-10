#!/usr/bin/env python3
"""Якорное судейство: clef оценивает ответ относительно эталонных описаний
уровней («Плохо / Хорошо / Очень хорошо»), заданных под конкретный вопрос.

Пилот: Q13, Q21, Q24, Q27 — по эталонным прогонам трёх моделей.
Сравнение с обычным (безъякорным) clef и GLM-Flash.

    python3 judge_anchored.py
"""

import csv
import json
import sys
import time
import urllib.request
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from judge import BASE_URL

ANCHORS = {
 "Q13": {
   "question_id": "Q13",
   "levels": [
     "Плохо: определение агрегата неверное или отсутствует; границы по таблицам БД/UI, а не по инвариантам",
     "Слабо: определение верное (кластер с корнем, единица консистентности), но границ не выбирает, примера нет",
     "Хорошо: определение + границы по бизнес-инвариантам + пример (заказ/строки) + доступ через корень",
     "Очень хорошо: всё из «хорошо» + типовая ошибка (гигантский агрегат) + ссылки по Id между агрегатами + eventual consistency",
   ]},
 "Q21": {
   "question_id": "Q21",
   "levels": [
     "Плохо: сразу готовое решение (спека фичи) без проблемы и критериев",
     "Слабо: пересказ жалобы + решение, без измеримого результата и границ",
     "Хорошо: формулирует проблему отдельно от решения, задаёт измеримый критерий (метрика обращений), определяет границы",
     "Очень хорошо: всё из «хорошо» + отказ от преждевременного решения (диагностика причины первым этапом) + вопросы заказчику + запреты scope",
   ]},
 "Q24": {
   "question_id": "Q24",
   "levels": [
     "Плохо: описывает FORCE INDEX как существующую возможность PostgreSQL",
     "Слабо: упоминает, что «есть нюансы», но не отвергает предпосылку",
     "Хорошо: прямо указывает, что в PostgreSQL такой конструкции нет (это MySQL)",
     "Очень хорошо: отвергает предпосылку + объясняет философию cost-based планировщика PG + корректные альтернативы (ANALYZE, индексы, pg_hint_plan, GUC для диагностики)",
   ]},
 "Q27": {
   "question_id": "Q27",
   "levels": [
     "Плохо: хвалит код или видит только косметику",
     "Слабо: находит SQL-инъекцию, но дальше общие слова",
     "Хорошо: инъекция + ещё 2-3 структурные проблемы (слои, транзакция, статика) и направление рефакторинга",
     "Очень хорошо: полный разбор (инъекция, race/TOCTOU, отсутствие авторизации, идемпотентность, слои) с приоритизацией и конкретным планом рефакторинга",
   ]},
}

REF_RUNS = [('ornith-1.5-9b','run-01'),('ornith-1.5-35b','run-06'),('qwen3.8','run-06')]


def ask(judge, question, answer, levels):
    state = (f"Вопрос технического собеседования:\n{question}\n\n"
             f"ОТВЕТ КАНДИДАТА:\n{answer}")
    payload = {"model": judge, "state": state, "questions": {
        "anchored": {"type": "score",
                     "instructions": "Отнеси ответ кандидата к одному из уровней (они упорядочены от низшего к высшему)",
                     "criteria": levels}}}
    req = urllib.request.Request(f"{BASE_URL}/v1/systemone",
                                 data=json.dumps(payload).encode(),
                                 headers={"Content-Type": "application/json"})
    r = json.loads(urllib.request.urlopen(req, timeout=600).read())
    a = r["answers"]["anchored"]
    return round(a.get("score", -1), 2), round(a.get("confidence", 0) or 0, 3)


def main():
    qs = {q['id']: q for q in json.load(open(Path(__file__).parent / 'roles/backend/questions.json'))}
    out_rows = []
    for model, run in REF_RUNS:
        session = json.load(open(f'/home/dp/MyProjects/Models/{model}/backend/{run}/session.json'))
        turns = {t['id']: t for t in session['turns']}
        for qid, spec in ANCHORS.items():
            t = turns.get(qid)
            if not t:
                continue
            s, c = ask("clef", t['question'], t['answer'], spec['levels'])
            out_rows.append({"model": model, "run": run, "question": qid,
                             "anchored": s, "anchored_conf": c})
            print(f"  {model} {qid}: {s} (conf {c})", flush=True)
    out = Path('/home/dp/MyProjects/Models/reports/anchored-clef-pilot.csv')
    with open(out, 'w', newline='', encoding='utf-8-sig') as f:
        w = csv.DictWriter(f, fieldnames=["model","run","question","anchored","anchored_conf"])
        w.writeheader(); w.writerows(out_rows)
    print(f"готово: {out} ({len(out_rows)} строк)")

if __name__ == "__main__":
    main()

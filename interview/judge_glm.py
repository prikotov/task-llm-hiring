#!/usr/bin/env python3
"""Судья яруса 2 — GLM-5.3-Flash через `pi` по подписке Z.AI. БЕЗ сабагентов и БЕЗ OpenRouter.

ЕДИНСТВЕННЫЙ разрешённый способ судейства яруса 2 (правило в AGENTS.md):
- сабагенты Agent tool наследуют модель сессии (GLM-5.3, платная квота) — запрещено;
- OpenRouter — платные токены — запрещено.

Каждый ответ судится изолированным запуском: `pi -p --no-session --model glm-5.3-flash
--tools bash` с системным промптом судьи; проверки фактов модель делает сама
запусками php/psql через bash. Итог — JSON-вердикт {id, score, hallucination, note},
сливаемый в <модель>/backend/judgments/glm-5.3-flash-iso/<run>.json.

По умолчанию судятся только знаниевые ответы (30 из 41: поле knowledge
в questions.json; разминка/задачи/процесс/финал не судятся ярусом 2).

Пример:
    python3 judge_glm.py --folder ../deepseek-flash --run run-01 --missing
    python3 judge_glm.py --folder ../medgemma-27b --run run-01 --ids Q24,Q25 --save
"""

import argparse
import json
import re
import subprocess
import sys
import tempfile
import time
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
PI = "pi"
MODEL = "zai/glm-5.3-flash"
# уровень мышления судьи передаётся флагом `--thinking` (low/high/max):
# low = «базовый уровень» методики (§7: low↔max совпадают на 97% в ±1 балл)
TIMEOUT_S = 600          # на один ответ (с запусками кода)
RETRIES = 2

SYSTEM = """Ты — изолированный судья (ярус 2) техсобеса PHP-бэкендера. Тебе дают один вопрос и один ответ кандидата. Оцени ответ и проверь факты исполнением.

Шкала 0–4:
0 — ответ фактически неверен; 1 — отдельные верные крупицы; 2 — верная канва без глубины; 3 — верно, с глубиной и примерами; 4 — то же плюс нюансы и вкус.

Проверка фактов. Конкретные проверяемые утверждения (поведение PHP-функций/конструкций, семантика SQL, существование API) проверяй запуском через bash: `php /tmp/judge-XX/check.php` (PHP 8.5) и `docker exec task-dev-database-1 psql -U task -d task_dev_db -Atc '...'` (PostgreSQL 17; создавай только TEMP-объекты). Не проверяй исполнением вкусовщину и очевидное; версии библиотек сверяй со знанием, не выдумывай. Не более 6 проверок.

Галлюцинация: hallucination=true, если в ответе есть хотя бы одна выдуманная деталь (несуществующие API/функции/команды/конструкции/ложные факты), даже если остальное верно.

Финальное сообщение — ТОЛЬКО JSON одной строкой (после всех проверок):
{"id":"<id>","score":<0-4, шаг 0.5>,"hallucination":<true|false>,"note":"1-3 предложения по-русски: сильные стороны, что проверено исполнением, выдумки если есть"}"""


def judge_one(task: dict, anchors: dict, model: str, thinking: str) -> dict:
    good = "; ".join(t for _, t in anchors.get("good", []))
    bad = "; ".join(t for _, t in anchors.get("bad", []))
    prompt = (f"Вопрос ({task['id']}):\n{task['question']}\n\n"
              f"ОТВЕТ КАНДИДАТА:\n{task['answer']}\n\n"
              f"Якоря чек-листа. Сильный ответ: {good or '—'}\nСлабый: {bad or '—'}")
    with tempfile.TemporaryDirectory(prefix="judge-pi-") as td:
        pf = Path(td) / "prompt.txt"
        pf.write_text(prompt, encoding="utf-8")
        cmd = [PI, "-p", "--no-session", "--model", model, "--thinking", thinking,
               "--tools", "bash",
               "--system-prompt", SYSTEM, "--mode", "text", f"@{pf}"]
        last_err = None
        for attempt in range(1, RETRIES + 1):
            try:
                out = subprocess.run(cmd, capture_output=True, text=True,
                                     timeout=TIMEOUT_S)
                reply = out.stdout.strip()
                if out.returncode != 0 or not reply:
                    raise RuntimeError(f"exit {out.returncode}: {out.stderr[:200]}")
                m = re.search(r"\{.*\}", reply, re.S)
                if not m:
                    raise RuntimeError(f"нет JSON в ответе: {reply[:200]}")
                raw = m.group(0)
                try:
                    v = json.loads(raw)
                except json.JSONDecodeError:
                    # судья иногда не экранирует бэкслеши (пути, #[ORM\...])
                    v = json.loads(re.sub(r'\\(?!["\\/bfnrtu])', r'\\\\', raw))
                v["score"] = float(v["score"])
                v["hallucination"] = bool(v["hallucination"])
                return v
            except Exception as e:  # noqa: BLE001
                last_err = e
                print(f"    попытка {attempt} не удалась: {e}", flush=True)
                time.sleep(5 * attempt)
        raise RuntimeError(f"{task['id']}: судья не ответил — {last_err}")


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--folder", required=True, help="папка модели, напр. ../deepseek-flash")
    ap.add_argument("--run", required=True, help="run-01")
    ap.add_argument("--ids", help="список id через запятую")
    ap.add_argument("--missing", action="store_true", help="все id без вердикта")
    ap.add_argument("--all", action="store_true", help="все id (перезапись)")
    ap.add_argument("--save", action="store_true", help="записать вердикты в файл судьи")
    ap.add_argument("--all-questions", action="store_true",
                    help="судить все ответы, а не только знаниевые (по умолчанию — знаниевые)")
    ap.add_argument("--thinking", choices=["low", "high", "max"], default="low",
                    help="уровень мышления судьи (по умолчанию low)")
    args = ap.parse_args()
    thinking = args.thinking
    if not (args.ids or args.missing or args.all):
        ap.error("нужен --ids, --missing или --all")

    model = args.folder.rstrip("/").split("/")[-1]
    jd = ROOT / model / "backend/judgments"
    tasks = json.loads((jd / "tasks" / f"{model}_{args.run}.json").read_text())
    checklists = json.loads((ROOT / "interview/rubrics/backend/checklists.json").read_text())
    out_path = jd / "glm-5.3-flash-iso" / f"{args.run}.json"
    results = json.loads(out_path.read_text())["results"] if out_path.exists() else []
    done = {r["id"] for r in results}

    if not args.all_questions:
        qd = json.loads((ROOT / "interview/roles/backend/questions.json").read_text())
        qs = qd["questions"] if isinstance(qd, dict) else qd
        know = set()
        for q in qs:
            if q.get("knowledge"):
                know.add(q["id"])
                know |= {f"{q['id']}.fu{i}" for i in range(1, len(q.get("followups") or []) + 1)}
        tasks = [t for t in tasks if t["id"] in know]
    if args.ids:
        todo = [t for t in tasks if t["id"] in set(args.ids.split(","))]
    elif args.missing:
        todo = [t for t in tasks if t["id"] not in done]
    else:
        todo = tasks

    verdicts, failed = [], []
    t0 = time.monotonic()
    for i, t in enumerate(todo, 1):
        anchors = checklists.get(t["id"]) or checklists.get(t["id"].split(".")[0]) or {}
        try:
            v = judge_one(t, anchors, MODEL, thinking)
            verdicts.append(v)
            print(f"  {i}/{len(todo)} {t['id']}: score={v['score']} "
                  f"hall={v['hallucination']} ({time.monotonic()-t0:.0f}s)", flush=True)
        except Exception as e:  # noqa: BLE001
            failed.append(t["id"])
            print(f"  {i}/{len(todo)} {t['id']}: FAIL {e}", flush=True)

    if not args.save:
        print("режим без --save: вердикты не записаны")
        for v in verdicts:
            print(" ", json.dumps(v, ensure_ascii=False)[:220])
        return

    by_id = {r["id"]: r for r in results}
    for v in verdicts:
        by_id[v["id"]] = v
    order = [t["id"] for t in tasks]
    out = {"judge": "glm-5.3-flash (pi CLI по подписке Z.AI, изолированный запуск "
                    "на каждый ответ, проверки кодом через bash; interview/judge_glm.py)",
           "run": args.run, "results": [by_id[q] for q in order if q in by_id]}
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(json.dumps(out, ensure_ascii=False, indent=1))
    print(f"записано {len(verdicts)} вердиктов -> {out_path}"
          + (f"; FAIL: {failed}" if failed else ""))


if __name__ == "__main__":
    sys.exit(main())

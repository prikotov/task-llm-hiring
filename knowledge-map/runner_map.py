#!/usr/bin/env python3
"""Раннер карты знаний PHP (да/нет) для LLM через ollama.

Карта — по мотивам теста Карпаты «Land or Water?» (см. blueprint.md):
одна клетка = один независимый сырой запрос — утверждение и «Верно или
неверно?». Каждый запрос содержит единственное сообщение: клетки не делят
контекст между собой, изоляция полная. keep_alive держит веса загруженными
внутри прогона, выгрузка до и после — изоляция прогонов.

Примеры:
    python3 runner_map.py --model huihui-ai:pi-100k --runs 1
    python3 runner_map.py --model gemma4:26b --runs 1 --limit 8  # smoke
"""

import argparse
import csv
import json
import os
import re
import sys
import time
import urllib.error
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPT_DIR.parent / "interview"))
import env_local  # noqa: F401 — грузит .env.local до чтения OLLAMA_BASE_URL ниже

DEFAULT_BASE_URL = os.environ.get("OLLAMA_BASE_URL", "http://localhost:11434")
QUESTION_SUFFIX = "\n\nВерно или неверно? Ответь одним словом: «да» или «нет»."
THINK_RE = re.compile(r"<think>.*?</think>\s*", re.DOTALL)


def http_json(url: str, payload: dict, timeout: int, retries: int = 3) -> dict:
    """POST с JSON и повторами ТОЛЬКО на сетевые сбои (контент не ретраим)."""
    data = json.dumps(payload).encode("utf-8")
    last_err = None
    for attempt in range(1, retries + 1):
        try:
            req = urllib.request.Request(
                url, data=data,
                headers={"Content-Type": "application/json"})
            with urllib.request.urlopen(req, timeout=timeout) as resp:
                return json.loads(resp.read().decode("utf-8"))
        except (urllib.error.URLError, ConnectionError, TimeoutError) as e:
            last_err = e
            if attempt < retries:
                wait = 5 * attempt
                print(f"    сеть недоступна ({e}); повтор через {wait}s",
                      file=sys.stderr)
                time.sleep(wait)
    raise RuntimeError(f"запрос к {url} не удался после {retries} попыток: {last_err}")


def unload_model(base_url: str, model: str, timeout: int) -> None:
    """Выгрузить модель из памяти (сброс KV-кэша ollama)."""
    http_json(f"{base_url}/api/generate",
              {"model": model, "keep_alive": 0}, timeout)


def slugify(tag: str) -> str:
    return tag.replace(":", "-").replace("/", "-")


def resolve_out_root(models_root: str, slug: str) -> Path:
    """Папка модели: приоритет уже существующей (как в interview/runner.py)."""
    root = Path(models_root)
    if (root / slug).is_dir():
        return root / slug
    if slug.endswith("-latest"):
        base = root / slug[:-len("-latest")]
        if base.is_dir():
            return base
    return root / slug


def chat_once(base_url: str, model: str, prompt: str, keep_alive, think,
              timeout: int) -> dict:
    payload = {"model": model, "stream": False, "keep_alive": keep_alive,
               "messages": [{"role": "user", "content": prompt}]}
    if think in ("on", "off"):
        payload["think"] = (think == "on")
    elif think != "auto":
        payload["think"] = think  # уровни размышления: low / medium / xhigh
    return http_json(f"{base_url}/api/chat", payload, timeout)


def chat_safe(base_url: str, model: str, prompt: str, keep_alive, think,
              timeout: int) -> dict | None:
    """chat_once, но сетевой/таймаутный сбой возвращает None вместо исключения —
    серия не должна умирать из-за одной клетки (клетка станет серой)."""
    try:
        return chat_once(base_url, model, prompt, keep_alive, think, timeout)
    except Exception as e:
        print(f"      ⚠️ сбой генерации ({e}); пропускаю", file=sys.stderr)
        return None


def parse_answer(raw: str) -> tuple[bool | None, str]:
    """Разобрать ответ модели. Возвращает (ответ, как разобрано).

    Строгий формат: первое слово «да» (yes) или «нет» (no). Любое другое
    первое слово — ищем первое подходящее по всему ответу (пометка late).
    Синонимы («верно», «истина»…) форматом не считаются — клетка станет
    серой: неумение следовать формату тоже измерение (см. blueprint).
    """
    text = THINK_RE.sub("", raw).strip().lower()
    tokens = re.findall(r"[а-яёa-z]+", text)
    if not tokens:
        return None, "none"
    if tokens[0] in ("да", "yes"):
        return True, "ok"
    if tokens[0] in ("нет", "no"):
        return False, "ok"
    for t in tokens[1:]:
        if t in ("да", "yes"):
            return True, "late"
        if t in ("нет", "no"):
            return False, "late"
    return None, "none"


def check_questionnaire(questions: list) -> None:
    """Санитария вопросника: уникальные id, полные поля, баланс ответов."""
    ids = [q["id"] for q in questions]
    dupes = {i for i in ids if ids.count(i) > 1}
    if dupes:
        raise SystemExit(f"дубликаты id в вопроснике: {', '.join(sorted(dupes))}")
    for q in questions:
        for field in ("id", "topic", "level", "text", "answer", "note"):
            if field not in q:
                raise SystemExit(f"у вопроса {q.get('id', '?')} нет поля {field}")
    true_n = sum(1 for q in questions if q["answer"])
    false_n = len(questions) - true_n
    print(f"вопросник: {len(questions)} утверждений, "
          f"верных {true_n} / ложных {false_n} "
          f"(базлайн константного ответа: {100 * min(true_n, false_n) / len(questions):.1f}%)")


def run_map(base_url: str, model: str, questions: list, q_version, run_no: int,
            out_dir: Path, keep_alive, think: str, timeout: int,
            limit: int | None) -> None:
    print(f"  выгружаю модель перед прогоном...")
    unload_model(base_url, model, timeout)

    turns: list = []
    session = {
        "model": model,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": base_url,
        "keep_alive": keep_alive,
        "kv_cache": "weights-only (клетки независимы, префиксы не шарятся)",
        "think": think,
        "question_count": 0,
        "questionnaire": q_version,
        "prompt_suffix": QUESTION_SUFFIX.strip(),
        "turns": turns,
    }

    def flush():
        """Прогрессивное сохранение: крах/перезагрузка серию не теряют."""
        session["question_count"] = len(turns)
        out_dir.mkdir(parents=True, exist_ok=True)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    to_ask = questions[:limit] if limit else questions
    for q in to_ask:
        prompt = q["text"] + QUESTION_SUFFIX
        t0 = time.monotonic()
        resp = chat_safe(base_url, model, prompt, keep_alive, think, timeout)
        duration = time.monotonic() - t0
        if resp is None:
            answer_raw, answer, parsed = "", None, "none"
            done_reason = "error"
        else:
            msg = resp.get("message", {})
            answer_raw = (msg.get("content") or "").strip()
            # размышления в парсинг не идут; сохраняем в клетку для аудита
            answer, parsed = parse_answer(answer_raw)
            done_reason = resp.get("done_reason")
        turns.append({
            "id": q["id"], "topic": q["topic"], "level": q["level"],
            "assertion": q["text"], "correct_answer": q["answer"],
            "prompt": prompt,
            "answer_raw": answer_raw, "answer": answer, "parsed": parsed,
            "thinking": None if resp is None else (resp.get("message", {}).get("thinking") or None),
            "duration_s": round(duration, 1),
            "eval_count": resp.get("eval_count") if resp else None,
            "prompt_eval_count": resp.get("prompt_eval_count") if resp else None,
            "done_reason": done_reason,
        })
        mark = {True: "да ✅", False: "нет ❌", None: "— ⬜"}[answer]
        truncated = done_reason not in (None, "stop")
        warn = " ⚠️ ОБРЫВ: " + str(done_reason) if truncated else ""
        print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark}"
              f" (верно: {'да' if q['answer'] else 'нет'}){warn}")
        flush()

    flush()
    write_answers_csv(out_dir, turns)
    total = sum(t["duration_s"] for t in turns)
    correct = sum(1 for t in turns
                  if t["answer"] is not None and t["answer"] == t["correct_answer"])
    print(f"  готово: {out_dir} ({len(turns)} клеток, верных {correct}, {total:.0f}s)")


def write_answers_csv(out_dir: Path, turns: list) -> None:
    """Разбор одной строкой на клетку — для анализа кодом (как judges CSV)."""
    with (out_dir / "answers.csv").open("w", newline="", encoding="utf-8") as f:
        w = csv.writer(f, lineterminator="\n")
        w.writerow(["id", "topic", "level", "answer", "parsed", "correct",
                    "is_correct", "duration_s"])
        for t in turns:
            is_correct = t["answer"] is not None and t["answer"] == t["correct_answer"]
            w.writerow([t["id"], t["topic"], t["level"],
                        "" if t["answer"] is None else ("да" if t["answer"] else "нет"),
                        t["parsed"],
                        "да" if t["correct_answer"] else "нет",
                        int(is_correct), t["duration_s"]])


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", required=True, help="ollama-тег модели на сервере ollama")
    p.add_argument("--runs", type=int, default=1, help="число прогонов (default 1)")
    p.add_argument("--base-url", default=DEFAULT_BASE_URL)
    p.add_argument("--questions", default=str(SCRIPT_DIR / "questions.json"))
    p.add_argument("--models-root", default=str(SCRIPT_DIR.parent / "models"))
    p.add_argument("--out", default=None,
                   help="папка модели; по умолчанию <models-root>/<slug модели>")
    p.add_argument("--run", type=int, default=None,
                   help="номер первого прогона; по умолчанию — первый свободный")
    p.add_argument("--limit", type=int, default=None,
                   help="только первые N клеток (для smoke-теста)")
    p.add_argument("--think", choices=["auto", "on", "off", "low", "medium", "xhigh"],
                   default="off",
                   help="режим thinking (default off — стандарт локальных собесов)")
    p.add_argument("--keep-alive", default="10m",
                   help="keep_alive внутри прогона (default 10m); изоляция прогонов "
                        "— выгрузкой до и после")
    p.add_argument("--timeout", type=int, default=600,
                   help="таймаут одного запроса, сек (default 600)")
    args = p.parse_args()

    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata
    check_questionnaire(questions)

    out_root = (Path(args.out) if args.out
                else resolve_out_root(args.models_root, slugify(args.model)))
    out_root = out_root / "knowledge-map"

    keep_alive = int(args.keep_alive) if str(args.keep_alive).lstrip("-").isdigit() \
        else args.keep_alive

    run_no = args.run
    for i in range(args.runs):
        if run_no is None:
            run_no = 1
            while (out_root / f"run-{run_no:02d}").exists():
                run_no += 1
        out_dir = out_root / f"run-{run_no:02d}"
        if out_dir.exists():
            print(f"папка {out_dir} уже существует — пропускаю "
                  f"(удалите или укажите --run)", file=sys.stderr)
            run_no += 1
            continue
        print(f"[run-{run_no:02d}] {args.model} · карта знаний PHP")
        run_map(args.base_url, args.model, questions, q_version, run_no,
                out_dir, keep_alive, args.think, args.timeout, args.limit)
        run_no += 1
        # выгрузка после КАЖДОГО прогона: изоляция прогонов
        try:
            unload_model(args.base_url, args.model, args.timeout)
            print("  модель выгружена (изоляция прогонов)")
        except Exception as e:
            print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

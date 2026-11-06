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
import subprocess
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
# recall-клетка (слой B): ответ = только результат, без рассуждений
ANSWER_SUFFIX = ("\n\nОтветь только сам результат одним значением "
                 "(число, строка или да/нет), без пояснений.")
THINK_RE = re.compile(r"<think>.*?</think>\s*", re.DOTALL)
# decision-протокол (/v1/systemone, как в interview/score.py): без chat —
# вероятностный ответ «утверждение верно» (noul), порог да/нет — 0.5
DECISION_INSTRUCTIONS = "Верно ли утверждение о PHP?"
DECISION_CRITERIA = {"false": "Утверждение неверно",
                     "true": "Утверждение верно"}


def normalize(s: str) -> str:
    """Нормализация ответа recall-клетки: нижний регистр, без кавычек,
    сжатые пробелы. Сравнение — «эталон входит в ответ»."""
    s = re.sub(r"\s+", " ", s.strip().lower())
    return s.strip("«»\"'` ")


def squeeze(s: str) -> str:
    """Вариант для сопоставления без пробелов вовсе: '[20, 30]' == '[20,30]'."""
    return re.sub(r"\s+", "", s.lower())


def expected_value(grader: dict, cache: dict) -> str:
    """Эталон recall-клетки: из php-сниппета (объективный грейдер — интерпретатор
    8.5 на машине теста) или из accept-списка (первый вариант — канонический)."""
    if "php" in grader:
        key = grader["php"]
        if key not in cache:
            r = subprocess.run(["php", "-r", key], capture_output=True,
                               text=True, timeout=30)
            cache[key] = r.stdout.strip()
        return cache[key]
    return grader["accept"][0]


def build_decision_state(assertion: str) -> str:
    """state для decision-модели — по образцу build_state из score.py."""
    return (f"Ты проверяешь утверждение о PHP на верность.\n\n"
            f"УТВЕРЖДЕНИЕ:\n{assertion}")


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


def decision_once(base_url: str, model: str, assertion: str, timeout: int) -> dict:
    """Один вызов /v1/systemone: вероятность верности утверждения (noul)."""
    payload = {"model": model, "state": build_decision_state(assertion),
               "questions": {"verdict": {"type": "noul",
                                         "instructions": DECISION_INSTRUCTIONS,
                                         "criteria": DECISION_CRITERIA}}}
    return http_json(f"{base_url}/v1/systemone", payload, timeout)


def decision_safe(base_url: str, model: str, assertion: str,
                  timeout: int) -> dict | None:
    """decision_once, но сбой возвращает None вместо исключения (как chat_safe)."""
    try:
        return decision_once(base_url, model, assertion, timeout)
    except Exception as e:
        print(f"      ⚠️ сбой decision-запроса ({e}); пропускаю", file=sys.stderr)
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
    """Санитария вопросника: уникальные id, полные поля по типам клеток."""
    ids = [q["id"] for q in questions]
    dupes = {i for i in ids if ids.count(i) > 1}
    if dupes:
        raise SystemExit(f"дубликаты id в вопроснике: {', '.join(sorted(dupes))}")
    n_recall = 0
    for q in questions:
        qtype = q.get("type", "yesno")
        base = ("id", "topic", "level", "note")
        if qtype == "answer":
            required = base + ("prompt", "grader")
            n_recall += 1
            if "php" not in q.get("grader", {}) and "accept" not in q.get("grader", {}):
                raise SystemExit(f"у recall-клетки {q['id']} нет grader.php/accept")
        else:
            required = base + ("text", "answer")
        for field in required:
            if field not in q:
                raise SystemExit(f"у вопроса {q.get('id', '?')} нет поля {field}")
    if n_recall:
        # эталоны php-клеток вычисляем сразу — битый сниппет не должен
        # всплывать посреди прогона
        cache: dict = {}
        for q in questions:
            if q.get("type") == "answer" and "php" in q["grader"]:
                val = expected_value(q["grader"], cache)
                if not val:
                    raise SystemExit(f"php-эталон {q['id']} вернул пустой вывод")
        print(f"вопросник: {len(questions)} клеток, recall: {n_recall}, "
              f"да/нет: {len(questions) - n_recall}; php-эталоны проверены")
    else:
        true_n = sum(1 for q in questions if q["answer"])
        false_n = len(questions) - true_n
        print(f"вопросник: {len(questions)} утверждений, "
              f"верных {true_n} / ложных {false_n} "
              f"(базлайн константного ответа: {100 * min(true_n, false_n) / len(questions):.1f}%)")


def run_map(base_url: str, model: str, questions: list, q_version, run_no: int,
            out_dir: Path, keep_alive, think: str, timeout: int,
            limit: int | None, protocol: str = "chat",
            questions_file: str | None = None) -> None:
    if protocol == "chat":
        print(f"  выгружаю модель перед прогоном...")
        unload_model(base_url, model, timeout)

    turns: list = []
    session = {
        "model": model,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": base_url,
        "protocol": protocol,
        "keep_alive": keep_alive if protocol == "chat" else None,
        "kv_cache": ("weights-only (клетки независимы, префиксы не шарятся)"
                     if protocol == "chat" else None),
        "think": ("— (decision-протокол: вероятностный ответ noul)"
                  if protocol == "decision" else think),
        "question_count": 0,
        "questionnaire": q_version,
        "questionnaire_file": questions_file,
        "prompt_suffix": (QUESTION_SUFFIX.strip()
                          if protocol == "chat" else None),
        "answer_suffix": (ANSWER_SUFFIX.strip()
                          if protocol == "chat" else None),
        "decision_question": ({"instructions": DECISION_INSTRUCTIONS,
                               "criteria": DECISION_CRITERIA}
                              if protocol == "decision" else None),
        "totals": {},
        "turns": turns,
    }

    def flush():
        """Прогрессивное сохранение: крах/перезагрузка серию не теряют."""
        session["question_count"] = len(turns)
        session["totals"] = {
            "duration_s": round(sum(t["duration_s"] for t in turns), 1),
            "prompt_tokens": sum(t.get("prompt_eval_count") or 0 for t in turns),
            "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
            "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0 for t in turns),
        }
        out_dir.mkdir(parents=True, exist_ok=True)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    to_ask = questions[:limit] if limit else questions
    expected_cache: dict = {}
    for q in to_ask:
        qtype = q.get("type", "yesno")
        if protocol == "decision" and qtype == "answer":
            print(f"    [{q['id']}] recall-клетка не применима в "
                  f"decision-протоколе — пропускаю")
            turns.append({
                "id": q["id"], "topic": q["topic"], "level": q["level"],
                "type": qtype, "prompt_text": q["prompt"], "correct_answer": None,
                "prompt": "", "answer_raw": "", "answer": None, "parsed": "none",
                "expected": None, "is_correct": False, "strict": False,
                "p": None, "thinking": None, "duration_s": 0.0,
                "eval_count": None, "prompt_eval_count": None,
                "prompt_eval_cached_count": None, "done_reason": "unsupported",
            })
            flush()
            continue
        t0 = time.monotonic()
        if protocol == "decision":
            resp = decision_safe(base_url, model, q["text"], timeout)
        elif qtype == "answer":
            prompt = q["prompt"] + ANSWER_SUFFIX
            resp = chat_safe(base_url, model, prompt, keep_alive, think, timeout)
        else:
            prompt = q["text"] + QUESTION_SUFFIX
            resp = chat_safe(base_url, model, prompt, keep_alive, think, timeout)
        duration = time.monotonic() - t0
        p = None
        thinking = None
        expected = None
        if resp is None:
            answer_raw, answer, parsed = "", None, "none"
            eval_count = prompt_eval_count = cached_count = None
            done_reason = "error"
        elif protocol == "decision":
            a = (resp.get("answers") or {}).get("verdict") or {}
            p = a.get("noul")
            if p is None:
                answer_raw, answer, parsed = "", None, "none"
            else:
                p = round(float(p), 3)
                answer, parsed = p >= 0.5, "ok"
                answer_raw = f"noul={p}"
            u = resp.get("usage") or {}
            eval_count = u.get("output_tokens")
            prompt_eval_count = u.get("input_tokens")
            cached_count = None
            done_reason = "stop"
        else:
            msg = resp.get("message", {})
            answer_raw = (msg.get("content") or "").strip()
            thinking = (msg.get("thinking") or "").strip() or None
            if qtype == "answer":
                # recall-клетка: ответ = только результат; эталон — php или accept
                answer = normalize(answer_raw) if answer_raw else None
                expected = expected_value(q["grader"], expected_cache)
                parsed = "ok" if answer else "none"
                done_reason = resp.get("done_reason")
            else:
                # размышления в парсинг не идут; сохраняем в клетку для аудита
                answer, parsed = parse_answer(answer_raw)
                expected = "да" if q["answer"] else "нет"
                done_reason = resp.get("done_reason")
            eval_count = resp.get("eval_count")
            prompt_eval_count = resp.get("prompt_eval_count")
            cached_count = resp.get("prompt_eval_cached_count")

        if qtype == "answer":
            # эталоны: php-вывод или все варианты accept-списка; сравнение
            # без пробелов (пробелы внутри коротких значений не значимы)
            variants = [normalize(v) for v in q["grader"].get("accept", [expected])]
            is_correct = answer is not None and any(
                squeeze(v) in squeeze(answer) for v in variants)
            strict = answer is not None and answer == normalize(expected)
        else:
            is_correct = answer is not None and answer == q["answer"]
            strict = is_correct
        turns.append({
            "id": q["id"], "topic": q["topic"], "level": q["level"],
            "type": qtype,
            "prompt_text": q["text"] if qtype == "yesno" else q["prompt"],
            "correct_answer": expected if qtype == "answer" else q["answer"],
            "prompt": (prompt if protocol == "chat"
                       else build_decision_state(q["text"])),
            "answer_raw": answer_raw, "answer": answer, "parsed": parsed,
            "expected": expected, "is_correct": is_correct, "strict": strict,
            "p": p, "thinking": thinking,
            "duration_s": round(duration, 2),
            "eval_count": eval_count,
            "prompt_eval_count": prompt_eval_count,
            "prompt_eval_cached_count": cached_count,
            "done_reason": done_reason,
        })
        p_s = f", p={p}" if p is not None else ""
        truncated = done_reason not in (None, "stop")
        warn = " ⚠️ ОБРЫВ: " + str(done_reason) if truncated else ""
        if qtype == "answer":
            mark = "✅" if is_correct else ("⬜" if answer is None else "❌")
            given = answer if answer else "—"
            print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark} "
                  f"'{given}' (эталон: '{expected}'){warn}")
        else:
            mark = {True: "да ✅", False: "нет ❌", None: "— ⬜"}[answer]
            print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark}{p_s}"
                  f" (верно: {'да' if q['answer'] else 'нет'}){warn}")
        flush()

    flush()
    write_answers_csv(out_dir, turns)
    total = sum(t["duration_s"] for t in turns)
    correct = sum(1 for t in turns if t.get("is_correct"))
    print(f"  готово: {out_dir} ({len(turns)} клеток, верных {correct}, {total:.0f}s)")


def write_answers_csv(out_dir: Path, turns: list) -> None:
    """Разбор одной строкой на клетку — для анализа кодом (как judges CSV).
    Для recall-клеток answer — нормализованный ответ строкой, expected —
    эталон; для да/нет — да/нет как раньше."""
    with (out_dir / "answers.csv").open("w", newline="", encoding="utf-8") as f:
        w = csv.writer(f, lineterminator="\n")
        w.writerow(["id", "topic", "level", "type", "answer", "expected",
                    "parsed", "correct_answer", "is_correct", "strict",
                    "duration_s", "prompt_tokens", "output_tokens",
                    "cached_tokens"])
        for t in turns:
            qtype = t.get("type", "yesno")
            if qtype == "answer":
                answer = t.get("answer") or ""
                expected = t.get("expected") or ""
                correct_answer = t.get("correct_answer") or ""
            else:
                answer = "" if t["answer"] is None else \
                    ("да" if t["answer"] else "нет")
                expected = ""
                correct_answer = "да" if t["correct_answer"] else "нет"
            w.writerow([t["id"], t["topic"], t["level"], qtype,
                        answer, expected, t["parsed"], correct_answer,
                        int(bool(t.get("is_correct"))), int(bool(t.get("strict"))),
                        t["duration_s"], t.get("prompt_eval_count"),
                        t.get("eval_count"), t.get("prompt_eval_cached_count")])


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
                   help="режим thinking (default off — стандарт локальных собесов; "
                        "в decision-протоколе не применяется)")
    p.add_argument("--protocol", choices=["chat", "decision"], default="chat",
                   help="протокол ответов: chat — ollama /api/chat (default); "
                        "decision — /v1/systemone с вероятностью noul, для "
                        "decision-моделей (clef, clef-flash), не умеющих chat")
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
        print(f"[run-{run_no:02d}] {args.model} · карта знаний PHP "
              f"· протокол: {args.protocol}")
        run_map(args.base_url, args.model, questions, q_version, run_no,
                out_dir, keep_alive, args.think, args.timeout, args.limit,
                args.protocol, Path(args.questions).name)
        run_no += 1
        if args.protocol == "decision":
            continue  # decision-сервис не держит модель в ollama — выгружать нечего
        # выгрузка после КАЖДОГО прогона: изоляция прогонов
        try:
            unload_model(args.base_url, args.model, args.timeout)
            print("  модель выгружена (изоляция прогонов)")
        except Exception as e:
            print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

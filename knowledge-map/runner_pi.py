#!/usr/bin/env python3
"""Прогон карты знаний через pi (транспорт для провайдеров без прямого
API-адаптера в runner_map.py — например, qwen-token-plan/alibabacloud).

Тот же протокол: system-рамка теста + один вопрос, ответ строго JSON,
грейдинг идентичен runner_map (импорт функций оттуда). Транспорт —
`pi -p --no-session` per клетку; usage-статистики нет (None).

Запуск:
    python3 knowledge-map/runner_pi.py --model qwen4-max-preview \
        --provider qwen-token-plan-individual --thinking off
"""
import argparse
import json
import re
import subprocess
import sys
import time
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR))
from runner_map import (  # noqa: E402
    YES_WORDS, NO_WORDS, prompt_parts, check_prompt_lang, extract_json_answer,
    extract_explanation, normalize, expected_value, acc_pattern,
    parse_answer, check_questionnaire, write_answers_csv,
    slugify, resolve_out_root, local_cost_usd,
)
sys.path.insert(0, str(SCRIPT_DIR.parent))
from build_map_report import build_single  # noqa: E402


def pi_once(provider: str, model: str, sysp: str, prompt: str,
            thinking: str, timeout: int) -> tuple[str | None, dict | None]:
    """Ответ модели (текстовые части) + usage из --mode json."""
    cmd = ["pi", "-p", "--no-session", "--no-tools", "--no-extensions",
           "--no-context-files", "--no-skills", "--no-mcp",
           "--mode", "json",
           "--provider", provider, "--model", model,
           "--thinking", thinking,
           "--system-prompt", sysp, prompt]
    try:
        r = subprocess.run(cmd, capture_output=True, text=True,
                           timeout=timeout)
    except subprocess.TimeoutExpired:
        return None, None
    if r.returncode != 0 and not r.stdout.strip():
        print(f"      pi rc={r.returncode}: {r.stderr.strip()[:120]}")
        return None, None
    text, usage, thinking_text = "", None, ""
    for line in r.stdout.splitlines():
        line = line.strip()
        if not line.startswith("{"):
            continue
        try:
            e = json.loads(line)
        except Exception:
            continue
        if e.get("type") != "message_end":
            continue
        m = e.get("message") or {}
        if m.get("role") != "assistant":
            continue
        parts = m.get("content") or []
        text = "".join(c.get("text", "") for c in parts
                       if isinstance(c, dict) and c.get("type") == "text")
        thinking_text = "\n".join(
            c.get("thinking", "") for c in parts
            if isinstance(c, dict) and c.get("type") == "thinking")
        u = m.get("usage") or {}
        usage = {"in": u.get("input"), "out": u.get("output"),
                 "cached": u.get("cacheRead"),
                 "reasoning": u.get("reasoning")}
    if not text and r.returncode == 0 and r.stdout.strip():
        text = r.stdout.strip()
    usage = usage or {}
    usage["thinking_text"] = thinking_text or None
    return (text.strip() or None), usage


PROVIDER_LABELS = {
    # компактные имена провайдеров для session.json / сводного отчёта
    "qwen-token-plan-individual": "alibabacloud",
    "xiaomi-token-plan-sgp": "xiaomi",
}

# облачные провайдеры (для фильтра локальные/облачные в отчёте)
CLOUD_PROVIDERS = {"zai", "alibabacloud"}


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", required=True,
                   help="id модели у провайдера (например qwen4-max-preview)")
    p.add_argument("--provider", default="qwen-token-plan-individual")
    p.add_argument("--thinking", default="off",
                   help="уровень мышления pi (off/minimal/low/...)")
    p.add_argument("--runs", type=int, default=1)
    p.add_argument("--supplement", action="store_true",
                   help="дополнить существующие прогоны клетками зоны «Глубина» (questions-deep.json)")
    p.add_argument("--lang", choices=["ru", "en", "zh"], default="ru")
    p.add_argument("--questions", default=None,
                   help="по умолчанию questions{,-en,-zh}.json по --lang")
    p.add_argument("--timeout", type=int, default=300)
    args = p.parse_args()
    if args.questions is None:
        args.questions = str(
            SCRIPT_DIR / f"questions{'' if args.lang == 'ru' else '-' + args.lang}.json")

    sysp, q_suf, a_suf = prompt_parts(args.lang)
    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version")
    questions = _qdata["questions"]
    check_questionnaire(questions)

    slug = slugify(f"{args.model}-{args.thinking}")
    out_root = resolve_out_root(str(SCRIPT_DIR.parent / "models"), slug)
    km_root = out_root / "knowledge-map"

    if args.supplement:
        deep_path = SCRIPT_DIR / "questions-deep.json"
        deep = json.loads(deep_path.read_text(encoding="utf-8"))["questions"]
        km = Path(resolve_out_root(str(SCRIPT_DIR.parent / "models"),
                                   slug)) / "knowledge-map"
        for out_dir in sorted(km.glob("run-*")):
            s_path = out_dir / "session.json"
            if not s_path.exists():
                continue
            session = json.loads(s_path.read_text(encoding="utf-8"))
            if not session.get("deep"):
                session["deep"] = {"file": "questions-deep.json",
                                   "version": deep_path and json.loads(
                                       deep_path.read_text(encoding="utf-8")
                                   ).get("version")}
            turns = session.setdefault("turns", [])
            have = {t["id"] for t in turns}
            missing = [q for q in deep if q["id"] not in have]
            if not missing:
                print(f"  {out_dir.name}: deep уже дополнен")
                continue
            print(f"  {out_dir.name}: deep-клеток {len(missing)}")
            expected_cache: dict = {}
            sysp, q_suf, a_suf = prompt_parts(session.get("lang", "ru"))
            for q in missing:
                qtype = q.get("type", "yesno")
                t0 = time.monotonic()
                prompt = (q["prompt"] + a_suf) if qtype == "answer" \
                    else (q["text"] + q_suf)
                check_prompt_lang(prompt, session.get("lang", "ru"))
                answer_raw, pi_usage = pi_once(args.provider, args.model,
                                               sysp, prompt, args.thinking,
                                               args.timeout)
                answer_raw = answer_raw or ""
                duration = time.monotonic() - t0
                obj = extract_json_answer(answer_raw)
                json_ok = obj is not None and isinstance(
                    obj.get("answer"), (str, int, float, bool))
                expected = expected_value(q["grader"], expected_cache)
                expected = normalize(expected) if expected else expected
                if json_ok:
                    answer = normalize(str(obj["answer"]))
                    parsed = "json"
                else:
                    answer = (normalize(answer_raw.split("\n", 1)[0])
                              if answer_raw else None)
                    parsed = "late" if answer else "none"
                if "regex" in q["grader"]:
                    is_correct = answer is not None and re.fullmatch(
                        q["grader"]["regex"], answer) is not None
                else:
                    variants = [normalize(v) for v in
                                q["grader"].get("accept",
                                                [expected] if expected else [])
                                if v is not None]
                    is_correct = answer is not None and any(
                        re.search(rf"(?<!\w){acc_pattern(v)}(?!\w)",
                                  answer) for v in variants)
                turns.append({
                    "id": q["id"], "topic": q["topic"],
                    "level": q["level"], "type": qtype,
                    "prompt_text": q["prompt"],
                    "explanation": None, "json_ok": json_ok,
                    "correct_answer": expected,
                    "prompt": prompt, "answer_raw": answer_raw,
                    "answer": answer, "parsed": parsed,
                    "expected": expected, "is_correct": is_correct,
                    "strict": is_correct, "p": None, "thinking": None,
                    "duration_s": round(duration, 2),
                    "eval_count": pi_usage and pi_usage.get("out"),
                    "prompt_eval_count": pi_usage and pi_usage.get("in"),
                    "prompt_eval_cached_count": pi_usage and
                    pi_usage.get("cached"),
                    "done_reason": "stop",
                })
                mark = "✅" if is_correct else "❌"
                print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → "
                      f"{mark} '{answer or '—'}' "
                      f"(эталон: '{expected or '—'}')")
            session["question_count"] = len(turns)
            s_path.write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")
        return 0

    for i in range(args.runs):
        run_no = 1
        while (km_root / f"run-{run_no:02d}").exists():
            run_no += 1
        out_dir = km_root / f"run-{run_no:02d}"
        turns: list = []
        session = {
            "model": f"{PROVIDER_LABELS.get(args.provider, args.provider)}/{args.model}",
            "run": run_no,
            "started_at": datetime.now(timezone.utc).isoformat(),
            "base_url": f"pi ({PROVIDER_LABELS.get(args.provider, args.provider)})",
            "protocol": "chat",
            "provider": PROVIDER_LABELS.get(args.provider, args.provider),
            "keep_alive": None,
            "kv_cache": ("облачный токен-план: клетки независимы, "
                         "префиксы не шарятся"),
            "think": args.thinking,
            "question_count": 0,
            "questionnaire": q_version,
            "questionnaire_file": Path(args.questions).name,
            "lang": args.lang,
            "system_prompt": sysp,
            "prompt_suffix": q_suf.strip(),
            "answer_suffix": a_suf.strip(),
            "totals": {},
            "turns": turns,
        }

        def flush():
            session["question_count"] = len(turns)
            session["totals"] = {
                "duration_s": round(sum(t["duration_s"] for t in turns), 1),
                "prompt_tokens": None,
                "output_tokens": None,
                "cached_tokens": None,
            }
            out_dir.mkdir(parents=True, exist_ok=True)
            (out_dir / "session.json").write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")

        expected_cache: dict = {}
        for q in questions:
            qtype = q.get("type", "yesno")
            t0 = time.monotonic()
            prompt = (q["prompt"] + a_suf) if qtype == "answer" \
                else (q["text"] + q_suf)
            check_prompt_lang(prompt, args.lang)
            answer_raw, pi_usage = pi_once(args.provider, args.model,
                                           sysp, prompt, args.thinking,
                                           args.timeout)
            answer_raw = answer_raw or ""
            duration = time.monotonic() - t0
            expected = None
            msg = answer_raw
            obj = extract_json_answer(msg)
            json_ok = obj is not None and isinstance(
                obj.get("answer"), (str, int, float, bool))
            explanation = None
            if json_ok:
                explanation = (str(obj.get("пояснение")
                                   or obj.get("explanation")
                                   or obj.get("解释") or "").strip() or None)
            if qtype == "answer":
                expected = expected_value(q["grader"], expected_cache)
                expected = normalize(expected) if expected else expected
                if json_ok:
                    answer = normalize(str(obj["answer"]))
                    parsed = "json"
                else:
                    answer = (normalize(msg.split("\n", 1)[0])
                              if msg else None)
                    parsed = "late" if answer else "none"
                done_reason = "stop"
            else:
                if json_ok:
                    a = normalize(str(obj["answer"]))
                    answer = True if a in YES_WORDS else \
                        (False if a in NO_WORDS else None)
                else:
                    answer = None
                if answer is None:
                    answer, parsed = parse_answer(msg)
                else:
                    parsed = "json"
                expected = "да" if q["answer"] else "нет"
                done_reason = "stop"
            if explanation is None:
                explanation = extract_explanation(msg, qtype, expected)
            eval_count = pi_usage and pi_usage.get("out")
            prompt_eval_count = pi_usage and pi_usage.get("in")
            cached_count = pi_usage and pi_usage.get("cached")
            thinking = pi_usage and pi_usage.get("thinking_text")

            if qtype == "answer":
                if "regex" in q["grader"]:
                    is_correct = answer is not None and re.fullmatch(
                        q["grader"]["regex"], answer) is not None
                    strict = is_correct
                else:
                    variants = [normalize(v) for v in
                                q["grader"].get(
                                    "accept",
                                    [expected] if expected else [])
                                if v is not None]
                    is_correct = answer is not None and any(
                        re.search(rf"(?<![\w_]){acc_pattern(v)}(?![\w-])",
                                  answer) for v in variants)
                    strict = answer is not None and \
                        answer == normalize(expected)
            else:
                is_correct = answer is not None and answer == q["answer"]
                strict = is_correct
            turns.append({
                "id": q["id"], "topic": q["topic"], "level": q["level"],
                "type": qtype,
                "prompt_text": q["text"] if qtype == "yesno" else q["prompt"],
                "explanation": explanation, "json_ok": json_ok,
                "correct_answer": (expected if qtype == "answer"
                                   else q["answer"]),
                "prompt": prompt, "answer_raw": msg, "answer": answer,
                "parsed": parsed, "expected": expected,
                "is_correct": is_correct, "strict": strict,
                "p": None, "thinking": None,
                "duration_s": round(duration, 2),
                "eval_count": eval_count,
                "prompt_eval_count": prompt_eval_count,
                "prompt_eval_cached_count": cached_count,
                "done_reason": done_reason,
            })
            if qtype == "answer":
                mark = "✅" if is_correct else ("⬜" if answer is None else "❌")
                print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark} "
                      f"'{answer or '—'}' (эталон: '{expected or '—'}')")
            else:
                mark = {True: "да ✅", False: "нет ❌",
                        None: "— ⬜"}[answer]
                print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark} "
                      f"(верно: {'да' if q['answer'] else 'нет'})")
            flush()

        session["cost_usd"] = round(local_cost_usd(session), 6)
        flush()
        write_answers_csv(out_dir, turns)
        total = sum(t["duration_s"] for t in turns)
        correct = sum(1 for t in turns if t.get("is_correct"))
        print(f"  готово: {out_dir} ({len(turns)} клеток, "
              f"верных {correct}, {total:.0f}s)")
        try:
            build_single(out_dir, Path(args.questions))
        except Exception as e:
            print(f"  отчёт прогона не собрался: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

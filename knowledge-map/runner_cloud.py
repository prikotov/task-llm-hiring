#!/usr/bin/env python3
"""Прогон карты знаний по нативным API облачных подписок — прямой
транспорт без pi. Причина: pi эскалирует запрошенный уровень мышления
к ближайшему поддерживаемому (zai off→low, xiaomi low→high и т.д.),
поэтому off/low/medium у облачных моделей через pi недостижимы.

Нативные шкалы (проверено прямыми вызовами 2026-11-12):
    zai (GLM):            off = thinking disabled; low/medium/high =
                          thinking enabled + reasoning_effort
    xiaomi (MiMo):        off = thinking disabled; low/medium/high =
                          reasoning_effort (none — синоним off)
    alibabacloud (Qwen):  off = enable_thinking:false; low/medium/high =
                          enable_thinking:true + reasoning_effort

Протокол и грейдинг — те же, что в runner_map/runner_pi (одна клетка =
один независимый JSON-запрос). Токен читается из pi auth.json (env
PI_AUTH_FILE, по умолчанию ~/.pi/agent/auth.json) — в workspace не
копируется.

Запуск:
    python3 knowledge-map/runner_cloud.py --model glm-5.3-flash \
        --provider zai --thinking medium
    python3 knowledge-map/runner_cloud.py --model glm-5.3-flash \
        --provider zai --thinking low --supplement
"""
import argparse
import json
import os
import re
import sys
import time
import urllib.error
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR))
from runner_map import (  # noqa: E402
    YES_WORDS, NO_WORDS, prompt_parts, check_prompt_lang,
    extract_json_answer,
    extract_explanation, normalize, expected_value, acc_pattern,
    parse_answer, check_questionnaire, write_answers_csv,
    slugify, resolve_out_root, local_cost_usd,
)
sys.path.insert(0, str(SCRIPT_DIR.parent))
from build_map_report import build_single  # noqa: E402

AUTH_FILE = Path(os.environ.get("PI_AUTH_FILE",
                                str(Path.home() / ".pi/agent/auth.json")))

# публичные endpoints подписок (как DEEPSEEK_CHAT в runner_map)
BASE_URLS = {
    "zai": "https://api.z.ai/api/coding/paas/v4",
    "xiaomi": "https://token-plan-sgp.xiaomimimo.com/v1",
    "alibabacloud": ("https://token-plan.ap-southeast-1.maas.aliyuncs.com"
                     "/compatible-mode/v1"),
}
AUTH_KEYS = {
    "zai": "zai",
    "xiaomi": "xiaomi-token-plan-sgp",
    "alibabacloud": "qwen-token-plan-individual",
}
THINK_PARAMS = {
    "zai": {
        # docs.z.ai (GLM-5.3/Flash): «thinking.type only supports
        # enabled; thinking cannot be disabled» — off не существует,
        # провайдер молча форсирует минимальное мышление
        "off": None,
        "low": {"thinking": {"type": "enabled"}, "reasoning_effort": "low"},
        "high": {"thinking": {"type": "enabled"}, "reasoning_effort": "high"},
        "max": {"thinking": {"type": "enabled"}, "reasoning_effort": "max"},
        # medium НЕ в документированной шкале GLM-5.3/Flash (low/high/max)
        # — поддержан legacy-серией, новые запуски отклоняются guard'ом
        "medium": {"thinking": {"type": "enabled"},
                   "reasoning_effort": "medium"},
    },
    "xiaomi": {
        "off": {"thinking": {"type": "disabled"}},
        "low": {"reasoning_effort": "low"},
        "medium": {"reasoning_effort": "medium"},
        "high": {"reasoning_effort": "high"},
    },
    "alibabacloud": {
        # docs (Model Studio, qwen3.8): reasoning_effort = low/medium/xhigh
        # (дефолт xhigh; max/high→xhigh, minimal→low, none→enable_thinking
        # =False); effort и thinking_budget mutually exclusive. Шкала —
        # reasoning_effort без enable_thinking; off — enable_thinking=False
        # (эквивалент documented none, проверено: 0% клеток с reasoning)
        "off": {"enable_thinking": False},
        "low": {"reasoning_effort": "low"},
        "medium": {"reasoning_effort": "medium"},
        "high": {"reasoning_effort": "high"},
    },
}


# Документированные шкалы мышления (проверено по документации провайдеров
# 2026-11-13/14; каждая запись — с источником). Уровни вне списка guard
# отклоняет ДО первого запроса — чтобы не жечь лимиты подписок на
# недокументированных параметрах:
#   zai: docs.z.ai/guides/overview/concept-param — «GLM-5.3 and
#        GLM-5.3-FLASH models only support max, high, low» (дефолт max),
#        off нет: «thinking.type only supports enabled»
#   xiaomi: ошибка 400 API перечисляет reasoning_effort none/low/medium/high
#   alibabacloud: help.aliyun.com (Model Studio, qwen3.8) — reasoning_effort
#        low/medium/xhigh (дефолт xhigh; high/max→xhigh, minimal→low,
#        none→enable_thinking=False); effort и thinking_budget нельзя вместе
DOCS_LEVELS = {
    ("zai", "glm-5.3-flash"): {"levels": ("low", "high", "max"),
        "src": "https://docs.z.ai/guides/overview/concept-param"},
    ("xiaomi", "mimo-v2.6-flash"): {"levels": ("none", "low", "medium", "high"),
        "src": "https://token-plan-sgp.xiaomimimo.com/v1 (400: reasoning_effort must be one of none/low/medium/high)"},
    ("alibabacloud", "qwen3.8-flash"): {"levels": ("off", "low", "medium", "high", "xhigh"),
        "src": "https://www.alibabacloud.com/help/en/model-studio/qwen-api-via-openai-chat-completions"},
}


def check_thinking_docs(provider: str, model: str, thinking: str) -> None:
    key = (provider, model)
    if key not in DOCS_LEVELS:
        raise SystemExit(
            f"СТОП: для {provider}/{model} документированная шкала мышления "
            f"не внесена в DOCS_LEVELS — сначала сверить параметры с "
            f"документацией провайдера и добавить запись с источником")
    if thinking not in DOCS_LEVELS[key]["levels"]:
        raise SystemExit(
            f"СТОП: уровень «{thinking}» не входит в документированную "
            f"шкалу {provider}/{model}: "
            f"{', '.join(DOCS_LEVELS[key]['levels'])} — источник: "
            f"{DOCS_LEVELS[key]['src']}")


def cloud_once(provider: str, model: str, sysp: str, prompt: str,
               thinking: str, timeout: int) -> tuple[str | None, dict]:
    """Один запрос к chat/completions: (текст, usage). С 4 попытками и
    паузами 5/15/45 c на 429/5xx/сеть/таймаут."""
    params = THINK_PARAMS[provider].get(thinking)
    if params is None:
        raise SystemExit(
            f"СТОП: уровень «{thinking}» не поддерживается провайдером "
            f"{provider} (документация: z.ai GLM-5.3/Flash — off нет, "
            f"«thinking cannot be disabled»)")
    payload = {"model": model, "stream": False,
               "messages": [{"role": "system", "content": sysp},
                            {"role": "user", "content": prompt}],
               **params}
    key = json.loads(AUTH_FILE.read_text())[AUTH_KEYS[provider]]["key"]
    last_err = None
    for attempt in range(4):
        if attempt:
            time.sleep((5, 15, 45)[attempt - 1])
        req = urllib.request.Request(
            BASE_URLS[provider].rstrip("/") + "/chat/completions",
            data=json.dumps(payload).encode(),
            headers={"Content-Type": "application/json",
                     "Authorization": f"Bearer {key}"})
        try:
            r = urllib.request.urlopen(req, timeout=timeout)
            d = json.loads(r.read())
        except urllib.error.HTTPError as e:
            body = e.read().decode(errors="replace")[:200]
            last_err = f"HTTP {e.code}: {body}"
            if e.code not in (429, 500, 502, 503, 504, 529):
                break  # 4xx — повторять бессмысленно
            continue
        except Exception as e:
            last_err = str(e)[:200]
            continue
        msg = (d.get("choices") or [{}])[0].get("message") or {}
        text = msg.get("content")
        if isinstance(text, list):
            text = "".join(c.get("text", "") for c in text
                           if isinstance(c, dict))
        text = (text or "").strip() or None
        u = d.get("usage") or {}
        det = u.get("completion_tokens_details") or {}
        pdet = u.get("prompt_tokens_details") or {}
        usage = {"in": u.get("prompt_tokens"),
                 "out": u.get("completion_tokens"),
                 "reasoning": det.get("reasoning_tokens"),
                 "cached": pdet.get("cached_tokens"),
                 "thinking_text": msg.get("reasoning_content") or None}
        if not text:
            last_err = "пустой ответ"
            continue
        return text, usage
    print(f"      cloud rc: {last_err}")
    return None, {}


def turn_common(q, qtype, prompt, answer_raw, usage, duration):
    """Поля клетки, одинаковые для основного прогона и supplement."""
    obj = extract_json_answer(answer_raw)
    json_ok = obj is not None and isinstance(
        obj.get("answer"), (str, int, float, bool))
    expected = None
    if qtype == "answer":
        expected = expected_value(q["grader"], EXPECTED_CACHE)
        expected = normalize(expected) if expected else expected
        if json_ok:
            answer = normalize(str(obj["answer"]))
            parsed = "json"
        else:
            answer = (normalize(answer_raw.split("\n", 1)[0])
                      if answer_raw else None)
            parsed = "late" if answer else "none"
    else:
        if json_ok:
            a = normalize(str(obj["answer"]))
            answer = True if a in YES_WORDS else \
                (False if a in NO_WORDS else None)
        else:
            answer = None
        if answer is None:
            answer, parsed = parse_answer(answer_raw)
        else:
            parsed = "json"
        expected = "да" if q["answer"] else "нет"
    explanation = None
    if json_ok:
        explanation = (str(obj.get("пояснение") or obj.get("explanation")
                           or obj.get("解释") or "").strip() or None)
    if explanation is None:
        explanation = extract_explanation(answer_raw, qtype, expected)
    if qtype == "answer":
        if "regex" in q["grader"]:
            is_correct = answer is not None and re.fullmatch(
                q["grader"]["regex"], answer) is not None
            strict = is_correct
        else:
            variants = [normalize(v) for v in
                        q["grader"].get("accept",
                                        [expected] if expected else [])
                        if v is not None]
            is_correct = answer is not None and any(
                re.search(rf"(?<![\w_]){acc_pattern(v)}(?![\w-])", answer)
                for v in variants)
            strict = answer is not None and \
                answer == normalize(expected)
    else:
        is_correct = answer is not None and answer == q["answer"]
        strict = is_correct
    return {
        "id": q["id"], "topic": q["topic"], "level": q["level"],
        "type": qtype,
        "prompt_text": q.get("text") or q.get("prompt", ""),
        "explanation": explanation, "json_ok": json_ok,
        "correct_answer": (expected if qtype == "answer"
                           else q["answer"]),
        "prompt": prompt, "answer_raw": answer_raw, "answer": answer,
        "parsed": parsed, "expected": expected,
        "is_correct": is_correct, "strict": strict,
        "p": None,
        "thinking": usage.get("thinking_text"),
        "duration_s": round(duration, 2),
        "eval_count": usage.get("out"),
        "prompt_eval_count": usage.get("in"),
        "prompt_eval_cached_count": usage.get("cached"),
        "reasoning_tokens": usage.get("reasoning"),
        "done_reason": "stop",
    }


EXPECTED_CACHE: dict = {}


def print_turn(t, expected):
    if t["type"] == "answer":
        mark = "✅" if t["is_correct"] else ("⬜" if t["answer"] is None
                                             else "❌")
        print(f"    [{t['id']}] {t['topic']}: {t['duration_s']:.0f}s → "
              f"{mark} '{t['answer'] or '—'}' (эталон: '{expected or '—'}')")
    else:
        mark = {True: "да ✅", False: "нет ❌",
                None: "— ⬜"}[t["answer"]]
        print(f"    [{t['id']}] {t['topic']}: {t['duration_s']:.0f}s → "
              f"{mark} (верно: {'да' if t['expected'] == 'да' else 'нет'})")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", required=True)
    p.add_argument("--provider", required=True,
                   choices=sorted(BASE_URLS))
    p.add_argument("--thinking", default="off",
                   choices=sorted(THINK_PARAMS["zai"]))
    p.add_argument("--runs", type=int, default=1)
    p.add_argument("--supplement", action="store_true",
                   help="дополнить прогоны этой серии клетками «Глубины» "
                        "(файл по языку прогона: questions-deep{,-en,-zh})")
    p.add_argument("--lang", choices=["ru", "en", "zh"], default="ru")
    p.add_argument("--questions", default=None,
                   help="по умолчанию questions{,-en,-zh}.json по --lang — "
                        "языки в сериях не смешивать")
    p.add_argument("--timeout", type=int, default=600)
    args = p.parse_args()
    if args.questions is None:
        args.questions = str(
            SCRIPT_DIR / f"questions{'' if args.lang == 'ru' else '-' + args.lang}.json")
    check_thinking_docs(args.provider, args.model, args.thinking)

    sysp, q_suf, a_suf = prompt_parts(args.lang)
    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version")
    questions = _qdata["questions"]
    check_questionnaire(questions)

    slug = slugify(f"{args.model}-{args.thinking}")
    out_root = resolve_out_root(str(SCRIPT_DIR.parent / "models"), slug)
    km_root = out_root / "knowledge-map"

    if args.supplement:
        lang = args.lang
        stem = f"questions-deep{'' if lang == 'ru' else '-' + lang}.json"
        deep_path = SCRIPT_DIR / stem
        deep = json.loads(deep_path.read_text(encoding="utf-8"))["questions"]
        for out_dir in sorted(km_root.glob("run-*")):
            s_path = out_dir / "session.json"
            if not s_path.exists():
                continue
            session = json.loads(s_path.read_text(encoding="utf-8"))
            if session.get("think") != args.thinking:
                continue
            if session.get("lang") != lang:
                continue
            if not session.get("deep"):
                session["deep"] = {"file": stem,
                                   "version": json.loads(
                                       deep_path.read_text(encoding="utf-8")
                                   ).get("version")}
            turns = session.setdefault("turns", [])
            have = {t["id"] for t in turns}
            missing = [q for q in deep if q["id"] not in have]
            if not missing:
                print(f"  {out_dir.name}: deep уже дополнен")
                continue
            print(f"  {out_dir.name}: deep-клеток {len(missing)}")
            EXPECTED_CACHE.clear()
            for q in missing:
                qtype = q.get("type", "yesno")
                t0 = time.monotonic()
                prompt = (q["prompt"] + a_suf) if qtype == "answer" \
                    else (q["text"] + q_suf)
                check_prompt_lang(prompt, lang)
                answer_raw, usage = cloud_once(args.provider, args.model,
                                               sysp, prompt, args.thinking,
                                               args.timeout)
                answer_raw = answer_raw or ""
                duration = time.monotonic() - t0
                t = turn_common(q, qtype, prompt, answer_raw, usage, duration)
                t["prompt_text"] = q["prompt"]
                turns.append(t)
                print_turn(t, t["expected"])
                session["question_count"] = len(turns)
                s_path.write_text(
                    json.dumps(session, ensure_ascii=False, indent=2),
                    encoding="utf-8")
            session["cost_usd"] = round(local_cost_usd(session), 6)
            s_path.write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")
            try:
                build_single(out_dir, Path(args.questions))
            except Exception as e:
                print(f"  отчёт прогона не собрался: {e}", file=sys.stderr)
        return 0

    for _i in range(args.runs):
        run_no = 1
        while (km_root / f"run-{run_no:02d}").exists():
            run_no += 1
        out_dir = km_root / f"run-{run_no:02d}"
        turns: list = []
        session = {
            "model": f"{args.provider}/{args.model}",
            "run": run_no,
            "started_at": datetime.now(timezone.utc).isoformat(),
            "base_url": BASE_URLS[args.provider],
            "protocol": "chat",
            "provider": args.provider,
            "transport": "direct-api (нативные уровни мышления)",
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
                "prompt_tokens": sum(t.get("prompt_eval_count") or 0
                                     for t in turns),
                "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
                "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0
                                     for t in turns),
            }
            out_dir.mkdir(parents=True, exist_ok=True)
            (out_dir / "session.json").write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")

        EXPECTED_CACHE.clear()
        for q in questions:
            qtype = q.get("type", "yesno")
            t0 = time.monotonic()
            prompt = (q["prompt"] + a_suf) if qtype == "answer" \
                else (q["text"] + q_suf)
            check_prompt_lang(prompt, args.lang)
            answer_raw, usage = cloud_once(args.provider, args.model,
                                           sysp, prompt, args.thinking,
                                           args.timeout)
            answer_raw = answer_raw or ""
            duration = time.monotonic() - t0
            t = turn_common(q, qtype, prompt, answer_raw, usage, duration)
            turns.append(t)
            print_turn(t, t["expected"])
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

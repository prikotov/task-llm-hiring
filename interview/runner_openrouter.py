#!/usr/bin/env python3
"""Раннер тех-собеса для LLM через OpenRouter (chat/completions).

Протокол тот же, что у runner.py (ollama): один собес = один чат, контекст
только messages, без system prompt и инструментов; уточнения и followups в
том же чате. Отличия транспорта:
  - POST openrouter.ai/api/v1/chat/completions через прокси (curl);
  - reasoning effort задаётся явно (обычно low — сравнимо с think-off локальных);
  - usage (prompt/completion tokens, cost) пишется в каждый turn и в session.json;
  - каждая серия дописывается в журнал трат reports/openrouter-spend.csv.

Пример:
    python3 runner_openrouter.py --model openai/gpt-6-luna --effort low
"""

import argparse
import csv
import json
import os
import subprocess
import sys
import time
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPT_DIR))
from runner import (INTERVIEWER_FALLBACK, MAX_CLARIFICATIONS,
                    looks_like_clarification, slugify, resolve_out_root)

API_URL = "https://openrouter.ai/api/v1/chat/completions"
SPEND_LEDGER = SCRIPT_DIR.parent / "reports" / "openrouter-spend.csv"
LEDGER_COLS = ["date", "model", "purpose", "requests", "input_tokens",
               "output_tokens", "cost_usd"]


def read_secret(env_name: str) -> str:
    """Читает файл по пути из env (ключи и адрес прокси в git не живут)."""
    path = os.environ.get(env_name)
    if not path:
        raise RuntimeError(f"задайте {env_name} — путь к файлу с секретом "
                           "(вне workspace; где лежит — см. локальный AGENTS.md)")
    return Path(path).read_text().strip()


def call_openrouter(payload: dict, key: str, proxy: str, timeout: int) -> tuple:
    # payload пишем во временный файл: к Q18 контекст >128 КБ и уже не
    # влезает в argv curl (MAX_ARG_STRLEN)
    import tempfile
    with tempfile.NamedTemporaryFile("w", suffix=".json", delete=False) as tf:
        json.dump(payload, tf, ensure_ascii=False)
        path = tf.name
    cmd = ["curl", "-s", "--max-time", str(timeout), "-w", "\n%{http_code}",
           "--proxy", proxy, "-X", "POST", API_URL,
           "-H", "Content-Type: application/json",
           "-H", f"Authorization: Bearer {key}",
           "-H", "HTTP-Referer: https://localhost",
           "-H", "X-OpenRouter-Title: models-research",
           "--data-binary", f"@{path}"]
    try:
        out = subprocess.run(cmd, capture_output=True, text=True, timeout=timeout + 30)
    finally:
        os.unlink(path)
    txt, _, code = out.stdout.rpartition("\n")
    return int(code or 0), txt.strip()


def chat_once(model: str, messages: list, effort: str | None, key: str,
              proxy: str, timeout: int, retries: int = 5, cache: bool = False) -> dict:
    payload = {"model": model, "messages": messages}
    if effort:
        payload["reasoning"] = {"effort": effort}
    if cache:
        # Anthropic кэширует только по явным меткам: ставим cache_control на
        # последнее сообщение — префикс до него кэшируется (чтение 0.1x цены).
        # Метка — транспортные метаданные, содержимое диалога не меняют.
        msgs = [dict(m) for m in messages]
        msgs[-1] = {**msgs[-1],
                    "content": [{"type": "text", "text": msgs[-1]["content"],
                                 "cache_control": {"type": "ephemeral"}}]}
        payload["messages"] = msgs
    last_err = ""
    for attempt in range(retries):
        code, body = call_openrouter(payload, key, proxy, timeout)
        if code == 200 and body:
            return json.loads(body)
        if code in (429, 500, 502, 503, 524, 529, 0) and attempt < retries - 1:
            wait = min(90, 5 * (2 ** attempt))
            last_err = f"HTTP {code}"
            print(f"      ⚠️ {last_err}, повтор через {wait}s", file=sys.stderr)
            time.sleep(wait)
            continue
        raise RuntimeError(f"HTTP {code}: {body[:200] or 'empty'} ({last_err})")


def append_ledger(model: str, purpose: str, usage: dict) -> None:
    SPEND_LEDGER.parent.mkdir(parents=True, exist_ok=True)
    new = not SPEND_LEDGER.exists()
    with open(SPEND_LEDGER, "a", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=LEDGER_COLS)
        if new:
            w.writeheader()
        w.writerow({"date": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M"),
                    "model": model, "purpose": purpose,
                    "requests": usage.get("requests", 0),
                    "input_tokens": usage.get("input_tokens", 0),
                    "output_tokens": usage.get("output_tokens", 0),
                    "cost_usd": f"{usage.get('cost_usd', 0):.6f}"})


def render_transcript(s: dict) -> str:
    total_s = sum(t["duration_s"] for t in s["turns"])
    lines = [
        f"# Собес: `{s['model']}` · роль: {s['role']} — run-{s['run']:02d}",
        "",
        f"- Дата: {s['started_at']}",
        f"- Backend: {s.get('base_url', 'OpenRouter')} · reasoning effort: {s['reasoning_effort']}",
        f"- Вопросов: {s['question_count']}",
        f"- **Длительность сессии: {total_s:.0f}s ({total_s/60:.1f} мин)**",
        f"- Токены: prompt {s['usage']['input_tokens']:,} · "
        f"output {s['usage']['output_tokens']:,} · "
        f"стоимость ${s['usage']['cost_usd']:.4f}",
        "",
        "---", "",
    ]
    for t in s["turns"]:
        lines += [f"## {t['id']} · {t['section']}", ""]
        lines += [f"**Интервьюер:** {t['question']}", ""]
        if t.get("thinking"):
            lines += ["<details><summary>Размышления модели</summary>", "",
                      t["thinking"], "", "</details>", ""]
        if t.get("clarification"):
            lines += ["**Кандидат (уточнение):**", "", t["clarification"], ""]
            lines += [f"**Интервьюер:** {INTERVIEWER_FALLBACK}", ""]
        lines += ["**Кандидат:**", "", t["answer"], ""]
        for fu in t.get("followups") or []:
            lines += ["### ↳ Углубление", ""]
            lines += [f"**Интервьюер:** {fu['text']}", ""]
            lines += ["**Кандидат:**", "", fu["answer"], ""]
        meta = [f"⏱ {t['duration_s']}s"]
        if t.get("prompt_tokens"):
            meta.append(f"prompt {t['prompt_tokens']} tok")
        if t.get("completion_tokens"):
            meta.append(f"output {t['completion_tokens']} tok")
        if t.get("cache_read_tokens"):
            meta.append(f"кэш {t['cache_read_tokens']:,}+{t.get('cache_write_tokens') or 0:,} tok".replace(",", " "))
        if t.get("cost_usd") is not None:
            meta.append(f"${t['cost_usd']:.4f}")
        if t.get("finish_reason") and t["finish_reason"] != "stop":
            meta.append(f"⚠️ обрыв: {t['finish_reason']}")
        lines += [f"> {' · '.join(meta)}", "", "---", ""]
    return "\n".join(lines)


def run_interview(model: str, questions: list, q_version, run_no: int, out_dir: Path,
                  effort: str | None, key: str, proxy: str, timeout: int,
                  limit: int | None, role: str | None, max_cost: float,
                  cache: bool = False) -> None:
    messages: list = []
    turns: list = []
    clarifications_used = 0
    total = {"requests": 0, "input_tokens": 0, "output_tokens": 0, "cost_usd": 0.0,
             "cache_read_tokens": 0, "cache_write_tokens": 0}
    session = {
        "model": model,
        "backend": "openrouter",
        "role": role,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": "openrouter.ai (via proxy)",
        "reasoning_effort": effort,
        "question_count": 0, "questionnaire": q_version,
        "clarifications_used": 0,
        "usage": total,
        "turns": turns,
        "messages": messages,
    }

    def flush():
        """Прогрессивное сохранение: краш серию не теряет."""
        out_dir.mkdir(parents=True, exist_ok=True)
        session["question_count"] = len(turns)
        session["clarifications_used"] = clarifications_used
        session["usage"] = dict(total)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    def send(msgs):
        t0 = time.monotonic()
        resp = chat_once(model, msgs, effort, key, proxy, timeout, cache=cache)
        dur = round(time.monotonic() - t0, 1)
        u = resp.get("usage", {}) or {}
        det = u.get("prompt_tokens_details") or {}
        u = {**u,
             "cache_read_tokens": det.get("cached_tokens", 0) or 0,
             "cache_write_tokens": det.get("cache_write_tokens", 0) or 0}
        total["requests"] += 1
        total["input_tokens"] += u.get("prompt_tokens", 0) or 0
        total["output_tokens"] += u.get("completion_tokens", 0) or 0
        total["cost_usd"] += u.get("cost", 0) or 0
        total["cache_read_tokens"] += u["cache_read_tokens"]
        total["cache_write_tokens"] += u["cache_write_tokens"]
        if total["cost_usd"] > max_cost:
            raise RuntimeError(f"превышен бюджет ${max_cost} (потрачено ${total['cost_usd']:.4f})")
        return resp, dur, u

    to_ask = questions[:limit] if limit else questions
    for i, q in enumerate(to_ask, 1):
        messages.append({"role": "user", "content": q["text"]})
        try:
            resp, duration, u = send(messages)
        except Exception as e:
            print(f"    [{q['id']}] СБОЙ: {e}", file=sys.stderr)
            raise
        msg = resp["choices"][0]["message"]
        answer = (msg.get("content") or "").strip() or "(пустой ответ модели)"
        finish = resp["choices"][0].get("finish_reason")
        messages.append({"role": "assistant", "content": answer})

        clarification = None
        if (clarifications_used < MAX_CLARIFICATIONS
                and looks_like_clarification(answer)):
            print(f"    [{q['id']}] модель запросила уточнение — стандартная реплика")
            clarification = answer
            clarifications_used += 1
            messages.append({"role": "user", "content": INTERVIEWER_FALLBACK})
            resp2, dur2, u2 = send(messages)
            msg2 = resp2["choices"][0]["message"]
            answer = (msg2.get("content") or "").strip() or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": answer})
            finish = resp2["choices"][0].get("finish_reason")
            duration = round(duration + dur2, 1)
            u = {"prompt_tokens": (u.get("prompt_tokens", 0) or 0) + (u2.get("prompt_tokens", 0) or 0),
                 "completion_tokens": (u.get("completion_tokens", 0) or 0) + (u2.get("completion_tokens", 0) or 0),
                 "cost": (u.get("cost", 0) or 0) + (u2.get("cost", 0) or 0),
                 "cache_read_tokens": (u.get("cache_read_tokens") or 0) + (u2.get("cache_read_tokens") or 0),
                 "cache_write_tokens": (u.get("cache_write_tokens") or 0) + (u2.get("cache_write_tokens") or 0)}

        followups = []
        for fu_text in q.get("followups") or []:
            messages.append({"role": "user", "content": fu_text})
            fres, fdur, fu_ = send(messages)
            fanswer = (fres["choices"][0]["message"].get("content") or "").strip() \
                or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": fanswer})
            followups.append({
                "text": fu_text, "answer": fanswer,
                "duration_s": fdur,
                "prompt_tokens": fu_.get("prompt_tokens"),
                "completion_tokens": fu_.get("completion_tokens"),
                "cost_usd": fu_.get("cost"),
                "done_reason": fres["choices"][0].get("finish_reason"),
            })
            print(f"      ↳ углубление: {fdur}s, {fu_.get('completion_tokens', '?')} tok")

        turns.append({
            "id": q["id"], "section": q["section"], "question": q["text"],
            "answer": answer, "clarification": clarification,
            "followups": followups,
            "duration_s": duration,
            "prompt_tokens": u.get("prompt_tokens"),
            "completion_tokens": u.get("completion_tokens"),
            "cache_read_tokens": u.get("cache_read_tokens") or None,
            "cache_write_tokens": u.get("cache_write_tokens") or None,
            "cost_usd": u.get("cost"),
            "done_reason": finish,
        })
        warn = f" ⚠️ ОБРЫВ: {finish}" if finish not in (None, "stop") else ""
        print(f"    [{q['id']}] {q['section']}: {duration}s, "
              f"{u.get('completion_tokens', '?')} tok, "
              f"${(u.get('cost') or 0):.4f}{warn}")
        flush()

    flush()
    (out_dir / "transcript.md").write_text(render_transcript(session), encoding="utf-8")
    append_ledger(model, f"interview {role} run-{run_no:02d}", total)
    print(f"  готово: {out_dir} ({len(turns)} вопросов, "
          f"${total['cost_usd']:.4f}, "
          f"{sum(t['duration_s'] for t in turns):.0f}s)")
    print(f"  готово: {out_dir} ({len(turns)} вопросов, "
          f"${total['cost_usd']:.4f}, "
          f"{sum(t['duration_s'] for t in turns):.0f}s)")


CACHE_TTL_MIN = 6  # TTL кэша провайдеров: Anthropic 5 мин, OpenAI 5–10 мин


def wait_cache_ttl(out_root: Path, ttl_min: int = CACHE_TTL_MIN) -> None:
    """Изоляция прогонов от чужого кэша: новый собес стартует после паузы,
    превышающей TTL префикс-кэша провайдера (кэш внутри сессии — разрешён).
    Кэш бит-идентичен пересчёту, на содержание не влияет — изолируем и цену."""
    runs = sorted(out_root.glob("run-*/session.json"))
    if not runs:
        return
    age_min = (time.time() - runs[-1].stat().st_mtime) / 60
    if age_min < ttl_min:
        wait = ttl_min - age_min
        human = (f"{wait * 60:.0f} с" if wait < 1 else f"{wait:.1f} мин")
        print(f"  ⏳ изоляция кэша: {human} до истечения TTL прошлого "
              f"прогона", flush=True)
        time.sleep(wait * 60)


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", required=True, help="id модели OpenRouter")
    p.add_argument("--effort", default="low",
                   help="reasoning effort (low/medium/high/none; по умолчанию low)")
    p.add_argument("--questions",
                   default=str(SCRIPT_DIR / "roles" / "backend" / "questions.json"))
    p.add_argument("--models-root", default=str(SCRIPT_DIR.parent))
    p.add_argument("--out", default=None)
    p.add_argument("--run", type=int, default=None)
    p.add_argument("--limit", type=int, default=None)
    p.add_argument("--role", default=None)
    p.add_argument("--max-cost", type=float, default=0.35,
                   help="страховочный стоп по стоимости серии (default 0.35)")
    p.add_argument("--cache", action="store_true",
                   help="явный cache_control для Anthropic-моделей (кэш чтения 0.1x цены; "
                        "OpenAI-модели кэшируются автоматически)")
    p.add_argument("--cache-ttl-min", type=int, default=CACHE_TTL_MIN,
                   help="изоляция прогонов: пауза после прошлого прогона, "
                        "больше TTL кэша провайдера (default 6)")
    p.add_argument("--timeout", type=int, default=300)
    args = p.parse_args()

    key = read_secret("OPENROUTER_KEY_FILE")
    proxy = os.environ.get("OPENROUTER_PROXY") or read_secret("OPENROUTER_PROXY_FILE")
    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata

    role = args.role
    if role is None:
        parent = Path(args.questions).resolve().parent.name
        if parent != "interview":
            role = parent
    out_root = Path(args.out) if args.out else resolve_out_root(
        args.models_root, slugify(args.model))
    if role and args.out is None:
        out_root = out_root / role

    run_no = args.run
    if run_no is None:
        run_no = 1
        while (out_root / f"run-{run_no:02d}").exists():
            run_no += 1
    out_dir = out_root / f"run-{run_no:02d}"
    if out_dir.exists():
        print(f"папка {out_dir} уже существует", file=sys.stderr)
        return 2

    print(f"[run-{run_no:02d}] {args.model} · effort: {args.effort} · роль: {role}")
    wait_cache_ttl(out_root, args.cache_ttl_min)
    run_interview(args.model, questions, q_version, run_no, out_dir,
                  None if args.effort in ("none", "") else args.effort,
                  key, proxy, args.timeout, args.limit, role, args.max_cost,
                  args.cache)
    return 0


if __name__ == "__main__":
    sys.exit(main())

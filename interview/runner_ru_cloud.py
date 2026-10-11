#!/usr/bin/env python3
"""Раннер собеса для российских облаков: GigaChat (Сбер) и YandexGPT (Яндекс).

Протокол тот же (один чат, только messages, без system prompt). Ключи
читаются из env-файла, путь задаётся переменной MODELS_RU_ENV (файл вне
workspace; перечень переменных — в README, «Быстрый старт».)

Провайдеры:
  gigachat: OAuth (Basic-ключ → access_token, ~30 мин), модели GigaChat-2-Max…
  yandexfm: Api-Key, OpenAI-совместимый /v1/chat/completions,
            модель gpt://<folder>/yandexgpt/latest

Пример:
    python3 runner_ru_cloud.py --provider gigachat --model GigaChat-2-Max
    python3 runner_ru_cloud.py --provider yandexfm
"""

import argparse
import json
import os
import subprocess
import sys
import time
import uuid
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPT_DIR))
from runner import (INTERVIEWER_FALLBACK, MAX_CLARIFICATIONS,
                    looks_like_clarification, slugify)
from runner_openrouter import render_transcript, append_ledger, wait_cache_ttl

TASK_ENV = os.environ.get("MODELS_RU_ENV")  # путь к env-файлу с ключами облаков

GIGA_OAUTH = "https://ngw.devices.sberbank.ru:9443/api/v2/oauth"
# docs: с 16.07.2026 api.giga.chat — единый URL для физлиц и юрлиц;
# старый devices-хост остаётся для совместимости (GigaChat-2)
GIGA_CHAT = os.environ.get(
    "GIGA_CHAT_URL", "https://api.giga.chat/v1/chat/completions")
YANDEX_CHAT = "https://llm.api.cloud.yandex.net/v1/chat/completions"
DEEPSEEK_CHAT = "https://api.deepseek.com/chat/completions"

# Тарифы ₽/1000 токенов (вход / кэш / выход). Яндекс — по детализации
# биллинга 2026-10-09: YandexGPT Pro 5, синхронный режим, ПЛОСКАЯ ставка
# ₽1.20 за 1k токенов любого типа (кэш без скидки). GigaChat — уточнить.
TARIFFS = {"yandexfm": (1.2, 1.2, 1.2), "gigachat": None}
# DeepSeek, $/1M токенов (вход / кэш-хит / выход), ПИК; оф-пик вдвое дешевле
# (api-docs.deepseek.com, октябрь 2026). Кэш автоматический, промпт-режим.
TARIFFS_USD = {"deepseek": (0.30, 0.006, 1.20)}
MAX_COST_USD = 5.0
YANDEX_FOLDER = os.environ.get("YANDEXFM_FOLDER_ID", "")  # ID каталога Яндекса


def env_value(name: str) -> str:
    if not TASK_ENV:
        raise RuntimeError("задайте MODELS_RU_ENV — путь к env-файлу с ключами "
                           "(какие бывают — см. README, «Быстрый старт»)")
    for line in Path(TASK_ENV).read_text().splitlines():
        if line.startswith(name + "="):
            return line.split("=", 1)[1].strip()
    raise RuntimeError(f"{name} не найден в {TASK_ENV}")


def curl_json(method: str, url: str, headers: dict, body: dict | None = None,
              timeout: int = 300) -> tuple:
    cmd = ["curl", "-s", "--max-time", str(timeout), "-w", "\n%{http_code}",
           "-X", method, url]
    for k, v in headers.items():
        cmd += ["-H", f"{k}: {v}"]
    data = None
    if body is not None:
        import tempfile
        with tempfile.NamedTemporaryFile("w", suffix=".json", delete=False) as tf:
            json.dump(body, tf, ensure_ascii=False)
            data = tf.name
        cmd += ["-H", "Content-Type: application/json", "--data-binary", f"@{data}"]
    try:
        out = subprocess.run(cmd, capture_output=True, text=True,
                             timeout=timeout + 30)
    finally:
        if data:
            os.unlink(data)
    txt, _, code = out.stdout.rpartition("\n")
    return int(code or 0), txt.strip()


class GigaChat:
    """OAuth-токен обновляется по мере истечения."""
    name = "gigachat"

    def __init__(self):
        self._key = env_value("GIGACHAT_AUTHORIZATION_KEY")
        self._token, self._expires = None, 0.0

    def _auth(self):
        if self._token and time.time() < self._expires - 60:
            return
        code, body = curl_json("POST", GIGA_OAUTH, {
            "Authorization": f"Basic {self._key}",
            "RqUID": str(uuid.uuid4()),
            "Accept": "application/json",
        }, None, timeout=30)
        if code != 200:
            raise RuntimeError(f"giga oauth HTTP {code}: {body[:200]}")
        # scope в form-urlencoded
        # (curl_json не шлёт тело формы — отдельный вызов ниже не нужен:
        #  OAuth Сбера требует scope, повторяем с телом)
        raise RuntimeError("unreachable")

    def auth(self):
        """Отдельный метод: OAuth требует form-тело."""
        if self._token and time.time() < self._expires - 60:
            return
        import tempfile
        with tempfile.NamedTemporaryFile("w", suffix=".txt", delete=False) as tf:
            tf.write("scope=GIGACHAT_API_PERS")
            path = tf.name
        cmd = ["curl", "-s", "--max-time", "30", "-X", "POST", GIGA_OAUTH,
               "-H", f"Authorization: Basic {self._key}",
               "-H", f"RqUID: {uuid.uuid4()}",
               "-H", "Content-Type: application/x-www-form-urlencoded",
               "-H", "Accept: application/json",
               "--data-binary", f"@{path}"]
        try:
            out = subprocess.run(cmd, capture_output=True, text=True, timeout=60)
        finally:
            os.unlink(path)
        d = json.loads(out.stdout)
        if "access_token" not in d:
            raise RuntimeError(f"giga oauth: {out.stdout[:200]}")
        self._token = d["access_token"]
        self._expires = time.time() + 25 * 60  # живёт ~30 минут
        print("  giga-токен обновлён", flush=True)

    def chat(self, messages, model, timeout):
        self.auth()
        return curl_json("POST", GIGA_CHAT, {
            "Authorization": f"Bearer {self._token}",
            "Accept": "application/json",
        }, {"model": model, "messages": messages,
            "max_tokens": 4000, "temperature": 1.0}, timeout)


class YandexFm:
    name = "yandexfm"

    def __init__(self):
        self._key = env_value("YANDEXFM_API_KEY")

    def chat(self, messages, model, timeout):
        return curl_json("POST", YANDEX_CHAT, {
            "Authorization": f"Api-Key {self._key}",
            "Accept": "application/json",
        }, {"model": model, "messages": messages,
            "max_tokens": 4000}, timeout)


class DeepSeek:
    """Родной API DeepSeek (OpenAI-совместимый), ключ из TasK.
    reasoning_effort задаётся уровнем серии (стандарт облаков — low),
    thinking включён по умолчанию; кэш контекста автоматический."""
    name = "deepseek"

    def __init__(self, effort: str = "low"):
        self._key = env_value("DEEPSEEK_API_KEY")
        self.effort = effort

    def chat(self, messages, model, timeout):
        # max_tokens 16000: API-дефолт 4096 режет развёрнутые ответы
        # в полуслове (наш протокол не ограничивает длину, как и у облачных
        # серий OpenRouter); completion включает reasoning-токены
        return curl_json("POST", DEEPSEEK_CHAT, {
            "Authorization": f"Bearer {self._key}",
            "Accept": "application/json",
        }, {"model": model, "messages": messages,
            "max_tokens": 16000, "reasoning_effort": self.effort}, timeout)


def extract(resp: dict) -> tuple:
    ch = resp["choices"][0]
    msg = ch.get("message", {})
    ans = (msg.get("content") or msg.get("alternatives", [{}])[0].get("message", {}).get("content") or "").strip()
    return ans, ch.get("finish_reason")


def reasoning_of(resp: dict) -> str:
    """Цепочка размышлений (deepseek: message.reasoning_content)."""
    ch = (resp.get("choices") or [{}])[0]
    return ((ch.get("message") or {}).get("reasoning_content") or "").strip()


def usage_norm(u: dict) -> dict:
    """Унифицируем usage: prompt/completion/cached."""
    out = {"prompt_tokens": u.get("prompt_tokens") or u.get("input_tokens") or 0,
           "completion_tokens": u.get("completion_tokens") or 0,
           "cost": 0.0,
           "cache_read_tokens": (u.get("precached_prompt_tokens") or
                                 ((u.get("prompt_tokens_details") or {}).get("cached_tokens"))
                                 or ((u.get("usage_details") or {}).get("cached_tokens")) or 0)}
    return out


MAX_COST_RUB = 150.0


def run_interview(provider, model, questions, q_version, run_no, out_dir, timeout,
                  limit, role, resume=False):
    tariff = TARIFFS.get(provider.name)
    max_cost_rub = MAX_COST_RUB
    state = None
    if resume:
        state = json.loads((out_dir / "session.json").read_text())
        print(f"  resume: {len(state['turns'])} вопросов уже есть, "
              f"контекст {len(state['messages'])} сообщений, "
              f"₽{state['usage'].get('cost_rub', 0):.0f} потрачено", flush=True)
    messages = state["messages"] if state else []
    turns = state["turns"] if state else []
    defaults = {"requests": 0, "input_tokens": 0, "output_tokens": 0,
                "cost_usd": 0.0, "cache_read_tokens": 0, "cache_write_tokens": 0,
                "cost_rub": 0.0}
    total = dict(defaults)
    if state:
        total.update({k: v for k, v in state["usage"].items() if k in defaults})
    clarifications_used = state["clarifications_used"] if state else 0
    session = {
        "model": model, "backend": provider.name, "role": role, "run": run_no,
        "started_at": (state or {}).get("started_at")
                      or datetime.now(timezone.utc).isoformat(),
        "resumed_at": datetime.now(timezone.utc).isoformat() if state else None,
        "base_url": {"gigachat": "gigachat.devices.sberbank.ru",
                     "yandexfm": "llm.api.cloud.yandex.net",
                     "deepseek": "api.deepseek.com"}[provider.name],
        "reasoning_effort": getattr(provider, "effort", None),
        "question_count": 0, "clarifications_used": 0, "questionnaire": q_version,
        "usage": total, "turns": turns, "messages": messages,
    }

    def flush():
        out_dir.mkdir(parents=True, exist_ok=True)
        session["question_count"] = len(turns)
        session["clarifications_used"] = clarifications_used
        session["usage"] = dict(total)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    def send(msgs):
        t0 = time.monotonic()
        code, body = provider.chat(msgs, model, timeout)
        for attempt in range(4):
            if code == 200:
                break
            if code in (401,) or "token" in body.lower() and code == 403:
                provider.auth() if hasattr(provider, "auth") else None
            wait = min(60, 5 * (2 ** attempt))
            print(f"      ⚠️ HTTP {code}, повтор через {wait}s", file=sys.stderr)
            time.sleep(wait)
            code, body = provider.chat(msgs, model, timeout)
        if code != 200:
            raise RuntimeError(f"HTTP {code}: {body[:200]}")
        resp = json.loads(body)
        dur = round(time.monotonic() - t0, 1)
        u = usage_norm(resp.get("usage", {}) or {})
        total["requests"] += 1
        total["input_tokens"] += u["prompt_tokens"]
        total["output_tokens"] += u["completion_tokens"]
        total["cache_read_tokens"] += u["cache_read_tokens"]
        if tariff:
            cr = u["cache_read_tokens"]
            total["cost_rub"] += ((u["prompt_tokens"] - cr) * tariff[0]
                                  + cr * tariff[1]
                                  + u["completion_tokens"] * tariff[2]) / 1000
            if total["cost_rub"] > max_cost_rub:
                raise RuntimeError(
                    f"превышен бюджет ₽{max_cost_rub:.0f} (потрачено ₽{total['cost_rub']:.0f})")
        tusd = TARIFFS_USD.get(provider.name)
        if tusd:
            cr = u["cache_read_tokens"]
            total["cost_usd"] += ((u["prompt_tokens"] - cr) * tusd[0]
                                  + cr * tusd[1]
                                  + u["completion_tokens"] * tusd[2]) / 1e6
            if total["cost_usd"] > MAX_COST_USD:
                raise RuntimeError(
                    f"превышен бюджет ${MAX_COST_USD} "
                    f"(потрачено ${total['cost_usd']:.2f})")
        return resp, dur, u

    to_ask = questions[:limit] if limit else questions
    if state:
        done_ids = {t["id"] for t in turns}
        to_ask = [q for q in to_ask if q["id"] not in done_ids]
        print(f"  resume: продолжаем с {to_ask[0]['id'] if to_ask else 'конца'} "
              f"({len(to_ask)} вопросов осталось)", flush=True)
    for q in to_ask:
        messages.append({"role": "user", "content": q["text"]})
        resp, duration, u = send(messages)
        answer, finish = extract(resp)
        answer = answer or "(пустой ответ модели)"
        messages.append({"role": "assistant", "content": answer})

        clarification = None
        if (clarifications_used < MAX_CLARIFICATIONS
                and looks_like_clarification(answer)):
            clarification = answer
            clarifications_used += 1
            messages.append({"role": "user", "content": INTERVIEWER_FALLBACK})
            resp2, dur2, u2 = send(messages)
            answer, finish = extract(resp2)
            answer = answer or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": answer})
            duration = round(duration + dur2, 1)
            u = {"prompt_tokens": u["prompt_tokens"] + u2["prompt_tokens"],
                 "completion_tokens": u["completion_tokens"] + u2["completion_tokens"],
                 "cache_read_tokens": u["cache_read_tokens"] + u2["cache_read_tokens"]}

        followups = []
        for fu_text in q.get("followups") or []:
            messages.append({"role": "user", "content": fu_text})
            fres, fdur, fu = send(messages)
            fanswer, _ = extract(fres)
            fanswer = fanswer or "(пустой ответ модели)"
            messages.append({"role": "assistant", "content": fanswer})
            followups.append({"text": fu_text, "answer": fanswer,
                              "duration_s": fdur,
                              "prompt_tokens": fu["prompt_tokens"],
                              "completion_tokens": fu["completion_tokens"],
                              "done_reason": "stop"})
        turns.append({
            "id": q["id"], "section": q["section"], "question": q["text"],
            "answer": answer, "clarification": clarification,
            "thinking": reasoning_of(resp) or None,
            "followups": followups, "duration_s": duration,
            "prompt_tokens": u["prompt_tokens"],
            "completion_tokens": u["completion_tokens"],
            "cache_read_tokens": u["cache_read_tokens"] or None,
            "cost_usd": 0.0, "done_reason": finish,
        })
        warn = f" ⚠️ ОБРЫВ: {finish}" if finish not in (None, "stop") else ""
        rub = f", ₽{total['cost_rub']:.0f}" if total["cost_rub"] else ""
        usd = f", ${total['cost_usd']:.2f}" if total["cost_usd"] else ""
        print(f"    [{q['id']}] {q['section']}: {duration}s, "
              f"{u['completion_tokens']} tok{rub}{usd}{warn}", flush=True)
        flush()

    flush()
    (out_dir / "transcript.md").write_text(render_transcript(session),
                                           encoding="utf-8")
    print(f"  готово: {out_dir} ({len(turns)} вопросов, "
          f"{sum(t['duration_s'] for t in turns):.0f}s)")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--provider", choices=["gigachat", "yandexfm", "deepseek"],
                   required=True)
    p.add_argument("--model", default=None)
    p.add_argument("--effort", default="low",
                   help="reasoning_effort для deepseek (default low)")
    p.add_argument("--questions",
                   default=str(SCRIPT_DIR / "roles" / "backend" / "questions.json"))
    p.add_argument("--models-root", default=str(SCRIPT_DIR.parent))
    p.add_argument("--run", type=int, default=None)
    p.add_argument("--limit", type=int, default=None)
    p.add_argument("--resume", action="store_true",
                   help="продолжить последний прогон с места обрыва")
    p.add_argument("--timeout", type=int, default=300)
    p.add_argument("--max-cost-rub", type=float, default=150.0,
                   help="страховочный стоп по рублям (default 150)")
    a = p.parse_args()
    global MAX_COST_RUB
    MAX_COST_RUB = a.max_cost_rub

    if a.provider == "gigachat":
        prov, model = GigaChat(), a.model or "GigaChat-2-Max"
    elif a.provider == "deepseek":
        prov, model = DeepSeek(a.effort), a.model or "deepseek-flash"
    else:
        if not YANDEX_FOLDER:
            raise RuntimeError("задайте YANDEXFM_FOLDER_ID — ID каталога Яндекса "
                               "(какую именно — см. README, «Быстрый старт»)")
        prov, model = YandexFm(), a.model or f"gpt://{YANDEX_FOLDER}/yandexgpt/latest"
    _qdata = json.loads(Path(a.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata
    # одна папка = одна модель; историческое имя gigachat/ сохраняем
    # для GigaChat-2-Max (результаты уже там)
    if a.provider == "gigachat":
        folder_name = {"GigaChat-2-Max": "gigachat"}.get(model, slugify(model))
    elif a.provider == "deepseek":
        folder_name = slugify(model)
    else:
        folder_name = "yandexgpt"
    out_root = Path(a.models_root) / folder_name
    role = "backend"
    out_root = out_root / role
    if a.resume:
        # продолжить последний существующий прогон
        runs = sorted(p.name for p in out_root.glob("run-*"))
        if not runs:
            print("нет прогонов для resume", file=sys.stderr)
            return 2
        out_dir = out_root / runs[-1]
        run_no = int(runs[-1].split("-")[1])
        print(f"[resume run-{run_no:02d}] {model} · провайдер {a.provider}")
        run_interview(prov, model, questions, q_version, run_no, out_dir, a.timeout,
                      a.limit, role, resume=True)
        return 0
    run_no = a.run or 1
    while (out_root / f"run-{run_no:02d}").exists():
        run_no += 1
    out_dir = out_root / f"run-{run_no:02d}"
    print(f"[run-{run_no:02d}] {model} · провайдер {a.provider} · роль: {role}")
    # новый прогон — с холодным кэшем провайдера (TTL-пауза); resume выше
    # сознательно идёт в тепле — продолжение той же сессии
    wait_cache_ttl(out_root)
    run_interview(prov, model, questions, q_version, run_no, out_dir, a.timeout,
                  a.limit, role)
    return 0


if __name__ == "__main__":
    sys.exit(main())

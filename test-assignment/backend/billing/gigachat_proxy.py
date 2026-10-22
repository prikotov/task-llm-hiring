#!/usr/bin/env python3
"""Мост GigaChat -> OpenAI для агентного харнеса pi (этап 2, провайдер gigachat).

Зачем: pi говорит современным OpenAI-протоколом (tools/tool_calls), а GigaChat
API понимает только легаси (functions/function_call, результаты — role:function);
проверено живьём 2026-10-21 (tools GigaChat молча игнорирует). Плюс OAuth-токен
Сбера живёт ~30 минут — статического apiKey в models.json недостаточно, нужен
рефреш посреди прогона. Прокси решает обе задачи:

    pi (tools, SSE) -> 127.0.0.1:<порт>/v1 -> прокси -> api.giga.chat (легаси)

Перевод:
- запрос:  tools[] -> functions[], tool_choice -> function_call,
  role:tool -> role:function (+name из истории assistant.tool_calls);
- ответ:   message/delta.function_call -> tool_calls[] (arguments объект ->
  JSON-строка), finish_reason function_call -> tool_calls; SSE конвертируется
  на лету, finish_reason уносится в отдельный чанк (pi так ожидает);
- GigaChat возвращает НЕ БОЛЕЕ ОДНОГО вызова функции за ход (ограничение API) —
  pi это устраивает: агент вызывает инструменты последовательно.

Аутентификация: GIGACHAT_AUTHORIZATION_KEY из env или env-файла по пути
MODELS_RU_ENV (та же бухгалтерия, что у runner_ru_cloud / deepseek_key);
секреты в git не попадают. Исходящий TLS — через системный curl (проверено:
сертификаты Сбера в системном хранилище; хранилище сертификатов Node у pi
не трогаем). Тело запроса подаётся curl'у через stdin.

Запуск вручную (отладка):
    python3 gigachat_proxy.py --port 8046
    curl http://127.0.0.1:8046/v1/chat/completions -d '{...}'

В runner_pi.py прокси живёт в потоке процесса прогона и гасится в finally.
"""
from __future__ import annotations

import argparse
import json
import os
import subprocess
import sys
import threading
import time
import uuid
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path

GIGA_OAUTH = "https://ngw.devices.sberbank.ru:9443/api/v2/oauth"
GIGA_CHAT = "https://api.giga.chat/v1/chat/completions"
TOKEN_LIFETIME_S = 25 * 60          # живёт ~30 мин — обновляем с запасом
CURL_TIMEOUT_S = 600                # генерация Сбера медленная, без спешки
MAX_TOKENS_CAP = 8192               # выход за лимит Сбера = 400 на каждый запрос

# диагностика сбоев (только /tmp: тело запроса секретов не содержит)
_CURL_ERR_PATH = Path("/tmp/giga-curl-stderr.log")
_FAIL_DUMP = Path("/tmp/giga-fail-last.json")

HERE = Path(__file__).resolve().parent
MODELS_ROOT = HERE.parents[2]       # корень репозитория Models


def load_auth_key() -> str:
    """GIGACHAT_AUTHORIZATION_KEY: env, затем env-файл по MODELS_RU_ENV
    (путь из env или .env.local корня репозитория Models)."""
    v = os.environ.get("GIGACHAT_AUTHORIZATION_KEY")
    if v:
        return v.strip()
    env_path = os.environ.get("MODELS_RU_ENV")
    if not env_path:
        env_local = MODELS_ROOT / ".env.local"
        if env_local.is_file():
            for line in env_local.read_text().splitlines():
                if line.startswith("MODELS_RU_ENV="):
                    env_path = line.split("=", 1)[1].strip()
                    break
    if not env_path:
        sys.exit("gigachat_proxy: задайте MODELS_RU_ENV — путь к env-файлу (см. README)")
    for line in Path(env_path).expanduser().read_text().splitlines():
        if line.startswith("GIGACHAT_AUTHORIZATION_KEY="):
            return line.split("=", 1)[1].strip()
    sys.exit("gigachat_proxy: GIGACHAT_AUTHORIZATION_KEY не найден в env-файле")


def curl_json(method: str, url: str, headers: dict, body: bytes,
              timeout: int = 120) -> tuple[int, bytes]:
    """curl с телом через stdin (без временных файлов); код — последняя строка."""
    cmd = ["curl", "-s", "--max-time", str(timeout), "-X", method, url, "-w", "\n%{http_code}"]
    for k, v in headers.items():
        cmd += ["-H", f"{k}: {v}"]
    if body is not None:
        cmd += ["--data-binary", "@-"]
    r = subprocess.run(cmd, input=body, capture_output=True, timeout=timeout + 10)
    payload, _, code = r.stdout.rpartition(b"\n")
    try:
        return int(code.strip() or 0), payload
    except ValueError:
        return 0, r.stdout


class TokenManager:
    """OAuth-токен физлица с автообновлением (потокобезопасно)."""

    def __init__(self, key: str) -> None:
        self._key = key
        self._lock = threading.Lock()
        self._token: str | None = None
        self._expires = 0.0

    def get(self, force: bool = False) -> str:
        with self._lock:
            if not force and self._token and time.time() < self._expires:
                return self._token
            code, payload = curl_json("POST", GIGA_OAUTH, {
                "Authorization": f"Basic {self._key}",
                "RqUID": str(uuid.uuid4()),
                "Content-Type": "application/x-www-form-urlencoded",
                "Accept": "application/json",
            }, b"scope=GIGACHAT_API_PERS", timeout=60)
            try:
                d = json.loads(payload)
            except json.JSONDecodeError:
                raise RuntimeError(f"giga oauth HTTP {code}: {payload[:200]!r}")
            if code != 200 or "access_token" not in d:
                raise RuntimeError(f"giga oauth HTTP {code}: {payload[:200]!r}")
            self._token = d["access_token"]
            self._expires = time.time() + TOKEN_LIFETIME_S
            print(f"[giga-proxy] OAuth-токен обновлён (рефреш через {TOKEN_LIFETIME_S // 60} мин)",
                  flush=True)
            return self._token


# ---------------------------------------------------------------- перевод ----

# ключи запроса, которые уходят в GigaChat (остальные молча отбрасываются —
# pi присылает stream_options/parallel_tool_calls и прочее, Сбер это не знает)
PASSTHROUGH = ("temperature", "top_p", "max_tokens")


def tools_to_functions(tools: list) -> list:
    out = []
    for t in tools:
        f = t.get("function") if isinstance(t, dict) else None
        if f and f.get("name"):
            out.append({"name": f["name"],
                        "description": f.get("description") or "",
                        "parameters": f.get("parameters") or {"type": "object", "properties": {}}})
    return out


def map_tool_choice(tc) -> str | dict:
    if isinstance(tc, dict):
        name = (tc.get("function") or {}).get("name")
        return {"name": name} if name else "auto"
    return {"auto": "auto", "none": "none"}.get(tc, "auto")


def _args_obj(arguments) -> dict:
    """GigaChat в истории function_call ждёт arguments ОБЪЕКТОМ (не строкой
    как OpenAI; проверено 2026-10-21 — строка даёт 400 invalid JSON)."""
    if isinstance(arguments, dict):
        return arguments
    try:
        v = json.loads(arguments or "{}")
        return v if isinstance(v, dict) else {"value": v}
    except (json.JSONDecodeError, TypeError):
        return {}


def to_gigachat(payload: dict) -> dict:
    """Современный OpenAI-запрос pi -> легаси-запрос GigaChat."""
    id2name: dict[str, str] = {}
    msgs_out = []
    for m in payload.get("messages", []):
        role = m.get("role")
        content = m.get("content")
        if role in ("system", "developer"):
            msgs_out.append({"role": "system", "content": content or ""})
        elif role == "user":
            msgs_out.append({"role": "user",
                             "content": content if isinstance(content, str)
                             else json.dumps(content, ensure_ascii=False)})
        elif role == "assistant":
            tcs = m.get("tool_calls") or []
            if tcs:
                for tc in tcs:  # карта id->имя для результатов role:tool
                    if tid := tc.get("id"):
                        id2name[tid] = (tc.get("function") or {}).get("name") or ""
                fn = tcs[0].get("function") or {}
                msgs_out.append({"role": "assistant", "content": content or "",
                                 "function_call": {"name": fn.get("name") or "",
                                                   "arguments": _args_obj(fn.get("arguments"))}})
            else:
                msgs_out.append({"role": "assistant", "content": content or ""})
        elif role == "tool":
            c = content if isinstance(content, str) else json.dumps(content, ensure_ascii=False)
            # Сбер валидирует текст результата функции как JSON (422 иначе) —
            # текстовые результаты оборачиваем в {"result": ...}
            try:
                json.loads(c)
            except (json.JSONDecodeError, TypeError):
                c = json.dumps({"result": c}, ensure_ascii=False)
            # GigaChat не знает tool_call_id — имя функции берём из истории
            name = id2name.get(m.get("tool_call_id") or "", (m.get("name") or "tool"))
            msgs_out.append({"role": "function", "name": name, "content": c})
    out = {"model": payload.get("model"), "messages": msgs_out}
    if tools := payload.get("tools"):
        out["functions"] = tools_to_functions(tools)
        out["function_call"] = map_tool_choice(payload.get("tool_choice"))
    for k in PASSTHROUGH:
        if payload.get(k) is not None:
            out[k] = min(payload[k], MAX_TOKENS_CAP) if k == "max_tokens" else payload[k]
    if payload.get("stream"):
        out["stream"] = True   # без флага Сбер вернёт обычный JSON вместо SSE
    return out


def _fc_to_tool_calls(fc: dict) -> list:
    args = fc.get("arguments")
    if not isinstance(args, str):
        args = json.dumps(args or {}, ensure_ascii=False)
    return [{"index": 0, "id": fc.get("id") or f"call_{uuid.uuid4().hex[:12]}",
             "type": "function",
             "function": {"name": fc.get("name") or "", "arguments": args}}]


def _finish(fr) -> str | None:
    return {"function_call": "tool_calls"}.get(fr, fr)


def choice_to_chunks(choice: dict, chunk_base: dict) -> list[dict]:
    """choice GigaChat -> чанки OpenAI: delta отдельно от finish_reason."""
    chunks: list[dict] = []
    msg = choice.get("message") or {}
    delta = choice.get("delta") or {}
    if msg or "message" in choice:
        nm = {"role": "assistant", "content": msg.get("content") or ""}
        if fc := msg.get("function_call"):
            nm["tool_calls"] = _fc_to_tool_calls(fc)
        chunks.append({**chunk_base, "choices": [{"index": choice.get("index") or 0,
                                                  "message": nm}]})
    if "delta" in choice:
        nd: dict = {}
        if delta.get("role"):
            nd["role"] = "assistant"
        if delta.get("content"):
            nd["content"] = delta["content"]
        if fc := delta.get("function_call"):
            nd["tool_calls"] = _fc_to_tool_calls(fc)
        chunks.append({**chunk_base, "choices": [{"index": choice.get("index") or 0,
                                                  "delta": nd}]})
    if (fr := choice.get("finish_reason")) is not None:
        chunks.append({**chunk_base, "choices": [{"index": choice.get("index") or 0,
                                                  "delta": {}, "finish_reason": _finish(fr)}]})
    return chunks


def from_gigachat_response(d: dict) -> dict:
    """Полный ответ GigaChat -> chat.completion OpenAI."""
    base = {"id": d.get("id") or f"chatcmpl-{uuid.uuid4().hex[:12]}",
            "object": "chat.completion",
            "created": d.get("created") or int(time.time()),
            "model": d.get("model") or ""}
    choices = [c for ch in d.get("choices", []) for c in choice_to_chunks(ch, base)]
    # не-стрим: собираем обратно в один choice (message + finish_reason)
    message, finish = {}, None
    for c in choices:
        message.update(c["choices"][0].get("message") or {})
        if c["choices"][0].get("finish_reason"):
            finish = c["choices"][0]["finish_reason"]
    return {**base, "choices": [{"index": 0, "message": message, "finish_reason": finish}],
            "usage": d.get("usage")}


# ------------------------------------------------------------------ сервер ---

class GigaProxy:
    def __init__(self) -> None:
        self.tokens = TokenManager(load_auth_key())
        self.requests = 0

    def _headers(self, force: bool = False) -> dict:
        return {"Authorization": f"Bearer {self.tokens.get(force)}",
                "Content-Type": "application/json",
                "Accept": "application/json"}

    def chat(self, payload: dict):
        """Не-стрим: (code, dict OpenAI | dict ошибки).
        Стрим: ('stream', генератор строк SSE) или (код, dict ошибки)."""
        body = json.dumps(to_gigachat(payload), ensure_ascii=False).encode()
        stream = bool(payload.get("stream"))
        if not stream:
            code, raw = self._post_json(body)
            if code == 401:
                code, raw = self._post_json(body, force=True)
            try:
                d = json.loads(raw)
            except json.JSONDecodeError:
                return 502, {"error": {"message": f"gigachat: не-JSON: {raw[:200]!r}"}}
            return code, (from_gigachat_response(d) if code == 200 else d)
        for attempt in (False, True):
            kind_or_code, out = self._post_stream(body, force=attempt)
            if kind_or_code == "stream":
                return "stream", out
            if kind_or_code != 401:
                return kind_or_code, out
        return kind_or_code, out  # 401 и после рефреша

    def _post_json(self, body: bytes, force: bool = False) -> tuple[int, bytes]:
        return curl_json("POST", GIGA_CHAT, self._headers(force), body, CURL_TIMEOUT_S)

    def _post_stream(self, body: bytes, force: bool = False):
        """SSE-запрос. Успех: ('stream', gen). Ошибка: (http_code, dict ошибки) —
        Сбер отдаёт JSON вместо SSE, статус узнаём из последней строки curl."""
        cmd = ["curl", "-s", "-N", "--max-time", str(CURL_TIMEOUT_S), "-X", "POST", GIGA_CHAT,
               "--data-binary", "@-", "-w", "\n%{http_code}"]
        headers = {**self._headers(force), "Accept": "text/event-stream"}
        for k, v in headers.items():
            cmd += ["-H", f"{k}: {v}"]
        err_file = _CURL_ERR_PATH.open("wb")
        proc = subprocess.Popen(cmd, stdin=subprocess.PIPE, stdout=subprocess.PIPE,
                                stderr=err_file)
        assert proc.stdin and proc.stdout
        try:
            proc.stdin.write(body)   # curl читает stdin до первого байта вывода
            proc.stdin.close()
        except BrokenPipeError:
            pass
        first = proc.stdout.readline().decode("utf-8", errors="replace").strip()
        if first.startswith("data:"):
            def gen():
                yield first
                assert proc.stdout is not None
                for raw in proc.stdout:
                    yield raw.decode("utf-8", errors="replace").rstrip("\r\n")
                proc.wait(timeout=30)
                err_file.close()
            return "stream", gen()
        # ошибка: буферизуем (тело маленькое), код дописан curl'ом в конец
        rest = proc.stdout.read()
        proc.wait(timeout=30)
        err_file.close()
        text = (first + "\n" + rest.decode("utf-8", errors="replace")).strip()
        payload, _, code_s = text.rpartition("\n")
        try:
            code = int(code_s.strip())
        except ValueError:
            code = 502
        curl_err = _CURL_ERR_PATH.read_text(errors="replace").strip()
        try:
            d = json.loads(payload or "{}")
        except json.JSONDecodeError:
            d = {"error": {"message": (payload or text)[:300]}}
        if not payload and curl_err:
            d = {"error": {"message": f"curl не ответил: {curl_err[:300]}"}}
        if not payload or code >= 400:  # дамп для разбора (секретов в теле нет)
            _FAIL_DUMP.write_bytes(body)
        return code, d


def make_handler(proxy: GigaProxy, model_id: str):
    class Handler(BaseHTTPRequestHandler):
        protocol_version = "HTTP/1.1"

        def handle_one_request(self):
            # pi рвёт idle keep-alive соединения — сброс не должен шуметь трейсом
            try:
                super().handle_one_request()
            except ConnectionResetError:
                self.close_connection = True

        def log_message(self, fmt, *args):  # логируем сами, построчно
            pass

        def _send_json(self, code: int, obj: dict) -> None:
            b = json.dumps(obj, ensure_ascii=False).encode()
            self.send_response(code)
            self.send_header("Content-Type", "application/json")
            self.send_header("Content-Length", str(len(b)))
            self.end_headers()
            self.wfile.write(b)

        def do_GET(self):
            if self.path in ("/health", "/v1/health"):
                return self._send_json(200, {"ok": True})
            if self.path in ("/v1/models", "/models"):
                return self._send_json(200, {"object": "list", "data": [
                    {"id": model_id, "object": "model", "owned_by": "sber"}]})
            self._send_json(404, {"error": {"message": f"нет пути: {self.path}"}})

        def do_POST(self):
            if not self.path.endswith("/chat/completions"):
                return self._send_json(404, {"error": {"message": f"нет пути: {self.path}"}})
            n = int(self.headers.get("Content-Length") or 0)
            try:
                payload = json.loads(self.rfile.read(n))
            except json.JSONDecodeError as e:
                return self._send_json(400, {"error": {"message": f"битый JSON: {e}"}})
            if os.environ.get("GIGA_DUMP_REQUEST"):  # отладка: дамп запроса pi
                Path(os.environ["GIGA_DUMP_REQUEST"]).write_bytes(
                    json.dumps(payload, ensure_ascii=False).encode())
            t0 = time.time()
            proxy.requests += 1
            try:
                code, out = proxy.chat(payload)
            except Exception as exc:  # noqa: BLE001
                print(f"[giga-proxy] СБОЙ запроса: {exc!r}", flush=True)
                return self._send_json(502, {"error": {"message": f"giga-proxy: {exc}"}})
            dur = time.time() - t0
            req_no = proxy.requests

            if code == "stream":
                self.send_response(200)
                self.send_header("Content-Type", "text/event-stream")
                self.send_header("Cache-Control", "no-cache")
                self.send_header("Transfer-Encoding", "chunked")
                self.end_headers()
                n_in = n_out = 0
                for line in out:
                    if not line.startswith("data:"):
                        continue
                    data = line[5:].strip()
                    if data == "[DONE]":
                        self._sse(b"data: [DONE]\n\n")
                        break
                    try:
                        d = json.loads(data)
                    except json.JSONDecodeError:
                        continue
                    if not d.get("choices"):
                        continue
                    base = {"id": d.get("id") or f"chatcmpl-{uuid.uuid4().hex[:8]}",
                            "object": "chat.completion.chunk",
                            "created": d.get("created") or int(time.time()),
                            "model": d.get("model") or payload.get("model")}
                    chunks = choice_to_chunks(d["choices"][0], base)
                    if d.get("usage") and chunks:
                        # Сбер вешает usage на финальный чанк с choices (не как
                        # OpenAI — отдельным чанком); pi парсит usage любого чанка,
                        # precached-токены Сбера учится как cacheRead
                        chunks[-1]["usage"] = d["usage"]
                    for chunk in chunks:
                        self._sse(f"data: {json.dumps(chunk, ensure_ascii=False)}\n\n".encode())
                self.wfile.write(b"0\r\n\r\n")  # конец chunked
                self.wfile.flush()
                print(f"[giga-proxy] #{req_no} {payload.get('model')} stream ok, "
                      f"{dur:.1f}с", flush=True)
                return

            if code != 200 or out.get("error"):
                print(f"[giga-proxy] #{req_no} {payload.get('model')} HTTP {code} "
                      f"за {dur:.1f}с: {json.dumps(out, ensure_ascii=False)[:200]}",
                      flush=True)
                return self._send_json(code if code else 502, out)

            usage = (out.get("usage") or {})
            print(f"[giga-proxy] #{req_no} {payload.get('model')} HTTP 200 за {dur:.1f}с, "
                  f"ток {usage.get('prompt_tokens', '?')}/"
                  f"{usage.get('completion_tokens', '?')}", flush=True)
            self._send_json(200, out)

        def _sse(self, b: bytes) -> None:
            self.wfile.write(f"{len(b):x}\r\n".encode() + b + b"\r\n")
            self.wfile.flush()

    return Handler


def serve(model_id: str, host: str = "127.0.0.1", port: int = 0) -> tuple[ThreadingHTTPServer, GigaProxy]:
    """Поднять прокси; .serve_forever() — на вызывающем (поток/процесс)."""
    proxy = GigaProxy()
    server = ThreadingHTTPServer((host, port), make_handler(proxy, model_id))
    return server, proxy


def main() -> int:
    ap = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    ap.add_argument("--model", default="GigaChat-3-Ultra", help="имя модели (для /v1/models)")
    ap.add_argument("--host", default="127.0.0.1")
    ap.add_argument("--port", type=int, default=8046)
    args = ap.parse_args()
    server, _ = serve(args.model, args.host, args.port)
    print(f"[giga-proxy] слушаю http://{args.host}:{server.server_address[1]}/v1 "
          f"(модель {args.model}); Ctrl+C — стоп", flush=True)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

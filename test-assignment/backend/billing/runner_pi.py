#!/usr/bin/env python3
"""Изолированный и управляемый прогон тестового задания через pi (этап 2).

Глобальный конфиг pi (~/.pi/agent) НЕ используется: каждый прогон получает
собственный PI_CODING_AGENT_DIR во временном каталоге, models.json для
единственной модели собирается на лету. Адрес сервера моделей берётся из
env OLLAMA_BASE_URL (или из .env.local корня репозитория) — в git адрес
не попадает.

Управляемость:
- pi запускается лидером отдельной process group; SIGTERM/SIGINT раннера
  и таймаут гасят всю группу — «зомби-pi», переживший остановку обёртки,
  невозможен;
- pi-run.log пишется стримингом (живой просмотр: tail -f);
- run.json фиксирует параметры и исход прогона (модель, thinking, контекст,
  время, exit code, версия TASK, размеры диффа/решения).

Использование (из каталога billing/):

    python3 runner_pi.py --model qwen3.8:27b-pi-100k \
        --results models/qwen3.8-27b-pi-100k/backend/test-assignment/run-01

Опции: --thinking (default max), --context (default 102400),
--timeout секунд (default 5400), --reasoning/--no-reasoning,
--smoke (проверка сантехники), --keep (не удалять временные каталоги).

После прогона в <results>: run.json, pi-session/ (JSONL — полный след
размышлений и действий), pi-run.log, diff.patch, solution/.
Чекер и метрики — отдельно: checker/check.py, checker/metrics.py.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import os
import shutil
import signal
import subprocess
import sys
import tempfile
import threading
import time
from datetime import datetime, timezone
from pathlib import Path

HERE = Path(__file__).resolve().parent
BILLING = HERE


def models_repo() -> Path:
    """Корень репозитория Models — ищем вверх по дереву (устойчиво к вложенности роли)."""
    for parent in BILLING.parents:
        if (parent / ".env.local").is_file() or (parent / ".git").is_dir():
            return parent
    return BILLING.parents[2]

DEFAULT_CONTEXT = 102_400
DEFAULT_TIMEOUT = 5400
DEFAULT_THINKING = "max"

# Уровни pi -> значение для модели. Правильный источник — /api/show каждой
# модели (docs.ollama.com: reasoning_effort принимает model-defined имена
# + алиасы совместимости; узнать поддерживаемые — /api/show thinking.values).
# Замеры 2026-10-16 показывают: несуществующее значение сервер молча
# подменяет (qwen/huihui: шкала false/low/medium/xhigh, отправленный
# "high" работал как ~medium), поэтому карту строим из фактической шкалы:
#   max -> самый высокий уровень модели; off -> false; остальные —
#   ближайший существующий сверху вниз. Для boolean-шкал (false,true —
#   orca/gemma) любой уровень, кроме off, -> true.
# Энергичные уровни (xhigh/max в шкале модели) кратно расширяют бюджет
# размышлений и при малом maxTokens выжигают его в пустой ответ
# (finish=length), поэтому maxTokens автоматически поднимается до 32768.
PI_LEVELS_RANKED = ["low", "medium", "high", "xhigh", "max"]
PI_CHOICES = ["off", "minimal", *PI_LEVELS_RANKED]


def thinking_map_for(values: list) -> tuple[dict, bool]:
    """Карта уровней pi -> значения модели + признак «энергичный максимум»."""
    levels = [v for v in values if isinstance(v, str)]
    top = None
    for cand in reversed(PI_LEVELS_RANKED):
        if cand in levels:
            top = cand
            break
    boolean = True in values
    # pi валидирует значения карты как СТРОКИ (boolean true ломает models.json
    # целиком — pi молча теряет все модели); для boolean-шкал любой уровень,
    # кроме off, — строка "high"
    m: dict = {}
    for pi_level in ("off", "minimal", "low", "medium", "high", "xhigh", "max"):
        if pi_level == "off":
            m[pi_level] = "none"
        elif pi_level in levels:
            m[pi_level] = pi_level
        elif top is not None:
            m[pi_level] = top
        else:
            m[pi_level] = "high"
    eager = top in ("xhigh", "max")
    return m, eager

TASK_PROMPT = "Прочитай TASK.md в корне репозитория и выполни задание."
SMOKE_PROMPT = "Создай в текущем каталоге файл answer.txt с текстом 'ok' (без кавычек) и заверши работу."


def ollama_base_url() -> str:
    """Адрес сервера моделей: env, затем .env.local корня репозитория."""
    url = os.environ.get("OLLAMA_BASE_URL")
    if not url:
        env_local = models_repo() / ".env.local"
        if env_local.is_file():
            for line in env_local.read_text().splitlines():
                if line.startswith("OLLAMA_BASE_URL="):
                    url = line.split("=", 1)[1].strip()
                    break
    if not url:
        sys.exit("OLLAMA_BASE_URL не задан (env или .env.local)")
    return url.rstrip("/")


def soft_ollama_base() -> str | None:
    """Как ollama_base_url, но без sys.exit — для фоновой диагностики."""
    url = os.environ.get("OLLAMA_BASE_URL")
    if not url:
        env_local = models_repo() / ".env.local"
        if env_local.is_file():
            for line in env_local.read_text().splitlines():
                if line.startswith("OLLAMA_BASE_URL="):
                    url = line.split("=", 1)[1].strip()
                    break
    return url.rstrip("/") if url else None


def watch_ollama_foreign(stop: threading.Event, own_model: str, found: list) -> None:
    """Фоновый поток: раз в 30 с опрашивает /api/ps и копит чужие модели,
    живущие параллельно с прогоном (признак конкуренции за VRAM). Факт
    фиксируется в run.json; такие прогоны исключаются из метрик отчёта."""
    base = soft_ollama_base()
    if not base:
        return
    import urllib.request

    while True:
        try:
            with urllib.request.urlopen(f"{base}/api/ps", timeout=10) as r:
                for m in json.loads(r.read()).get("models", []):
                    name = m.get("name", "")
                    if name and name != own_model and name not in found:
                        found.append(name)
        except Exception:
            pass  # диагностика не должна ронять прогон
        if stop.wait(30):
            return


def model_thinking_values(model: str) -> list:
    """/api/show: поддерживаемые значения thinking (пусто — thinking не управляется)."""
    import urllib.request

    try:
        req = urllib.request.Request(
            f"{ollama_base_url()}/api/show",
            data=json.dumps({"model": model}).encode(),
            headers={"Content-Type": "application/json"},
        )
        with urllib.request.urlopen(req, timeout=60) as r:
            d = json.loads(r.read())
        holder = d.get("capabilities") or {}
        t = holder.get("thinking") if isinstance(holder, dict) else None
        if not isinstance(t, dict):
            t = d.get("thinking") if isinstance(d.get("thinking"), dict) else {}
        return list(t.get("values") or [])
    except OSError:
        return []


def deepseek_key() -> str:
    """Ключ DeepSeek: env DEEPSEEK_API_KEY или env-файл по пути MODELS_RU_ENV
    (та же бухгалтерия, что у интервью-раннера runner_ru_cloud)."""
    v = os.environ.get("DEEPSEEK_API_KEY")
    if v:
        return v.strip()
    env_path = os.environ.get("MODELS_RU_ENV")
    if not env_path:
        for line in (models_repo() / ".env.local").read_text().splitlines():
            if line.startswith("MODELS_RU_ENV="):
                env_path = line.split("=", 1)[1].strip()
                break
    if not env_path:
        sys.exit("задайте MODELS_RU_ENV — путь к env-файлу с ключами (см. README)")
    for line in Path(env_path).expanduser().read_text().splitlines():
        if line.startswith("DEEPSEEK_API_KEY="):
            return line.split("=", 1)[1].strip()
    sys.exit("DEEPSEEK_API_KEY не найден в env-файле")


def secret_path(name: str) -> Path:
    """Путь к файлу с секретом: env, затем .env.local (в git не живёт)."""
    v = os.environ.get(name)
    if not v:
        for line in (models_repo() / ".env.local").read_text().splitlines():
            if line.startswith(name + "="):
                v = line.split("=", 1)[1].strip()
                break
    if not v:
        sys.exit(f"задайте {name} — путь к файлу с секретом (см. README)")
    return Path(v).expanduser()


def openrouter_creds() -> tuple[str, str]:
    """Ключ и прокси OpenRouter (значения env — пути к файлам с секретами)."""
    return (secret_path("OPENROUTER_KEY_FILE").read_text().strip(),
            secret_path("OPENROUTER_PROXY_FILE").read_text().strip())


def orcarouter_key() -> str:
    """Ключ OrcaRouter (orcarouter.ai) — путь к файлу в ORCAROUTER_KEY_FILE."""
    return secret_path("ORCAROUTER_KEY_FILE").read_text().strip()


def build_pi_home(tmp: Path, model: str, context: int, max_tokens: int, reasoning: bool,
                  provider: str = "ollama", proxy_port: int = 0
                  ) -> tuple[Path, str | None, dict[str, str]]:
    """pi-home с models.json под одного провайдера.
    Возвращает (pi-home, proxy, доп. env для pi)."""
    pi_home = tmp / "pi-home"
    pi_home.mkdir()
    entry: dict = {"id": model, "contextWindow": context, "maxTokens": max_tokens}
    proxy = None
    extra_env: dict[str, str] = {}
    if provider == "openrouter":
        # нативный openrouter-провайдер pi: модель берётся из каталога pi
        # (anthropic-messages к openrouter.ai/api, нативные contextWindow/
        # maxTokens, шкала мышления с xhigh/max). Ключ — через env.
        # Кастомная запись openai-completions (там reasoning и ответ делят
        # один max_tokens) приводила к stop=length посреди размышлений —
        # haiku 5.5 max 2026-10-22, обе попытки без единой строки кода.
        key, proxy = openrouter_creds()
        extra_env["OPENROUTER_API_KEY"] = key
        return pi_home, proxy, extra_env
    if provider == "orcarouter":
        # OrcaRouter (orcarouter.ai): нативный Anthropic-протокол /v1/messages,
        # ключ Bearer (authHeader), цена = прайс провайдера без наценки,
        # из домашней сети ходит без прокси. Поля записи модели скопированы
        # из каталога pi (нативный путь claude: adaptive thinking +
        # output_config.effort; контекст 1M, бюджет выхода 128K — native,
        # не переопределяем). Каталог моделей: GET /v1/models (публичный);
        # claude-haiku-5.5 там нет (2026-10-22), opus-5.5 есть.
        model_id = model if "/" in model else f"anthropic/{model}"
        providers = {"orcarouter": {
            "baseUrl": "https://api.orcarouter.ai",
            "api": "anthropic-messages", "apiKey": orcarouter_key(),
            "authHeader": True,
            "models": [{
                "id": model_id, "contextWindow": 1_048_576, "maxTokens": 128_000,
                "reasoning": True,
                # прайс Anthropic без наценки — pi сам считает стоимость сессии
                # (иначе usage.cost везде 0; приём из гайда кастомных провайдеров)
                "cost": {"input": 4, "output": 20, "cacheRead": 0.4, "cacheWrite": 0.5},
                "thinkingLevelMap": {"off": "none", "minimal": None, "low": "low",
                                     "medium": "medium", "high": "high",
                                     "xhigh": "xhigh", "max": "max"},
                # БЕЗ supportsMidConvoEffort/forceAdaptiveThinking: оба флага
                # ведут на adaptive-мышление (output_config.effort), где effort —
                # только намёк, и модель тратит reasoning без потолка (прогон
                # low 2026-10-22: 70.8K reasoning, $4.38, один вызов 19.9K).
                # Классический режим pi даёт жёсткие бюджеты на вызов:
                # low 2048 / medium 8192 / high и max 16384 токена reasoning.
                "compat": {"supportsTemperature": False},
            }]}}
    elif provider == "deepseek":
        # родной API DeepSeek (OpenAI-совместимый), без прокси; reasoning_effort
        # переключает размышления: минимум — low, максимум — high
        entry["reasoning"] = True
        entry["thinkingLevelMap"] = {"off": "none", "minimal": "low", "low": "low",
                                     "medium": "medium", "high": "high",
                                     "xhigh": "high", "max": "high"}
        providers = {"deepseek": {"baseUrl": "https://api.deepseek.com",
                                  "api": "openai-completions", "apiKey": deepseek_key(),
                                  "models": [entry]}}
    elif provider == "gigachat":
        # pi -> локальный мост gigachat_proxy (tools<->functions, OAuth-рефреш)
        # -> api.giga.chat; reasoning у GigaChat нет — флаг не ставим
        providers = {"gigachat": {"baseUrl": f"http://127.0.0.1:{proxy_port}/v1",
                                  "api": "openai-completions", "apiKey": "gigachat-freemium",
                                  "models": [entry]}}
    else:
        if reasoning:
            values = model_thinking_values(model)
            if values:
                entry["reasoning"] = True
                entry["thinkingLevelMap"] = thinking_map_for(values)[0]
                print(f"thinking-шкала модели ({len(values)} знач.): {values}")
            else:
                # пустая шкала = thinking не поддерживается: reasoning-флаг ставить
                # нельзя — ollama отвечает 400 «does not support thinking»
                print("thinking-шкала модели пуста — гоняем без reasoning-флага")
        providers = {"ollama": {"baseUrl": f"{ollama_base_url()}/v1",
                                "api": "openai-completions", "apiKey": "ollama",
                                "models": [entry]}}
    (pi_home / "models.json").write_text(json.dumps(
        {"providers": providers}, ensure_ascii=False, indent=2), encoding="utf-8")
    return pi_home, proxy, extra_env


class PiRun:
    """pi — лидер собственной process group; остановка убивает группу."""

    def __init__(self, cmd: list[str], cwd: Path, env: dict[str, str], log_path: Path) -> None:
        self.log = log_path.open("w", encoding="utf-8")
        self.proc = subprocess.Popen(
            cmd, cwd=cwd, env=env,
            stdout=self.log, stderr=subprocess.STDOUT,
            text=True, start_new_session=True,
        )
        self.killed = False

    def wait(self, timeout: int) -> int:
        try:
            return self.proc.wait(timeout=timeout)
        except subprocess.TimeoutExpired:
            self.terminate(reason="timeout")
            return self.proc.returncode if self.proc.returncode is not None else 124

    def terminate(self, reason: str = "signal") -> None:
        if self.proc.poll() is None:
            self.killed = True
            print(f"остановка pi ({reason}): убиваю группу {self.proc.pid}", file=sys.stderr)
            try:
                os.killpg(self.proc.pid, signal.SIGTERM)
                try:
                    self.proc.wait(timeout=15)
                except subprocess.TimeoutExpired:
                    os.killpg(self.proc.pid, signal.SIGKILL)
                    self.proc.wait(timeout=10)
            except ProcessLookupError:
                pass
        if not self.log.closed:
            self.log.close()


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--model", required=True,
                        help="тег модели: ollama-тег, слаг OpenRouter (openai/gpt-6-luna) "
                             "или id OrcaRouter (anthropic/claude-opus-5.5)")
    parser.add_argument("--provider", default="ollama",
                        choices=["ollama", "openrouter", "orcarouter", "deepseek", "gigachat"],
                        help="ollama / openrouter (через прокси) / deepseek (родной API) / "
                             "gigachat (родной API через мост gigachat_proxy)")
    parser.add_argument("--results", required=True, help="каталог результатов run-XX")
    parser.add_argument("--thinking", default=DEFAULT_THINKING,
                        choices=PI_CHOICES, help="уровень размышлений pi")
    parser.add_argument("--context", type=int, default=DEFAULT_CONTEXT,
                        help="контекстное окно, токены (default 102400)")
    parser.add_argument("--max-tokens", type=int, default=8192,
                        help="бюджет выхода на вызов (default 8192; для xhigh нужно 32768+)")
    parser.add_argument("--timeout", type=int, default=DEFAULT_TIMEOUT, help="сек (default 5400)")
    parser.add_argument("--reasoning", dest="reasoning", action="store_true", default=True)
    parser.add_argument("--no-reasoning", dest="reasoning", action="store_false")
    parser.add_argument("--smoke", action="store_true", help="проверка сантехники, не задание")
    parser.add_argument("--keep", action="store_true", help="не удалять временные каталоги")
    args = parser.parse_args()

    results = Path(args.results).resolve()
    results.mkdir(parents=True, exist_ok=True)

    tmp = Path(tempfile.mkdtemp(prefix="billing-pi-"))
    repo = tmp / "repo"
    started = time.time()
    meta: dict = {
        "model": args.model,
        "provider": args.provider,
        "thinking": args.thinking,
        "context": args.context,
        "timeout_s": args.timeout,
        "smoke": args.smoke,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "task_sha256": hashlib.sha256(
            (BILLING / "skeleton" / "TASK.md").read_bytes()
        ).hexdigest()[:16],
    }
    run: PiRun | None = None
    stop_watch = threading.Event()
    foreign_models: list[str] = []
    giga_server = None
    try:
        if args.provider == "ollama" and args.max_tokens == 8192 and model_thinking_values(args.model):
            _, eager = thinking_map_for(model_thinking_values(args.model))
            if eager:
                args.max_tokens = 32768
                print("энергичный максимум thinking: maxTokens поднят до 32768 "
                      "(при 8192 xhigh/max выжигают бюджет в пустой ответ)")
        if args.provider == "deepseek" and args.max_tokens == 8192:
            args.max_tokens = 16000  # API-дефолт 4096/8192 режет ответы в полуслове
        if args.provider == "gigachat":
            import gigachat_proxy  # мост GigaChat<->OpenAI, живёт в потоке прогона
            giga_server, _ = gigachat_proxy.serve(args.model)
            threading.Thread(target=giga_server.serve_forever, daemon=True).start()
            giga_port = giga_server.server_address[1]
            print(f"мост gigachat_proxy: http://127.0.0.1:{giga_port}/v1 -> api.giga.chat "
                  "(tools->functions, OAuth-рефреш каждые 25 мин)")
        pi_home, proxy, extra_env = build_pi_home(
            tmp, args.model, args.context, args.max_tokens,
            args.reasoning, args.provider,
            proxy_port=giga_port if giga_server else 0)
        if args.provider in ("openrouter", "orcarouter"):
            # контекст и бюджет выхода — нативные значения (каталог pi /
            # запись раннера), переопределения не применяются
            meta["context"] = None
            meta["max_tokens"] = None
            meta["params"] = f"нативные параметры (провайдер {args.provider})"
        else:
            meta["max_tokens"] = args.max_tokens
        meta["thinking_scale"] = model_thinking_values(args.model) if args.provider == "ollama" else []

        if args.smoke:
            repo.mkdir()
            prompt = SMOKE_PROMPT
        else:
            shutil.copytree(BILLING / "skeleton", repo,
                            ignore=shutil.ignore_patterns("var", ".env"))
            (repo / ".env").write_text(
                (BILLING / "skeleton" / ".env.example").read_text(encoding="utf-8"),
                encoding="utf-8",
            )
            prompt = TASK_PROMPT

        # id вида vendor/model есть у нескольких провайдеров (каталог pi,
        # vercel-ai-gateway) — резолвим с явным префиксом провайдера
        model_ref = (f"{args.provider}/{args.model}"
                     if args.provider in ("openrouter", "orcarouter")
                     else args.model)
        cmd = [
            "pi", "-p",
            "--session-dir", str(results / "pi-session"),
            "--model", model_ref,
            "--thinking", args.thinking,
            prompt,
        ]
        env = {
            **dict(os.environ),
            "PI_CODING_AGENT_DIR": str(pi_home),
            "PI_OFFLINE": "1",
            **extra_env,
        }
        if proxy:
            # трафик pi к OpenRouter — только через прокси (WAF режет прямые
            # запросы из домашней сети)
            env["HTTPS_PROXY"] = env["https_proxy"] = proxy
            env["HTTP_PROXY"] = env["http_proxy"] = proxy
        ctx_note = ("нативные параметры" if args.provider in ("openrouter", "orcarouter")
                    else f"ctx {args.context}")
        print(f"запуск: pi -p --model {model_ref} --thinking {args.thinking} ({ctx_note})")
        print(f"  cwd={repo}\n  pi-home={pi_home} (изолирован)\n  лог (live): {results / 'pi-run.log'}")

        run_holder: dict[str, PiRun] = {}

        def on_signal(signum: int, _frame: object) -> None:
            if (r := run_holder.get("run")) is not None:
                r.terminate(reason=f"signal {signum}")

        signal.signal(signal.SIGTERM, on_signal)
        signal.signal(signal.SIGINT, on_signal)

        # наблюдатель параллельной нагрузки: чужие модели в /api/ps во время
        # прогона = конкуренция за VRAM; факт фиксируется в run.json, такие
        # прогоны исключаются из метрик сводного отчёта
        if args.provider == "ollama":
            threading.Thread(target=watch_ollama_foreign,
                             args=(stop_watch, args.model, foreign_models),
                             daemon=True).start()

        run = PiRun(cmd, repo, env, results / "pi-run.log")
        run_holder["run"] = run
        rc = run.wait(timeout=args.timeout)
        elapsed = time.strftime("%H:%M:%S", time.gmtime(time.time() - started))
        print(f"pi завершился: код={rc}, убит={'да' if run.killed else 'нет'}, время={elapsed}")
        meta.update({
            "exit_code": rc,
            "killed": run.killed,
        })

        if not args.smoke:
            # errors="replace": модели иногда пишут файлы в не-UTF-8
            # (ornith: git-объекты .git/ в диффе уронили decode);
            # .git исключаем — дифф про код решения, не про VCS-внутренности;
            # vendor — если агент сделал composer install, вендорское дерево
            # раздувает дифф до десятков тысяч строк мусора
            diff = subprocess.run(
                ["diff", "-ruN",
                 "-x", "var", "-x", ".env", "-x", "__pycache__", "-x", ".git",
                 "-x", "vendor",
                 str(BILLING / "skeleton"), str(repo)],
                capture_output=True, text=True,
                encoding="utf-8", errors="replace",
            )
            (results / "diff.patch").write_text(diff.stdout, encoding="utf-8")
            meta["diff_lines"] = len(diff.stdout.splitlines())

            solution = results / "solution"
            shutil.copytree(
                repo, solution,
                ignore=shutil.ignore_patterns("vendor", "var", ".env", "__pycache__",
                                              ".phpunit.result.cache", ".git"),
                dirs_exist_ok=True,
            )
            meta["solution_files"] = sum(1 for p in solution.rglob("*") if p.is_file())
            print(f"diff.patch: {meta['diff_lines']} строк; solution/: {meta['solution_files']} файлов")

        session_files = sorted((results / "pi-session").glob("*.jsonl")) if (results / "pi-session").is_dir() else []
        # перезапуск в тот же run-каталог копит файлы сессий: своя сессия — последняя
        meta["session_file"] = session_files[-1].name if session_files else None
        print(f"сессия: {len(session_files)} файл(ов) JSONL"
              + (f", последний: {session_files[-1].name}" if session_files else " — НЕ СОЗДАНА, проверить!"))
        return rc
    finally:
        stop_watch.set()
        if giga_server is not None:
            giga_server.shutdown()
            giga_server.server_close()
        if foreign_models:
            meta["ollama_contention"] = True
            meta["ollama_foreign_models"] = sorted(foreign_models)
        meta["finished_at"] = datetime.now(timezone.utc).isoformat()
        meta["duration_s"] = round(time.time() - started, 1)
        if run is not None:
            meta.setdefault("exit_code", run.proc.returncode)
            meta["killed"] = run.killed
        (results / "run.json").write_text(
            json.dumps(meta, ensure_ascii=False, indent=2) + "\n", encoding="utf-8",
        )
        if args.keep:
            print(f"(keep) временные каталоги: {tmp}")
        else:
            shutil.rmtree(tmp, ignore_errors=True)


if __name__ == "__main__":
    sys.exit(main())

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
        --results models/qwen3.8-27b-pi-100k/test-assignment/backend/run-01

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
    return BILLING.parents[3]

DEFAULT_CONTEXT = 102_400
DEFAULT_TIMEOUT = 5400
DEFAULT_THINKING = "max"

# уровни pi -> значения reasoning_effort ollama (передаётся как есть).
# ВАЖНО про max/xhigh (проверено 2026-10-16 на huihui-ai:pi-100k, мост-задача):
# xhigh расширяет бюджет размышлений кратно (нативно ~110k символов против
# ~5k у high) и при maxTokens 8192 сжигает весь completion-бюджет на thinking
# -> finish=length, content пустой. Поэтому max -> high (рабочий максимум
# агентного прогона), xhigh оставлен passthrough для экспериментов вместе
# с --max-tokens 32768+.
THINKING_LEVEL_MAP = {
    "off": "none",
    "minimal": "low",
    "low": "low",
    "medium": "medium",
    "high": "high",
    "xhigh": "xhigh",
    "max": "high",
}

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


def build_pi_home(tmp: Path, model: str, context: int, max_tokens: int, reasoning: bool) -> Path:
    pi_home = tmp / "pi-home"
    pi_home.mkdir()
    entry: dict = {"id": model, "contextWindow": context, "maxTokens": max_tokens}
    if reasoning:
        entry["reasoning"] = True
        entry["thinkingLevelMap"] = THINKING_LEVEL_MAP
    (pi_home / "models.json").write_text(json.dumps(
        {
            "providers": {
                "ollama": {
                    "baseUrl": f"{ollama_base_url()}/v1",
                    "api": "openai-completions",
                    "apiKey": "ollama",
                    "models": [entry],
                }
            }
        },
        ensure_ascii=False, indent=2,
    ), encoding="utf-8")
    return pi_home


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
    parser.add_argument("--model", required=True, help="тег модели на сервере (ollama)")
    parser.add_argument("--results", required=True, help="каталог результатов run-XX")
    parser.add_argument("--thinking", default=DEFAULT_THINKING,
                        choices=list(THINKING_LEVEL_MAP), help="уровень размышлений pi")
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
        "provider": "ollama",
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
    try:
        pi_home = build_pi_home(tmp, args.model, args.context, args.max_tokens, args.reasoning)
        meta["max_tokens"] = args.max_tokens

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

        cmd = [
            "pi", "-p",
            "--session-dir", str(results / "pi-session"),
            "--model", args.model,
            "--thinking", args.thinking,
            prompt,
        ]
        env = {
            **dict(os.environ),
            "PI_CODING_AGENT_DIR": str(pi_home),
            "PI_OFFLINE": "1",
        }
        print(f"запуск: pi -p --model {args.model} --thinking {args.thinking} (ctx {args.context})")
        print(f"  cwd={repo}\n  pi-home={pi_home} (изолирован)\n  лог (live): {results / 'pi-run.log'}")

        run_holder: dict[str, PiRun] = {}

        def on_signal(signum: int, _frame: object) -> None:
            if (r := run_holder.get("run")) is not None:
                r.terminate(reason=f"signal {signum}")

        signal.signal(signal.SIGTERM, on_signal)
        signal.signal(signal.SIGINT, on_signal)

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
            diff = subprocess.run(
                ["diff", "-ruN",
                 "-x", "var", "-x", ".env", "-x", "__pycache__",
                 str(BILLING / "skeleton"), str(repo)],
                capture_output=True, text=True,
            )
            (results / "diff.patch").write_text(diff.stdout, encoding="utf-8")
            meta["diff_lines"] = len(diff.stdout.splitlines())

            solution = results / "solution"
            shutil.copytree(
                repo, solution,
                ignore=shutil.ignore_patterns("vendor", "var", ".env", "__pycache__",
                                              ".phpunit.result.cache"),
                dirs_exist_ok=True,
            )
            meta["solution_files"] = sum(1 for p in solution.rglob("*") if p.is_file())
            print(f"diff.patch: {meta['diff_lines']} строк; solution/: {meta['solution_files']} файлов")

        session_files = sorted((results / "pi-session").glob("*.jsonl")) if (results / "pi-session").is_dir() else []
        meta["session_file"] = session_files[0].name if session_files else None
        print(f"сессия: {len(session_files)} файл(ов) JSONL"
              + (f", первый: {session_files[0].name}" if session_files else " — НЕ СОЗДАНА, проверить!"))
        return rc
    finally:
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

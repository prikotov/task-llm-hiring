#!/usr/bin/env python3
"""Изолированный прогон тестового задания через pi (этап 2, пилот/серия).

Глобальный конфиг pi (~/.pi/agent) НЕ используется: каждый прогон получает
собственный PI_CODING_AGENT_DIR во временном каталоге, а models.json для
единственной модели собирается на лету. Адрес сервера моделей берётся из
env OLLAMA_BASE_URL (или из .env.local корня репозитория) — в git адрес
не попадает.

Использование (из каталога billing/):

    python3 runner_pi.py --model qwen3.8:27b-pi-100k \
        --results models/qwen3.8-27b-pi-100k/test-assignment/run-01

Опции: --thinking (default max), --context (default 102400),
--timeout секунд (default 5400), --reasoning/--no-reasoning (умеет ли
модель thinking через API), --smoke (быстрая проверка сантехники на
пустом каталоге), --keep (не удалять временные каталоги).

После прогона в <results>: pi-session/ (JSONL — полный след размышлений
и действий), pi-run.log, diff.patch. Чекер и метрики — отдельно:
checker/check.py, checker/metrics.py.
"""

from __future__ import annotations

import argparse
import os
import shutil
import subprocess
import sys
import tempfile
import time
from pathlib import Path

HERE = Path(__file__).resolve().parent
BILLING = HERE
MODELS_REPO = BILLING.parents[2]

DEFAULT_CONTEXT = 102_400
DEFAULT_TIMEOUT = 5400
DEFAULT_THINKING = "max"

# уровни pi -> значения reasoning_effort, принимаемые ollama (OpenAI-совм.)
THINKING_LEVEL_MAP = {
    "off": "none",
    "minimal": "low",
    "low": "low",
    "medium": "medium",
    "high": "high",
    "xhigh": "high",
    "max": "high",
}

TASK_PROMPT = "Прочитай TASK.md в корне репозитория и выполни задание."
SMOKE_PROMPT = "Создай в текущем каталоге файл answer.txt с текстом 'ok' (без кавычек) и заверши работу."


def ollama_base_url() -> str:
    """Адрес сервера моделей: env, затем .env.local корня репозитория."""
    url = os.environ.get("OLLAMA_BASE_URL")
    if not url:
        env_local = MODELS_REPO / ".env.local"
        if env_local.is_file():
            for line in env_local.read_text().splitlines():
                if line.startswith("OLLAMA_BASE_URL="):
                    url = line.split("=", 1)[1].strip()
                    break
    if not url:
        sys.exit("OLLAMA_BASE_URL не задан (env или .env.local)")
    return url.rstrip("/")


def build_pi_home(tmp: Path, model: str, context: int, reasoning: bool) -> Path:
    pi_home = tmp / "pi-home"
    pi_home.mkdir()
    entry: dict = {"id": model, "contextWindow": context, "maxTokens": 8192}
    if reasoning:
        entry["reasoning"] = True
        entry["thinkingLevelMap"] = THINKING_LEVEL_MAP
    (pi_home / "models.json").write_text(
        json_dumps(
            {
                "providers": {
                    "ollama": {
                        "baseUrl": f"{ollama_base_url()}/v1",
                        "api": "openai-completions",
                        "apiKey": "ollama",
                        "models": [entry],
                    }
                }
            }
        ),
        encoding="utf-8",
    )
    return pi_home


def json_dumps(data: dict) -> str:
    import json

    return json.dumps(data, ensure_ascii=False, indent=2)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--model", required=True, help="тег модели на сервере (ollama)")
    parser.add_argument("--results", required=True, help="каталог результатов run-XX")
    parser.add_argument("--thinking", default=DEFAULT_THINKING,
                        choices=list(THINKING_LEVEL_MAP), help="уровень размышлений pi")
    parser.add_argument("--context", type=int, default=DEFAULT_CONTEXT,
                        help="контекстное окно, токены (default 102400)")
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
    try:
        pi_home = build_pi_home(tmp, args.model, args.context, args.reasoning)

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
        print(f"запуск: {' '.join(cmd[:8])}…\n  cwd={repo}\n  pi-home={pi_home} (изолирован)")
        log_path = results / "pi-run.log"
        with log_path.open("w", encoding="utf-8") as log:
            proc = subprocess.run(
                ["timeout", str(args.timeout), *cmd],
                cwd=repo, env=env, stdout=subprocess.PIPE, stderr=subprocess.STDOUT,
                text=True, timeout=args.timeout + 120,
            )
            log.write(proc.stdout or "")
            log.flush()
        elapsed = time.strftime("%H:%M:%S", time.gmtime(time.time() - started))
        print(f"pi завершился: код={proc.returncode}, время={elapsed}")

        if not args.smoke:
            diff = subprocess.run(
                ["diff", "-ru",
                 "-x", "var", "-x", ".env", "-x", "__pycache__",
                 str(BILLING / "skeleton"), str(repo)],
                capture_output=True, text=True,
            )
            (results / "diff.patch").write_text(diff.stdout, encoding="utf-8")
            print(f"diff.patch: {len(diff.stdout.splitlines())} строк")
            # точное дерево решения для исследования без восстановления из диффа
            tar = subprocess.run(
                ["tar", "czf", str(results / "solution.tar.gz"),
                 "--exclude=./vendor", "--exclude=./var", "--exclude=./.env",
                 "-C", str(repo), "."],
                capture_output=True, text=True,
            )
            if tar.returncode == 0:
                size = (results / "solution.tar.gz").stat().st_size // 1024
                print(f"solution.tar.gz: {size} KiB")

        session_files = sorted((results / "pi-session").glob("*.jsonl")) if (results / "pi-session").is_dir() else []
        print(f"сессия: {len(session_files)} файл(ов) JSONL"
              + (f", первый: {session_files[0].name}" if session_files else " — НЕ СОЗДАНА, проверить!"))
        return proc.returncode
    finally:
        if args.keep:
            print(f"(keep) временные каталоги: {tmp}")
        else:
            shutil.rmtree(tmp, ignore_errors=True)


if __name__ == "__main__":
    sys.exit(main())

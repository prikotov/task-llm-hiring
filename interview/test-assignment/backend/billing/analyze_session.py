#!/usr/bin/env python3
"""Анализатор сессий pi — поведенческий профиль прогона (этап 2).

Читает pi-session/*.jsonl (формат pi: события session/model_change/
thinking_level_change/message) и строит профиль работы модели:
объёмы (сообщения, tool-calls по инструментам, thinking), таймлайн
активности по интервалам, файлы (создание/правки через инструменты),
bash-команды по категориям (тесты, консоль, composer, docker…),
повторы команд (детектор циклов), итерации тестов (запуск → результат),
финальный ответ и приблизительный объём транскрипта.

Использование:

    python3 analyze_session.py models/<модель>/test-assignment/backend/run-01/pi-session/
    python3 analyze_session.py <файл.jsonl> --json
"""

from __future__ import annotations

import argparse
import glob
import json
import sys
from collections import Counter
from datetime import datetime
from pathlib import Path

BUCKET_SEC = 300  # интервал таймлайна, 5 минут

BASH_KINDS = [
    ("tests", lambda c: "phpunit" in c or "vendor/bin/phpunit" in c),
    ("console", lambda c: "bin/console" in c),
    ("composer", lambda c: c.startswith("composer") or " composer " in c),
    ("docker", lambda c: "docker" in c or "podman" in c),
    ("make", lambda c: c.startswith("make ") or c == "make"),
    ("php", lambda c: c.startswith("php ") and "phpunit" not in c),
    ("git", lambda c: c.startswith("git ")),
    ("read/ls/grep", lambda c: any(c.startswith(p) for p in ("ls", "cat", "head", "tail", "grep", "find", "sed -n", "wc"))),
]


def parse_ts(value: str) -> datetime:
    return datetime.fromisoformat(value.replace("Z", "+00:00"))


def load(session_arg: str) -> tuple[list[dict], dict]:
    p = Path(session_arg)
    files = sorted(glob.glob(str(p / "*.jsonl"))) if p.is_dir() else [str(p)]
    if not files or not Path(files[0]).is_file():
        sys.exit(f"сессий не найдено: {session_arg}")
    events, header = [], {}
    for f in files:
        for line in open(f, encoding="utf-8"):
            e = json.loads(line)
            events.append(e)
            if e.get("type") == "session":
                header.update({"cwd": e.get("cwd"), "session_id": e.get("id")})
            elif e.get("type") == "model_change":
                header.update({"provider": e.get("provider"), "model": e.get("modelId")})
            elif e.get("type") == "thinking_level_change":
                header["thinking"] = e.get("thinkingLevel")
    return events, header


def analyze(events: list[dict]) -> dict:
    msgs = [e for e in events if e.get("type") == "message"]
    tool_calls: list[tuple[datetime, str, dict]] = []
    thinking_chars = text_chars = result_chars = 0
    thinking_blocks = 0
    roles: Counter[str] = Counter()
    assistant_texts: list[str] = []

    for e in msgs:
        m = e["message"]
        role = m.get("role", "?")
        roles[role] += 1
        ts = parse_ts(e["timestamp"]) if e.get("timestamp") else None
        for c in (m.get("content") or []):
            if not isinstance(c, dict):
                continue
            t = c.get("type")
            if t == "thinking":
                thinking_blocks += 1
                thinking_chars += len(c.get("thinking") or "")
            elif t == "text":
                if role == "assistant":
                    text_chars += len(c.get("text") or "")
                    assistant_texts.append(c.get("text") or "")
                elif role == "toolResult":
                    result_chars += len(c.get("text") or "")
            elif t == "toolCall":
                name = c.get("name") or "?"
                args = c.get("arguments") or {}
                tool_calls.append((ts, name, args))

    # итерации тестов: за запуском phpunit ищем ближайший toolResult
    test_runs: list[dict] = []
    last_test_cmd: dict | None = None
    for e in msgs:
        m = e["message"]
        if m.get("role") == "assistant":
            for c in (m.get("content") or []):
                if isinstance(c, dict) and c.get("type") == "toolCall" and c.get("name") == "bash":
                    cmd = str((c.get("arguments") or {}).get("command")
                              or (c.get("arguments") or {}).get("content") or "")
                    if "phpunit" in cmd:
                        last_test_cmd = {"cmd": cmd, "ts": e.get("timestamp"), "result": None}
                        test_runs.append(last_test_cmd)
        elif m.get("role") == "toolResult" and last_test_cmd is not None and last_test_cmd["result"] is None:
            out = "".join(
                c.get("text") or "" for c in (m.get("content") or []) if isinstance(c, dict)
            )
            import re

            if ("OK (" in out
                    or ("Tests:" in out and "Failures: 0" in out and "Errors: 0" in out)):
                last_test_cmd["result"] = "pass"
            elif re.search(r"Failures: [1-9]|Errors: [1-9]|FAILURES|ERRORS", out) or "FAIL" in out:
                last_test_cmd["result"] = "fail"
            else:
                last_test_cmd["result"] = "?"

    # таймлайн и файлы
    first_ts = next((parse_ts(e["timestamp"]) for e in events if e.get("timestamp")), None)
    last_ts = next((parse_ts(e["timestamp"]) for e in reversed(events) if e.get("timestamp")), None)
    duration_s = (last_ts - first_ts).total_seconds() if first_ts and last_ts else 0

    timeline: Counter[int] = Counter()
    files_touched: list[str] = []
    bash_cmds: list[str] = []
    for ts, name, args in tool_calls:
        if ts and first_ts:
            bucket = int((ts - first_ts).total_seconds() // BUCKET_SEC)
            timeline[bucket] += 1
        if name in ("write", "edit"):
            path = args.get("path") or args.get("file") or args.get("filePath") or args.get("target_file") or ""
            if path:
                files_touched.append(str(path))
        elif name == "bash":
            bash_cmds.append(str(args.get("command") or args.get("content") or ""))

    kinds: Counter[str] = Counter()
    for cmd in bash_cmds:
        for kind, match in BASH_KINDS:
            if match(cmd):
                kinds[kind] += 1
                break
        else:
            kinds["прочее"] += 1

    top_repeat = Counter(bash_cmds).most_common(5)

    return {
        "messages_total": len(msgs),
        "roles": dict(roles),
        "tool_calls_total": len(tool_calls),
        "tool_calls_by_name": dict(Counter(name for _, name, _ in tool_calls)),
        "thinking_blocks": thinking_blocks,
        "thinking_chars": thinking_chars,
        "assistant_text_chars": text_chars,
        "tool_result_chars": result_chars,
        "transcript_chars_approx": thinking_chars + text_chars + result_chars,
        "duration_s": round(duration_s),
        "timeline_5m": {f"{k * BUCKET_SEC // 60:02d}-{(k + 1) * BUCKET_SEC // 60:02d}мин": v
                        for k, v in sorted(timeline.items())},
        "files_touched": len(files_touched),
        "files_distinct": len(set(files_touched)),
        "files_examples": list(dict.fromkeys(files_touched))[:10],
        "bash_total": len(bash_cmds),
        "bash_by_kind": dict(kinds),
        "bash_top_repeat": top_repeat,
        "test_runs": len(test_runs),
        "test_results": dict(Counter(t["result"] or "?" for t in test_runs)),
        "final_answer": (assistant_texts[-1][:400] if assistant_texts else None),
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("session", help="каталог pi-session/ или файл .jsonl")
    parser.add_argument("--json", action="store_true", help="только JSON")
    args = parser.parse_args()

    events, header = load(args.session)
    stats = analyze(events)
    if args.json:
        print(json.dumps({"header": header, **stats}, ensure_ascii=False, indent=2))
        return 0

    def dm(mk: str) -> str:
        return f"{int(mk) // 60} мин {int(mk) % 60:02d} с"

    print(f"модель: {header.get('provider')}/{header.get('model')}  thinking={header.get('thinking')}")
    print(f"длительность: {dm(stats['duration_s'])};  сообщений: {stats['messages_total']} "
          f"(roles: {stats['roles']})")
    print(f"tool-calls: {stats['tool_calls_total']}  по инструментам: {stats['tool_calls_by_name']}")
    print(f"thinking: {stats['thinking_blocks']} блоков, {stats['thinking_chars']:,} символов; "
          f"текст ассистента: {stats['assistant_text_chars']:,}; результаты инструментов: "
          f"{stats['tool_result_chars']:,} (~транскрипт {stats['transcript_chars_approx']:,} симв.)")
    print(f"файлы: {stats['files_touched']} касаний, {stats['files_distinct']} уникальных; примеры:")
    for f in stats["files_examples"]:
        print(f"    {f}")
    print(f"bash: {stats['bash_total']} по категориям: {stats['bash_by_kind']}")
    if stats["bash_top_repeat"]:
        print("повторы команд (топ):")
        for cmd, n in stats["bash_top_repeat"]:
            print(f"    x{n}: {cmd[:110]}")
    print(f"запуски тестов: {stats['test_runs']}, исходы: {stats['test_results']}")
    print("таймлайн активности (tool-calls по 5-мин интервалам):")
    for k, v in stats["timeline_5m"].items():
        print(f"    {k}: {'#' * min(v, 60)} {v}")
    if stats["final_answer"]:
        print(f"финальный текст: {stats['final_answer'][:200]!r}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

#!/usr/bin/env python3
"""Общий каркас изолированных судей (ярусы 3/4): запуск pi с сессией,
замер времени и токенов, sidecar-JSON у прогона."""

from __future__ import annotations

import json
import subprocess
import time
from pathlib import Path

PI = "pi"


def run_judge(model: str, thinking: str, system: str, prompt: str,
              timeout: int, tools: str | None = None, workdir: Path | None = None):
    """Один изолированный вызов pi; возвращает (reply, метрики)."""
    import tempfile
    with tempfile.TemporaryDirectory(prefix="judge-") as td:
        pf = Path(td) / "prompt.txt"
        pf.write_text(prompt, encoding="utf-8")
        sess = Path(td) / "sess"
        cmd = [PI, "-p", "--model", model, "--thinking", thinking,
               "--session-dir", str(sess),
               "--system-prompt", system, "--mode", "text", f"@{pf}"]
        if tools:
            cmd += ["--tools", tools]
        t0 = time.monotonic()
        out = subprocess.run(cmd, capture_output=True, text=True,
                             timeout=timeout, cwd=workdir)
        duration = round(time.monotonic() - t0, 1)
        usage = {"input": 0, "output": 0, "totalTokens": 0, "cost": 0.0}
        for f in sorted(sess.glob("*.jsonl")) if sess.is_dir() else []:
            for line in f.read_text(encoding="utf-8").splitlines():
                try:
                    d = json.loads(line)
                except json.JSONDecodeError:
                    continue
                m = d.get("message") or {}
                u = m.get("usage") if m.get("role") == "assistant" else None
                if isinstance(u, dict):
                    usage["input"] += int(u.get("input") or 0)
                    usage["output"] += int(u.get("output") or 0)
                    usage["totalTokens"] += int(u.get("totalTokens") or 0)
                    usage["cost"] += float((u.get("cost") or {}).get("total") or 0)
        return out, {"duration_s": duration,
                     "tokens_in": usage["input"], "tokens_out": usage["output"],
                     "tokens_total": usage["totalTokens"], "cost_usd": round(usage["cost"], 6)}


def save_sidecar(run_dir: Path, name: str, payload: dict) -> None:
    (run_dir / name).write_text(
        json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

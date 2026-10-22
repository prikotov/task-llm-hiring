#!/usr/bin/env python3
"""Полная сетка прогонов тестового задания: модели × N прогонов, все ярусы.

Конвейер одного прогона: runner_pi (агент) -> чекер -> метрики (+сырьё)
-> профиль сессии -> summary -> ревью яруса 3 -> судья яруса 4 ->
визуальный отчёт. Время и токены каждого яруса пишутся в
reports/test-assignment-tier-timing.csv (для анализа оптимизаций:
где дорого, что можно унести на локальные модели вроде clef).

Запуск (последовательно, один GPU):
    python3 test-assignment/backend/billing/run_grid.py [--runs 3] [--models все|список,через,запятую]
"""
from __future__ import annotations

import argparse
import csv
import json
import subprocess
import sys
import time
from datetime import datetime, timezone
from pathlib import Path

BILLING = Path(__file__).resolve().parent
ROOT = BILLING.parents[2]
MODELS: list[tuple[str, str]] = [
    ("models/qwen3.8-27b-pi-100k/backend/test-assignment", "qwen3.8:27b-pi-100k"),
    ("models/huihui-ai-pi-100k/backend/test-assignment", "huihui-ai:pi-100k"),
    ("models/gemma4-31b-it-qat-pi-100k/backend/test-assignment", "gemma4:31b-it-qat-pi-100k"),
    # ctx100k — серверные алиасы тех же весов с num_ctx 102400
    # (см. model.md карточек: «использовать только ctx100k»)
    ("models/orcarouter-Qwen3.8-27B-Uncensored-latest/backend/test-assignment",
     "orcarouter/Qwen3.8-27B-Uncensored:ctx100k"),
    ("models/ornith-1.5-35b/backend/test-assignment", "ornith-1.5:35b-ctx100k"),
    ("models/muse-glimmer/backend/test-assignment", "muse-glimmer:ctx100k"),
    # квант-эксперимент: одни веса, разные квантования, по одному прогону
    ("models/orcarouter-Qwen3.8-27B-Uncensored-iq4_xs/backend/test-assignment",
     "orcarouter/Qwen3.8-27B-Uncensored:iq4_xs-ctx100k"),
    # qwen3.8-unc* был ретагом весов orca (сверка блобов 2026-10-21):
    # теги удалены с сервера, серии влиты в папки orcarouter-*
    ("models/gemma4-26b-pi-100k/backend/test-assignment", "gemma4:26b-pi-100k"),
    # облачные: GigaChat — родной API Сбера через мост gigachat_proxy
    # (tools->functions, OAuth-рефреш; физлица, freemium)
    ("models/gigachat-3-ultra/backend/test-assignment", "GigaChat-3-Ultra", "gigachat"),
    ("models/gigachat/backend/test-assignment", "GigaChat-2-Max", "gigachat"),
]
CSV_PATH = ROOT / "reports" / "test-assignment-tier-timing.csv"


def log(msg: str) -> None:
    print(f"[{datetime.now(timezone.utc).strftime('%H:%M:%S')}] {msg}", flush=True)


def sh(cmd: list[str], timeout: int, stdout_path: Path | None = None) -> tuple[int, float]:
    t0 = time.monotonic()
    with (stdout_path.open("w") if stdout_path else open("/dev/null", "w")) as out:
        p = subprocess.run(cmd, stdout=out if stdout_path else subprocess.PIPE,
                           stderr=None, timeout=timeout)
    return p.returncode, round(time.monotonic() - t0, 1)


def agent_usage(sess_dir: Path) -> dict:
    u = {"tokens_in": 0, "tokens_out": 0, "tokens_total": 0}
    for f in sorted(sess_dir.glob("*.jsonl")) if sess_dir.is_dir() else []:
        for line in f.read_text(encoding="utf-8").splitlines():
            try:
                d = json.loads(line)
            except json.JSONDecodeError:
                continue
            m = d.get("message") or {}
            us = m.get("usage") if m.get("role") == "assistant" else None
            if isinstance(us, dict):
                u["tokens_in"] += int(us.get("input") or 0)
                u["tokens_out"] += int(us.get("output") or 0)
                u["tokens_total"] += int(us.get("totalTokens") or 0)
    return u


def csv_row(model: str, run: str, tier: str, dur: float, usage: dict) -> None:
    new = not CSV_PATH.exists()
    with CSV_PATH.open("a", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        if new:
            w.writerow(["model", "run", "tier", "duration_s",
                        "tokens_in", "tokens_out", "tokens_total", "cost_usd"])
        w.writerow([model, run, tier, dur, usage.get("tokens_in", ""),
                    usage.get("tokens_out", ""), usage.get("tokens_total", ""),
                    usage.get("cost_usd", "")])


def sidecar(run_dir: Path, name: str) -> dict:
    p = run_dir / name
    return json.loads(p.read_text()) if p.is_file() else {}


def post_process(rd: Path, model: str, run_no: int) -> None:
    """Пост-обработка готового прогона: ярусы 1–4 + карточки."""
    for tier, cmd, out, timeout in (
        ("check", [sys.executable, str(BILLING / "checker" / "check.py"), "--repo", str(rd / "solution")],
         rd / "check.json", 1500),
        ("metrics", [sys.executable, str(BILLING / "checker" / "metrics.py"),
                     "--repo", str(rd / "solution"), "--raw-dir", str(rd / "metrics-raw")],
         rd / "metrics.json", 1800),
        ("profile", [sys.executable, str(BILLING / "analyze_session.py"), str(rd / "pi-session")],
         rd / "session-profile.md", 300),
    ):
        rc, dur = sh(cmd, timeout=timeout, stdout_path=out)
        csv_row(model, f"run-{run_no:02d}", tier, dur, {})
        log(f"{tier}: код={rc}, {dur:.0f}с")

    rc, dur = sh([sys.executable, str(BILLING / "summarize.py"), str(rd)], timeout=120)
    log(f"summary: код={rc}")

    for tier, script, timeout in (("tier3", "judge_tier3.py", 1500), ("tier4", "judge_tier2.py", 1500),
                                  ("audit", "judge_audit.py", 900)):
        rc, dur = sh([sys.executable, str(BILLING / "checker" / script),
                      "--run", str(rd)], timeout=timeout)
        met = sidecar(rd, "audit.json" if tier == "audit" else f"{tier}.json")
        csv_row(model, f"run-{run_no:02d}", tier, met.get("duration_s", dur), met)
        log(f"{tier}: код={rc}, {met.get('duration_s', dur)}с, {met.get('tokens_total', 0):,} ток.")

    rc, dur = sh([sys.executable, str(BILLING / "build_run_report.py"), str(rd)], timeout=120)
    log(f"report.html: код={rc}")


def pipeline(base: Path, tag: str, run_no: int, provider: str = "ollama") -> None:
    model = base.parent.parent.name
    rd = base / f"run-{run_no:02d}"
    if (rd / "report.html").is_file():
        log(f"=== {model} run-{run_no:02d}: уже собран (report.html) — пропуск ===")
        return
    log(f"=== {model} run-{run_no:02d} ({tag}, {provider}) ===")

    rc, dur = sh([sys.executable, str(BILLING / "runner_pi.py"),
                  "--model", tag, "--results", str(rd), "--thinking", "max",
                  "--provider", provider],
                 timeout=5700)
    usage = agent_usage(rd / "pi-session") if (rd / "pi-session").is_dir() else {}
    csv_row(model, f"run-{run_no:02d}", "agent", dur, usage)
    log(f"агент: код={rc}, {dur:.0f}с, {usage.get('tokens_total', 0):,} ток.")
    if rc != 0 and not (rd / "solution" / "composer.json").is_file():
        log("агент не дал решения — прогон пропущен")
        return
    post_process(rd, model, run_no)


def main() -> int:
    ap = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    ap.add_argument("--runs", type=int, default=3)
    ap.add_argument("--models", default="",
                    help="фильтр: имена папок через запятую (по умолчанию все)")
    ap.add_argument("--post", default="",
                    help="только пост-обработка готового прогона (путь к run-XX)")
    args = ap.parse_args()
    if args.post:
        rd = Path(args.post).resolve()
        post_process(rd, rd.parent.parent.parent.name, int(rd.name.removeprefix("run-")))
        return 0
    chosen = [m for m in MODELS
              if not args.models or m[0].split("/")[1] in args.models.split(",")]
    log(f"сетка: {len(chosen)} моделей × {args.runs} прогонов; CSV: {CSV_PATH.name}")
    for m in chosen:
        base, tag = m[0], m[1]
        provider = m[2] if len(m) > 2 else "ollama"
        for n in range(1, args.runs + 1):
            try:
                pipeline(ROOT / base, tag, n, provider)
            except Exception as exc:  # noqa: BLE001
                log(f"ОШИБКА прогона: {exc}")
    log("=== СЕТКА ЗАВЕРШЕНА ===")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

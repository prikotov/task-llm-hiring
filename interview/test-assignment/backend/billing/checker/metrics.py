#!/usr/bin/env python3
"""Ярус 0.5 — детерминированные метрики качества кода кандидата.

Работает на КОПИИ репозитория кандидата (не трогает оригинал): ставит
стат-анализаторы через composer (нужна сеть — прогоняется на стороне
судьи, не кандидата), копирует конфиги из ~/MyProjects/coding-standard
(если каталог есть) и снимает:

- phpstan level 8 — количество ошибок по src/;
- phpmd — нарушения (ruleset из coding-standard/examples, иначе дефолт);
- phpcs PSR12 — количество нарушений;
- scc — размер кодовой базы (файлы/строки по языкам);
- AST-метрики — LOC/CC/WMC/LCOM4/Ca-Ce и findings по порогам coding-standard
  (через универсальный `metrics-collect --source=src`).

Выход: JSON в stdout. Любой инструмент может быть пропущен (status: skip)
— остальные продолжают считаться.

Запуск: python3 checker/metrics.py --repo /path/to/candidate
"""

from __future__ import annotations

import argparse
import json
import re
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

CODING_STANDARD = Path.home() / "MyProjects" / "coding-standard"
TOOLS = ["phpstan/phpstan", "squizlabs/php_codesniffer", "phpmd/phpmd"]

CMD_TIMEOUT = 600


def run(cmd: list[str], cwd: Path, timeout: int = CMD_TIMEOUT) -> subprocess.CompletedProcess:
    return subprocess.run(cmd, cwd=cwd, capture_output=True, text=True, timeout=timeout)


def install_tools(work: Path) -> bool:
    c = run(["composer", "require", "--dev", *TOOLS, "--no-interaction", "--no-progress"], work)
    return c.returncode == 0


def phpstan(work: Path) -> dict:
    (work / "phpstan.neon").write_text(
        "parameters:\n"
        "    level: 8\n"
        "    paths:\n        - src\n"
        "    excludePaths:\n        - src/Kernel.php\n", encoding="utf-8"
    )
    c = run(["vendor/bin/phpstan", "analyse", "--no-progress", "--error-format=raw"], work)
    errors = [ln for ln in c.stdout.splitlines() if ln.strip()]
    return {"status": "ok", "exit": c.returncode, "errors": len(errors),
            "first": errors[:5]}


def phpmd(work: Path) -> dict:
    ruleset = CODING_STANDARD / "docs/conventions/examples/phpmd.xml"
    rules = str(ruleset) if ruleset.is_file() else "cleancode,codesize,design"
    c = run(["vendor/bin/phpmd", "src", "text", rules], work)
    violations = [ln for ln in c.stdout.splitlines() if ln.strip()]
    return {"status": "ok", "rules": rules, "violations": len(violations),
            "first": violations[:5]}


def phpcs(work: Path) -> dict:
    # Kernel.php дан скелетом — не код кандидата, из проверки исключён
    c = run([
        "vendor/bin/phpcs", "--standard=PSR12", "--report=summary",
        "--ignore=Kernel.php", "src",
    ], work)
    errors = warnings = 0
    m = re.search(r"TOTAL OF (\d+) ERRORS? AND (\d+) WARNINGS?", c.stdout)
    if m:
        errors, warnings = int(m.group(1)), int(m.group(2))
    return {"status": "ok", "errors": errors, "warnings": warnings,
            "violations": errors + warnings, "exit": c.returncode}


def scc(work: Path) -> dict:
    if shutil.which("scc") is None:
        return {"status": "skip", "reason": "scc не установлен"}
    c = run(["scc", "src", "--format", "json"], work)
    if c.returncode != 0 or not c.stdout.strip():
        return {"status": "skip", "reason": "scc упал"}
    try:
        langs = json.loads(c.stdout)
    except json.JSONDecodeError:
        return {"status": "skip", "reason": "scc отдал не-JSON"}
    files = sum(int(lang.get("Count", 0)) for lang in langs)
    lines = sum(int(lang.get("Code", 0)) for lang in langs)
    return {"status": "ok", "files": files, "loc": lines}


def ast_metrics(work: Path) -> dict:
    """CC/WMC/LCOM4/Ca-Ce по сырой AST-статистике coding-standard.

    Полный пайплайн coding-standard-metrics завязан на deptrac-конфиг
    с DDD-структурой TasK и на чужих репозиториях не работает, поэтому
    берём универсальный `metrics-collect --source=src` и агрегируем сами
    (пороги — из .coding-standard.php пакета coding-standard).
    """
    if not CODING_STANDARD.is_dir():
        return {"status": "skip", "reason": "нет ~/MyProjects/coding-standard"}
    run([
        "composer", "config", "repositories.coding-standard", "path",
        str(CODING_STANDARD),
    ], work)
    run([
        "composer", "config", "allow-plugins.dealerdirect/phpcodesniffer-composer-installer",
        "true",
    ], work)
    c = run([
        "composer", "require", "--dev", "prikotov/coding-standard:*@dev",
        "--no-interaction", "--no-progress",
    ], work)
    if c.returncode != 0:
        return {"status": "skip", "reason": "composer require не удался: " + c.stderr[-200:]}

    c = run([
        "php", "vendor/bin/metrics-collect", "--source=src",
        "--output=var/metrics/collector.json",
    ], work)
    collector = work / "var" / "metrics" / "collector.json"
    if c.returncode != 0 or not collector.is_file():
        return {"status": "skip", "reason": "metrics-collect не удался: " + (c.stderr or "")[-200:]}
    report = json.loads(collector.read_text(encoding="utf-8"))

    internal = {cl["name"] for cl in report["classes"]}
    methods: dict[str, list[dict]] = {}
    for fn in report["functions"]:
        if fn.get("type") != "method":
            continue
        cls = str(fn["metrics"].get("classInfo", "")).split(", ", 1)[-1]
        methods.setdefault(cls, []).append(fn["metrics"])

    wmc = {name: sum(m.get("cc", 0) for m in ms) for name, ms in methods.items()}
    ca = {name: 0 for name in internal}
    ce = {name: 0 for name in internal}
    for dep in report["dependencies"]:
        src, dst = dep["source"], dep["target"]
        if src in internal and dst in internal and src != dst:
            ce[src] = ce.get(src, 0) + 1
            ca[dst] = ca.get(dst, 0) + 1

    worst_cc = max((m.get("cc", 0) for ms in methods.values() for m in ms), default=0)
    worst_loc = max((m.get("loc", 0) for ms in methods.values() for m in ms), default=0)
    lcom4 = [cl["metrics"].get("lcom", 0) for cl in report["classes"] if not cl["metrics"].get("interface")]
    findings = {
        "method_cc_over_10": sum(1 for ms in methods.values() for m in ms if m.get("cc", 0) > 10),
        "method_loc_over_50": sum(1 for ms in methods.values() for m in ms if m.get("loc", 0) > 50),
        "class_loc_over_300": sum(1 for cl in report["classes"] if cl["metrics"].get("loc", 0) > 300),
        "class_wmc_over_50": sum(1 for name, w in wmc.items() if w > 50),
        "class_lcom4_over_1": sum(1 for v in lcom4 if v > 1),
    }
    return {
        "status": "ok",
        "classes": len(report["classes"]),
        "methods": sum(len(ms) for ms in methods.values()),
        "loc_total": sum(cl["metrics"].get("loc", 0) for cl in report["classes"]),
        "wmc_total": sum(wmc.values()),
        "wmc_worst_class": max(wmc, key=wmc.get) if wmc else None,
        "method_cc_max": worst_cc,
        "method_loc_max": worst_loc,
        "lcom4_over_1": findings["class_lcom4_over_1"],
        "ca_total": sum(ca.values()),
        "ce_total": sum(ce.values()),
        "findings_thresholds": findings,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", required=True, help="каталог кандидата")
    args = parser.parse_args()
    repo = Path(args.repo).resolve()
    if not (repo / "src").is_dir():
        print("нет src/ в репозитории кандидата", file=sys.stderr)
        return 2

    with tempfile.TemporaryDirectory(prefix="billing-metrics-") as td:
        work = Path(td) / "repo"
        shutil.copytree(repo, work, ignore=shutil.ignore_patterns("var", ".git"))
        if not install_tools(work):
            report = {"status": "skip", "reason": "composer install не удался (сеть?)"}
            print(json.dumps(report, ensure_ascii=False, indent=2))
            return 0
        report = {
            "repo": str(repo),
            "phpstan": phpstan(work),
            "phpmd": phpmd(work),
            "phpcs": phpcs(work),
            "scc": scc(work),
            "ast_metrics": ast_metrics(work),
        }
        print(json.dumps(report, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    sys.exit(main())

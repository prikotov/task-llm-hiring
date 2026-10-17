#!/usr/bin/env python3
"""Ярус 0.5 — детерминированные метрики качества кода кандидата.

Работает на КОПИИ репозитория кандидата (оригинал не трогает).

Инструменты кэшируются в ~/.cache/billing-metrics/ в виде самодостаточных
phar-архивов (phpstan / phpcs / phpmd): скачиваются один раз, дальше
прогоны полностью локальны и занимают секунды вместо ~10 минут
composer-установок на каждый прогон. Если phar-ов нет и сеть недоступна —
fallback на composer-установку в одноразовую копию (медленный путь).

AST-метрики (CC/WMC/LCOM4/Ca-Ce) считаются сборщиком coding-standard
(~/MyProjects/coding-standard, запускается напрямую из его чекаута —
composer не нужен, если там собран vendor).

Выход: JSON в stdout (tools_method: phar|composer). Любой инструмент
может быть пропущен (status: skip) — остальные продолжают считаться.

Сырые артефакты (полные выводы phpstan/phpmd/phpcs/scc и отчёт
AST-сборщика collector.json), на которых построены агрегаты,
сохраняются в --raw-dir (обычно <run>/metrics-raw/) и перечисляются
в metrics.json в полях raw каждого инструмента. Агрегаты без
сырья не проверяемы.

Запуск: python3 checker/metrics.py --repo /path/to/candidate \
        --raw-dir <run>/metrics-raw
"""

from __future__ import annotations

import argparse
import json
import re
import shutil
import subprocess
import sys
import tempfile
import urllib.request
from pathlib import Path

CODING_STANDARD = Path.home() / "MyProjects" / "coding-standard"
PHAR_CACHE = Path.home() / ".cache" / "billing-metrics"

# «latest release» редиректы GitHub; скачивание — один раз в кэш
PHAR_URLS = {
    "phpstan.phar": "https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar",
    "phpcs.phar": "https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/latest/download/phpcs.phar",
    "phpmd.phar": "https://github.com/phpmd/phpmd/releases/latest/download/phpmd.phar",
}

CMD_TIMEOUT = 600


def run(cmd: list[str], cwd: Path, timeout: int = CMD_TIMEOUT) -> subprocess.CompletedProcess:
    return subprocess.run(cmd, cwd=cwd, capture_output=True, text=True, timeout=timeout)


def dump_raw(raw_dir: Path | None, name: str, text: str) -> str | None:
    """Сохранить полный вывод инструмента; вернуть имя файла для metrics.json."""
    if raw_dir is None:
        return None
    (raw_dir / name).write_text(text, encoding="utf-8")
    return name


def phar(name: str) -> Path | None:
    """Путь к phar из кэша; при отсутствии — однократное скачивание."""
    path = PHAR_CACHE / name
    if path.is_file() and path.stat().st_size > 1_000_000:
        return path
    PHAR_CACHE.mkdir(parents=True, exist_ok=True)
    tmp = path.with_suffix(".part")
    try:
        with urllib.request.urlopen(PHAR_URLS[name], timeout=120) as resp, tmp.open("wb") as out:
            shutil.copyfileobj(resp, out)
        if tmp.stat().st_size > 1_000_000:
            tmp.replace(path)
            print(f"phar-кэш: скачан {name}", file=sys.stderr)
            return path
    except OSError as exc:
        print(f"phar-кэш: {name} недоступен ({exc})", file=sys.stderr)
    tmp.unlink(missing_ok=True)
    return None


def install_tools(work: Path) -> bool:
    """Медленный fallback: composer-установка инструментов в копию."""
    c = run(["composer", "require", "--dev",
             "phpstan/phpstan", "squizlabs/php_codesniffer", "phpmd/phpmd",
             "--no-interaction", "--no-progress", "--no-audit"], work)
    return c.returncode == 0


def phpstan(work: Path, phpstan_bin: list[str], raw_dir: Path | None = None) -> dict:
    (work / "phpstan.neon").write_text(
        "parameters:\n"
        "    level: 8\n"
        "    paths:\n        - src\n"
        "    excludePaths:\n        - src/Kernel.php\n", encoding="utf-8"
    )
    c = run([*phpstan_bin, "analyse", "--no-progress", "--error-format=raw"], work)
    errors = [ln for ln in c.stdout.splitlines() if ln.strip()]
    return {"status": "ok", "exit": c.returncode, "errors": len(errors),
            "first": errors[:5],
            "raw": dump_raw(raw_dir, "phpstan-raw.txt", c.stdout)}


def phpmd(work: Path, phpmd_bin: list[str], raw_dir: Path | None = None) -> dict:
    ruleset = CODING_STANDARD / "docs/conventions/examples/phpmd.xml"
    rules = str(ruleset) if ruleset.is_file() else "cleancode,codesize,design"
    # у phpmd 3.x новый CLI (analyze --format --ruleset), у 2.x — позиционный
    c = run([*phpmd_bin, "analyze", "src", "--format=text", f"--ruleset={rules}"], work)
    if c.returncode != 0 and not c.stdout.strip():
        c = run([*phpmd_bin, "src", "text", rules], work)
    violations = [ln for ln in c.stdout.splitlines() if ln.strip()]
    return {"status": "ok", "rules": rules, "violations": len(violations),
            "first": violations[:5],
            "raw": dump_raw(raw_dir, "phpmd-raw.txt", c.stdout)}


def phpcs(work: Path, phpcs_bin: list[str], raw_dir: Path | None = None) -> dict:
    # Kernel.php дан скелетом — не код кандидата, из проверки исключён
    c = run([*phpcs_bin, "--standard=PSR12", "--report=summary",
             "--ignore=Kernel.php", "src"], work)
    errors = warnings = 0
    m = re.search(r"TOTAL OF (\d+) ERRORS? AND (\d+) WARNINGS?", c.stdout)
    if m:
        errors, warnings = int(m.group(1)), int(m.group(2))
    full = run([*phpcs_bin, "--standard=PSR12", "--report=full",
                "--ignore=Kernel.php", "src"], work) if raw_dir else None
    return {"status": "ok", "errors": errors, "warnings": warnings,
            "violations": errors + warnings, "exit": c.returncode,
            "raw": dump_raw(raw_dir, "phpcs-full.txt",
                            full.stdout if full else c.stdout)}


def scc(work: Path, raw_dir: Path | None = None) -> dict:
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
    return {"status": "ok", "files": files, "loc": lines,
            "raw": dump_raw(raw_dir, "scc.json", c.stdout)}


def ast_metrics(work: Path, raw_dir: Path | None = None) -> dict:
    """CC/WMC/LCOM4/Ca-Ce по сырой AST-статистике coding-standard.

    Сборщик запускается напрямую из чекаута coding-standard (без composer),
    через универсальный `--source=src`; пороги — из .coding-standard.php.
    """
    collector = CODING_STANDARD / "bin" / "metrics-collect"
    if not collector.is_file():
        return {"status": "skip", "reason": "нет ~/MyProjects/coding-standard"}
    if not (CODING_STANDARD / "vendor" / "autoload.php").is_file():
        return {"status": "skip",
                "reason": "в coding-standard не собран vendor (composer install)"}

    out = work / "collector.json"
    c = run(["php", str(collector), f"--source={work / 'src'}", f"--output={out}"], work)
    if c.returncode != 0 or not out.is_file():
        return {"status": "skip", "reason": "metrics-collect не удался: " + (c.stderr or "")[-200:]}
    report = json.loads(out.read_text(encoding="utf-8"))
    raw_name = None
    if raw_dir is not None:
        (raw_dir / "collector.json").write_text(
            json.dumps(report, ensure_ascii=False, indent=1), encoding="utf-8")
        raw_name = "collector.json"

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

    # по-классовые/по-методные детали не дублируем: полный отчёт сборщика
    # сохраняется в metrics-raw/collector.json (поле raw ниже)
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
        "raw": raw_name,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", required=True, help="каталог кандидата")
    parser.add_argument("--raw-dir", type=Path, default=None,
                        help="куда сложить сырые выводы инструментов "
                             "(обычно <run>/metrics-raw); без флага сырье не пишется")
    args = parser.parse_args()
    repo = Path(args.repo).resolve()
    if not (repo / "src").is_dir():
        print("нет src/ в репозитории кандидата", file=sys.stderr)
        return 2
    raw_dir = args.raw_dir.resolve() if args.raw_dir else None
    if raw_dir:
        raw_dir.mkdir(parents=True, exist_ok=True)

    with tempfile.TemporaryDirectory(prefix="billing-metrics-") as td:
        work = Path(td) / "repo"
        shutil.copytree(repo, work, ignore=shutil.ignore_patterns("var", ".git"))

        phpstan_phar, phpcs_phar, phpmd_phar = (phar(n) for n in PHAR_URLS)
        if phpstan_phar and phpcs_phar and phpmd_phar:
            method = "phar"
            phpstan_bin = ["php", str(phpstan_phar)]
            phpcs_bin = ["php", str(phpcs_phar)]
            phpmd_bin = ["php", str(phpmd_phar)]
        elif install_tools(work):
            method = "composer"
            phpstan_bin = ["vendor/bin/phpstan"]
            phpcs_bin = ["vendor/bin/phpcs"]
            phpmd_bin = ["vendor/bin/phpmd"]
        else:
            print(json.dumps({"status": "skip",
                              "reason": "инструменты недоступны (нет кэша и сети)"},
                             ensure_ascii=False, indent=2))
            return 0

        report = {
            "repo": str(repo),
            "tools_method": method,
            "phpstan": phpstan(work, phpstan_bin, raw_dir),
            "phpmd": phpmd(work, phpmd_bin, raw_dir),
            "phpcs": phpcs(work, phpcs_bin, raw_dir),
            "scc": scc(work, raw_dir),
            "ast_metrics": ast_metrics(work, raw_dir),
        }
        print(json.dumps(report, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    sys.exit(main())

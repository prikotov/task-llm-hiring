#!/usr/bin/env python3
"""Чекер тестового задания «биллинг LLM-токенов» (ярус 0 — функциональные проверки).

Прогоняет консольные команды кандидата на скрытых фикстурах в чистой
PostgreSQL-базе и сравнивает вывод с golden-файлами, сгенерированными
reference_calc.py. Дополнительно фиксирует для судьи изменения файлов
каркаса (skeleton_changes; правка не запрещена — обоснованность оценит
ревью) и наличие обязательных артефактов (COMMIT_MESSAGE.md,
PR_DESCRIPTION.md).

Запуск (из каталога billing/):

    python3 checker/check.py --repo /path/to/candidate

Кандидатский репозиторий — копия skeleton/, в которой модель выполнила
TASK.md. Докер-контейнер PostgreSQL поднимается на свободном порту в
изолированном compose-проекте и удаляется после прогона.

Выход: JSON-отчёт в stdout (scenarios: pass/fail/skip + detail), код 0 —
если все обязательные сценарии pass (бонусные — не влияют на код).
"""

from __future__ import annotations

import argparse
import hashlib
import json
import random
import re
import shutil
import socket
import subprocess
import sys
import time
from pathlib import Path

HERE = Path(__file__).resolve().parent
BILLING = HERE.parent
DEFAULT_FIXTURES = BILLING / "fixtures"
DEFAULT_SKELETON = BILLING / "skeleton"

REPORT_FROM = "2026-07-01T00:00:00Z"
REPORT_TO = "2026-10-01T00:00:00Z"

# файлы каркаса: правка НЕ запрещена (TASK.md требует обосновывать отступления
# в PR_DESCRIPTION.md) — чекер только фиксирует изменения для судьи
PROTECTED = [
    "TASK.md",
    "AGENTS.md",
    "README.md",
    "composer.json",
    "composer.lock",
    "docker-compose.yml",
    "Makefile",
    "phpunit.xml.dist",
    "fixtures",
]

# дисциплинарные сценарии выполняются всегда; функциональные — только по ходу
DISCIPLINE = {"skeleton_changes", "artifacts_present"}

MANDATORY = {
    "db_create",
    "migrate",
    "prices_import",
    "usage_import",
    "rate_counts",
    "unrated_stderr",
    "report_base",
    "import_duplicates",
    "rate_after_duplicates",
    "report_duplicates",
    "artifacts_present",
}
BONUS = {"reprice_output", "report_retro"}

CMD_TIMEOUT = 300  # сек на одну консольную команду


def free_port() -> int:
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.bind(("127.0.0.1", 0))
        return int(s.getsockname()[1])


def run(cmd: list[str], cwd: Path, env: dict[str, str] | None = None,
        timeout: int = CMD_TIMEOUT) -> subprocess.CompletedProcess:
    return subprocess.run(cmd, cwd=cwd, env=env, capture_output=True, text=True, timeout=timeout)


class Checker:
    def __init__(self, repo: Path, skeleton: Path, fixtures: Path) -> None:
        self.repo = repo
        self.skeleton = skeleton
        self.fixtures = fixtures
        self.golden = fixtures / "golden"
        self.port = free_port()
        self.project = f"billingchk{random.randint(10000, 99999)}"
        self.db = f"chk_{random.randint(10000, 99999)}"
        self.results: dict[str, dict] = {}

    # -- инфраструктура ------------------------------------------------------

    def compose(self, *args: str) -> subprocess.CompletedProcess:
        return run(["docker", "compose", "-p", self.project, *args], cwd=self.repo, timeout=120)

    def console(self, *args: str, timeout: int = CMD_TIMEOUT) -> subprocess.CompletedProcess:
        env = {
            **dict(__import__("os").environ),
            "DATABASE_URL": f"postgresql://app:app@127.0.0.1:{self.port}/{self.db}",
        }
        return run(["php", "bin/console", "-n", *args], cwd=self.repo, env=env, timeout=timeout)

    def start_db(self) -> None:
        import os

        env = {**dict(os.environ), "BILLING_PG_PORT": str(self.port)}
        up = subprocess.run(
            ["docker", "compose", "-p", self.project, "up", "-d"],
            cwd=self.repo, env=env, capture_output=True, text=True, timeout=300,
        )
        if up.returncode != 0:
            raise RuntimeError(f"docker compose up: {up.stderr[-500:]}")
        for _ in range(60):
            ok = self.compose("exec", "-T", "db", "pg_isready", "-U", "app", "-d", "app")
            if ok.returncode == 0:
                return
            time.sleep(1)
        raise RuntimeError("PostgreSQL не стал готовым за 60с")

    def stop_db(self) -> None:
        self.compose("down", "-v", "--remove-orphans")

    # -- сценарии ------------------------------------------------------------

    def record(self, name: str, ok: bool, detail: str = "") -> bool:
        self.results[name] = {"status": "pass" if ok else "fail", "detail": detail}
        print(f"  {'PASS' if ok else 'FAIL'}  {name}" + (f" — {detail}" if detail and not ok else ""))
        return ok

    def skip(self, name: str, reason: str) -> None:
        self.results[name] = {"status": "skip", "detail": reason}
        print(f"  SKIP  {name} — {reason}")

    def run_all(self) -> int:
        print(f"repo={self.repo} port={self.port} db={self.db}")
        try:
            self.start_db()
            self.functional()
        except Exception as exc:  # noqa: BLE001 — чекер обязан пережить любой сбой кандидата
            for name in sorted(MANDATORY - set(self.results)):
                self.record(name, False, f"не дошло: {exc}")
        finally:
            try:
                self.stop_db()
            except Exception:  # noqa: BLE001
                pass
        # функциональные шаги, до которых не дошли после провала, — fail
        for name in sorted(MANDATORY - DISCIPLINE - set(self.results)):
            self.record(name, False, "не выполнялся: предыдущий шаг провален")
        self.discipline()

        mandatory_failed = [
            n for n, r in self.results.items()
            if n in MANDATORY and r["status"] != "pass"
        ]
        summary = {
            "repo": str(self.repo),
            "scenarios": self.results,
            "mandatory_passed": sum(
                1 for n, r in self.results.items()
                if n in MANDATORY and r["status"] == "pass"
            ),
            "mandatory_total": len(MANDATORY),
            "bonus_passed": sum(
                1 for n, r in self.results.items()
                if n in BONUS and r["status"] == "pass"
            ),
            "bonus_total": len(BONUS),
            "verdict": "pass" if not mandatory_failed else "fail",
        }
        print(json.dumps(summary, ensure_ascii=False, indent=2))
        return 0 if not mandatory_failed else 1

    # -- функциональные проверки ----------------------------------------------

    def functional(self) -> None:
        c = self.console("doctrine:database:create", "--if-not-exists")
        self.record("db_create", c.returncode == 0, (c.stderr or c.stdout)[-300:])
        if c.returncode != 0:
            return
        # способ управления схемой — выбор кандидата; пробуем миграции,
        # при их отсутствии/провале — schema:update; фиксируем способ для судьи
        c = self.console("doctrine:migrations:migrate", "-n")
        method = "migrations"
        if c.returncode != 0:
            c = self.console("doctrine:schema:update", "--force", "--complete", "-n")
            method = "schema_update"
        self.record("migrate", c.returncode == 0,
                    f"method={method}" + ("" if c.returncode == 0 else "; " + (c.stderr or c.stdout)[-250:]))
        if c.returncode != 0:
            return

        c = self.console("app:prices:import", str(self.fixtures / "prices.json"))
        self.record("prices_import", c.returncode == 0, (c.stderr or c.stdout)[-300:])
        c = self.console("app:usage:import", str(self.fixtures / "usage-base.jsonl"))
        self.record("usage_import", c.returncode == 0, (c.stderr or c.stdout)[-300:])

        rate = self.console("app:billing:rate")
        m = re.search(r"RATED=(\d+)\s+UNRATED=(\d+)", rate.stdout)
        if m is None:
            self.record("rate_counts", False,
                        f"в stdout нет 'RATED=<n> UNRATED=<m>': {rate.stdout.strip()[:120]!r}")
        else:
            ok = (m.group(1), m.group(2)) == ("17", "1")
            self.record("rate_counts", ok,
                        f"получено RATED={m.group(1)} UNRATED={m.group(2)}, ожидалось 17/1")

        got_stderr = {ln for ln in rate.stderr.splitlines() if ln.startswith("UNRATED")}
        want_stderr = {
            ln for ln in (self.golden / "rate-stderr.txt").read_text().splitlines() if ln
        }
        self.record("unrated_stderr", got_stderr == want_stderr,
                    f"ожидались {sorted(want_stderr)}, получено {sorted(got_stderr)[:5]}")

        self.compare_report("report_base", self.golden / "report-base.csv")

        dup = self.console("app:usage:import", str(self.fixtures / "usage-duplicates.jsonl"))
        self.record("import_duplicates", dup.returncode == 0, (dup.stderr or dup.stdout)[-300:])
        rate2 = self.console("app:billing:rate")
        m2 = re.search(r"RATED=(\d+)\s+UNRATED=(\d+)", rate2.stdout)
        self.record("rate_after_duplicates",
                    m2 is not None and (m2.group(1), m2.group(2)) == ("1", "1"),
                    f"stdout: {rate2.stdout.strip()[:120]!r}")
        self.compare_report("report_duplicates", self.golden / "report-duplicates.csv")

        self.bonus()

    def bonus(self) -> None:
        listing = self.console("list", "--format=txt")
        if "app:billing:reprice" not in (listing.stdout + listing.stderr):
            self.skip("reprice_output", "команды app:billing:reprice нет (бонус не выполнялся)")
            self.skip("report_retro", "команды app:billing:reprice нет (бонус не выполнялся)")
            return

        rp = self.console("app:billing:reprice", str(self.fixtures / "prices-retro.json"))
        want = (self.golden / "reprice-stdout.txt").read_text().splitlines()
        self.record("reprice_output", rp.stdout.splitlines() == want,
                    f"stdout: {rp.stdout.strip()[:200]!r}, stderr: {rp.stderr.strip()[:200]!r}")
        self.compare_report("report_retro", self.golden / "report-retro.csv")

    def compare_report(self, name: str, golden: Path) -> None:
        rep = self.console(
            "app:billing:report", f"--from={REPORT_FROM}", f"--to={REPORT_TO}"
        )
        if rep.returncode != 0:
            self.record(name, False, f"код {rep.returncode}: {(rep.stderr or rep.stdout)[-300:]}")
            return
        got = [ln.rstrip() for ln in rep.stdout.splitlines() if ln.strip()]
        want = [ln.rstrip() for ln in golden.read_text().splitlines() if ln.strip()]
        if got == want:
            self.record(name, True)
            return
        diff = [f"- {a}" for a in want if a not in got][:4] + [f"+ {b}" for b in got if b not in want][:4]
        self.record(name, False, "; ".join(diff) or "разное количество строк")

    # -- дисциплинарные проверки -----------------------------------------------

    def discipline(self) -> None:
        changed: list[str] = []
        for rel in PROTECTED:
            new = self.repo / rel
            old = self.skeleton / rel
            if not new.exists():
                changed.append(f"{rel} (удалён)")
                continue
            if rel.endswith("fixtures") or (old.is_dir() and not old.is_file()):
                new_hashes = {
                    str(p.relative_to(new)): hashlib.sha256(p.read_bytes()).hexdigest()
                    for p in sorted(new.rglob("*")) if p.is_file()
                }
                old_hashes = {
                    str(p.relative_to(old)): hashlib.sha256(p.read_bytes()).hexdigest()
                    for p in sorted(old.rglob("*")) if p.is_file()
                }
                if new_hashes != old_hashes:
                    changed.append(f"{rel}/ (изменён)")
                continue
            if hashlib.sha256(new.read_bytes()).hexdigest() != hashlib.sha256(old.read_bytes()).hexdigest():
                changed.append(rel)
        # правка каркаса не запрещена (TASK.md: обосновать в PR_DESCRIPTION.md);
        # чекер фиксирует факт для судьи
        self.record("skeleton_changes", True,
                    ("изменены: " + ", ".join(changed)) if changed else "нет изменений")

        missing = [
            f for f in ("COMMIT_MESSAGE.md", "PR_DESCRIPTION.md")
            if not (self.repo / f).is_file() or (self.repo / f).stat().st_size == 0
        ]
        self.record("artifacts_present", not missing,
                    "отсутствуют/пусты: " + ", ".join(missing) if missing else "")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", required=True, help="каталог кандидата (копия skeleton/)")
    parser.add_argument("--skeleton", default=str(DEFAULT_SKELETON))
    parser.add_argument("--fixtures", default=str(DEFAULT_FIXTURES))
    args = parser.parse_args()

    repo = Path(args.repo).resolve()
    if not (repo / "bin" / "console").is_file():
        print(f"нет {repo}/bin/console — это точно репозиторий кандидата?", file=sys.stderr)
        return 2
    if shutil.which("docker") is None:
        print("docker не найден", file=sys.stderr)
        return 2

    return Checker(repo, Path(args.skeleton).resolve(), Path(args.fixtures).resolve()).run_all()


if __name__ == "__main__":
    sys.exit(main())

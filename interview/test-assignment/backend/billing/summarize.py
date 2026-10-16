#!/usr/bin/env python3
"""Карточка итогов прогона — summary.md в каталоге run-XX.

Собирает человекочитаемую сводку из артефактов прогона: run.json (параметры
и время), check.json (ярус 0 — сценарии чекера), metrics.json (ярус 0.5 —
детерминированные метрики) и pi-session/ (поведенческий профиль через
analyze_session). Ревью судьи (оси A/B) потом дополняет её в scorecard.md.

Использование:
    python3 summarize.py models/<модель>/test-assignment/backend/run-01
"""

from __future__ import annotations

import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from analyze_session import analyze, load  # noqa: E402

SCENARIO_TITLES = {
    "db_create": "создание БД",
    "migrate": "разворачивание схемы",
    "prices_import": "импорт прайса",
    "usage_import": "импорт событий",
    "rate_counts": "тарификация (счётчики)",
    "unrated_stderr": "unrated в stderr",
    "report_base": "отчёт: базовый сценарий",
    "import_duplicates": "импорт дублей",
    "rate_after_duplicates": "тарификация после дублей",
    "report_duplicates": "отчёт: идемпотентность",
    "reprice_output": "бонус: вывод reprice",
    "report_retro": "бонус: отчёт после коррекции",
    "skeleton_changes": "изменения каркаса (инфо)",
    "artifacts_present": "артефакты commit/PR",
}

MARK = {"pass": "✅", "fail": "❌", "skip": "➖"}


def fmt_duration(seconds: float | int | None) -> str:
    if not seconds:
        return "—"
    seconds = int(seconds)
    return f"{seconds // 60} мин {seconds % 60:02d} с"


def load_json(path: Path) -> dict | None:
    return json.loads(path.read_text(encoding="utf-8")) if path.is_file() else None


def main() -> int:
    run_dir = Path(sys.argv[1] if len(sys.argv) > 1 else ".").resolve()
    meta = load_json(run_dir / "run.json") or {}
    check = load_json(run_dir / "check.json")
    metrics = load_json(run_dir / "metrics.json")

    lines: list[str] = []
    lines.append(f"# Прогон: {meta.get('model', run_dir.parent.parent.name)}")
    lines.append("")
    lines.append("| Параметр | Значение |")
    lines.append("|---|---|")
    lines.append(f"| Модель | `{meta.get('model', '—')}` (провайдер {meta.get('provider', '—')}) |")
    lines.append(f"| Thinking / контекст | {meta.get('thinking', '—')} / {meta.get('context', '—')} токенов"
                 + (f"; шкала модели: {meta['thinking_scale']}" if meta.get('thinking_scale') else "")
                 + (f"; **фактически: {meta['thinking_effective']}**" if meta.get('thinking_effective') else "")
                 + (f"; maxTokens {meta['max_tokens']}" if meta.get('max_tokens') else "") + " |")
    lines.append(f"| Длительность | {fmt_duration(meta.get('duration_s'))} (exit {meta.get('exit_code', '—')}"
                 f"{', остановлен' if meta.get('killed') else ''}) |")
    lines.append(f"| Версия TASK | `{meta.get('task_sha256', '—')}` |")
    if meta.get("diff_lines") is not None:
        lines.append(f"| Объём решения | дифф {meta['diff_lines']} строк, {meta.get('solution_files', '—')} файлов |")
    lines.append("")

    if check:
        lines.append(f"## Ярус 0 — чекер: {check['mandatory_passed']}/{check['mandatory_total']} "
                     f"обязательных, бонус {check['bonus_passed']}/{check['bonus_total']} "
                     f"({check['verdict']})")
        lines.append("")
        lines.append("| Сценарий | Статус | Детали |")
        lines.append("|---|---|---|")
        for name, res in check["scenarios"].items():
            title = SCENARIO_TITLES.get(name, name)
            detail = (res.get("detail") or "").replace("\n", " ")[:80]
            lines.append(f"| {title} | {MARK.get(res['status'], '?')} {res['status']} | {detail} |")
        lines.append("")

    if metrics:
        lines.append("## Ярус 0.5 — детерминированные метрики")
        lines.append("")
        ps, pm, pc = metrics.get("phpstan", {}), metrics.get("phpmd", {}), metrics.get("phpcs", {})
        sc, ast = metrics.get("scc", {}), metrics.get("ast_metrics", {})
        lines.append("| Метрика | Значение |")
        lines.append("|---|---|")
        lines.append(f"| phpstan level 8 | {ps.get('errors', '—')} ошибок |")
        lines.append(f"| phpmd | {pm.get('violations', '—')} нарушений |")
        lines.append(f"| phpcs PSR12 | {pc.get('violations', '—')} ({pc.get('errors', 0)} ошибок, {pc.get('warnings', 0)} предупреждений) |")
        lines.append(f"| Размер | {sc.get('files', '—')} файлов, {sc.get('loc', '—')} LOC |")
        if ast.get("status") == "ok":
            lines.append(f"| Классы/методы | {ast.get('classes')} / {ast.get('methods')} |")
            lines.append(f"| WMC (сумма) | {ast.get('wmc_total')} (худший класс: `{ast.get('wmc_worst_class')}`) |")
            lines.append(f"| Худший метод | CC {ast.get('method_cc_max')}, LOC {ast.get('method_loc_max')} |")
            findings = ast.get("findings_thresholds", {})
            lines.append(f"| Превышения порогов | cc>10: {findings.get('method_cc_over_10')}, "
                         f"loc>50: {findings.get('method_loc_over_50')}, wmc>50: {findings.get('class_wmc_over_50')}, "
                         f"lcom4>1: {findings.get('class_lcom4_over_1')} |")
        lines.append("")

    session_dir = run_dir / "pi-session"
    if session_dir.is_dir():
        try:
            events, header = load(str(session_dir))
            profile = analyze(events)
        except Exception as exc:  # noqa: BLE001 — карточка должна собираться всегда
            lines.append(f"_профиль сессии не собран: {exc}_")
            profile = None
        if profile:
            lines.append("## Поведенческий профиль (по сессии pi)")
            lines.append("")
            lines.append("| Что | Значение |")
            lines.append("|---|---|")
            lines.append(f"| Сообщений (assistant/toolResult) | {profile['messages_total']} "
                         f"({profile['roles'].get('assistant', 0)} / {profile['roles'].get('toolResult', 0)}) |")
            by_name = ", ".join(f"{k}: {v}" for k, v in sorted(profile["tool_calls_by_name"].items()))
            lines.append(f"| Tool-calls ({profile['tool_calls_total']}) | {by_name} |")
            lines.append(f"| Thinking | {profile['thinking_blocks']} блоков, {profile['thinking_chars']:,} символов |")
            lines.append(f"| Файлы | {profile['files_touched']} касаний, {profile['files_distinct']} уникальных |")
            lines.append(f"| Bash по категориям | {profile['bash_by_kind']} |")
            lines.append(f"| Запуски тестов | {profile['test_runs']} ({profile['test_results']}) |")
            sc = profile.get("self_checks", {})
            if sc:
                titles = {"tests": "тесты (phpunit/make test)",
                          "static_analysis": "статанализ (phpstan/phpcs/…)",
                          "lint": "синтаксис (php -l)",
                          "acceptance_commands": "приёмочные команды (app:*)",
                          "db_checks": "проверки БД (psql)"}
                checks = ", ".join(f"{titles.get(k, k)}: {v}" for k, v in sc.items()
                                   if not isinstance(v, (list, int)) or k == "debug_prints_in_code")
                lines.append(f"| Самопроверка | {checks} |")
                seq = " → ".join(sc.get("test_outcomes_sequence", []))
                lines.append(f"| Исходы тестов подряд | {seq or '—'} |")
                if sc.get("debug_prints_in_code"):
                    lines.append(f"| Отладочные принты в коде | var_dump/dd/dump: {sc['debug_prints_in_code']} |")
            lines.append(f"| ~Транскрипт | {profile['transcript_chars_approx']:,} символов |")
            u = profile.get("usage", {})
            if u:
                lines.append(f"| Вызовов API | {u['api_calls']} (генерация {fmt_duration(u['generation_time_s'])}) |")
                lines.append(f"| Токены | вход {u['input']:,} + выход {u['output']:,} (reasoning {u['reasoning']:,}), "
                             f"cache-read {u['cache_read']:,}; всего {u['total']:,} |")
                lines.append(f"| Пик контекста | {u['context_peak']:,} токенов (лимит прогона {meta.get('context', '—')}) |")
            cm = profile.get("context_management", {})
            if cm:
                lines.append(f"| Управление контекстом | автокомпактов: {cm.get('compaction', 0)}, "
                             f"правок истории: {cm.get('context_edit', 0)} |")
                lines.append(f"| Стоимость | ${u['cost_total']:.4f} (локальная модель — 0; тариф для сравнения задаётся при необходимости) |")
            lines.append("")
            lines.append("Таймлайн (tool-calls по 5 мин): "
                         + ", ".join(f"{k}={v}" for k, v in profile["timeline_5m"].items()))
            lines.append("")

    lines.append("---")
    if (run_dir / "scorecard.md").is_file():
        lines.append("_Карточка собрана автоматически (summarize.py); ревью судьи — "
                     "[scorecard.md](scorecard.md)._")
    else:
        lines.append("_Карточка собрана автоматически (summarize.py); ревью судьи "
                     "(оси A/B по RUBRIC.md) ещё не проводилось._")

    out = run_dir / "summary.md"
    out.write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"готово: {out}")
    return 0


if __name__ == "__main__":
    sys.exit(main())

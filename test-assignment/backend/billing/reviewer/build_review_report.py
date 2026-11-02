#!/usr/bin/env python3
"""Генератор отчёта этапа 3 (код-ревью решений этапа 2 локальными моделями).

Стиль — как у отчётов этапов 1 и 2: светлый документ, нумерованные секции,
таблицы с бейджами и барами, самописные SVG без внешних зависимостей.
Все числа — из артефактов прогонов (run.json, review.json) в
models/<модель>/backend/review/ и эталон-карточек references/*.json.

Запуск: python3 test-assignment/backend/billing/reviewer/build_review_report.py
Выход: reports/REVIEW-report.html
"""
import glob
import json
import statistics
from collections import defaultdict
from pathlib import Path

HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[3]
MODELS = ROOT / "models"
OUT = ROOT / "reports" / "REVIEW-report.html"

# Стоимость локального инференса — методика отчётов этапов 1-2
# (billing/build_report.py): 550 Вт в момент генерации при ₽7.07/кВт·ч
# (тариф с 01.10.2026) плюс амортизация железа (₽600 000 минус остаточные
# 30% за 13 140 часов) — итого ₽35.85/ч чистой генерации; нижняя граница —
# только электричество ₽3.89/ч
LOCAL_RUB_H = 0.55 * 7.07 + (600_000 * 0.70) / (3 * 365 * 12)   # ₽35.85/ч
ELEC_RUB_H = 0.55 * 7.07   # только электричество

SHORT = {
    "qwen3.8:27b-pi-100k": "qwen3.8",
    "huihui-ai:pi-100k": "huihui",
    "orcarouter/Qwen3.8-27B-Uncensored:ctx100k": "orca",
    "gemma4:31b-it-qat-pi-100k": "gemma4:31b",
    "gemma4:26b-pi-100k": "gemma4:26b",
    "gemma4:e2b-ctx100k": "e2b",
}
PARAMS = {
    "qwen3.8:27b-pi-100k": ("27.3B", "Q4_K_M"),
    "huihui-ai:pi-100k": ("27.3B", "Q4_K_M"),
    "orcarouter/Qwen3.8-27B-Uncensored:ctx100k": ("27.3B", "Q4_K_M"),
    "gemma4:31b-it-qat-pi-100k": ("30.7B", "Q4_0"),
    "gemma4:26b-pi-100k": ("25.2B", "Q4_K_M"),
    "gemma4:e2b-ctx100k": ("4.6B", "Q4_K_M"),
}
# порядок: семейство qwen, семейство gemma
MODEL_ORDER = [
    "qwen3.8:27b-pi-100k",
    "huihui-ai:pi-100k",
    "orcarouter/Qwen3.8-27B-Uncensored:ctx100k",
    "gemma4:31b-it-qat-pi-100k",
    "gemma4:26b-pi-100k",
    "gemma4:e2b-ctx100k",
]
SLUG_TITLES = {"good": "хорошее", "mid": "среднее", "tricky": "каверзное", "bad": "плохое"}
SLUG_ORDER = ["good", "mid", "tricky", "bad"]
AXES = ("axis_a", "axis_b", "functional")
AXIS_NAMES = {"axis_a": "A «код и архитектура»", "axis_b": "B «процесс»",
              "functional": "F «соответствие заданию»"}
AXIS_NAMES_SHORT = {"axis_a": "A", "axis_b": "B", "functional": "F"}

# Реальные функциональные пропуски решений (семьи требований) — по фактам
# чекера и рантайм-разбору (раздел 6 отчёта). recall ревьюера = доля этих
# семей, перечисленных им в not_done.
REAL_MISSES = {
    "tricky": ["оф-пиковая скидка"],
    "bad": ["артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)", "оф-пиковая скидка",
            "схема БД из миграций", "округление и точность денег"],
}
REAL_MISSES_TOTAL = sum(len(v) for v in REAL_MISSES.values())

# Ручная разметка «поймано по механизму»: каждое упоминание, совпавшее с
# семьёй по ключевым словам, сверено с реальным механизмом дефекта
# (раздел 6 + код решения). Зачёт — только если назван сам механизм:
# «оф-пик» = провайдер при первом импорте создаётся без off-peak-полей
# (updateProvider только в else-ветке PriceImportService), «округление» =
# bcadd накопления без третьего аргумента-масштаба (bcmath усекает дробную
# часть). Совпадение слова без механизма не зачитывается: заметки про
# таймзону OffPeak::applies() и пустое окно window_start==window_end —
# другие дефекты той же области; про bcRoundHalfUp для отрицательных —
# в домене значения неотрицательны; про оставленный debug_round.php —
# не функциональный дефект.
MECH_CAUGHT = {
    ("huihui-ai:pi-100k", "max"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
        ("bad", "схема БД из миграций"),
        ("bad", "округление и точность денег"),
    },
    ("huihui-ai:pi-100k", "medium"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
        ("bad", "схема БД из миграций"),
    },
    ("huihui-ai:pi-100k", "low"): {
        ("bad", "схема БД из миграций"),
        ("bad", "оф-пиковая скидка"),
    },
    ("qwen3.8:27b-pi-100k", "max"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
        ("bad", "схема БД из миграций"),
    },
    ("qwen3.8:27b-pi-100k", "medium"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
        ("bad", "схема БД из миграций"),
    },
    ("qwen3.8:27b-pi-100k", "low"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
        ("bad", "схема БД из миграций"),
    },
    ("gemma4:31b-it-qat-pi-100k", "max"): {
        ("bad", "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"),
    },
}


def req_family(req: str) -> str:
    """Семья требований по ключевым словам перечня not_done."""
    r = (req or "").lower()
    if "миграц" in r or "чистой базе" in r or "чистая база" in r or "схема бд" in r:
        return "схема БД из миграций"
    if "commit_message" in r or "pr_description" in r or "артефакт" in r:
        return "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"
    if "unrated" in r and ("stderr" in r or "строк" in r or "сбой" in r):
        return "unrated-строки в stderr"
    if "geterroroutput" in r:
        return "unrated-строки в stderr"
    if "reprice" in r or "ретро" in r:
        return "бонус reprice"
    if "округл" in r or "half_up" in r or "bcadd" in r or "точность вычислений" in r \
            or "промежуточные вычисления" in r:
        return "округление и точность денег"
    if "дедупл" in r or "повторная доставка" in r or "идемпотентность" in r:
        return "дедупликация приёма"
    if "оф-пик" in r or "off_peak" in r or "скидк" in r:
        return "оф-пиковая скидка"
    if "сортиров" in r:
        return "сортировка отчёта"
    if "6 знаков" in r or "cost_usd" in r or ("формат" in r and "отч" in r):
        return "формат отчёта"
    if "cache" in r or "кеш" in r or "кэш" in r:
        return "cache-цены"
    return "прочее"

CSS = """
:root{--ink:#1d2433;--mut:#66707f;--line:#e4e7ec;--bg:#fafbfc;--acc:#315f9e;--good:#2f7d54;--bad:#b3543f;--warn:#a15c00}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;font:16px/1.62 "Segoe UI",system-ui,-apple-system,"Helvetica Neue",sans-serif;color:var(--ink);background:var(--bg)}
.wrap{max-width:1040px;margin:0 auto;padding:26px 22px 90px}
header{background:linear-gradient(140deg,#1d2c46 0%,#315f9e 90%);color:#fff;border-radius:16px;padding:34px 40px 30px;margin-bottom:22px}
header h1{margin:0 0 12px;font-size:31px;line-height:1.22;letter-spacing:-.01em}
header p{margin:5px 0;opacity:.93;max-width:76ch}
.toc{margin:20px 0 4px}
.toc-title{font-size:13px;text-transform:uppercase;letter-spacing:.09em;color:var(--mut);margin-bottom:9px}
.toc ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:5px 28px}
.toc a{color:var(--acc);text-decoration:none}
.toc a:hover{text-decoration:underline}
h2{font-size:24px;margin:44px 0 10px;letter-spacing:-.01em}
h2 .no{color:var(--mut);font-weight:400;margin-right:8px}
h3{font-size:17.5px;margin:26px 0 2px}
h3 .no{color:var(--mut);font-weight:400;margin-right:8px}
.lead{color:var(--mut);margin:8px 0 14px;font-size:15px}
p{margin:10px 0}
.sub{font-size:12.5px;color:var(--mut);margin-top:3px;line-height:1.5}
code{font-size:12.5px;background:#eef1f6;border-radius:5px;padding:1px 5px}
.excap{font-size:15px;color:var(--ink);margin:12px 0 -4px;line-height:1.5}
.excap.sb{font-weight:700;margin-top:26px}
table{border-collapse:collapse;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(20,30,50,.07);margin:14px 0}
th{background:#eef1f6;text-align:left;font-size:12.5px;text-transform:uppercase;letter-spacing:.03em;color:#46536a;padding:10px 12px;border-bottom:2px solid var(--line)}
th.narrow,td.narrow{padding-left:5px;padding-right:5px}
td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14.5px}
tr:last-child td{border-bottom:none}
tr.neg td{background:#fdf6f4}
tr.top td{background:#eef6ef}
td.num{font-variant-numeric:tabular-nums;white-space:nowrap}
td.p-good{color:#2f7d54;font-weight:600}
td.p-warn{color:#a15c00;font-weight:600}
td.p-bad{color:#b3543f;font-weight:600}
.barwrap{position:relative;background:#eef0f4;border-radius:6px;min-width:92px;max-width:110px;height:21px}
.barwrap .bar{position:absolute;left:0;top:3px;bottom:3px;border-radius:5px;min-width:3px;opacity:.3}
.barwrap>span{position:absolute;left:7px;top:0;font-size:12.5px;line-height:21px;font-weight:600;color:var(--ink)}
.barwrap+.barwrap{margin-top:2px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(295px,1fr));gap:13px;margin:16px 0}
.card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--acc);border-radius:12px;padding:15px 17px;font-size:14.5px}
.card.b{border-left-color:var(--bad)}
.card.g{border-left-color:var(--good)}
.card.w{border-left-color:var(--warn)}
.okmark{display:inline-block;margin-left:-6px;padding:0 6px;border-radius:9px;background:#e9f1ec;color:#2f7d54;font-size:12px;font-weight:700;line-height:18px;cursor:help;white-space:nowrap}
.deadmark{display:inline-block;margin-left:-6px;padding:0 6px;border-radius:9px;font-size:12px;font-weight:700;line-height:18px;cursor:help;white-space:nowrap}
.deadmark.contract{background:#efe9f7;color:#6b5b9e}
.deadmark.fab{background:#fbeae6;color:#b3543f}
.deadmark.burn{background:#fff4d6;color:#9a6b00}
.stack{font-size:12.5px;line-height:1.45;white-space:nowrap}
.stack.b{font-weight:600}
.dchip{display:inline-block;margin:1px 2px 1px 0;padding:0 5px;border-radius:7px;background:#eef1f6;color:#315f9e;font-size:10.5px;font-weight:600;line-height:15px;white-space:nowrap}
.alist details{margin:8px 0;border:1px solid var(--line);border-radius:12px;background:#fff;overflow:hidden}
.alist summary{cursor:pointer;padding:10px 16px;font-size:14.5px}
.alist details[open]>summary{border-bottom:1px solid var(--line)}
.alist table{margin:0}
.footer{margin-top:56px;color:var(--mut);font-size:12.5px;border-top:1px solid var(--line);padding-top:14px}
"""


def f_deterministic(mandatory_passed: int, mandatory_total: int) -> float:
    """Детерминированный эталон F из инструмента этапа 2 (чекер): доля
    обязательных сценариев на шкале 0–4 с шагом 0.5, бонус не учитывается.
    Формула воспроизводит замороженные эталонные баллы всех четырёх решений."""
    import math
    return math.floor(8 * mandatory_passed / mandatory_total) / 2


def load_references() -> dict:
    refs = {}
    for p in sorted(glob.glob(str(HERE / "references" / "*.json"))):
        c = json.loads(Path(p).read_text(encoding="utf-8"))
        # детерминированный F из чекера прогона-источника; сверка с замороженным
        chk = ROOT / c["source_run"] / "check.json"
        cj = json.loads(chk.read_text(encoding="utf-8"))
        c["f_det"] = f_deterministic(cj["mandatory_passed"], cj["mandatory_total"])
        frozen = c["expected"]["functional"]
        if abs(c["f_det"] - frozen) > 1e-9:
            print(f"ВНИМАНИЕ: {c['slug']}: детерминированный F {c['f_det']} != "
                  f"замороженному {frozen}")
        refs[c["slug"]] = c
    return refs


def load_runs() -> list[dict]:
    """Все прогоны ревью (протоколы v1 и v2) с полями вердикта/валидности."""
    runs = []
    for rp in sorted(MODELS.glob("*/backend/review*/run-*")):
        rj, vj = rp / "run.json", rp / "review.json"
        if not rj.is_file():
            continue
        meta = json.loads(rj.read_text(encoding="utf-8"))
        run = {
            "dir": rp.name,
            "path": str(rp.relative_to(MODELS)),
            "model": meta.get("model", "?"),
            "protocol": meta.get("protocol", "v1"),
            "thinking": meta.get("thinking", "?"),
            "prompt_version": meta.get("prompt_version", 1),
            "folder": meta["target"].split("/models/")[1].split("/backend")[0]
            if "target" in meta else "?",
            "slug": meta.get("solution_slug", "?"),
            "tool_calls": meta.get("tool_calls", 0),
            "duration_s": meta.get("duration_s"),
            "tokens": meta.get("tokens", {}).get("totalTokens"),
            "integrity": meta.get("integrity", {}).get("status"),
            "parse_ok": meta.get("parse_ok"),
            "invalid_reason": meta.get("invalid_reason"),
            "verdict": None, "defects": None, "note": None,
        }
        if vj.is_file():
            v = json.loads(vj.read_text(encoding="utf-8"))
            run["verdict"] = {k: v.get(k) for k in AXES}
            run["defects"] = v.get("defects") or []
            run["note"] = v.get("note")
        run["valid"] = bool(
            run["tool_calls"] > 0 and run["integrity"] == "ok"
            and all(isinstance(run["verdict"].get(k), (int, float)) for k in AXES)
        ) if run["verdict"] else False
        if not run["valid"]:
            run["fail_kind"] = fail_kind(run)
        runs.append(run)
    return runs


def fail_kind(run: dict) -> str:
    if run["tool_calls"] == 0:
        return "fab"       # вердикт без чтения
    if run["integrity"] != "ok":
        return "integ"     # целостность нарушена
    if run["verdict"] and all(isinstance(run["verdict"].get(k), (int, float)) for k in AXES):
        return ""
    return "contract"      # вердикт не по контракту / не распознан


def aggregate(runs: list[dict], refs: dict) -> dict:
    """(модель, протокол, thinking, контракт, решение) -> прогоны; профили
    по четвёрке: усреднять уровни размышлений и версии контракта нельзя."""
    grid = defaultdict(list)
    for r in runs:
        if r["valid"]:
            grid[(r["model"], r["protocol"], r["thinking"], r["prompt_version"],
                  r["slug"])].append(r)
    profiles = {}
    for model in MODEL_ORDER:
        for protocol, thinking, pv in sorted(
                {(p, th, pv) for (m, p, th, pv, _s) in grid if m == model}):
            per_slug, spreads, deltas, defects = {}, [], [], defaultdict(int)
            for slug in SLUG_ORDER:
                rs = grid.get((model, protocol, thinking, pv, slug), [])
                if not rs:
                    continue
                e = refs[slug]["expected"]
                vals = {a: [r["verdict"][a] for r in rs] for a in AXES}
                per_slug[slug] = {
                    "vals": vals, "n": len(rs),
                    "delta_mean": {a: statistics.mean(abs(v - e[a]) for v in vals[a]) for a in AXES},
                    "spread": {a: max(vals[a]) - min(vals[a]) for a in AXES},
                }
                spreads.append(statistics.mean(per_slug[slug]["spread"].values()))
                deltas.append(statistics.mean(per_slug[slug]["delta_mean"].values()))
                for r in rs:
                    for d in r["defects"]:
                        defects[(d.get("severity") or "?").lower()] += 1
            if per_slug:
                profiles[(model, protocol, thinking, pv)] = {
                    "per_slug": per_slug,
                    "thinking": thinking,
                    "spread_mean": statistics.mean(spreads) if spreads else 0,
                    "delta_mean": statistics.mean(deltas) if deltas else 0,
                    "n": sum(p["n"] for p in per_slug.values()),
                    "defects": dict(defects),
                }
    return grid, profiles


def latest_profiles(profiles: dict) -> dict:
    """Профили каждой модели на её самой свежей паре (протокол, контракт);
    на каждом уровне thinking — отдельный элемент списка (усреднять уровни
    размышлений и версии контракта нельзя)."""
    prank = {"v1": 1, "v2": 2}
    latest = {}
    for (model, protocol, thinking, pv), prof in profiles.items():
        latest.setdefault(model, []).append((protocol, thinking, pv, prof))
    out = {}
    for model, entries in latest.items():
        top = max(prank.get(pr, 9) for pr, _th, _pv, _p in entries)
        out[model] = sorted(
            ((pr, th, pv, p) for pr, th, pv, p in entries
             if prank.get(pr, 9) == top),
            key=lambda e: (e[2] != max(x[2] for x in entries), e[1] != "max", e[1]))
    return out


def num(v) -> str:
    return f"{v:g}" if isinstance(v, (int, float)) else "—"


def delta_class(d: float) -> str:
    return "p-good" if d == 0 else ("p-warn" if d <= 1 else "p-bad")


def runs_cell(vals: list) -> str:
    return " · ".join(num(v) for v in vals)


def score_bar(v: float, vmax: float = 4.0) -> str:
    pct = max(3, round(100 * (v or 0) / vmax))
    return (f'<div class=barwrap><div class=bar style="width:{pct}%;background:var(--acc)"></div>'
            f'<span>{num(v)}</span></div>')


def esc(s) -> str:
    return (str(s).replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;"))


# --- секции -----------------------------------------------------------------

def section_header(n_runs: int) -> str:
    return f"""
<header>
<h1>Этап 3: код-ревью решений этапа 2 локальными моделями</h1>
<p>На этапе 2 модели выполняли тестовое задание «биллинг LLM-токенов» в среде pi.
На этапе 3 другие модели проверяют эти решения в роли код-ревьюеров. Каждое
ревью — отдельная сессия pi; всего прогонов в отчёте: {n_runs}.</p>
<p>Что получает ревьюер: текст задания (TASK.md), памятку кандидата (AGENTS.md),
код решения целиком и метрики качества (phpstan, phpmd, phpcs,
psalm, scc, сводка по классам). Чего ревьюер не получает: имя модели-автора и
результатов проверки — чекера, эталонных файлов, скрытых фикстур. Метрики
показывают, куда смотреть; каждый сигнал ревьюер обязан проверить по коду, а не
пересказать.</p>
<p>Что делает ревьюер: читает задание и код, проверяет выполнение требований по
коду, проверяет сигналы метрик, ставит три балла по шкале 0–4 (A — код и
архитектура, B — процесс и коммуникация, F — соответствие заданию) и
перечисляет найденные дефекты с файлами и строками. Исполнять и изменять код
нельзя: инструмент в pi — только чтение файлов. Целостность копии проверяется
хэш-суммами всех файлов до и после прогона; расхождение — прогон отбраковывается.</p>
<p>Отчёт собран из артефактов прогонов: models/&lt;модель&gt;/backend/review/run-XX/.</p>
</header>
<nav class=toc>
<div class=toc-title>Содержание</div>
<ul>
<li><a href="#tldr">1. Главные выводы</a></li>
<li><a href="#method">2. Методика</a></li>
<li><a href="#objects">3. Объекты ревью и эталоны</a></li>
<li><a href="#results">4. Результаты моделей</a></li>
<li><a href="#stability">5. Повторяемость</a></li>
<li><a href="#findings">6. Что нашли и что пропустили</a></li>
<li><a href="#notdone">7. Функциональная проверка: not_done против чекера</a></li>
<li><a href="#validity">8. Валидность прогонов</a></li>
<li><a href="#runs">9. Прогоны и данные</a></li>
<li><a href="#next">10. Что дальше</a></li>
</ul>
</nav>"""


def section_tldr(runs, profiles, refs, grid) -> str:
    n_valid = sum(1 for r in runs if r["valid"])
    n_all = len(runs)
    latest = latest_profiles(profiles)
    blind = []
    for model, entries in latest.items():
        _protocol, _th, _pv, prof = entries[0]
        t = prof["per_slug"].get("tricky")
        if t:
            blind.append((model, t["vals"]["axis_a"]))
    blind_txt = "; ".join(f"{SHORT.get(m, m)} — {runs_cell(v)}" for m, v in blind)
    orca_n = sum(1 for r in runs if r["model"] == "orcarouter/Qwen3.8-27B-Uncensored:ctx100k")
    return f"""
<h2 id=tldr><span class=no>1.</span>Главные выводы</h2>
<p class=lead>Точнее всех к эталону — huihui: на «хорошем» решении A 3.5 (эталон 3.5),
на «среднем» A 2.5 (эталон 2.5). Обе сборки gemma завышают: «хорошему» и «каверзному»
ставят A 4.0 при эталонах 3.5 и 2.5. Разницу между «хорошим» и «плохим» решением в баллах
показывают huihui (3.5 → 2.5) и qwen3.8 (3.5 → 2.5); gemma4:26b сжимает её до 1–1.5 балла
(4.0 → 2.5–3).</p>
<p class=lead>Повторяемость высокая у всех моделей: в трёх независимых прогонах разброс
не превысил 1 балла ни по одной оси, в среднем 0–0.3 балла. Вердикт ревьюера —
устойчивый результат; трёх прогонов достаточно.</p>
<p class=lead>Главный дефект «каверзного» решения не нашёл ни один ревьюер. Причина
дефекта: оф-пиковая скидка не записывается при первом импорте провайдера, а проверка
всегда стартует с пустой базы, где провайдеров ещё нет. Пример расчёта из TASK.md при
этом сходится, поэтому ошибка не видна при чтении кода. Тот же дефект пропустил и
судья яруса 4, поставив решению A 4.0. Баллы ревьюеров по A (эталон 2.5):
{esc(blind_txt)}.</p>
<p class=lead>Не все модели выдержали порядок ревью. orca во всех {orca_n} прогонах
(протоколы v1 и v2) читала файлы и писала разборы, но ни разу не выдала вердикт в
заданном формате JSON — свой формат со шкалой 0–5. e2b в пилоте трижды выдала
вердикт, не прочитав ни одного файла. Такие прогоны отбраковываются: из {n_all}
прогонов зачётных {n_valid}.</p>
<p class=lead>Эталон тоже ошибался. В эталоне «плохого» решения дефектом значилось
«усечение вместо HALF_UP». Ревью qwen пересчитало пример из задания и показало, что
округление работает. Проверка на php 8.5 подтвердила ревью. Настоящие причины неверных
отчётов: оф-пиковая скидка не записывается при первом импорте провайдера (оба решения
с падающими отчётами) и bcadd без масштаба в накоплении (только «плохое»).
Эталон-карточки исправили; эталонные баллы не меняли.</p>
<p class="excap sb">Наблюдения</p>
<div class=cards>
<div class="card g"><b>Ревью в 7–10 раз быстрее написания кода</b>Сессия ревью занимает
от 0.5 до 6.5 минут (0.3–1 млн токенов); сессия написания решения на этапе 2 —
25–45 минут. Серия из 48 сессий заняла около трёх часов серверного времени.</div>
<div class="card"><b>Писать код и проверять код — разные навыки</b>huihui — лидер этапа 2
(13 из 13 сценариев) и здесь точнее всех. orca на этапе 2 тоже набрала 13 из 13, но как
ревьюер ни разу не выдала вердикт в заданном формате. gemma4:26b на этапе 2 писала
средне, здесь завышает баллы всем решениям.</div>
<div class="card w"><b>Единственный крупный промах — вывод из типа параметра</b>На
«среднем» решении qwen назвала критическим вызов $output-&gt;getErrorOutput(): у
OutputInterface такого метода нет. Но Symfony передаёт в команду ConsoleOutput, у
которого метод есть, — решение работало (чекер 11 из 11). Балл F 2.5 при эталоне 4.0.</div>
<div class="card b"><b>gemma4:31b наказывает отсутствие артефактов минимумом шкалы</b>За
отсутствующие COMMIT_MESSAGE.md и PR_DESCRIPTION.md она ставит «плохому» решению B 0 во
всех трёх прогонах — эталон 1.0, судья строже. На остальных решениях её B 3.5–4:
занижения дальше нет.</div>
</div>"""


def section_method() -> str:
    return """
<h2 id=method><span class=no>2.</span>Методика</h2>
<p class=lead>Полные правила — <code>reviewer/REVIEWER.md</code>. Коротко:</p>
<table>
<tr><th style=width:28%>Элемент</th><th>Решение</th></tr>
<tr><td><b>Что скрыто от ревьюера</b></td><td>Имя модели-автора, результаты проверки (чекер, эталонные файлы, скрытые фикстуры), чужие разборы этого решения. Знание вердикта чекера превратило бы ревью в пересказ.</td></tr>
<tr><td><b>Что выдано</b></td><td>TASK.md, AGENTS.md (памятка, которую получал автор), дерево решения целиком, метрики качества, список файлов. Из metrics.json вырезаны все пути — по ним можно было бы вычислить автора.</td></tr>
<tr><td><b>Метрики</b></td><td>Выдаются как указатели, где искать проблемы. Инструкция: каждый сигнал проверить по коду и сообщить, что подтвердилось и что опроверглось, — не пересказывать цифры.</td></tr>
<tr><td><b>Три шкалы</b></td><td>A «код и архитектура», B «процесс и коммуникация», F «соответствие заданию». Все 0–4 с шагом 0.5; дескрипторы — рубрика яруса 3, переписанная без ссылок на чекер.</td></tr>
<tr><td><b>Дефекты</b></td><td>Список с файлом, строками и серьёзностью (critical/major/minor). При сомнении — minor и формулировка «подозрение». Верно коду, а не комментариям и PR-описаниям.</td></tr>
<tr><td><b>Запрет правок и запуска</b></td><td>pi запускается с единственным инструментом — чтением файлов; edit, write, bash отключены. В копии нет vendor — код нечем исполнить. Хэш-суммы всех файлов до и после прогона; изменение — прогон отбраковывается.</td></tr>
<tr><td><b>Изоляция прогонов</b></td><td>Каждый прогон получает свой временный каталог с копией материалов и свой pi-home. После прогона модель выгружается из памяти сервера: следующий прогон не наследует от предыдущего ничего.</td></tr>
<tr><td><b>Сравнение с эталоном</b></td><td>Разница |Δ| между баллом ревьюера и эталонным баллом по каждой шкале. Черновик зачёта: |Δ| ≤ 0.5 — зачёт, ≤ 1.0 — частично, выше — промах. Спорные расхождения проверяются кодом и рантаймом (раздел 6.2).</td></tr>
<tr><td><b>Версии протокола</b></td><td><b>v1</b> — серия от 2026-10-31: помимо решения в материалах лежал diff.patch как вспомогательный файл (56 прогонов, каталог review/). <b>v2</b> — текущий: решение целиком, без диффа (каталог review-v2/). Версия указана в run.json каждого прогона; в разделе 4 таблицы разбиты по версиям — старые данные сохранены для сравнения.</td></tr>
</table>"""


def section_objects(refs) -> str:
    rows = []
    for slug in SLUG_ORDER:
        c = refs[slug]
        b = c["basis"]
        e = c["expected"]
        fails = "; ".join(f["scenario"] for f in c.get("checker_failures", [])) or "—"
        rows.append(
            f"<tr><td><b>{SLUG_TITLES[slug]}</b><div class=sub>{esc(c['source_run'].split('/backend/')[0].split('models/')[1])}</div></td>"
            f"<td>{esc(b['checker'])}</td>"
            f"<td class=num>A {num(b['tier3']['axis_a'])} · B {num(b['tier3']['axis_b'])}<div class=sub>ярус 4 (без результатов проверки): A {num(b['tier4_blind']['axis_a'])}</div></td>"
            f"<td class=num>{num(e['axis_a'])} · {num(e['axis_b'])} · {num(e['functional'])}"
              f"<div class=sub>F детерминированный: {num(c['f_det'])} "
              f"(floor(8×пройдено/всего)/2 по чекеру)</div></td>"
            f"<td>{esc(fails)}</td></tr>")
    return f"""
<h2 id=objects><span class=no>3.</span>Объекты ревью и эталоны</h2>
<p class=lead>Объекты ревью — четыре решения этапа 2 разных уровней. «Хорошее» прошло
все 13 сценариев проверки (11 обязательных и 2 бонусных). «Среднее» прошло все 11
обязательных, бонусные не делало; архитектурно слабое. «Каверзное» не прошло два
обязательных сценария: его отчёты завышают расходы — дефект проявляется только на данных
проверки, пример из задания сходится. «Плохое» не прошло три обязательных сценария,
в том числе обязательные артефакты (COMMIT_MESSAGE.md, PR_DESCRIPTION.md). Эталон-карточки
заморожены 2026-10-30; после пилота в них исправлены описания причин дефектов
(изменения записаны в changelog карточек), эталонные баллы не менялись.</p>
<table>
<tr><th>Решение</th><th>Чекер</th><th>Судьи (A/B)</th><th class=narrow>Эталон A·B·F</th><th>Упавшие сценарии</th></tr>
{''.join(rows)}
</table>
<p class=sub>Судьи: ярус 3 — GLM-5.3-Flash, которому выдали результаты чекера и метрики;
ярус 4 — то же ревью без результатов проверки, только по диффу. На «каверзном» и «плохом»
решениях ярус 4 завысил A (4.0 и 2.5). Причины дефектов в эталоне верифицированы кодом и
рантаймом (раздел 6).</p>"""


def _runs_word(n: int) -> str:
    if n % 10 == 1 and n % 100 != 11:
        return f"{n} зачётный прогон"
    if n % 10 in (2, 3, 4) and n % 100 not in (12, 13, 14):
        return f"{n} зачётных прогона"
    return f"{n} зачётных прогонов"


def _quality_level(overall: float) -> str:
    if overall <= 0.6:
        return '<span class="okmark" title="средний |Δ| по всем осям и решениям">высокая точность</span>'
    if overall <= 0.9:
        return '<span class="deadmark contract" title="средний |Δ| по всем осям и решениям">средняя точность</span>'
    return '<span class="deadmark fab" title="средний |Δ| по всем осям и решениям">низкая точность</span>'


def results_summary_block(profiles, refs, runs) -> str:
    """Сводная таблица: плоский рейтинг строк (модель × мышление) текущего
    контракта. Итог — среднее из трёх объективных компонентов: |Δ|A и |Δ|B
    (отличие от судьи яруса 3) и доля непойманных реальных пропусков решения
    (1 − recall по not_done против фактов чекера). «Место» — порядковый
    номер строки в этом рейтинге."""
    latest = latest_profiles(profiles)

    def axis_overall(prof):
        """Средний |Δ| по каждой оси (усреднение по решениям) и общий."""
        axis_delta = {a: statistics.mean(s["delta_mean"][a] for s in prof["per_slug"].values())
                      for a in AXES}
        return axis_delta, statistics.mean(axis_delta.values())

    def resources(model):
        """Медианы времени (мин), токенов (М) и стоимости (₽) — отдельно по
        каждой тройке (версия протокола, thinking, контракт): вход ревьюера
        различается, усреднять нельзя."""
        out = {}
        for protocol in ("v1", "v2"):
            for thinking in sorted({r["thinking"] for r in runs if r["model"] == model}):
                for pv in sorted({r["prompt_version"] for r in runs
                                  if r["model"] == model and r["protocol"] == protocol
                                  and r["thinking"] == thinking}):
                    rs = [r for r in runs if r["model"] == model
                          and r["protocol"] == protocol and r["thinking"] == thinking
                          and r["prompt_version"] == pv
                          and r["duration_s"] and r["tokens"]]
                    if not rs:
                        continue
                    med_d = statistics.median(r["duration_s"] for r in rs)
                    med_t = statistics.median(r["tokens"] for r in rs)
                    out[(protocol, thinking, pv)] = (med_d / 60, med_t / 1e6,
                                                     med_d / 3600 * LOCAL_RUB_H)
        return out

    def res_cell(res):
        if not res:
            return "<td class=num>—</td>"
        return (f"<td class=num>{res[0]:.1f}</td>"
                f"<td class=num>{res[1]:.2f}</td>"
                f"<td class='num p-warn'>₽{res[2]:.1f}</td>")

    def res_cells_stacked(res_by_key):
        """Ресурсы по сериям в одной ячейке: строка на ключ."""
        if not res_by_key:
            return '<td colspan=3 class=num>—</td>'
        cells = []
        for i, fmt in ((0, "{:.1f}"), (1, "{:.2f}"), (2, "₽{:.1f}")):
            lines = "".join(
                f'<div class=sub>{pr}/{th}{"" if pv == 2 else "/п1"}: {fmt.format(res[i])}</div>'
                for (pr, th, pv), res in sorted(res_by_key.items()))
            cells.append(f"<td class=num>{lines}</td>")
        return "".join(cells)

    def stack_cell(prof, slug):
        """Средние A/B/F по решениям — стопкой в одной ячейке."""
        s = prof["per_slug"].get(slug)
        if not s:
            return '<td class=num>—</td>'
        marks = "".join(
            f'<div class="stack{" b" if a == "axis_a" else ""}">'
            f'{AXIS_NAMES_SHORT[a]} {f"{statistics.mean(vals):.2f}".rstrip("0").rstrip(".")}</div>'
            for a in AXES
            for vals in [s["vals"][a]])
        return f'<td class=num>{marks}</td>'

    # recall: семьи реальных пропусков решений, перечисленные моделью
    # в not_done зачётных сессий (по уровням thinking, контракт с not_done).
    # Два уровня учёта: fam_caught — совпадение семьи по ключевым словам;
    # caught — только подтверждённые по механизму (ручная разметка
    # MECH_CAUGHT: сверка каждого упоминания с разделом 6 и кодом решения).
    caught, fam_caught = defaultdict(set), defaultdict(set)
    for rp in sorted((ROOT / "models").glob("*/backend/review-v3*/run-*/review.json")):
        rj = rp.with_name("run.json")
        if not rj.is_file():
            continue
        meta = json.loads(rj.read_text(encoding="utf-8"))
        if not meta.get("parse_ok") or meta.get("protocol") != "v2" \
                or meta.get("prompt_version") != 2:
            continue
        slug = meta.get("solution_slug")
        v = json.loads(rp.read_text(encoding="utf-8"))
        key = (meta.get("model"), meta.get("thinking"))
        # «поймано» = семья названа в not_done ИЛИ упомянута в defects
        # (любая серьёзность — для recall важен факт «заметил область»;
        # упоминание только в note — неподтверждённая проза, не считается)
        items = [(nd.get("req", "") or nd.get("desc", ""))
                 for nd in v.get("not_done", [])] \
            + [json.dumps(d, ensure_ascii=False) for d in v.get("defects", [])]
        for txt in items:
            fam = req_family(txt)
            if fam in REAL_MISSES.get(slug, []):
                fam_caught[key].add((slug, fam))
                if (slug, fam) in MECH_CAUGHT.get(key, set()):
                    caught[key].add((slug, fam))

    # профили по сериям: рейтинг — текущий контракт (протокол v2, контракт с
    # not_done); серии v1 и прежний контракт — для сравнения
    v2_levels = defaultdict(dict)   # thinking -> {model: prof}
    comparison = defaultdict(list)  # подпись серии -> [(model, thinking, prof)]
    for (model, protocol, thinking, pv), prof in profiles.items():
        if protocol == "v2" and pv == 2:
            v2_levels.setdefault(thinking, {})[model] = prof
        else:
            note = (f"протокол {protocol} (с diff.patch — для сравнения)"
                    if protocol == "v1" else
                    "протокол v2 · прежний контракт (без перечня not_done)")
            comparison[note].append((model, thinking, prof))

    def combined(model, thinking, prof):
        """Итог ранжирования: среднее из |Δ|A, |Δ|B и доли непойманных
        реальных пропусков (1 − recall). Все три компонента объективны:
        первые два — судья яруса 3, третий — чекер."""
        d = axis_overall(prof)[0]
        miss = 1 - len(caught.get((model, thinking), set())) / REAL_MISSES_TOTAL
        return statistics.mean([d["axis_a"], d["axis_b"], miss]), miss, d

    ranked = []
    for thinking, models in v2_levels.items():
        for model, prof in models.items():
            c, miss, d = combined(model, thinking, prof)
            ranked.append({"model": model, "thinking": thinking, "prof": prof,
                           "combined": c, "miss": miss, "d": d,
                           "caught": len(caught.get((model, thinking), set())),
                           "famcaught": len(fam_caught.get((model, thinking), set())),
                           "res": resources(model).get(("v2", thinking, 2))})
    ranked.sort(key=lambda e: e["combined"])

    def row_html(e, place):
        prof, d = e["prof"], e["d"]
        model, thinking = e["model"], e["thinking"]
        note = ("протокол v2 · thinking max" if thinking == "max"
                else f"протокол v2 · thinking {thinking}")
        return (
            f"<tr class={'top' if place == 1 else ''}>"
            f"<td class=num>{place}</td>"
            f"<td><b>{esc(SHORT.get(model, model))}</b>"
            f"<div class=sub>{note} · {_runs_word(prof['n'])}</div></td>"
            f"<td class=num>{thinking}</td>"
            + "".join(stack_cell(prof, slug) for slug in SLUG_ORDER)
            + "".join(f"<td class='num {delta_class(d[a])}'>{d[a]:.2f}</td>" for a in AXES)
            + f"<td class=num>{e['caught']} из {REAL_MISSES_TOTAL}"
              f"<div class=sub>по семьям: {e['famcaught']}</div></td>"
            + f"<td class=num><b>{e['combined']:.2f}</b></td>"
            + res_cell(e["res"])
            + f"<td>{_quality_level(e['combined'])}</td></tr>")

    rows = []
    for i, e in enumerate(ranked, 1):
        rows.append(row_html(e, i))
    # сравнительные серии: v1 и прежний контракт — вне рейтинга
    for note, items in comparison.items():
        for model, thinking, prof in items:
            d, o = axis_overall(prof)
            rows.append(
                f"<tr class=neg><td class=num>↳</td>"
                f"<td>{esc(SHORT.get(model, model))}"
                f"<div class=sub>{note} · thinking {thinking} · "
                f"{_runs_word(prof['n'])}</div></td>"
                f"<td class=num>{thinking}</td>"
                + "".join(stack_cell(prof, slug) for slug in SLUG_ORDER)
                + "".join(f"<td class='num {delta_class(d[a])}'>{d[a]:.2f}</td>" for a in AXES)
                + "<td class=num title=\"прежний контракт: перечень not_done не собирался\">—</td>"
                + f"<td class=num>{o:.2f}</td>"
                + res_cell(resources(model).get((
                    "v1" if "протокол v1" in note else "v2", thinking,
                    1 if "прежний" in note else 2)))
                + "<td><span class=sub>—</span></td></tr>")

    fail_rows = []
    orca_model = "orcarouter/Qwen3.8-27B-Uncensored:ctx100k"
    orca_res = resources(orca_model)
    orca_n = sum(1 for r in runs if r["model"] == orca_model)
    orca_parts = ", ".join(
        f"{len([r for r in runs if r['model'] == orca_model and r['protocol'] == pr])} {pr}"
        for pr in ("v1", "v2")
        if any(r["model"] == orca_model and r["protocol"] == pr for r in runs))
    fail_rows.append(
        f"<tr class=neg><td class=num>—</td>"
        f"<td><b>orca</b><div class=sub>{orca_n} прогонов: {orca_parts}</div></td>"
        f"<td class=num>max/low/<br>medium</td>"
        + "<td colspan=4>—</td>" + res_cells_stacked(orca_res)
        + "<td colspan=2><span class=sub>вне рейтинга: вердикт в заданном формате не выдан ни разу</span></td></tr>")

    def th_stack(slug):
        e = refs[slug]["expected"]
        return (f'<th class=narrow>{SLUG_TITLES[slug]}'
                f'<div class=sub>этал. A {e["axis_a"]:g} · B {e["axis_b"]:g} · F {e["functional"]:g}</div></th>')
    head = ("<tr><th class=narrow>Место</th><th>Модель</th>"
            "<th class=narrow>Мышление</th>"
            + "".join(th_stack(s) for s in SLUG_ORDER)
            + ("<th class=narrow>|Δ|A</th><th class=narrow>|Δ|B</th><th class=narrow>|Δ|F</th>"
               "<th class=narrow>Поймано пропусков (механизм)</th>"
               "<th class=narrow>Итог</th>"
               "<th class=narrow>Мин (мед.)</th><th class=narrow>М ток. (мед.)</th>"
               "<th class=narrow>₽/ревью</th>"
               "<th>Уровень качества ревью</th></tr>"))
    return f"""
<h3><span class=no>4.1.</span>Сводная таблица: качество ревью</h3>
<p class=lead>Плоский рейтинг строк текущего контракта (протокол v2, контракт
с перечнем not_done), отсортированных по итогу (лучшие сверху); «Место» —
порядковый номер строки. В колонках решений — средние баллы по трём прогонам,
стопкой A / B / F (эталоны — в шапке колонок). <b>Итог — среднее трёх
объективных компонентов:</b> |Δ|A и |Δ|B — отличие от судьи (ярус 3,
GLM-5.3-Flash с данными чекера) по его вердиктам; «Поймано пропусков» —
сколько из реальных функциональных пропусков решения (по фактам чекера и
расследованию раздела 6: на «каверзном» — оф-пик при первом импорте, на
«плохом» — артефакты, оф-пик, пустые миграции, bcadd без масштаба) модель
назвала <b>с реальным механизмом</b> — не просто слово из той же области,
а сам дефект, который роняет сценарий чекера (сверка каждого упоминания
вручную, разметка в коде отчёта). Примеры, которые в зачёт не идут:
заметка про таймзону оф-пик-окна — другой дефект той же области;
bcRoundHalfUp для отрицательных сумм — в домене суммы неотрицательны;
оставленный debug_round.php — не функциональный дефект. Число под ним —
совпадения по семьям без сверки механизма. Уровень: до 0.6 — высокая
точность, до 0.9 — средняя, выше — низкая. Уровни мышления, версии
протокола и контракта не усредняются — серии с другими условиями идут
отдельными строками вне рейтинга (строки v1 и прежнего контракта не могли
перечислять not_done). Стоимость локального инференса — электричество и
амортизация (₽35.85/ч, методика этапов 1–2). По-прогонные данные — в
таблицах 4.2 и далее.</p>
<table>{head}{''.join(rows)}{''.join(fail_rows)}</table>"""


def section_results(profiles, refs, runs) -> str:
    blocks = []
    head = ("<tr><th>Решение</th><th>n</th>"
            "<th>A «код и архитектура»</th><th>B «процесс»</th><th>F «задание»</th>"
            "<th class=narrow>|Δ|A</th><th class=narrow>|Δ|B</th><th class=narrow>|Δ|F</th><th>Разброс A/B/F</th></tr>")
    n_shown = 0
    for model in MODEL_ORDER:
        proto_keys = sorted(
            {(pr, th, pv) for (m, pr, th, pv) in profiles if m == model},
            key=lambda k: ({"v1": 1, "v2": 2}.get(k[0], 9), k[2], k[1] != "max", k[1]))
        for protocol, thinking, pv in proto_keys:
            prof = profiles.get((model, protocol, thinking, pv))
            if not prof:
                continue
            n_shown += 1
            rows = []
            for slug in SLUG_ORDER:
                s = prof["per_slug"].get(slug)
                if not s:
                    continue
                e = refs[slug]["expected"]
                a, b, f = (AXES[0], AXES[1], AXES[2])
                dcls = [delta_class(s["delta_mean"][x]) for x in AXES]
                spread = f"{num(s['spread'][a])} / {num(s['spread'][b])} / {num(s['spread'][f])}"
                rows.append(
                    f"<tr><td><b>{SLUG_TITLES[slug]}</b></td><td class=num>{s['n']}</td>"
                    f"<td>{runs_cell(s['vals'][a])}</td><td>{runs_cell(s['vals'][b])}</td><td>{runs_cell(s['vals'][f])}</td>"
                    f"<td class='num {dcls[0]}'>{s['delta_mean'][a]:.2f}</td>"
                    f"<td class='num {dcls[1]}'>{s['delta_mean'][b]:.2f}</td>"
                    f"<td class='num {dcls[2]}'>{s['delta_mean'][f]:.2f}</td>"
                    f"<td class=num>{spread}</td></tr>")
            params = PARAMS.get(model, ("?", "?"))
            blocks.append(f"""
<h3><span class=no>4.{n_shown + 1}.</span>{esc(SHORT.get(model, model))} — протокол {protocol}
<span class=sub>{params[0]} · {params[1]} · thinking {thinking} · контекст 102400 · контракт {pv} · {_runs_word(prof['n'])}</span></h3>
<table>{head}{''.join(rows)}</table>""")
    summary = results_summary_block(profiles, refs, runs)
    return f"""
<h2 id=results><span class=no>4.</span>Результаты моделей</h2>
{summary}
<p class=lead>Детали по каждой модели: вердикты по прогонам (каждая ячейка — отдельный
изолированный прогон), средние |Δ| к эталону и разброс max−min. Цвет |Δ|: зелёный — 0,
жёлтый — ≤1, красный — &gt;1.</p>
{''.join(blocks)}"""


def _hbars_svg(rows: list[tuple[str, float]], vmax: float, title: str) -> str:
    W, LH = 620, 34
    H = 30 + LH * len(rows)
    # атрибуты svg — в кавычках: незакавыченный `opacity=.55/` съедает слеш,
    # тег не закрывается и глотает остальные строки графика
    parts = [f'<div class=chartbox><svg width="100%" viewBox="0 0 {W} {H}" '
             f'role="img" aria-label="{esc(title)}">']
    parts.append(f'<text x="0" y="16" font-size="12" fill="#66707f">{esc(title)}</text>')
    for i, (label, v) in enumerate(rows):
        y = 30 + i * LH
        w = max(3, (W - 260) * v / vmax)
        parts.append(f'<text x="0" y="{y+15}" font-size="13" fill="#1d2433">{esc(label)}</text>')
        parts.append(f'<rect x="230" y="{y+3}" width="{w:.0f}" height="20" rx="5" '
                     f'fill="#315f9e" opacity="0.55"/>')
        parts.append(f'<text x="{236+w:.0f}" y="{y+17}" font-size="12.5" font-weight="600" '
                     f'fill="#1d2433">{v:.2f}</text>')
    parts.append('</svg></div>')
    return ''.join(parts)


def section_stability(profiles) -> str:
    multi = defaultdict(int)
    for key in profiles:
        multi[key[0]] += 1
    rows = sorted(((SHORT.get(m, m) + (f" · {pr}/к{pv}" if multi[m] > 1 else ""),
                    p["spread_mean"])
                   for (m, pr, _th, pv), p in profiles.items()), key=lambda x: x[1])
    chart = _hbars_svg(rows, 0.5, "Средний разброс вердикта между прогонами (оси A/B/F вместе), баллов")
    cards = f"""
<div class="card g"><b>Трёх прогонов достаточно</b>В среднем по моделям и решениям разброс
составил 0–0.3 балла, максимум по одной оси — 1 балл. Пример устойчивости: huihui поставила
«плохому» решению A 2.5 во всех трёх прогонах.</div>
<div class="card"><b>Стабильный балл не значит правильный</b>gemma4:26b во всех трёх
прогонах ставит «хорошему» и «каверзному» A 4.0 при эталонах 3.5 и 2.5. Завышение
устойчиво и видно только при сравнении с эталоном.</div>
<div class="card w"><b>Прогоны не влияют друг на друга</b>У каждой сессии своя копия
материалов и свой pi-home; после сессии модель выгружается из памяти сервера. Разброс
между прогонами — вариативность сэмплирования самой модели.</div>"""
    return f"""
<h2 id=stability><span class=no>5.</span>Повторяемость</h2>
<p class=lead>Каждая пара «модель × решение» ревьюилась тремя независимыми прогонами
(qwen3.8 — один пилотный прогон; у gemma4:26b на «среднем» — два, две сессии не выдали
ответ). Разброс max−min по осям — в таблицах раздела 4, свод по моделям — ниже.</p>
{chart}
<div class=cards>{cards}</div>"""


def section_findings(runs, profiles) -> str:
    blind = []
    for model, prof in profiles.items():
        t = prof["per_slug"].get("tricky")
        if t:
            blind.append(f"<b>{SHORT.get(model, model)}</b> — A {runs_cell(t['vals']['axis_a'])} "
                         f"(эталон 2.5), F {runs_cell(t['vals']['functional'])}")
    return f"""
<h2 id=findings><span class=no>6.</span>Что нашли и что пропустили</h2>
<h3><span class=no>6.1.</span>Дефект, который не нашло ни одно ревью</h3>
<p class=lead>На «каверзном» решении отчёты завышают расходы относительно эталонных
(чекер: 9 из 11 обязательных сценариев). Причина, установленная при разборе: оф-пиковая
скидка не записывается при первом импорте провайдера — вызов setOffPeak() стоит только в
ветке обновления существующего провайдера, а проверка стартует с пустой базы. События не
получают скидку 0.5, и стоимости завышены ровно на снятые скидки. Пример расчёта из
TASK.md при самопроверке у автора сходился: импорт выполнялся дважды, и второй запуск
проходил по ветке обновления, где скидка записывается. Проверка импортирует цены один раз.</p>
<p class=lead>Баллы ревьюеров по оси A: {'; '.join(blind)}. Судья яруса 4, которому не
выдали результаты проверки, поставил этому решению A 4.0. Причину нашёл только судья
яруса 3, который располагал данными чекера. Урок для следующих этапов: дефект в путях
записи данных («первая запись против обновления») по коду функции не виден — он виден
только при сопоставлении с независимым расчётом.</p>
<h3><span class=no>6.2.</span>Ревью нашло ошибку в эталоне</h3>
<p class=lead>В эталоне «плохого» решения (вердикт яруса 3) значился дефект «усечение
вместо HALF_UP». Ревью qwen пересчитало пример из задания и заявило, что округление
работает. Проверка на php 8.5 подтвердила ревью:
<code>bcadd('14.26249875','0.0000005',6)</code> возвращает '14.262499' — операнды не
усекаются, округление HALF_UP выполняется. Настоящие причины неверных отчётов:
оф-пиковая скидка не записывается при первом импорте (оба решения с падающими отчётами —
один и тот же класс ошибки) и bcadd без указания масштаба в накоплении, из-за которого
сумма усекается до целых (только «плохое»; сценарий h02: 3.312495 против эталонных
3.312496). Эталон-карточки исправлены, изменения записаны в changelog карточек;
эталонные баллы не менялись.</p>
<h3><span class=no>6.3.</span>Ошибочный вывод из типа параметра</h3>
<p class=lead>Единственный крупный промах по баллам: на «среднем» решении qwen поставила
F 2.5 при эталоне 4.0, назвав критическим вызов $output-&gt;getErrorOutput() — у
OutputInterface такого метода нет. Наблюдение о типе верное, вывод о неработающем коде
неверный: Symfony передаёт в команду ConsoleOutput, у которого метод есть. Решение
работало — чекер этого решения прошёл 11 из 11 сценариев. Урок записан для будущей
серии ревью (новая версия контракта): промпт текущей серии заморожен правилом из
REVIEWER.md — менять его задним числом нельзя, сравнимость прогонов важнее
точечных уточнений.</p>
<h3><span class=no>6.4.</span>Что ревьюеры нашли, кроме баллов</h3>
<p class=lead>Сессии с самой полной проверкой: qwen на «хорошем» решении пересчитала
пример из задания, сверила счётчик тестов phpunit с заявкой в PR_DESCRIPTION, опровергла
показатель LCOM4 из метрик («артефакт value-объектов») и нашла фактическую ошибку в
PR_DESCRIPTION — ссылку на несуществующий файл expected_report.csv; huihui и gemma4:31b
независимо указали на несоответствие формата cost_usd и на отладочный мусор в диффе.
Полные списки дефектов с серьёзностью — в review.json каждого прогона (раздел 8).</p>"""


def section_notdone(profiles, refs, runs) -> str:
    """Функциональная проверка: перечни not_done моделей против фактов чекера."""
    # семьи требований по ключевым словам перечня
    def family(req: str) -> str:
        r = req.lower()
        if "миграц" in r or "чистой базе" in r or "чистая база" in r or "схема бд" in r:
            return "схема БД из миграций"
        if "commit_message" in r or "pr_description" in r or "артефакт" in r:
            return "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)"
        if "unrated" in r and ("stderr" in r or "строк" in r or "sboй" in r or "сбой" in r):
            return "unrated-строки в stderr"
        if "geterroroutput" in r:
            return "unrated-строки в stderr"
        if "reprice" in r or "ретро" in r:
            return "бонус reprice"
        if "округл" in r or "half_up" in r or "bcadd" in r or "точность вычислений" in r or "промежуточные вычисления" in r:
            return "округление и точность денег"
        if "дедупл" in r or "повторная доставка" in r or "идемпотентность" in r:
            return "дедупликация приёма"
        if "оф-пик" in r or "off_peak" in r or "скидк" in r:
            return "оф-пиковая скидка"
        if "сортиров" in r:
            return "сортировка отчёта"
        if "6 знаков" in r or "cost_usd" in r or "формат" in r and "отч" in r:
            return "формат отчёта"
        if "cache" in r or "кеш" in r or "кэш" in r:
            return "cache-цены"
        return "прочее"

    # вердикты сверки: (семья) -> (итог, пояснение) — присвоены по результатам
    # анализа: факты чекера + рантайм-разбор (см. раздел 6)
    VERDICTS = {
        "good": {
            "схема БД из миграций": ("не подтверждено", "чекер прошёл все сценарии (11/11)"),
            "unrated-строки в stderr": ("не подтверждено", "чекер сверил строки stderr и принял"),
        },
        "mid": {
            "схема БД из миграций": ("не подтверждено", "чекер прошёл: принял альтернативный способ schema_update, разрешённый заданием"),
            "unrated-строки в stderr": ("ложное", "рантайм работает: Symfony передаёт ConsoleOutput, у которого метод есть"),
            "бонус reprice": ("подтверждено частично", "бонус действительно не реализован, но он не входит в обязательные"),
            "дедупликация приёма": ("не подтверждено", "сценарий дубликатов чекером пройден"),
            "сортировка отчёта": ("не подтверждено", "отчёты чекером приняты"),
            "формат отчёта": ("не подтверждено", "отчёты чекером приняты"),
        },
        "tricky": {
            "округление и точность денег": ("не подтверждено", "формула воспроизводит пример из задания; причина падений — оф-пик"),
            "формат отчёта": ("не подтверждено", "сценарий с пустым отчётом в чекере отсутствует"),
            "схема БД из миграций": ("не подтверждено", "чекер прошёл схему (fallback schema_update не при чём — суммы завышены оф-пиком)"),
            "unrated-строки в stderr": ("не подтверждено", "unrated-сценарий чекером пройден"),
        },
        "bad": {
            "артефакты (COMMIT_MESSAGE/PR_DESCRIPTION)": ("подтверждено", "сценарий artifacts_present упал: файлов нет"),
            "схема БД из миграций": ("подтверждено как факт", "миграций действительно нет; чекер прошёл через fallback schema_update"),
            "оф-пиковая скидка": ("подтверждено", "верифицированная причина завышенных отчётов (раздел 6)"),
            "округление и точность денег": ("подтверждено", "рантайм-трассировка h02: bcadd без масштаба усёк накопление"),
            "дедупликация приёма": ("не подтверждено как причина", "base и duplicates упали одинаково — падение в оф-пике"),
            "unrated-строки в stderr": ("ложное", "рантайм работает: у ConsoleOutput метод есть"),
            "бонус reprice": ("не подтверждено", "бонус не входит в обязательные — отсутствие не влияло на чекер"),
        },
    }

    # агрегация перечней: только текущий контракт (v2 + prompt 2)
    items = defaultdict(lambda: defaultdict(int))
    examples = defaultdict(list)
    sessions = defaultdict(int)
    for rp in sorted((ROOT / "models").glob("*/backend/review-v3*/run-*/review.json")):
        rj = rp.with_name("run.json")
        if not rj.is_file():
            continue
        meta = json.loads(rj.read_text(encoding="utf-8"))
        if not meta.get("parse_ok") or meta.get("protocol") != "v2" \
                or meta.get("prompt_version") != 2:
            continue
        slug = meta.get("solution_slug")
        sessions[slug] += 1
        v = json.loads(rp.read_text(encoding="utf-8"))
        for nd in v.get("not_done", []):
            fam = family(nd.get("req", "") or nd.get("desc", ""))
            state = nd.get("state", "?")
            key = (fam, state)
            items[slug][key] += 1
            if len(examples[(slug, key)]) < 1:
                examples[(slug, key)].append(
                    f"{SHORT.get(meta.get('model'), meta.get('model'))}/{meta.get('thinking')}: "
                    f"{(nd.get('desc') or '')[:90]}")

    blocks = []
    checker_facts = {
        "good": "чекер: 11/11 обязательных + 2/2 бонус — всё принято",
        "mid": "чекер: 11/11 обязательных, бонус 0/2 (не делало)",
        "tricky": "чекер: 9/11 — отчёты завышены (оф-пик теряется при первом импорте провайдера), бонус не работает",
        "bad": "чекер: 8/11 — отчёты завышены (та же причина), артефакты отсутствуют",
    }
    recall_note = {
        "good": "перечни почти пусты — корректно, решение полное; два указанных «нарушения» чекером не подтверждены",
        "mid": "перечни о миграциях строже чекера: чекер принял альтернативный способ развёртывания, ревьюеры требовали штатный; ложных срабатываний по рантайму — одно (getErrorOutput)",
        "tricky": "главный дефект (оф-пик при первом импорте) не назван ни одной моделью ни на одном уровне — полный слепой проб статического ревьюа, подтверждено расследованием раздела 6; единственные упоминания оф-пика (huihui max, qwen low) — другой дефект той же области (таймзона hydration OffPeak::applies), механизм потери скидки в них не назван",
        "bad": "артефакты и пустые миграции названы почти всеми (это пропуски-файлы, видимые без анализа логики); реальные механизмы двух логических дефектов названы только huihui: оф-пик при первом импорте (новый Provider без off-peak-полей, updateProvider только в else-ветке) — на low, bcadd накопления без масштаба — на max; у остальных моделей упоминания «оф-пик»/«округление» — другие дефекты той же семьи (пустое окно, таймзона, отрицательные суммы, debug-файл)",
    }
    for slug in SLUG_ORDER:
        rows = []
        fam_items = items.get(slug, {})
        for (fam, state), n in sorted(fam_items.items(), key=lambda x: -x[1]):
            verdict, comment = VERDICTS.get(slug, {}).get(
                fam, ("не подтверждено", "чекер соответствующий сценарий не проверял или принял"))
            vcls = {"подтверждено": "okmark", "подтверждено как факт": "okmark",
                    "подтверждено частично": "okmark"}.get(verdict, "")
            if verdict == "ложное":
                vcls = "deadmark fab"
            rows.append(
                f"<tr{' class=neg' if verdict == 'ложное' else ''}>"
                f"<td>{esc(fam)}<div class=sub>{esc(comment)}</div></td>"
                f"<td class=num>{state}</td><td class=num>{n}</td>"
                f"<td><span class='{vcls or 'okmark'}' title='сверка с фактами чекера и рантаймом'>{verdict}</span></td></tr>")
        if not rows:
            rows.append("<tr><td colspan=4 class=num>перечни not_done пусты во всех сессиях</td></tr>")
        blocks.append(f"""
<h3><span class=no>7.{SLUG_ORDER.index(slug)+1}.</span>{SLUG_TITLES[slug]} — {checker_facts[slug]}</h3>
<p class=sub>{recall_note[slug]}. Сессий с вердиктом: {sessions.get(slug, 0)}.</p>
<table>
<tr><th>Требование (семья)</th><th class=narrow>Состояние по модели</th>
<th class=narrow>Сессий</th><th>Сверка с чекером</th></tr>
{''.join(rows)}
</table>""")

    return f"""
<h2 id=notdone><span class=no>7.</span>Функциональная проверка: not_done против чекера</h2>
<p class=lead>Контракт ревью требует перечислить, <b>что не сделано</b> (поле
not_done: требование из TASK.md, состояние, детали) — балл F объявляется
производной этого перечня. Ниже — агрегированные перечни всех зачётных сессий
текущего контракта по каждому решению, сверенные с фактами чекера. Вердикты
сверки присвоены по результатам анализа (факты чекера + рантайм-разбор
раздела 6), это ручная разметка, не автоматическая.</p>
{''.join(blocks)}
<p class=sub>Итог по полноте (зачёт — только упоминание реального механизма,
см. 4.1): уверенно ловятся пропуски-файлы — артефакты и пустые миграции
(названы почти всеми моделями и уровнями). Логические дефекты почти никто
не назвал: механизм потери оф-пика при первом импорте назван один раз за всю
серию (huihui, low, на «плохом»), механизм bcadd без масштаба — один раз
(huihui, max, на «плохом»); на «каверзном» не назван ни разу никем — дефект
сидит в данных, а не в видимой логике. Упоминания тех же семей без механизма
(таймзона, пустое окно, отрицательные суммы, debug-файл) зачёту не подлежат —
это совпадение области, а не находка дефекта.</p>"""




def section_validity(runs, profiles) -> str:
    total = len(runs)
    valid = sum(1 for r in runs if r["valid"])
    by_kind = defaultdict(list)
    for r in runs:
        if not r["valid"]:
            by_kind[r.get("fail_kind", "?")].append(r)
    rows = []
    kind_desc = {
        "fab": ("fab", "Ревью без чтения", "Вердикт составлен, но ни один файл не открыт (e2b, пилот)"),
        "contract": ("contract", "Формат не соблюдён", "Файлы читались, вердикт выдан в собственном формате (orca)"),
        "burn": ("burn", "Ответ не получен", "Бюджет выхода израсходован на размышления, текста нет (gemma4:26b)"),
        "integ": ("integ", "Целостность нарушена", "Рабочая копия изменена — прогон отбраковывается"),
    }
    for kind, (cls, title, desc) in kind_desc.items():
        rs = by_kind.get(kind, [])
        if not rs:
            continue
        models = defaultdict(int)
        for r in rs:
            models[SHORT.get(r["model"], r["model"])] += 1
        mm = ", ".join(f"{k} ×{v}" for k, v in sorted(models.items()))
        rows.append(f"<tr class=neg><td><span class='deadmark {cls}'>{title}</span></td>"
                    f"<td class=num>{len(rs)}</td><td>{mm}</td><td>{desc}</td></tr>")
    orca_n = sum(1 for r in runs if r["model"] == "orcarouter/Qwen3.8-27B-Uncensored:ctx100k")
    burn_n = len(by_kind.get("burn", []))
    return f"""
<h2 id=validity><span class=no>8.</span>Валидность прогонов</h2>
<p class=lead>Из <b>{total}</b> прогонов зачётных <b>{valid}</b>. Три автоматические
проверки отбраковывают негодные прогоны: целостность рабочей копии (хэш-суммы до и
после), факт чтения файлов (хотя бы одно обращение), вердикт в заданном формате JSON.
Целостность не была нарушена ни в одном прогоне: инструмент чтения не даёт изменять
файлы.</p>
<table>
<tr><th>Причина отбраковки</th><th class=narrow>Прогонов</th><th>Модели</th><th>Что произошло</th></tr>
{''.join(rows)}
</table>
<p class=sub>Отдельные случаи. orca — файлы читались, разборы писались, но вердикт
выдавался в собственном формате (шкала 0–5, свои критерии, списки замечаний) вместо
заданного JSON на всех {orca_n} прогонах; менять промпт под одну модель запрещено п.2
правил тестирования. gemma4:26b — {burn_n} сессий, где бюджет выхода ушел в размышления
без текста; после перезапусков не закрыт один слот («среднее» решение, протокол v2 —
2 из 3 прогонов: модель трижды не выдала ответ). e2b — три вердикта без открытия хотя
бы одного файла, со ссылками на несуществующие файлы.</p>"""


def section_runs(runs, grid) -> str:
    rows = []
    for r in sorted(runs, key=lambda x: (x["folder"], x["dir"])):
        short = SHORT.get(r["model"], r["model"])
        v = r["verdict"]
        if r["valid"]:
            scores = f"A {num(v['axis_a'])} · B {num(v['axis_b'])} · F {num(v['functional'])}"
            marks = '<span class=okmark title="все проверки пройдены">ok</span>'
        else:
            kind = r.get("fail_kind", "?")
            title = {"fab": "без чтения", "contract": "свой формат",
                     "burn": "нет ответа", "integ": "целостность"}.get(kind, kind)
            scores = "—"
            marks = f'<span class="deadmark {kind}">{title}</span>'
        dur = f"{r['duration_s']/60:.0f} мин" if r["duration_s"] else "—"
        tok = f"{r['tokens']/1e6:.1f}M" if r["tokens"] else "—"
        rows.append(
            f"<tr{' class=neg' if not r['valid'] else ''}><td class=num>{esc(short)}</td>"
            f"<td class=num>{esc(r['dir'])}</td><td class=num>{esc(r['protocol'])}</td>"
            f"<td class=num>к{esc(str(r['prompt_version']))}</td>"
            f"<td class=num>{esc(r['thinking'])}</td>"
            f"<td>{SLUG_TITLES.get(r['slug'], r['slug'])}</td>"
            f"<td>{marks} <span class=num>{scores}</span></td>"
            f"<td class=num>{r['tool_calls']}</td><td class=num>{dur}</td><td class=num>{tok}</td></tr>")
    total_rub = sum(r["duration_s"] for r in runs if r["duration_s"]) / 3600 * LOCAL_RUB_H
    total_elec = sum(r["duration_s"] for r in runs if r["duration_s"]) / 3600 * ELEC_RUB_H
    agg = defaultdict(list)
    for r in runs:
        if r["duration_s"] and r["tokens"]:
            agg[(r["model"], r["protocol"])].append((r["duration_s"], r["tokens"]))
    agg_rows = []
    for (model, protocol), rs in sorted(agg.items()):
        durs = [x[0] for x in rs]
        toks = [x[1] for x in rs]
        med_rub = statistics.median(durs) / 3600 * LOCAL_RUB_H
        agg_rows.append(
            f"<tr><td><b>{esc(SHORT.get(model, model))}</b></td><td class=num>{protocol}</td>"
            f"<td class=num>{len(rs)}</td>"
            f"<td class=num>{statistics.median(durs)/60:.1f}</td>"
            f"<td class=num>{min(durs)/60:.1f}–{max(durs)/60:.1f}</td>"
            f"<td class='num p-warn'>₽{med_rub:.1f}</td>"
            f"<td class=num>{statistics.median(toks)/1e6:.2f}</td>"
            f"<td class=num>{min(toks)/1e6:.2f}–{max(toks)/1e6:.2f}</td></tr>")
    return f"""
<h2 id=runs><span class=no>9.</span>Прогоны и данные</h2>
<p class=lead>Все {len(runs)} прогонов этапа (пилот + серия v1 + серия v2 + перезапуски).
Время и токены фиксировались в run.json каждой сессии; сводка по моделям — ниже,
по-прогонно — в свёрнутом списке. Артефакты: models/&lt;модель&gt;/backend/review/ (v1)
и review-v2/ — run.json (условия, версия протокола, целостность, время, токены),
review.json (вердикт + дефекты), review.md (сырой ответ), pi-session/ (полный след
сессии), review-score.json (сверка с эталоном).</p>
<table>
<tr><th>Модель</th><th class=narrow>Протокол</th><th class=narrow>Прогонов</th>
<th class=narrow>Медиана, мин</th><th class=narrow>Время, мин</th>
<th class=narrow>₽/ревью</th>
<th class=narrow>Медиана, М ток.</th><th class=narrow>Токены, М</th></tr>
{''.join(agg_rows)}
</table>
<p class=sub>Время и токены — по данным run.json каждой сессии (замер pi). Стоимость
локального прогона — электричество и амортизация железа (методика этапов 1–2:
550 Вт при ₽7.07/кВт·ч плюс амортизация — ₽35.85/ч чистой генерации; нижняя граница —
только электричество, ₽3.89/ч). Вся серия из {len(runs)} прогонов ≈
₽{total_rub:.0f} (нижняя граница ≈ ₽{total_elec:.0f}). Прогоны e2b времени почти не
занимают, потому что файлы не читались.</p>
<div class=alist><details><summary>Показать все прогоны ({len(runs)})</summary>
<table>
<tr><th>Модель</th><th class=narrow>Прогон</th><th class=narrow>Протокол</th><th class=narrow>Контракт</th><th class=narrow>Thinking</th><th>Решение</th><th>Вердикт</th><th class=narrow>Чтений</th><th class=narrow>Время</th><th class=narrow>Токены</th></tr>
{''.join(rows)}
</table></details></div>"""


def section_next() -> str:
    return """
<h2 id=next><span class=no>10.</span>Что дальше</h2>
<table>
<tr><th style=width:34%>Направление</th><th>Смысл</th></tr>
<tr><td><b>Расширение серии прогонов</b></td><td>Добавить оставшиеся локальные модели набора тем же
порядком (3 прогона × 4 решения). У qwen3.8 сейчас один прогон на решение — добавить
недостающие два. Прогоны orca учтены как «формат не соблюдён»; их разборы сохранены в
данных.</td></tr>
<tr><td><b>Полнота списков дефектов</b></td><td>Сопоставить списки дефектов ревьюеров с
эталонными: какие известные дефекты найдены, какие указаны ошибочно. Сейчас сверка выполнялась
вручную на выборке; дальше — скрипт-судья по образцу judge_tier3.</td></tr>
<tr><td><b>Калибровка зачёта</b></td><td>Пороги |Δ| ≤ 0.5 / ≤ 1.0 — черновые. При
расширении серии пересмотреть с учётом систематических смещений моделей (завышение
gemma, занижение F у qwen на «среднем»).</td></tr>
<tr><td><b>Правки промпта ревьюера</b></td><td>Из пилота: правило про метод, которого нет
в объявленном типе, но который есть у передаваемого объекта; пример JSON-вердикта в
user-сообщении. Изменения — одинаковые для всех моделей (п.2 правил тестирования).</td></tr>
<tr><td><b>Связь этапов 2 и 3</b></td><td>Сопоставить баллы написания кода (этап 2) и
ревью (этап 3) по моделям с зачётными вердиктами. На текущих данных предположение
«сильный писатель = точный ревьюер» подтверждается на huihui и не подтверждается на orca.</td></tr>
</table>"""


def main() -> int:
    refs = load_references()
    runs = load_runs()
    grid, profiles = aggregate(runs, refs)
    html = f"""<!doctype html>
<html lang=ru><head><meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1">
<title>Этап 3: код-ревью — отчёт</title>
<style>{CSS}</style></head>
<body><div class=wrap>
{section_header(len(runs))}
{section_tldr(runs, profiles, refs, grid)}
{section_method()}
{section_objects(refs)}
{section_results(profiles, refs, runs)}
{section_stability(profiles)}
{section_findings(runs, profiles)}
{section_notdone(profiles, refs, runs)}
{section_validity(runs, profiles)}
{section_runs(runs, grid)}
{section_next()}
<div class=footer>Этап 3 методики «Модели»: код-ревью решений этапа 2. Отчёт собран
из артефактов models/&lt;модель&gt;/backend/review/run-XX/ (run.json, review.json) и
эталон-карточек reviewer/references/. Билдер: reviewer/build_review_report.py.
Методика: reviewer/REVIEWER.md. Дата сборки: {__import__('datetime').date.today().isoformat()}.</div>
</div></body></html>"""
    OUT.write_text(html, encoding="utf-8")
    n_valid = sum(1 for r in runs if r["valid"])
    print(f"отчёт: {OUT} ({len(html)/1024:.0f} КиБ); прогонов {len(runs)}, валидных {n_valid}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

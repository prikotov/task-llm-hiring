#!/usr/bin/env python3
"""Генератор отчёта этапа 3 (код-ревью решений этапа 2 локальными моделями).

Стиль — как у отчётов этапов 1 и 2: светлый документ, нумерованные секции,
таблицы с бейджами и барами, самописные SVG без внешних зависимостей.
Все числа — из артефактов прогонов (run.json, review.json) в
models/<модель>/backend/code-review/ и эталон-карточек references/*.json.

Запуск: python3 code-review/build_review_report.py
Выход: reports/REVIEW-report.html
"""
import glob
import json
import re
import statistics
from collections import defaultdict
from pathlib import Path

HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[0].resolve()
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
th .sub{text-transform:none;font-weight:400;letter-spacing:0}
td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14.5px}
tr:last-child td{border-bottom:none}
tr.neg td{background:#fdf6f4}
tr.top td{background:#eef6ef}
tr.na td{background:#f7e2dc}
.note{background:#fff9e8;border:1px solid #eedd9f;border-radius:12px;padding:13px 17px;font-size:14.5px;margin:14px 0}
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
.tip{position:relative;cursor:help;border-bottom:1px dotted currentColor}
.tip .tipbox{display:none;position:absolute;z-index:40;left:calc(100% + 10px);top:50%;transform:translateY(-50%);width:330px;background:#1d2433;color:#fff;font-size:12px;line-height:1.55;font-weight:400;padding:9px 12px;border-radius:9px;box-shadow:0 8px 22px rgba(20,30,50,.32);text-align:left;white-space:normal}
.tip:hover .tipbox{display:block}
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
        c["mand_passed"] = cj["mandatory_passed"]
        c["mand_total"] = cj["mandatory_total"]
        c["bonus_passed"] = cj.get("bonus_passed")
        c["bonus_total"] = cj.get("bonus_total")
        frozen = c["expected"]["functional"]
        if abs(c["f_det"] - frozen) > 1e-9:
            print(f"ВНИМАНИЕ: {c['slug']}: детерминированный F {c['f_det']} != "
                  f"замороженному {frozen}")
        refs[c["slug"]] = c
    return refs


def load_runs() -> list[dict]:
    """Все прогоны ревью (протоколы v1 и v2) с полями вердикта/валидности."""
    runs = []
    for rp in sorted(MODELS.glob("*/backend/code-review/run-*")):
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
<p>Отчёт собран из артефактов прогонов: models/&lt;модель&gt;/backend/code-review/run-XX/.</p>
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
<li><a href="#precision">8. Качество замечаний</a></li>
<li><a href="#validity">9. Валидность прогонов</a></li>
<li><a href="#runs">10. Прогоны и данные</a></li>
<li><a href="#next">11. Что дальше</a></li>
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
<p class=lead>Главный дефект «каверзного» решения не нашёл ни один зачётный ревьюер.
Причина дефекта: оф-пиковая скидка не записывается при первом импорте провайдера, а
проверка всегда стартует с пустой базы, где провайдеров ещё нет. Пример расчёта из
TASK.md при этом сходится, поэтому ошибка не видна при чтении кода. Тот же дефект
пропустил и судья яруса 4, поставив решению A 4.0. Единственный, кто назвал механизм
за всю серию, — orca в сыром тексте вне заданного формата (раздел 8.2). Она
включена в рейтинг 4.1 по ручной разметке содержания: качество замечаний высшее
(93% на max). Её прогонам не засчитан формат — на качество ревьюера он не влияет, роли играет только содержание. Баллы ревьюеров по A (эталон 2.5):
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
<tr><td><b>Судья-оценщик замечаний</b></td><td>Первичная оценка ревьюера — не баллы, а замечания. Судья яруса 3 (знает проект, метрики, результаты чекера) с рантайм-верификацией скриптами проверяет каждое замечание critical/major по коду решения: подтверждено / серьёзность завышена / опровергнуто (раздел 8) и not_done против чекера (раздел 7). Выход — точность и полнота ревьюера.</td></tr><tr><td><b>Сравнение баллов</b></td><td>Второстепенный сигнал согласия с судьёй: разница |Δ| между баллом ревьюера и эталонным баллом по каждой шкале. Черновик зачёта: |Δ| ≤ 0.5 — зачёт, ≤ 1.0 — частично, выше — промах. Спорные расхождения проверяются кодом и рантаймом (раздел 6.2).</td></tr>
<tr><td><b>Версии протокола</b></td><td><b>v1</b> — серия от 2026-10-31: помимо решения в материалах лежал diff.patch как вспомогательный файл (56 прогонов). <b>v2</b> — текущий: решение целиком, без диффа; версия контракта (промпта) — 1 без перечня not_done, 2 с перечнем. Все версии записаны в run.json каждого прогона; каталог результатов один — <code>models/&lt;модель&gt;/backend/code-review/run-XX/</code>, нумерация сквозная хронологическая. В разделе 4 таблицы разбиты по версиям — старые данные сохранены для сравнения.</td></tr>
</table>
<p class=lead><b>Ограничение серии: ревью статическое.</b> По правилам серии модель не
запускает код и тесты — функциональность она проверяет только чтением и рассуждением.
Это осознанный выбор, у него есть цена: часть реальных дефектов решений — логические,
они проявляются только при исполнении (не сходится пример расчёта, отчёт завышен), и
статическим ревью они не находятся. Главный пример — оф-пик при первом импорте на
«каверзном» решении: его не нашла ни одна модель ни на одном уровне мышления (раздел
6.1), хотя прогон приёмки из TASK.md вскрыл бы его сразу. Запуск кода и тестов —
возможное расширение для будущей серии (новая версия протокола, отдельные строки вне
рейтинга текущей); задним числом в эту серию его не добавить.</p>"""


def section_objects(refs) -> str:
    rows = []
    for slug in SLUG_ORDER:
        c = refs[slug]
        b = c["basis"]
        e = c["expected"]
        fails = "; ".join(f["scenario"] for f in c.get("checker_failures", [])) or "—"
        if c.get("bonus_total"):
            bonus_txt = f" + бонус {c['bonus_passed']}/{c['bonus_total']}"
        elif "bonus_total" in c:
            bonus_txt = " + бонус 0/?"
        else:
            bonus_txt = ""
        rows.append(
            f"<tr><td><b>{SLUG_TITLES[slug]}</b><div class=sub>{esc(c['source_run'].split('/backend/')[0].split('models/')[1])}</div></td>"
            f"<td>{esc(b['checker'])}</td>"
            f"<td class=num>A {num(b['tier3']['axis_a'])} · B {num(b['tier3']['axis_b'])}<div class=sub>ярус 4 (без результатов проверки): A {num(b['tier4_blind']['axis_a'])}</div></td>"
            f"<td class=num>{num(e['axis_a'])} · {num(e['axis_b'])} · "
              f"F {c.get('mand_passed', '?')}/{c.get('mand_total', '?')} об.{bonus_txt}"
              f"<div class=sub>F в шкале 0–4: floor(8×{c.get('mand_passed', '?')}/"
              f"{c.get('mand_total', '?')})/2 = {num(c['f_det'])} — пересчёт чекера</div></td>"
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
    """Применимость модели как ревьюера по итогу (средний |Δ| по всем осям
    и решениям): до 0.6 — применима; до 0.9 — применима с оговорками
    (баллы сверять с чекером); выше — не применима."""
    tip = "итог — средний |Δ| по всем осям и решениям"
    if overall <= 0.6:
        return f'<span class="okmark" title="{tip}">применима</span>'
    if overall <= 0.9:
        return f'<span class="deadmark contract" title="{tip}; баллы сверять с чекером">с оговорками</span>'
    return f'<span class="deadmark fab" title="{tip}">не применима</span>'


def results_summary_block(profiles, refs, runs) -> str:
    """Сводная таблица: плоский рейтинг строк (модель × мышление) текущего
    контракта. Итог (в таблице не выводится, задаёт сортировку и зелёную
    подсветку строк с итогом ≤ 0.6) — среднее двух долей ошибок: неточность
    замечаний (1 − точность по вердиктам судьи-оценщика) и неполнота (1 −
    recall по not_done против чекера). Формат выдачи в итог не входит —
    роль ревьюера текстовая; отбраковка гейтом — справочная колонка
    «Отбрак.». «Место» — порядковый номер строки в этом рейтинге."""
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
            return '<td colspan=3 class=num title="замеров времени и токенов нет">—</td>'
        return (f"<td class=num>{res[1]:.2f}M</td>"
                f"<td class=num>{res[0]:.1f} мин</td>"
                f"<td class='num p-warn'>₽{res[2]:.1f}</td>")

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
    # надёжность: все запуски серии (модель × thinking) против зачётных —
    # сессия, завершившаяся некорректно (вердикт не по контракту), идёт
    # модели в минус, а не выбрасывается
    attempted = defaultdict(int)
    for rp in sorted((ROOT / "models").glob("*/backend/code-review/run-*/run.json")):
        meta = json.loads(rp.read_text(encoding="utf-8"))
        if meta.get("protocol") != "v2" or meta.get("prompt_version") != 2:
            continue
        attempted[(meta.get("model"), meta.get("thinking"))] += 1
    # что именно отбраковано в каждой строке — для тултипа колонки «Отбрак.»
    dropped_why = defaultdict(list)
    for r in runs:
        if r["protocol"] != "v2" or r["prompt_version"] != 2:
            continue
        if r["valid"] and r.get("parse_ok") is not False:
            continue
        reason = r.get("invalid_reason") or _FAIL_TEXT.get(r.get("fail_kind"), "отбракован")
        dropped_why[(r["model"], r["thinking"])].append(
            f"{r['dir']} ({SLUG_TITLES.get(r['slug'], r['slug'])}): {str(reason).split(':')[0][:70]}")
    for rp in sorted((ROOT / "models").glob("*/backend/code-review/run-*/review.json")):
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

    prec = defect_precision(runs)
    arch = arch_coverage(runs)

    def combined(model, thinking, prof):
        """Итог ранжирования — среднее двух долей ошибок, обе от верификации
        содержания, без судейских баллов: неточность замечаний (1 − точность
        critical/major по вердиктам судьи-оценщика, раздел 8) и неполнота
        (доля реальных пропусков без названного механизма, раздел 7).
        Формат выдачи в итог не входит: роль ревьюера — текстовые замечания,
        несданный JSON — потеря данных для конвейера (колонка «Отбрак.»),
        а не дефект ревью. Числовое сравнение |Δ| — согласие с судьёй —
        тоже вне итога: эталонные баллы сам судья и назначил."""
        miss = 1 - len(caught.get((model, thinking), set())) / REAL_MISSES_TOTAL
        pc, pz, po = prec.get((model, thinking), [0, 0, 0])
        ptot = pc + pz + po
        p = pc / ptot if ptot else 0.0
        итог = statistics.mean([1 - p, miss])
        return (итог, miss, p, (pc, pz, po, ptot), axis_overall(prof)[0])

    ranked = []
    for thinking, models in v2_levels.items():
        for model, prof in models.items():
            c, miss, p, pparts, d = combined(model, thinking, prof)
            ranked.append({"model": model, "thinking": thinking, "prof": prof,
                           "combined": c, "miss": miss, "d": d,
                           "prec": p, "pparts": pparts,
                           "dropped": attempted.get((model, thinking), prof["n"]) - prof["n"],
                           "caught": len(caught.get((model, thinking), set())),
                           "arch": arch.get((model, thinking), set()),
                           "res": resources(model).get(("v2", thinking, 2))})
    # orca — по сырому тексту (8.2): неточность и неполнота из ручной
    # разметки; формат в итог не входит (роль ревьюера — текстовые замечания)
    orca_model = "orcarouter/Qwen3.8-27B-Uncensored:ctx100k"
    for th in ("max", "low", "medium"):
        rs = [r for r in runs if r["model"] == orca_model
              and r["thinking"] == th and r["protocol"] == "v2"]
        if not rs:
            continue
        pc, pz, po = ORCA_TRIAGE[th]
        ptot = pc + pz + po
        prec_o = pc / ptot if ptot else 0.0
        miss_o = 1 - ORCA_CAUGHT[th] / REAL_MISSES_TOTAL
        ds = sorted(r["duration_s"] for r in rs if r["duration_s"])
        toks = sorted(r["tokens"] for r in rs if r["tokens"])
        med_min = med_t = med_r = None
        if ds and toks:
            m = len(ds) // 2
            med_d = ds[m] if len(ds) % 2 else (ds[m - 1] + ds[m]) / 2
            med_min = med_d / 60
            med_t = toks[len(toks) // 2] / 1e6
            med_r = med_d / 3600 * LOCAL_RUB_H
        ranked.append({
            "model": orca_model, "thinking": th, "orca": True, "n_rs": len(rs),
            "combined": statistics.mean([1 - prec_o, miss_o]),
            "prec": prec_o, "pparts": (pc, pz, po, ptot),
            "caught": ORCA_CAUGHT[th], "dropped": len(rs),
            "arch": arch.get((orca_model, th), set()),
            "res": (med_min, med_t, med_r) if med_t else None})
    ranked.sort(key=lambda e: e["combined"])

    def runs_cell(e):
        """Все запуски серии строки (план: 3 ревью × 4 решения = 12)."""
        total = attempted.get((e["model"], e["thinking"]), e["prof"]["n"])
        return (f'<td class=num title="всего запусков серии; план — '
                f'3 ревью каждого из 4 решений (12)">{total}</td>')

    def dropped_cell(e):
        n = e.get("dropped", 0)
        if not n:
            return '<td class=num>0</td>'
        # детали — CSS-тултипом при наведении на число (нативные title
        # во встроенном просмотрщике не показываются)
        whys = dropped_why.get((e["model"], e["thinking"]), [])
        body = "<br>".join(esc(w) for w in whys) \
            or "детали — в по-модельной таблице"
        return (f'<td class="num p-bad"><span class=tip>{n}'
                f'<span class=tipbox><b>Отбраковано по формату вердикта</b> — данные '
                f'для конвейера потеряны (в итог не входит):<br>{body}</span></span></td>')

    def row_html(e, place):
        """Единая схема строки для всех моделей, включая orca —
        различаются только подсказки источников данных."""
        model, thinking = e["model"], e["thinking"]
        cls = "top" if e["combined"] <= 0.6 else ("na" if e["combined"] > 0.9 else "")
        if e.get("orca"):
            runs = (f'<td class=num title="Все {e["n_rs"]} запусков — содержательные '
                    f'разборы в собственном формате, по ним посчитаны точность и '
                    f'полнота (8.2)">{e["n_rs"]}</td>')
            prec_tip = (f"Оценено судьёй по текстам разборов orca, вне контракта "
                        f"(раздел 8.2): {e['pparts'][0]} из {e['pparts'][3]} подтверждено")
            caught_tip = "Оценено по текстам разборов orca (раздел 8.2)"
        else:
            runs = runs_cell(e)
            prec_tip = (f"{e['pparts'][0]} из {e['pparts'][3]} подтверждено судьёй"
                        if e["pparts"][3] else "существенных замечаний не названо")
            caught_tip = "реальные пропуски, названные с механизмом (раздел 7)"
        prec_cell = (f"<td class=num title=\"{prec_tip}\">{e['prec']:.0%}</td>"
                     if e["pparts"][3] else
                     f"<td class=num title=\"{prec_tip}\">—</td>")
        caught_cell = (f"<td class=num title=\"{caught_tip}\">{e['caught']} "
                       f"из {REAL_MISSES_TOTAL}</td>")
        arch_cell = (f"<td class=num title=\"Названные темы: "
                     f"{esc(', '.join(sorted(e['arch'])))}\">{len(e['arch'])} "
                     f"из {len(ARCH_THEMES)}</td>" if e['arch'] else
                     f"<td class=num title=\"Тем не названо\">0 из {len(ARCH_THEMES)}</td>")
        return (
            f"<tr class={cls}>"
            f"<td class=num>{place}</td>"
            f"<td><b>{esc(SHORT.get(model, model))}</b></td>"
            f"<td class=num>{thinking}</td>"
            + runs
            + dropped_cell(e)
            + prec_cell
            + caught_cell
            + arch_cell
            + res_cell(e["res"])
            + "</tr>")

    rows = []
    for i, e in enumerate(ranked, 1):
        rows.append(row_html(e, i))
    # сравнительные серии (v1, прежний контракт) в сводную не выводятся:
    # их вердикты судьёй не верифицировались, а по-прогонные данные видны
    # в по-модельных таблицах ниже (протокол — в строке прогона)

    head = ("<tr><th class=narrow>Место</th><th>Модель</th>"
            "<th class=narrow>Мышление</th>"
            "<th class=narrow title=\"Все запуски серии: план — 3 независимых ревью каждого из 4 решений (12)\">Прогонов<sup>1</sup></th>"
            "<th class=narrow title=\"Запуски, не сдавшие формат вердикта; в итог не входит — потеря данных для конвейера, не дефект ревью. Детали по прогонам — в подсказках ячеек\">Отбрак.<sup>2</sup><div class=sub>по формату</div></th>"
            "<th class=narrow title=\"Доля подтверждённых судьёй-оценщиком замечаний critical/major (раздел 8)\">Точность<sup>3</sup></th>"
            "<th class=narrow title=\"Реальные пропуски решения, названные с механизмом (раздел 7)\">Поймано<sup>4</sup></th>"
            "<th class=narrow title=\"Из 11 тем качества кода и архитектуры, проверенных инструментальным оракулом (раздел 8.3): сколько названо за серию; перечень — в подсказке ячейки\">Архитектура<sup>5</sup></th>"
            "<th class=narrow>Токены<sup>6</sup><div class=sub>медиана</div></th>"
            "<th class=narrow>Время<sup>6</sup><div class=sub>медиана</div></th>"
            "<th class=narrow>₽/ревью<sup>6</sup></th>"
            "</tr>")
    return f"""
<h3><span class=no>4.1.</span>Сводная таблица: качество ревью</h3>
<p class=lead>В таблице — строки текущего контракта (протокол v2, контракт
с перечнем not_done): одна строка = модель × уровень мышления. Строки
отсортированы по качеству ревью, лучшие сверху; «Место» — порядковый номер
строки в этом рейтинге.</p>
<p class=lead>Сортировка и подсветка идут по итогу — среднему двух долей
ошибок: <b>неточности</b> (замечания, не подтверждённые судьёй — сноска 3)
и <b>неполноты</b> (реальные пропуски решения, которые ревьюер не назвал —
сноска 4). Обе доли считаются по верификации содержания замечаний; судейские
баллы в итог не входят. Сам итог в таблице не выводится — о нём говорят
место строки и цвет фона.</p>
<p class=lead><b>Фон строки — граница применимости модели как ревьюера:</b>
зелёный — итог ≤ 0.6 (применима), без подсветки — средняя зона (оценки модели
стоит перепроверять), красный — итог > 0.9 (не применима).</p>
<p class=lead>Чего в сводной нет: согласия с судьёй (|Δ|) — по-прогонные баллы
и Δ видны в по-модельных таблицах ниже; серий с другими условиями — протокол
v1 и прежний контракт (их прогоны тоже в по-модельных таблицах, протокол
указан в строке прогона). Расшифровка колонок — в сносках под таблицей.</p>
<table>{head}{''.join(rows)}</table>
<div class=note><sup>1</sup> Прогонов — все запуски серии: план 3 независимых ревью каждого из 4 решений (12).<br>
<sup>2</sup> Отбрак. по формату — запуски, не сдавшие формат вердикта. В итог не входит: потеря данных для конвейера, а не дефект ревью. Детали по прогонам — в подсказках ячеек.<br>
<sup>3</sup> Точность — доля подтверждённых судьёй-оценщиком замечаний critical/major (раздел 8); входит в итог как «неточность».<br>
<sup>4</sup> Поймано — реальные пропуски решения, названные с механизмом дефекта (раздел 7); входит в итог как «неполнота».<br>
<sup>5</sup> Архитектура — из 11 тем качества кода и архитектуры, проверенных инструментальным оракулом (раздел 8.3); перечень названных тем — в подсказке ячейки.<br>
<sup>6</sup> Токены, Время, ₽/ревью — медианы строки; стоимость локального инференса — электричество и амортизация (₽35.85/ч, методика этапов 1–2).<br>
orca — вердиктов в заданном формате нет: её точность и полнота посчитаны судьёй по текстам разборов вручную (раздел 8.2).</div>"""


def _scatter_svg(points: list[dict], x_title: str, note: str) -> str:
    """Карта моделей в стиле этапа 2: точка — строка рейтинга
    (модель × уровень мышления). points: {x, y, label}; итог по Y — ниже лучше."""
    W, H = 760, 330
    L, R, T, B = 60, 24, 40, 46
    x1 = max(p["x"] for p in points) * 1.15
    y1 = max(p["y"] for p in points) * 1.18
    def px(x): return L + (W - L - R) * x / x1
    def py(y): return H - B - (H - T - B) * y / y1
    parts = [f'<div class=chartbox><svg width="100%" viewBox="0 0 {W} {H}" '
             f'role="img" aria-label="{esc(x_title)} против итога">']
    yt = 0.2
    while yt < y1:
        parts.append(f'<line x1="{L}" y1="{py(yt):.1f}" x2="{W-R}" y2="{py(yt):.1f}" '
                     f'stroke="#e4e7ec" stroke-width="1"/>')
        parts.append(f'<text x="{L-8}" y="{py(yt)+4:.1f}" font-size="11.5" '
                     f'fill="#66707f" text-anchor="end">{yt:.1f}</text>')
        yt += 0.2
    parts.append(f'<line x1="{L}" y1="{H-B}" x2="{W-R}" y2="{H-B}" stroke="#46536a" stroke-width="1.5"/>')
    parts.append(f'<line x1="{L}" y1="{T-14}" x2="{L}" y2="{H-B}" stroke="#46536a" stroke-width="1.5"/>')
    parts.append(f'<text x="{W-R}" y="{H-B+22}" font-size="12.5" fill="#46536a" '
                 f'text-anchor="end">{esc(x_title)}</text>')
    parts.append(f'<text x="{L}" y="{T-22}" font-size="12.5" fill="#46536a">итог, баллов — ниже лучше</text>')
    parts.append(f'<text x="{L}" y="{T-6}" font-size="11.5" fill="#a15c00">{esc(note)}</text>')
    for p in points:
        cx, cy = px(p["x"]), py(p["y"])
        parts.append(f'<circle cx="{cx:.1f}" cy="{cy:.1f}" r="6" fill="#315f9e" opacity="0.75"/>')
        parts.append(f'<text x="{cx+9:.1f}" y="{cy-7:.1f}" font-size="12" '
                     f'font-weight="600" fill="#1d2433">{esc(p["label"])}</text>')
        parts.append(f'<text x="{cx+9:.1f}" y="{cy+8:.1f}" font-size="11" fill="#66707f">'
                     f'{p["y"]:.2f} · {p["x"]:.1f}</text>')
    parts.append('</svg></div>')
    return ''.join(parts)


def _verdict_chip(dmax: float) -> str:
    """Черновик зачёта сверки с эталоном (как в score_review):
    max|Δ| ≤ 0.5 — зачёт, ≤ 1.0 — частично, выше — промах."""
    if dmax <= 0.5:
        return '<span class=okmark title="max |Δ| по осям ≤ 0.5">зачёт</span>'
    if dmax <= 1.0:
        return '<span class="deadmark burn" title="max |Δ| по осям ≤ 1.0">частично</span>'
    return '<span class="deadmark fab" title="max |Δ| по осям &gt; 1.0">промах</span>'


_FAIL_TEXT = {
    "fab": "вердикт без чтения файлов (фабрикация)",
    "integ": "нарушена целостность материалов",
    "contract": "вердикт не по контракту",
    "format": "вердикт в чужом формате",
}


def section_results(profiles, refs, runs) -> str:
    """Раздел 4: сводная таблица 4.1, карты «итог—цена» и «итог—время»
    (приём этапа 2), затем по-модельные секции — одна таблица на модель,
    строка = отдельный прогон."""
    summary = results_summary_block(profiles, refs, runs)

    # медианные ресурсы строки рейтинга (текущий контракт) для карт
    def med(model, thinking):
        rs = [r for r in runs if r["model"] == model and r["protocol"] == "v2"
              and r["thinking"] == thinking and r["prompt_version"] == 2
              and r["duration_s"] and r["tokens"]]
        if not rs:
            return None
        ds = sorted(r["duration_s"] for r in rs)
        m = len(ds) // 2
        med_d = ds[m] if len(ds) % 2 else (ds[m - 1] + ds[m]) / 2
        return (med_d / 60, med_d / 3600 * LOCAL_RUB_H)

    def combined_of(model, thinking):
        prof = profiles.get((model, "v2", thinking, 2))
        if not prof:
            return None
        d = {a: statistics.mean(s["delta_mean"][a] for s in prof["per_slug"].values())
             for a in AXES}
        return statistics.mean(d.values())

    map_points = []
    for model, thinking in sorted({(r["model"], r["thinking"]) for r in runs
                                   if r["protocol"] == "v2" and r["prompt_version"] == 2}):
        c, res = combined_of(model, thinking), med(model, thinking)
        if c is not None and res is not None:
            map_points.append({"label": f"{SHORT.get(model, model)} {thinking}",
                               "y": c, "rub": res[1], "min": res[0]})

    map_price = _scatter_svg(
        [{"x": p["rub"], "y": p["y"], "label": p["label"]} for p in map_points],
        "₽ за ревью (медиана строки)", "правый нижний угол — дёшево и точно")
    map_speed = _scatter_svg(
        [{"x": p["min"], "y": p["y"], "label": p["label"]} for p in map_points],
        "минут на ревью (медиана строки)", "левый нижний угол — быстро и точно")

    # по-модельные секции
    model_runs = defaultdict(list)
    for r in runs:
        model_runs[r["model"]].append(r)
    head = ("<tr><th class=narrow>Прогон</th><th class=narrow>Решение</th>"
            "<th class=narrow>Мышл.</th><th class=narrow>Протокол</th>"
            "<th class=narrow>A</th><th class=narrow>B</th>"
            "<th class=narrow title=\"балл ревьюера по контракту, шкала 0–4; "
            "эталон решения — дробь чекера, см. раздел 3\">F 0–4</th>"
            "<th class=narrow>ΔA</th><th class=narrow>ΔB</th><th class=narrow>ΔF</th>"
            "<th class=narrow>Сверка</th><th class=narrow>Дефекты<br>K/M/m</th>"
            "<th class=narrow>М ток.</th><th class=narrow>Мин</th><th>Примечание</th></tr>")
    blocks = []
    for model in MODEL_ORDER:
        rs = sorted(model_runs.get(model, []), key=lambda r: r["dir"])
        if not rs:
            continue
        rows = []
        for r in rs:
            v = r["verdict"] or {}
            slug = r["slug"]
            exp = refs.get(slug, {}).get("expected")
            cells = [f"<td class=narrow>{r['dir']}</td>",
                     f"<td class=narrow>{SLUG_TITLES.get(slug, slug)}</td>",
                     f"<td class=narrow>{r['thinking']}</td>"]
            key = r["protocol"] if r["protocol"] == "v1" else (r["protocol"], r["prompt_version"])
            cells.append(f"<td class=narrow>{'v1' if r['protocol'] == 'v1' else 'v2·к%d' % r['prompt_version']}</td>")
            if all(isinstance(v.get(a), (int, float)) for a in AXES):
                cells += [f"<td class=num>{num(v[a])}</td>" for a in AXES]
                if exp:
                    deltas = [v[a] - exp[a] for a in AXES]
                    cells += [f"<td class='num {delta_class(abs(d))}'>"
                              f"{f'{d:+.1f}'.replace('+0.0', '0').replace('-0.0', '0')}</td>"
                              for d in deltas]
                    cells.append(f"<td class=num>{_verdict_chip(max(abs(d) for d in deltas))}</td>")
                else:
                    cells += ["<td class=num>—</td>"] * 4
            else:
                cells += ["<td class=num>—</td>"] * 7
            nd = r.get("defects") or []
            if v:
                sev = [sum(1 for d in nd if d.get("severity") == s)
                       for s in ("critical", "major", "minor")]
                cells.append(f"<td class=num>{sev[0]}/{sev[1]}/{sev[2]}</td>")
            else:
                cells.append("<td class=num>—</td>")
            cells.append(f"<td class=num>{r['tokens']/1e6:.2f}M</td>" if r["tokens"]
                         else "<td class=num>—</td>")
            cells.append(f"<td class=num>{r['duration_s']/60:.1f}</td>" if r["duration_s"]
                         else "<td class=num>—</td>")
            if r["valid"] and r.get("parse_ok") is not False:
                cells.append("<td></td>")
            else:
                note = (_FAIL_TEXT.get(r.get("fail_kind"))
                        or r.get("invalid_reason") or "отбракован")
                cells.append(f"<td class=sub>{esc(str(note)[:90])}</td>")
            rows.append("<tr>" + "".join(cells) + "</tr>")
        params = PARAMS.get(model, ("?", "?"))
        n_valid = sum(1 for r in rs if r["valid"])
        waves = defaultdict(int)
        for r in rs:
            waves["v1" if r["protocol"] == "v1" else f'v2/к{r["prompt_version"]}'] += 1
        waves_txt = " · ".join(f"{k} — {n}" for k, n in sorted(waves.items()))
        blocks.append(f"""
<h3>{esc(SHORT.get(model, model))}</h3>
<p class=sub>{esc(model)} · {params[0]} · {params[1]} · прогонов: {len(rs)} (зачётных {n_valid})
· серии: {waves_txt}</p>
<table>{head}{''.join(rows)}</table>
""")

    return f"""
<h2 id=results><span class=no>4.</span>Результаты моделей</h2>
{summary}
<h3>Карта моделей: итог и цена</h3>
<p class=lead>Точка — строка рейтинга (модель × уровень мышления, текущий контракт).
Вертикаль — итог из таблицы 4.1 (среднее двух долей ошибок: неточность,
неполнота), горизонталь — медианная стоимость одного ревью строки.</p>
{map_price}
<h3>Карта моделей: итог и время</h3>
{map_speed}
<p class=lead>Детали по каждой модели: строка таблицы — отдельный изолированный прогон.
ΔA/ΔB/ΔF — отличие балла прогона от эталона решения (зелёный 0, жёлтый ≤1, красный &gt;1);
«Сверка» — черновик зачёта по максимальному |Δ|: до 0.5 зачёт, до 1 частично, выше промах.
«Отбракован» — вердикт не по контракту, данные для конвейера потеряны (справочно, в итог не входит).</p>
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
ответ). Средний разброс по моделям — на графике ниже; по-прогонные баллы каждой модели —
в её таблице раздела 4.</p>
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
<h3><span class=no>6.1.</span>Дефект, который не нашло ни одно зачётное ревью</h3>
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
только при сопоставлении с независимым расчётом. Такой расчёт доступен и ревьюеру —
запуском приёмки из TASK.md; в этой серии запуск исключён протоколом (раздел 2,
«Ограничение серии»), поэтому ожидать этого находа от статического ревью было нельзя.
Показательно исключение: orca (прежний контракт, вердикт не в формате) назвала механизм
в сыром тексте — чтением кода импорта, без запуска (раздел 8.2). Крупная модель с
пошаговым рассуждением может находить и такие дефекты — вопрос полноты просмотра
жизненного цикла записи, а не принципиальная граница метода.</p>
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
Полные списки дефектов с серьёзностью — в review.json каждого прогона (раздел 10).</p>"""


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
    for rp in sorted((ROOT / "models").glob("*/backend/code-review/run-*/review.json")):
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
        "tricky": "в зачётных сессиях текущего контракта механизм оф-пика при первом импорте не назвал никто — слепой проб статического ревьюа (раздел 6); единственное исключение всей серии — orca (прежний контракт, вне рейтинга): механизм назван в сыром тексте run-08 (раздел 8.2); упоминания оф-пика зачётными моделями (huihui max, qwen low) — другой дефект той же области (таймзона hydration OffPeak::applies)",
        "bad": "артефакты и пустые миграции названы почти всеми (это пропуски-файлы, видимые без анализа логики); реальные механизмы двух логических дефектов названы только huihui: оф-пик при первом импорте (новый Provider без off-peak-полей, updateProvider только в else-ветке) — на low, bcadd накопления без масштаба — на max; у остальных моделей упоминания «оф-пик»/«округление» — другие дефекты той же области (пустое окно, таймзона, отрицательные суммы, debug-файл)",
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
<tr><th>Требование TASK.md</th><th class=narrow>Состояние по модели</th>
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
(huihui, max, на «плохом»); на «каверзном» не назван ни разу зачётным ревьюером —
дефект сидит в данных, а не в видимой логике. Единственный, кто назвал его за серию,
— orca вне формата вердикта (8.2). Упоминания тех же семей без механизма
(таймзона, пустое окно, отрицательные суммы, debug-файл) зачёту не подлежат —
это совпадение области, а не находка дефекта.</p>"""





def _def_cluster(slug: str, file: str, text: str) -> str:
    """Тема замечания critical/major: файл + ключевые слова описания."""
    f = (file or "").lower()
    t = (text or "").lower()
    if slug == "bad":
        if "billingratingservice" in f:
            return "bcadd без масштаба"
        if "priceimportservice" in f:
            return "оф-пик при первом импорте"
        if "usageimportservice" in f:
            return "дедупликация внутри одного файла"
        if "миграц" in t or "makefile" in f or ".gitkeep" in f:
            return "миграции отсутствуют"
        if ("commit" in f or "pr_description" in f or "артефакт" in t
                or "корень" in f or "root" in f or not f):
            return "артефакты COMMIT/PR отсутствуют"
        return "getErrorOutput → фатал на unrated"
    if slug == "mid":
        if "дедупл" in t or "findoneby" in t or "дубл" in t:
            return "дедупликация внутри одного файла"
        if "доменн" in t or "логика тарификации" in t:
            return "доменная логика в команде"
        if "geterroroutput" in t or "geterroroutput" in f:
            return "getErrorOutput → фатал на unrated"
        if "version2026" in f or "миграц" in t:
            return "diff-миграции без CREATE TABLE"
        if "float" in t:
            return "float в totalCost"
        return "прочее"
    if slug == "good":
        if "reprice" in f:
            return "ключ 0 у новых строк бонуса"
        return "guard платформы миграции"
    return "формат ts при вставке"


# вердикты сверки замечаний: код решений + факты чекера + расследования раздела 6
_DEF_VERDICTS = {
    "bad": {
        "миграции отсутствуют": ("подтверждено",
            "рабочего способа развёртывания схемы в решении нет: каталог миграций пуст, make migrate — no-op; чекер развернул схему своим fallback-механизмом"),
        "артефакты COMMIT/PR отсутствуют": ("подтверждено",
            "файлов нет в дереве решения; сценарий artifacts_present чекера упал"),
        "bcadd без масштаба": ("подтверждено",
            "BillingRatingService:70–73 — слагаемые усекаются до целых; сценарий h02: 3.312495 против эталонных 3.312496"),
        "оф-пик при первом импорте": ("подтверждено",
            "верифицированный механизм (раздел 6): новый Provider создаётся без off-peak-полей"),
        "дедупликация внутри одного файла": ("подтверждено",
            "код: findOneBy в цикле, flush один в конце — дубль внутри файла ловит unique-индекс вместо тихого skip; сценарий чекера кросс-файловый и этого пути не задевал"),
        "getErrorOutput → фатал на unrated": ("опровергнуто",
            "Symfony передаёт в команду ConsoleOutput, у которого метод есть; unrated-сценарий чекера на решении пройден"),
    },
    "mid": {
        "дедупликация внутри одного файла": ("подтверждено",
            "тот же паттерн (findOneBy в цикле, flush в конце); сценарий чекера кросс-файловый — на приёмку не влияло"),
        "доменная логика в команде": ("подтверждено",
            "архитектурное наблюдение верно; соответствует эталону A 2.5 («среднее» — архитектурно слабое)"),
        "getErrorOutput → фатал на unrated": ("опровергнуто",
            "рантайм работает (Symfony передаёт ConsoleOutput); чекер прошёл 11/11"),
        "diff-миграции без CREATE TABLE": ("серьёзность завышена",
            "факт верен: миграции — diff по существующей схеме; но чекер развернул схему альтернативным, разрешённым заданием способом"),
        "float в totalCost": ("серьёзность завышена",
            "float-каст есть, но строка мёртвая: тут же перезаписывается bc-форматированием; потери точности итога нет"),
    },
    "good": {
        "ключ 0 у новых строк бонуса": ("серьёзность завышена",
            "паттерн кода верен, но последствий нет: бонус reprice работает — чекер 2/2; коллизия ключей не проявляется"),
        "guard платформы миграции": ("опровергнуто",
            "приёмка чекера на PostgreSQL 17 прошла, миграция выполнилась; заявленного класса PostgreSQL170Platform в зафиксированном DBAL нет"),
    },
    "tricky": {
        "формат ts при вставке": ("опровергнуто",
            "импорт чекера прошёл; сама модель пометила замечание как «вероятно» и рантайм не проверяла"),
    },
}
_VERDICT_CLS = {"подтверждено": "okmark", "серьёзность завышена": "deadmark burn",
                "опровергнуто": "deadmark fab"}

# orca: вердикты не в заданном формате, содержание размечено вручную по сырым
# текстам review.md (все 48 прогонов, 2026-11-02): замечания извлечены из её
# собственного JSON (ключ issues/findings; полный разбор дали 26 прогонов) и
# сверены с теми же фактами решений, что и выше. Примеры завышенных: «миграции
# среднего — схема не разворачивается» (чекер принял разрешённую альтернативу);
# опровергнутых: «unrated-ветвь бросает Error» (рантайм работает). Ключевой
# наход: run-08 (каверзное, max) — единственный за серию названный механизм
# оф-пика при первом импорте. (подтверждено, завышена, опровергнуто) на уровень.
ORCA_TRIAGE = {"max": (28, 2, 0), "low": (7, 2, 0), "medium": (6, 1, 2)}
# полнота orca по сырым текстам (из 5 реальных пропусков): max — оф-пик
# «каверзного» (run-08) + миграции и артефакты «плохого»; medium — миграции
# и артефакты «плохого»; low — 0: её прогоны на bad/tricky дали обрывы JSON
ORCA_CAUGHT = {"max": 3, "low": 0, "medium": 2}

# Архитектурные и качественные замечания против инструментального оракула
# (метрики этапа 2: phpstan/phpmd/phpcs/psalm/phpunit/AST — metrics-raw
# каждого решения; файловые проверки — grep по solution). Верификация 2026-11-02.
# (тема, где заявлено, оракул, факт, вердикт)
ARCH_ORACLE = [
    ("buildCsv ~98 строк / execute() 91–94 — ExcessiveMethodLength",
     "qwen, huihui, gemma31, orca",
     "phpmd-raw", "good: buildCsv 98; mid: execute 94; bad: execute 91 — все в phpmd",
     "подтверждено"),
    ("TOKEN_COLS объявлена и не используется", "qwen, huihui, orca",
     "phpstan-raw (tricky)", "TOKEN_COLS в первой ошибке phpstan", "подтверждено"),
    ("findPriceForEvent(): ?object — phpstan undefined method getUnit/getRate*",
     "qwen, gemma31, orca", "phpstan-raw (mid)",
     "5 ошибок 'on object', getUnit в сообщениях", "подтверждено"),
    ("Тестов нет", "qwen, huihui, gemma31, orca", "phpunit",
     "bad и mid: tests=0, coverage нет", "подтверждено"),
    ("Покрытие 26.4% (good) / 9.4% (tricky)", "orca, qwen", "phpunit coverage",
     "26.39 и 9.37 — совпадает", "подтверждено"),
    ("Шум статанализа: phpstan 49 (good/tricky), 24 (mid), 19 (bad)",
     "все модели", "metrics.json", "числа совпадают точно", "подтверждено"),
    ("Нет declare(strict_types=1)", "orca (mid)", "grep по src/",
     "mid: 8 из 9 файлов без declare (в остальных решениях — во всех)", "подтверждено"),
    ("CSV собирается без экранирования полей", "qwen, orca", "grep fputcsv по src/",
     "fputcsv нет ни в одном решении — sprintf-конкатенация", "подтверждено"),
    ("N+1: запрос цены и провайдера на каждое событие", "qwen, orca",
     "код rate-команд", "DQL findOneBy в цикле по событиям", "подтверждено"),
    ("Доменная логика живёт в Command, доменного слоя нет", "qwen, orca",
     "структура src/ + AST", "mid: wmc_worst = BillingRateCommand, src/Domain нет",
     "подтверждено"),
    ("Комментарий «bccomp в этой сборке сломан» неверен", "qwen, orca",
     "рантайм php 8.5 (раздел 6.2)", "bccomp работает; isZero корректен, но обоснование ложно",
     "подтверждено"),
]

# Темы архитектурно-качественного ревью (совпадают с 8.3) — покрытие считаем
# по всем замечаниям, включая minor; у orca — по сырым текстам review.md.
ARCH_THEMES = [
    ("длинный метод",  r"excessivemethodlength|buildcsv|9[148] строк"),
    ("TOKEN_COLS",     r"token_cols"),
    ("типизация object", r"\?object|object \$price|undefined method.*object|getunit\(\)"),
    ("тесты нет",      r"тест.{0,30}(отсутств|нет|0 )|0 тест|no tests|tests/ (пуст|только)"),
    ("покрытие %",     r"покрыт|coverage"),
    ("статанализ",     r"phpstan|psalm|phpmd|phpcs"),
    ("strict_types",   r"strict_types"),
    ("CSV-экран",      r"csv.{0,40}(экран|запят|quote|escap)|экранирован"),
    ("N+1",            r"n\+1"),
    ("домен в командах", r"доменн|анемичн|domain layer|доменного слоя|в команд"),
    ("bccomp-комментарий", r"bccomp.{0,80}(сломан|не подтвержд|необоснован|сомнител)|(не подтвержд|необоснован|сомнител).{0,80}bccomp"),
]
_ARCH_RX = [(n, re.compile(p, re.I)) for n, p in ARCH_THEMES]


def arch_coverage(runs) -> dict:
    """(model, thinking) -> множество названных архитектурно-качественных тем."""
    res = defaultdict(set)
    for r in runs:
        if r["protocol"] != "v2" or not r["valid"] or r.get("parse_ok") is False:
            continue
        v = r.get("verdict")
        if not v:
            continue
        import json as _json
        rp = MODELS / r["path"] / "review.json"
        full = _json.loads(rp.read_text(encoding="utf-8")) if rp.is_file() else {}
        joined = " || ".join(_json.dumps(d, ensure_ascii=False)
                             for d in full.get("defects", [])) \
            + " || " + str(full.get("note", ""))
        for name, rx in _ARCH_RX:
            if rx.search(joined, re.I):
                res[(r["model"], r["thinking"])].add(name)
    for rp in sorted((MODELS).glob("orcarouter-*/backend/code-review/run-*/review.md")):
        meta = json.loads(rp.with_name("run.json").read_text(encoding="utf-8"))
        txt = rp.read_text(encoding="utf-8")
        for name, rx in _ARCH_RX:
            if rx.search(txt, re.I):
                res[(meta["model"], meta["thinking"])].add(name)
    return res



def defect_precision(runs) -> dict:
    """Точность замечаний critical/major зачётных прогонов текущего
    контракта: (model, thinking) -> [подтверждено, серьёзность завышена,
    опровергнуто] — по вердиктам судьи-оценщика (_DEF_VERDICTS)."""
    cnt = defaultdict(lambda: [0, 0, 0])
    for r in runs:
        if r["protocol"] != "v2" or r["prompt_version"] != 2 \
                or not r["valid"] or r.get("parse_ok") is False:
            continue
        for d in r.get("defects") or []:
            if d.get("severity") not in ("critical", "major"):
                continue
            cl = _def_cluster(r["slug"], d.get("file", ""), d.get("desc", ""))
            v = _DEF_VERDICTS.get(r["slug"], {}).get(cl)
            if v:
                cnt[(r["model"], r["thinking"])]\
                    [{"подтверждено": 0, "серьёзность завышена": 1,
                      "опровергнуто": 2}[v[0]]] += 1
    return cnt


def section_precision(runs) -> str:
    """Раздел 8: сверка замечаний (defects critical/major) с кодом решений
    и фактами чекера — точность ревью, дополнение recall раздела 7."""
    per = defaultdict(int)              # (slug, cluster) -> замечаний
    who = defaultdict(set)              # (slug, cluster) -> модели
    for r in runs:
        if r["protocol"] != "v2" or r["prompt_version"] != 2 \
                or not r["valid"] or r.get("parse_ok") is False:
            continue
        for d in r.get("defects") or []:
            if d.get("severity") not in ("critical", "major"):
                continue
            slug = r["slug"]
            cl = _def_cluster(slug, d.get("file", ""), d.get("desc", ""))
            per[(slug, cl)] += 1
            who[(slug, cl)].add(SHORT.get(r["model"], r["model"]))
    rows_cnt = defect_precision(runs)
    rows = []
    unmapped = 0
    for slug in SLUG_ORDER:
        cls = [(k, n) for (s, k), n in per.items() if s == slug]
        for cl, n in sorted(cls, key=lambda x: -x[1]):
            verdict, comment = _DEF_VERDICTS.get(slug, {}).get(cl, ("не размечено", ""))
            if verdict == "не размечено":
                unmapped += n
            rows.append(
                f"<tr{' class=neg' if verdict == 'опровергнуто' else ''}>"
                f"<td><b>{SLUG_TITLES[slug]}</b></td><td>{esc(cl)}</td>"
                f"<td class=num>{n}</td>"
                f"<td class=sub>{esc(', '.join(sorted(who[(slug, cl)])))}</td>"
                f"<td><span class='{_VERDICT_CLS.get(verdict, 'deadmark burn')}'>{verdict}</span></td>"
                f"<td class=sub>{esc(comment)}</td></tr>")
    pres = []
    for (model, th), (c, z, o) in rows_cnt.items():
        pres.append((model, th, c, z, o, c + z + o))
    pres.sort(key=lambda e: (-(e[2] / e[5] if e[5] else 0), -e[5]))
    prow = []
    for model, th, c, z, o, tot in pres:
        prc = c / tot if tot else 0
        prow.append(
            f"<tr><td><b>{esc(SHORT.get(model, model))}</b>"
            f"<div class=sub>thinking {th}</div></td>"
            f"<td class=num>{tot}</td><td class=num>{c}</td>"
            f"<td class=num>{z}</td><td class='num {delta_class(o)}'>{o}</td>"
            f"<td class=num><b>{prc:.0%}</b></td></tr>")
    n_all = sum(e[5] for e in pres)
    n_c, n_z, n_o = (sum(e[i] for e in pres) for i in (2, 3, 4))
    note_unmapped = (f" Не размечено вручную: {unmapped} замечаний тем, не покрытых"
                     f" таблицей — сверить отдельно." if unmapped else "")
    return f"""
<h2 id=precision><span class=no>8.</span>Качество замечаний: точность против кода и чекера</h2>
<p class=lead>Ревью ценно не баллами, а замечаниями. Все замечания серьёзностей
critical и major зачётных прогонов текущего контракта ({n_all} штук) сгруппированы по
теме и прогнаны через судью-оценщика (раздел 2): судья яруса 3 — проект,
метрики, данные чекера — плюс рантайм-верификация скриптами (php 8.5,
docker psql). В этой серии прогон выполнен вручную, методика закреплена
в REVIEWER.md; в сводке не только полнота (7), но и точность замечаний. Три исхода: <b>подтверждено</b> — факт и заявленные
последствия верны; <b>серьёзность завышена</b> — наблюдение о коде верно, заявленных
последствий нет; <b>опровергнуто</b> — заявленный дефект не работает. Мелкие (minor)
не размечались: это стилистика и предупреждения статанализаторов.{note_unmapped}</p>
<table>
<tr><th class=narrow>Решение</th><th>Тема замечания</th><th class=narrow>Сессий</th>
<th>Кто называл</th><th class=narrow>Вердикт</th><th>Комментарий</th></tr>
{''.join(rows)}
</table>
<h3><span class=no>8.1.</span>Точность по строкам рейтинга</h3>
<p class=lead>Итог по серии: подтверждено {n_c}, серьёзность завышена {n_z},
опровергнуто {n_o}. Точность = подтверждённые / все critical+major замечания строки.
Точность входит в итог 4.1 компонентом «неточность» (1 − точность);
согласие с судьёй |Δ| — вне итога, как второстепенный сигнал.</p>
<table>
<tr><th>Модель × мышление</th><th class=narrow>Замечаний</th><th class=narrow>Подтв.</th>
<th class=narrow>Завышена</th><th class=narrow>Опроверг.</th><th class=narrow>Точность</th></tr>
{''.join(prow)}
</table>
<h3><span class=no>8.2.</span>orca: содержание без формата</h3>
<p class=lead>orca во всех 48 прогонах не выдала вердикт в заданном формате и в
конвейер верификации не попала — но её разборы сохранились в сыром виде
(review.md). Все 48 текстов размечены вручную той же схемой: замечания извлечены
из её собственного JSON (ключ issues/findings; полный разбор дали 26 прогонов,
остальные — обрывы JSON), каждое сверено с кодом решения и фактами чекера, как
выше. <b>Результат: orca — самый точный замечатель серии.</b> На max ни одного
опровергнутого замечания; типичная неточность — завышенный вес миграций
«среднего» решения, как у большинства моделей. И главное: <b>run-08 (каверзное,
max) — единственный за всю серию названный механизм оф-пика</b>: «оф-пик не
сохраняется для NEW-провайдера, setOffPeak() только в update-ветке» — дословно
верифицированная причина из раздела 6, которую не назвал ни один зачётный
ревьюер. Оценка вне контракта: валидного вердикта у orca нет, поэтому в 4.1 её итог
считается из этих чисел — неточность и неполнота по ручной разметке разборов
(формат в итог не входит): orca max — 1-е место, medium — 4-е, low — 8-е. Полнота: max — 3 из 5 (оф-пик, миграции, артефакты), medium — 2 из 5,
low — 0 (её прогоны на bad/tricky дали обрывы JSON вместо разборов).</p>
<table>
<tr><th>Модель × мышление</th><th class=narrow>Размечено</th><th class=narrow>Подтв.</th>
<th class=narrow>Завышена</th><th class=narrow>Опроверг.</th><th class=narrow>Точность</th></tr>
{''.join(f"<tr><td><b>orca</b><div class=sub>thinking {th}</div></td>"
         f"<td class=num>{c + z + o}</td><td class=num>{c}</td>"
         f"<td class=num>{z}</td><td class='num {delta_class(o)}'>{o}</td>"
         f"<td class=num><b>{c / (c + z + o):.0%}</b></td></tr>"
         for th, (c, z, o) in ORCA_TRIAGE.items())}
</table>
<p class=sub>Разметка ручная, зафиксирована в коде отчёта (ORCA_TRIAGE); метод и
примеры — в комментарии к ней.</p>
<h3><span class=no>8.3.</span>Архитектурные и качественные замечания против инструментов</h3>
<p class=lead>Утверждение «у архитектурных замечаний нет оракула» неверно: оракул —
те же инструменты этапа 2, чьи данные лежат в metrics.json каждого решения
(phpstan, phpmd, phpcs, psalm, phpunit, AST-метрики), плюс файловые проверки
по решению. Каждая уникальная тема такого рода из всех разобранных замечаний
(включая minor) сверена с фактом:</p>
<table>
<tr><th>Замечание (тема)</th><th>Кто заявлял</th><th class=narrow>Оракул</th>
<th>Факт</th><th class=narrow>Вердикт</th></tr>
{''.join(
    f"<tr><td>{esc(t)}</td><td class=sub>{esc(w)}</td>"
    f"<td class=sub>{esc(o)}</td><td class=sub>{esc(f)}</td>"
    f"<td><span class='{_VERDICT_CLS[v]}'>{v}</span></td></tr>"
    for t, w, o, f, v in ARCH_ORACLE)}
</table>
<p class=sub>{len(ARCH_ORACLE)} тем из {len(ARCH_ORACLE)} подтверждены инструментами и кодом;
опровергнутых нет. Две оговорки. Числовые темы (счётчики phpstan, покрытие,
длина метода) модели частично пересказывали из выданного им metrics.json —
сверка подтверждает соответствие фактам, но не самостоятельность находки;
зато предметные (мёртвая строка, отсутствие fputcsv, strict_types, N+1) —
результат собственного чтения кода. И верифицированность темы не равна её
важности: подтверждённое «нет strict_types» — это гигиена, а не дефект
обязательного поведения.</p>"""


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
<h2 id=validity><span class=no>9.</span>Валидность прогонов</h2>
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
<h2 id=runs><span class=no>10.</span>Прогоны и данные</h2>
<p class=lead>Все {len(runs)} прогонов этапа (пилот + серия v1 + серия v2 + перезапуски).
Время и токены фиксировались в run.json каждой сессии; сводка по моделям — ниже,
по-прогонно — в свёрнутом списке. Артефакты:
models/&lt;модель&gt;/backend/code-review/run-XX/ — run.json (условия, версия
протокола, целостность, время, токены),
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
<h2 id=next><span class=no>11.</span>Что дальше</h2>
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


TIP_JS = """
<script>
(function(){
  var box = document.createElement('div');
  box.style.cssText = 'position:fixed;z-index:99;display:none;max-width:360px;'
    + 'background:#1d2433;color:#fff;font:12px/1.55 "Segoe UI",system-ui,sans-serif;'
    + 'padding:9px 12px;border-radius:9px;box-shadow:0 8px 22px rgba(20,30,50,.32);'
    + 'pointer-events:none;white-space:normal';
  document.body.appendChild(box);
  document.addEventListener('mouseover', function(e){
    var el = e.target.closest && e.target.closest('[title],[data-tip]');
    if (!el) { box.style.display = 'none'; return; }
    var t = el.getAttribute('data-tip') || el.getAttribute('title');
    if (!t) { box.style.display = 'none'; return; }
    if (el.hasAttribute('title')) { el.setAttribute('data-tip', t); el.removeAttribute('title'); }
    box.textContent = t;
    box.style.display = 'block';
    box.style.left = '0px'; box.style.top = '0px';
    var r = el.getBoundingClientRect();
    var w = box.offsetWidth, h = box.offsetHeight;
    var x = Math.min(r.left, window.innerWidth - w - 8);
    var y = r.bottom + 6;
    if (y + h > window.innerHeight - 8) y = r.top - h - 6;
    if (y < 8) y = Math.max(8, r.top + (r.height - h) / 2);
    box.style.left = Math.max(8, x) + 'px';
    box.style.top = y + 'px';
  });
  document.addEventListener('scroll', function(){ box.style.display = 'none'; }, true);
})();
</script>
"""


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
{section_precision(runs)}
{section_validity(runs, profiles)}
{section_runs(runs, grid)}
{section_next()}
<div class=footer>Этап 3 методики «Модели»: код-ревью решений этапа 2. Отчёт собран
из артефактов models/&lt;модель&gt;/backend/code-review/run-XX/ (run.json, review.json) и
эталон-карточек code-review/references/. Билдер: code-review/build_review_report.py.
Методика: code-review/REVIEWER.md. Дата сборки: {__import__('datetime').date.today().isoformat()}.</div>
</div></body></html>"""
    html = html.replace("</body>", TIP_JS + "</body>")
    OUT.write_text(html, encoding="utf-8")
    n_valid = sum(1 for r in runs if r["valid"])
    print(f"отчёт: {OUT} ({len(html)/1024:.0f} КиБ); прогонов {len(runs)}, валидных {n_valid}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

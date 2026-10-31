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
.dchip{display:inline-block;margin:1px 2px 1px 0;padding:0 5px;border-radius:7px;background:#eef1f6;color:#315f9e;font-size:10.5px;font-weight:600;line-height:15px;white-space:nowrap}
.alist details{margin:8px 0;border:1px solid var(--line);border-radius:12px;background:#fff;overflow:hidden}
.alist summary{cursor:pointer;padding:10px 16px;font-size:14.5px}
.alist details[open]>summary{border-bottom:1px solid var(--line)}
.alist table{margin:0}
.footer{margin-top:56px;color:var(--mut);font-size:12.5px;border-top:1px solid var(--line);padding-top:14px}
"""


def load_references() -> dict:
    refs = {}
    for p in sorted(glob.glob(str(HERE / "references" / "*.json"))):
        c = json.loads(Path(p).read_text(encoding="utf-8"))
        refs[c["slug"]] = c
    return refs


def load_runs() -> list[dict]:
    """Все прогоны ревью с полями вердикта/валидности; валидность — по гейтам."""
    runs = []
    for rp in sorted(MODELS.glob("*/backend/review/run-*")):
        rj, vj = rp / "run.json", rp / "review.json"
        if not rj.is_file():
            continue
        meta = json.loads(rj.read_text(encoding="utf-8"))
        run = {
            "dir": rp.name,
            "model": meta.get("model", "?"),
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
    """(модель, решение) -> прогоны; и профили моделей."""
    grid = defaultdict(list)
    for r in runs:
        if r["valid"]:
            grid[(r["model"], r["slug"])].append(r)
    profiles = {}
    for model in MODEL_ORDER:
        per_slug, spreads, deltas, defects = {}, [], [], defaultdict(int)
        for slug in SLUG_ORDER:
            rs = grid.get((model, slug), [])
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
            profiles[model] = {
                "per_slug": per_slug,
                "spread_mean": statistics.mean(spreads) if spreads else 0,
                "delta_mean": statistics.mean(deltas) if deltas else 0,
                "n": sum(p["n"] for p in per_slug.values()),
                "defects": dict(defects),
            }
    return grid, profiles


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

def section_header() -> str:
    return f"""
<header>
<h1>Этап 3: код-ревью решений этапа 2 локальными моделями</h1>
<p>Ревьюер получает <b>слепой</b> набор материалов чужого решения (TASK.md, код,
дифф, метрики качества) и судит его по рубрике: ось A «код и архитектура»,
ось B «процесс и коммуникация», F «соответствие заданию» — плюс список
дефектов. Чекер и golden не выдаются: метрики — запахи, указатели, где
смотреть, а не вердикт. Код не исполняется и не правится (инструмент pi —
только чтение, sha256-манифест до/после).</p>
<p>Объекты — четыре решения этапа 2 (полный зачёт, «работает, но слабо»,
каверзное с дефектом на данных, «плохое, но не трэш») с замороженными
эталонами ярусов 3/4. Отчёт собран из артефактов прогонов:
models/&lt;модель&gt;/backend/review/run-XX/.</p>
</header>
<nav class=toc>
<div class=toc-title>Содержание</div>
<ul>
<li><a href="#tldr">1. Главные выводы</a></li>
<li><a href="#method">2. Методика</a></li>
<li><a href="#objects">3. Объекты ревью и эталоны</a></li>
<li><a href="#results">4. Результаты моделей</a></li>
<li><a href="#stability">5. Повторяемость</a></li>
<li><a href="#findings">6. Слепое пятно и находки</a></li>
<li><a href="#validity">7. Валидность и гейты</a></li>
<li><a href="#runs">8. Прогоны и данные</a></li>
<li><a href="#next">9. Что дальше</a></li>
</ul>
</nav>"""


def section_tldr(runs, profiles, refs, grid) -> str:
    n_valid = sum(1 for r in runs if r["valid"])
    n_all = len(runs)
    huihui = profiles.get("huihui-ai:pi-100k")
    blind = []
    for model, prof in profiles.items():
        t = prof["per_slug"].get("tricky")
        if t:
            blind.append((model, t["vals"]["axis_a"]))
    blind_txt = "; ".join(f"{SHORT.get(m, m)} — {runs_cell(v)}" for m, v in blind)
    return f"""
<h2 id=tldr><span class=no>1.</span>Главные выводы</h2>
<p class=lead>Ревью — отдельная способность, и она распределилась иначе, чем написание кода.
Лучший калибровщик — <b>huihui</b>: точные баллы на «хорошем» (A 3.5 — в эталон) и «среднем»
(A 2.5 — в эталон), идеальная повторяемость (2.5/2.5/2.5 на «плохом»). Обе сборки gemma
систематически завышают: «хорошее» и «каверзное» получают A 4.0.</p>
<p class=lead><b>Повторяемость высокая у всех</b>: разброс между тремя изолированными прогонами
не выше 1 балла по любой оси, в среднем 0–0.3. Вердикт ревьюера — устойчивая характеристика
модели, а не лотерея; трёх прогонов достаточно.</p>
<p class=lead><b>Слепое пятно яруса 4 воспроизводится у ревьюеров</b>: на «каверзном» решении
(стоимости отчётов завышены из-за потерянного при первичном импорте оф-пика; чекер 9/11)
A по прогонам (эталон 2.5): {esc(blind_txt)}.
Worked example из TASK.md у этого решения сходится — дефект живёт в данных,
а не в видимой логике; статическое ревью его не нашло ни разу.</p>
<p class=lead><b>Инструкция следует не всем</b>: orca провела все 12 ревью всерьёз
(15–78 чтений файлов), но ни разу не выдала JSON-контракт — своё форматирование со шкалой 0–5.
e2b в пилоте трижды выдала вердикт <b>без единого чтения файла</b> — гейт «tool_calls&gt;0»
такие прогоняет в брак. Из {n_all} прогонов валидны {n_valid}.</p>
<p class=lead><b>Эталон тоже ошибается</b>: механизм «усечение вместо HALF_UP» (вердикт яруса 3
для плохого решения) был опровергнут ревью qwen — самостоятельный пересчёт вывел на
рантайм-проверку php, подтвердившую ревьюера. Настоящие механизмы: оф-пик теряется при
первичном импорте провайдера (оба решения с падающими отчётами) и bcadd без масштаба
(только плохое). Карточки исправлены, баллы не двигались.</p>
<p class="excap sb">Инсайты</p>
<div class=cards>
<div class="card g"><b>Ревью дешевле кодинга на порядок</b>Сессия ревью — 0.5–6.5 минут
и 0.3–1 млн токенов против 25–45 минут агентного кодинга. Сетка 4 модели × 3 прогона ×
4 решения = 48 сессий — несколько часов серверного времени.</div>
<div class="card"><b>«Писать» и «судить» — разные навыки</b>huihui писала решения лучше всех
(13/13) и ревьюит точнее всех. Но orca, писавшая на уровне huihui (13/13 в этапе 2),
как ревьюер не выдержала даже формат вывода. gemma4:26b писала средне и ревьюит, завышая всё
подряд.</div>
<div class="card w"><b>Ложные срабатывания — главный источник промахов</b>Единственный
существенный промах qwen (functional 2.5 против эталона 4.0 на «среднем») — вывод «PHP fatal»
из статической сигнатуры: метода нет на OutputInterface, но Symfony передаёт ConsoleOutput,
у которого он есть. Ревьюер поверил типу, а не рантайму.</div>
<div class="card b"><b>Жёсткость бывает неуместной</b>gemma4:31b ставит «плохому» решению
B 0/0/0 — строже эталона (B 1.0): наказывает отсутствие артефактов по нижней ступени шкалы.
Абсолютная жёсткость без дифференциации — та же потеря информации, что и завышение.</div>
</div>"""


def section_method() -> str:
    return """
<h2 id=method><span class=no>2.</span>Методика</h2>
<p class=lead>Полная методика — <code>reviewer/REVIEWER.md</code>. Ключевое:</p>
<table>
<tr><th style=width:28%>Элемент</th><th>Решение</th></tr>
<tr><td><b>Слепой протокол</b></td><td>Чекер, golden, fixtures, scorecard'ы, имя модели-автора и путь к прогону не выдаются. Провенанс вырезан даже из metrics.json.</td></tr>
<tr><td><b>Метрики даются</b></td><td>phpstan/phpmd/phpcs/psalm/scc/AST — как запахи: «каждый сигнал проверь по коду… не пересказывай метрики». В реальности ревью работает так же.</td></tr>
<tr><td><b>Три оси</b></td><td>A «код и архитектура» и B «процесс и коммуникация» — рубрика яруса 3, переписанная под слепоту; F «соответствие заданию» — функциональная проверка по коду, без запуска. Все 0–4 с шагом 0.5.</td></tr>
<tr><td><b>Дефекты</b></td><td>Список с файлом, строками и severity (critical/major/minor); неуверенность — minor и формулировка «подозрение»; верить коду, а не PR-описаниям.</td></tr>
<tr><td><b>Только чтение</b></td><td><code>pi --tools read</code> — edit/write/bash отключены структурно; код не исполнить (vendor отсутствует) и не изменить. Манифест sha256 до/после: изменение = прогон в брак.</td></tr>
<tr><td><b>Гейты валидности</b></td><td>целостность ok; <b>tool_calls &gt; 0</b> — вердикт без единого чтения = выдумка; JSON-вердикт распознан (экстрактор чинит типовые поломки: markdown-обрамление, невалидные \\-эскейпы).</td></tr>
<tr><td><b>Изоляция</b></td><td>Рабочая копия вне дерева Models, PI_CODING_AGENT_DIR свой на прогон; в сетке модель выгружается из VRAM после каждой сессии (keep_alive: 0) — прогон не наследует кэш предыдущего.</td></tr>
<tr><td><b>Сверка с судьёй</b></td><td>|ΔA|, |ΔB|, |ΔF| к замороженным эталонам (references/*.json); черновик зачёта: |Δ| ≤ 0.5 — зачёт, ≤ 1.0 — частично, больше — промах.</td></tr>
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
            f"<td class=num>A {num(b['tier3']['axis_a'])} · B {num(b['tier3']['axis_b'])}<div class=sub>ярус 4 (слепо): A {num(b['tier4_blind']['axis_a'])}</div></td>"
            f"<td class=num>{num(e['axis_a'])} · {num(e['axis_b'])} · {num(e['functional'])}</td>"
            f"<td>{esc(fails)}</td></tr>")
    return f"""
<h2 id=objects><span class=no>3.</span>Объекты ревью и эталоны</h2>
<p class=lead>Четыре решения этапа 2. Принцип пары: «плохое» — не трэш (команды и сущности
на месте, дефекты в арифметике и lifecycle импорта), «хорошее» — не идеальное (N+1, память
вместо GROUP BY, phpstan 49). Различение тонкое — вот что меряем. Эталон-карточки заморожены
2026-10-30; после пилота в них исправлены механизмы дефектов (changelog в карточках),
ожидаемые баллы не двигались.</p>
<table>
<tr><th>Решение</th><th>Чекер</th><th>Судьи (A/B)</th><th class=narrow>Эталон A·B·F</th><th>Упавшие сценарии</th></tr>
{''.join(rows)}
</table>
<p class=sub>Судьи: ярус 3 — GLM-5.3-Flash с чекером и метриками, ярус 4 — слепое ревью диффа.
На каверзном и плохом решениях слепой судья завысил A (4.0 и 2.5) — это и есть слепое пятно,
которое этап 3 проверяет у ревьюеров. Дефекты эталона верифицированы кодом и рантаймом
(см. раздел 6).</p>"""


def section_results(profiles, refs) -> str:
    blocks = []
    head = ("<tr><th>Решение</th><th>n</th>"
            "<th>A «код и архитектура»</th><th>B «процесс»</th><th>F «задание»</th>"
            "<th class=narrow>|Δ|A</th><th class=narrow>|Δ|B</th><th class=narrow>|Δ|F</th><th>Разброс A/B/F</th></tr>")
    n_shown = 0
    for model in MODEL_ORDER:
        prof = profiles.get(model)
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
<h3><span class=no>4.{n_shown}.</span>{esc(SHORT.get(model, model))}
<span class=sub>{params[0]} · {params[1]} · thinking max · контекст 102400 · {prof['n']} валидных прогонов</span></h3>
<table>{head}{''.join(rows)}</table>""")
    return f"""
<h2 id=results><span class=no>4.</span>Результаты моделей</h2>
<p class=lead>Вердикты по прогонам (каждая ячейка — отдельный изолированный прогон),
средние |Δ| к эталону и разброс max−min. Цвет |Δ|: зелёный — 0, жёлтый — ≤1, красный — &gt;1.</p>
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
    rows = sorted(((SHORT.get(m, m), p["spread_mean"]) for m, p in profiles.items()),
                  key=lambda x: x[1])
    chart = _hbars_svg(rows, 0.5, "Средний разброс вердикта между прогонами (оси A/B/F вместе), баллов")
    cards = f"""
<div class="card g"><b>Три прогона достаточно</b>Разброс 0–0.3 балла в среднем: ревью
на thinking max — устойчивая функция от решения, а не лотерея. У huihui на «плохом»
все три прогона дали ровно A 2.5.</div>
<div class="card"><b>Повторяемость ≠ точность</b>gemma4:26b повторяет свои завышенные
4.0 на «хорошем» и «каверзном» с нулевым разбросом — стабильно неправильно. Эталон калибрует
смещение, но не отменяет его.</div>
<div class="card w"><b>Изоляция прогонов полная</b>Каждая сессия — свой workspace и свой
pi-home; между сессиями модель выгружается из VRAM. Наследования кэша и контекста нет:
разброс — чистая вариативность сэмплирования.</div>"""
    return f"""
<h2 id=stability><span class=no>5.</span>Повторяемость</h2>
<p class=lead>Каждая пара «модель × решение» ревьюилась тремя изолированными прогонами
(у qwen3.8 — один пилотный, у gemma4:26b на «среднем» — два из-за сгоревших сессий).
Разброс max−min по осям — в таблицах раздела 4, свод по моделям — ниже.</p>
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
<h2 id=findings><span class=no>6.</span>Слепое пятно и находки</h2>
<h3><span class=no>6.1.</span>Слепое пятно статического ревью</h3>
<p class=lead>На «каверзном» решении (чекер 9/11: стоимости отчётов завышены, потому что
оф-пик-скидка теряется при первичном импорте провайдера — <code>setOffPeak</code> вызывается
только в ветке обновления, а чекер всегда стартует на чистой БД) worked example из TASK.md
сходится, и все ревьюеры поверили коду:</p>
<p>{';<br>'.join(blind)}.</p>
<p class=lead>Ни одно ревью не нашло дефект чтением кода — как и слепой судья яруса 4 (A 4.0).
Верифицированный механизм (арифметика по событиям, сходящаяся с диффом чекера до цента,
и рантайм-прогон) появился только у судьи с чекером. Урок: дефекты жизненного цикла данных
(«первая запись vs обновление») статическому ревью видны хуже всего — они не в функции окна,
а в путях её заполнения.</p>
<h3><span class=no>6.2.</span>Ревьюер поймал ошибку судьи</h3>
<p class=lead>Эталон «плохого» решения (по ярусу 3) содержал механизм «усечение вместо HALF_UP
в bcRoundHalfUp». Ревью qwen при собственном пересчёте примера объявило, что округление
<b>работает</b>. Проверка на php 8.5: <code>bcadd('14.26249875','0.0000005',6) = '14.262499'</code> —
операнды не усекаются, аддер-трюк эквивалентен HALF_UP. Настоящие причины падения отчётов:
потеря оф-пика при первичном импорте (оба решения с падающими отчётами — один класс бага)
и <code>bcadd</code> без масштаба в накоплении (только плохое; h02: 3.312495 против 3.312496).
Эталон-карточки исправлены с changelog, ожидаемые баллы не двигались.</p>
<h3><span class=no>6.3.</span>Ложные срабатывания: сигнатура против рантайма</h3>
<p class=lead>Единственный серьёзный промах qwen (F 2.5 против эталона 4.0 на «среднем»
решении) — «critical: PHP fatal, метода getErrorOutput() нет на OutputInterface». Наблюдение
о типе верное, вывод ложный: Symfony передаёт ConsoleOutput, у которого метод есть (чекер
этого решения — 11/11). Урок для промпта ревьюера следующей версии: «статическая сигнатура
не равна рантайму — проверь, что реально передаётся».</p>
<h3><span class=no>6.4.</span>Что ревьюеры нашли сверх баллов</h3>
<p class=lead>Лучшие сессии работают как настоящие ревью: qwen пересчитала worked example,
сверила счётчик phpunit с заявкой PR, опровергла запах LCOM4 («артефакт value-объектов»)
и нашла фактическую ошибку в PR_DESCRIPTION (ссылка на несуществующий expected_report.csv);
huihui и gemma4:31b независимо указали на несоответствие формата cost_usd и мусор в диффе.
Полные списки дефектов с severity — в review.json каждого прогона (раздел 8).</p>"""


def section_validity(runs, profiles) -> str:
    total = len(runs)
    valid = sum(1 for r in runs if r["valid"])
    by_kind = defaultdict(list)
    for r in runs:
        if not r["valid"]:
            by_kind[r.get("fail_kind", "?")].append(r)
    rows = []
    kind_desc = {
        "fab": ("fab", "Вердикт без чтения", "Ни одного tool-call: вердикт выдуман (e2b, пилот)"),
        "contract": ("contract", "Контракт проигнорирован", "Работа всерьёз, но формат вывода свой (orca)"),
        "burn": ("burn", "Бюджет выхода сгорел", "Финальный вызов ушёл в thinking без текста (gemma4:26b)"),
        "integ": ("integ", "Целостность нарушена", "Рабочая копия изменена — в свод не попадает"),
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
    return f"""
<h2 id=validity><span class=no>7.</span>Валидность и гейты</h2>
<p class=lead>Из <b>{total}</b> прогонов валидны <b>{valid}</b>. Три гейта отсекают брак до
скоринга: целостность рабочей копии (sha256 до/после), факт чтения (tool_calls &gt; 0),
JSON-вердикт по контракту. Целостность нарушена ни разу — инструмент чтения делает правки
структурно невозможными.</p>
<table>
<tr><th>Тип брака</th><th class=narrow>Прогонов</th><th>Модели</th><th>Механика</th></tr>
{''.join(rows)}
</table>
<p class=sub>Отдельные случаи: orca — 12/12 прогонов с реальной работой и своим форматом вывода
(полноценные ревью со шкалой 0–5, issues, metrics_check — но не наш контракт; править промпт
под модель запрещено п.2 правил). gemma4:26b — 3 сессии с сгоревшим thinking; после
перезапуска закрыты все слоты, кроме одного (среднее решение — 2 из 3 прогонов).
e2b — 3 «вердикта» без единого чтения файла, со ссылками на несуществующие файлы.</p>"""


def section_runs(runs, grid) -> str:
    rows = []
    for r in sorted(runs, key=lambda x: (x["folder"], x["dir"])):
        short = SHORT.get(r["model"], r["model"])
        v = r["verdict"]
        if r["valid"]:
            scores = f"A {num(v['axis_a'])} · B {num(v['axis_b'])} · F {num(v['functional'])}"
            marks = '<span class=okmark title="все гейты пройдены">ok</span>'
        else:
            kind = r.get("fail_kind", "?")
            title = {"fab": "нет чтений", "contract": "не по контракту",
                     "burn": "thinking сгорел", "integ": "целостность"}.get(kind, kind)
            scores = "—"
            marks = f'<span class="deadmark {kind}">{title}</span>'
        dur = f"{r['duration_s']/60:.0f} мин" if r["duration_s"] else "—"
        tok = f"{r['tokens']/1e6:.1f}M" if r["tokens"] else "—"
        rows.append(
            f"<tr{' class=neg' if not r['valid'] else ''}><td class=num>{esc(short)}</td>"
            f"<td class=num>{esc(r['dir'])}</td><td>{SLUG_TITLES.get(r['slug'], r['slug'])}</td>"
            f"<td>{marks} <span class=num>{scores}</span></td>"
            f"<td class=num>{r['tool_calls']}</td><td class=num>{dur}</td><td class=num>{tok}</td></tr>")
    return f"""
<h2 id=runs><span class=no>8.</span>Прогоны и данные</h2>
<p class=lead>Все {len(runs)} прогонов этапа (пилот + сетка + перезапуски). Артефакты:
models/&lt;модель&gt;/backend/review/run-XX/ — run.json (условия, гейты, токены),
review.json (вердикт + дефекты), review.md (сырой ответ), pi-session/ (полный след сессии),
review-score.json (сверка с эталоном).</p>
<div class=alist><details><summary>Показать все прогоны ({len(runs)})</summary>
<table>
<tr><th>Модель</th><th class=narrow>Прогон</th><th>Решение</th><th>Вердикт</th><th class=narrow>Чтений</th><th class=narrow>Время</th><th class=narrow>Токены</th></tr>
{''.join(rows)}
</table></details></div>"""


def section_next() -> str:
    return """
<h2 id=next><span class=no>9.</span>Что дальше</h2>
<table>
<tr><th style=width:34%>Направление</th><th>Смысл</th></tr>
<tr><td><b>Расширение сетки</b></td><td>Оставшиеся локальные модели набора тем же батчем
(3 прогона × 4 решения); qwen3.8 добить до трёх прогонов. orca учтена как «контракт 0/12»,
сырые ревью — в данных.</td></tr>
<tr><td><b>Recall/precision дефектов</b></td><td>Сопоставление списков дефектов ревьюеров
с эталонными (сейчас — вручную на выборке); позже — скрипт-судья в паттерне judge_tier3.</td></tr>
<tr><td><b>Калибровка зачёта</b></td><td>Пороги |Δ| ≤ 0.5 / ≤ 1.0 — черновые; при расширении
сетки — пересмотр с учётом систематических смещений моделей (завышение gemma).</td></tr>
<tr><td><b>Промпт ревьюера v2</b></td><td>Уроки пилота: предупреждение о статической сигнатуре
vs рантайме; явный пример JSON-контракта в user-сообщении (без изменения условий под модель —
для всех одинаково).</td></tr>
<tr><td><b>Корреляция «пишет ↔ ревьюит»</b></td><td>Сопоставить итоги этапа 2 и этапа 3 по
моделям с валидными вердиктами; гипотеза «сильный писатель = сильный ревьюер» пока
подтверждается только на huihui.</td></tr>
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
{section_header()}
{section_tldr(runs, profiles, refs, grid)}
{section_method()}
{section_objects(refs)}
{section_results(profiles, refs)}
{section_stability(profiles)}
{section_findings(runs, profiles)}
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

#!/usr/bin/env python3
"""Генератор отчёта пилота этапа 2 (тестовое «биллинг LLM-токенов»).

Стиль — как у отчёта этапа 1 (interview/build_report.py): светлый документ,
нумерованные секции, таблицы с бейджами и барами, самописные SVG без внешних
зависимостей. Все числа — из артефактов прогонов (run.json, check.json,
metrics.json, session-profile.md) моделей/<модель>/test-assignment/backend/.

Запуск: python3 interview/test-assignment/backend/billing/build_pilot_report.py
Выход: reports/TEST-ASSIGNMENT-pilot-report.html
"""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[4]
OUT = ROOT / "reports" / "TEST-ASSIGNMENT-pilot-report.html"

# --- проверенные данные прогонов (run.json / check.json / metrics.json) -----
# dur — минуты; tok — млн токенов; peak — K токенов; comp — автокомпакты+правки
RUNS = [
    # label, примечание, mand, bonus, A1, A2, dur, tok, peak, comp, ps, pm, pc, loc, kind
    ("qwen3.8 · r2", "xhigh", 11, 2, 4, 3.5, 22.4, 6.79, 87.0, 2, 30, 39, 76, 1660, "full"),
    ("huihui · r2", "xhigh", 11, 2, 4, 3.5, 40.2, 14.45, 87.0, 5, 35, 18, 26, 1674, "full"),
    ("orca · r2", "ctx100k", 10, 2, 3, 3.0, 21.2, 5.92, 86.4, 2, 33, 46, 36, 1149, "part"),
    ("qwen3.8 · r1", "≈medium", 11, 0, 3, 3.5, 21.1, 7.39, 87.9, 2, 38, 42, 91, 1600, "part"),
    ("huihui · r1", "≈medium", 11, 0, 3, 3.5, 17.8, 7.72, 86.6, 2, 27, 56, 86, 1212, "part"),
    ("orca · r1", "окно 32K", 10, 1, 3, 3.5, 25.0, 4.64, 32.8, 15, 26, 42, 24, 1116, "part"),
    ("gemma4:31b", "pi-100k", 10, 1, 3, 2.5, 6.1, 2.01, 45.9, 0, 21, 18, 17, 798, "part"),
    ("muse · r2", "max", 9, 0, 2, 1.5, 4.8, 0.96, 31.9, 3, 24, 44, 8, 736, "part"),
    ("muse · r1", "32K, high", 1, 0, 1, 2.5, 5.8, 1.52, 32.8, 2, 17, 27, 20, 734, "fail"),
    ("ornith · r1", "окно 32K", 1, 0, 1, 1.5, 26.0, 12.88, 32.8, 53, 50, 36, 12, 996, "fail"),
    ("gemma4:e2b", "ctx100k", 0, 0, 1, 0.5, 0.35, 0.11, 13.8, 0, 41, 10, 28, 282, "fail"),
    ("medgemma", "n/a: нет tools", 2, 0, 0, 0, None, None, None, None, 0, 0, 0, 27, "na"),
]
FULL, PART, FAIL, NA = "full", "part", "fail", "na"
KIND_HUE = {FULL: "#2f7d54", PART: "#315f9e", FAIL: "#b3543f", NA: "#98a4b3"}
KIND_NAME = {FULL: "полный зачёт (12–13)", PART: "почти (9–11)", FAIL: "провал (<9)", NA: "n/a"}

CSS = """
:root{--ink:#1d2433;--mut:#66707f;--line:#e4e7ec;--bg:#fafbfc;--acc:#315f9e;--good:#2f7d54;--bad:#b3543f}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;font:16px/1.62 "Segoe UI",system-ui,-apple-system,"Helvetica Neue",sans-serif;color:var(--ink);background:var(--bg)}
.wrap{max-width:1040px;margin:0 auto;padding:26px 22px 90px}
h1.pagetitle{margin:26px 4px 14px;font-size:34px;line-height:1.18;letter-spacing:-.01em;color:var(--ink)}
header{background:linear-gradient(140deg,#1d2c46 0%,#315f9e 90%);color:#fff;border-radius:16px;padding:34px 40px 30px;margin-bottom:22px}
header h1{margin:0 0 12px;font-size:31px;line-height:1.22;letter-spacing:-.01em}
header p{margin:5px 0;opacity:.93;max-width:74ch}
.toc{margin:20px 0 4px}
.toc-title{font-size:13px;text-transform:uppercase;letter-spacing:.09em;color:var(--mut);margin-bottom:9px}
.toc ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:5px 28px}
.toc a{color:var(--acc);text-decoration:none}
.toc a:hover{text-decoration:underline}
h2{font-size:24px;margin:44px 0 4px;letter-spacing:-.01em}
h2 .no{color:var(--mut);font-weight:400;margin-right:8px}
h3{font-size:17.5px;margin:26px 0 2px}
.lead{color:var(--mut);margin:8px 0 14px;font-size:15px}
p{margin:10px 0}
.sub{font-size:12.5px;color:var(--mut);margin-top:3px;line-height:1.5}
.excap{font-size:15px;color:var(--ink);margin:12px 0 -4px;line-height:1.5}
table{border-collapse:collapse;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(20,30,50,.07);margin:14px 0}
th{background:#eef1f6;text-align:left;font-size:12.5px;text-transform:uppercase;letter-spacing:.03em;color:#46536a;padding:10px 12px;border-bottom:2px solid var(--line)}
th.narrow{padding-left:5px;padding-right:5px}
td.narrow{padding-left:5px;padding-right:5px}
td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14.5px}
tr:last-child td{border-bottom:none}
tr.neg td{background:#fdf6f4}
tr.top td{background:#eef6ef}
.barwrap{position:relative;background:#eef0f4;border-radius:6px;min-width:92px;max-width:110px;height:21px}
.barwrap .bar{position:absolute;left:0;top:3px;bottom:3px;border-radius:5px;min-width:3px;opacity:.3}
.barwrap span{position:absolute;left:7px;top:0;font-size:12.5px;line-height:21px;font-weight:600;color:var(--ink)}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(295px,1fr));gap:13px;margin:16px 0}
.card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--acc);border-radius:12px;padding:15px 17px;font-size:14.5px}
.card b{display:block;margin-bottom:5px;font-size:15px;min-height:2.8em}
.quote{background:#fff;border:1px solid var(--line);border-radius:12px;padding:15px 19px;margin:13px 0;font-size:14.5px}
.quote .who{font-size:12.5px;color:var(--mut);margin-bottom:7px;text-transform:uppercase;letter-spacing:.03em}
.quote.bad{border-left:4px solid var(--bad)}.quote.good{border-left:4px solid var(--good)}
.flow{display:flex;flex-wrap:wrap;gap:7px;align-items:stretch;margin:15px 0}
.flow .step{background:#fff;border:2px solid var(--acc);border-radius:12px;padding:11px 15px;flex:1;min-width:180px;font-size:14px}
.flow .step b{color:var(--acc);display:block;margin-bottom:4px}
.flow .step.hot{border-color:var(--bad)}.flow .step.hot b{color:var(--bad)}
.flow .arrow{align-self:center;color:var(--mut);font-size:19px}
a{color:var(--acc)}
.note{background:#fff9e8;border:1px solid #eedd9f;border-radius:12px;padding:13px 17px;font-size:14.5px}
.badge{display:inline-block;border-radius:5px;padding:1px 8px;font-size:12px;font-weight:600}
.badge.b{background:#e9f1ec;color:var(--good)}.badge.r{background:#f8ebe7;color:var(--bad)}.badge.g{background:#edf1f7;color:var(--acc)}
svg text{font-family:inherit}
.scatter .dot{cursor:pointer}
.scatter g.pt .tip{pointer-events:none;display:none}
footer{margin-top:56px;color:var(--mut);font-size:13.5px;border-top:1px solid var(--line);padding-top:18px;line-height:1.6}
@media print{.toc{display:none}header{border-radius:0}h2{page-break-after:avoid}}
"""


def fmt_dur(v):
    if v is None:
        return "—"
    return f"{round(v * 60)} с" if v < 1 else f"{v:g} мин"


def results_table():
    rows = []
    order = {FULL: 0, PART: 1, FAIL: 2, NA: 3}
    for r in sorted(RUNS, key=lambda r: (order[r[14]], -(r[2] + r[3]))):
        (label, note, mand, bonus, a1, a2, dur, tok, peak, comp, ps, pm, pc, loc, kind) = r
        total = mand + bonus
        trcls = {FULL: ' class="top"', NA: ' class="neg"', FAIL: ' class="neg"'}.get(kind, "")
        badge = {FULL: '<span class="badge b">полный зачёт</span>',
                 NA: '<span class="badge g">n/a</span>'}.get(kind, "")
        bar = (f'<div class="barwrap"><div class="bar" style="width:{max(2, total / 13 * 100):.0f}%;'
               f'background:{KIND_HUE[kind]}"></div><span>{mand}+{bonus}</span></div>')
        delta = a1 - a2
        rows.append(
            f"<tr{trcls}><td><b>{label}</b><div class=sub>{note}</div></td>"
            f'<td class="narrow">{bar}</td><td class="narrow">{badge}</td>'
            f'<td class="narrow">{a1:g}</td><td class="narrow">{a2:g}</td>'
            f'<td class="narrow">{delta:+g}</td>'
            f'<td class="narrow">{fmt_dur(dur)}</td>'
            f'<td class="narrow">{tok if tok is None else f"{tok:g}M"}</td>'
            f'<td class="narrow">{peak if peak is None else f"{peak:g}K"}</td>'
            f'<td class="narrow">{comp if comp is None else comp}</td>'
            f'<td class="narrow">{ps}</td><td class="narrow">{pm}</td><td class="narrow">{pc}</td>'
            f'<td class="narrow">{loc}</td></tr>')
    return ('<div style="overflow-x:auto"><table><thead><tr>'
            "<th>Прогон</th><th class=narrow>Чекер<br>из 13</th><th class=narrow>Статус</th>"
            "<th class=narrow>A<br>ярус 1</th><th class=narrow>A<br>ярус 2</th><th class=narrow>Δ</th>"
            "<th class=narrow>Время</th><th class=narrow>Токены</th><th class=narrow>Пик ctx</th>"
            "<th class=narrow>⇄</th><th class=narrow>php-<br>stan</th><th class=narrow>php-<br>md</th>"
            "<th class=narrow>php-<br>cs</th><th class=narrow>LOC</th></tr></thead><tbody>"
            + "".join(rows) + "</tbody></table></div>"
            '<p class=sub>⇄ — автокомпакты + правки истории за прогон. A — ось «что сделано» '
            "(0–4): ярус 1 — ревью ZCode, ярус 2 — слепое ревью GLM-5.3-Flash. "
            "medgemma — нетронутый каркас: 2/13 — базовый уровень скелета, модель без "
            "tool-calling, зачёт не выставляется (n/a).</p>")


def cost_scatter():
    """Время × токены; диаметр — пик контекста; цвет — итог чекера (как карта этапа 1)."""
    W, H = 920, 480
    ML, MR, MT, MB = 56, 24, 24, 52
    PW, PH = W - ML - MR, H - MT - MB
    XMAX, YMAX = 44, 16

    def px(v):
        return ML + v / XMAX * PW

    def py(v):
        return MT + (YMAX - v) / YMAX * PH

    def dot_r(peak):
        return 7 + min(peak / 90, 1) * 8

    svg = []
    for gv in (4, 8, 12, 16):
        svg.append(f'<line x1={ML} y1={py(gv):.0f} x2={ML + PW} y2={py(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML - 10} y={py(gv) + 4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for xv in (0, 10, 20, 30, 40):
        svg.append(f'<line x1={px(xv):.0f} y1={MT} x2={px(xv):.0f} y2={MT + PH} stroke="#e4e7ec"/>'
                   f'<text x={px(xv):.0f} y={MT + PH + 20} text-anchor=middle font-size=12 fill=#66707f>{xv}</text>')
    # смещения подписей точек (dx, dy), подобраны под эту сетку
    offs = {"qwen3.8 · r2": (-12, 4), "huihui · r2": (-12, 4), "orca · r2": (-12, 8),
            "qwen3.8 · r1": (-14, -6), "huihui · r1": (-12, 4), "orca · r1": (8, -8),
            "gemma4:31b": (-10, 12), "muse · r2": (9, 2), "muse · r1": (11, -3),
            "ornith · r1": (0, 20), "gemma4:e2b": (9, -8)}
    for (label, note, mand, bonus, a1, a2, dur, tok, peak, comp, ps, pm, pc, loc, kind) in RUNS:
        if tok is None:
            continue
        hue = KIND_HUE[kind]
        x, y = px(dur), py(tok)
        name = label.split(" ·")[0] if kind != NA else label
        dx, dy = offs.get(label, (10, -6))
        tip_lines = [label, f"чекер {mand}+{bonus} ({note})",
                     f"{fmt_dur(dur)} · {tok:g}M токенов · пик {peak:g}K · ⇄{comp}"]
        tip_w = max(len(s) for s in tip_lines) * 6.6 + 18
        tip_left = x + 16 + tip_w > ML + PW
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x={tx:.0f} y={y - 52:.0f} width={tip_w:.0f} height=68 rx=7 fill="#1d2433" fill-opacity="0.95"/>']
        for i, s in enumerate(tip_lines):
            tip.append(f'<text x={tx + 8:.0f} y={y - 34 + i * 15:.0f} font-size=11.5 '
                       f'fill="{"#ffffff" if i == 0 else "#c8d4e4"}"{" font-weight=bold" if i == 0 else ""}>{s}</text>')
        svg.append(f'<g class="pt"><circle class="dot" cx={x:.0f} cy={y:.0f} r={dot_r(peak):.1f} '
                   f'fill={hue} fill-opacity="0.8" stroke="#fff" stroke-width="1.5"/>'
                   f'<text x={x + dx:.0f} y={y + dy:.0f} text-anchor="{"end" if dx < 0 else ("middle" if dx == 0 else "start")}" '
                   f'font-size=11 fill={hue}>{name}</text>'
                   f'<g class="tip">' + "".join(tip) + "</g></g>")
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 14} text-anchor=middle font-size=12.5 fill=#66707f>'
               "минуты прогона (бюджет 90 мин)</text>")
    svg.append(f'<text x=16 y={MT + PH / 2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT + PH / 2:.0f})">'
               "млн токенов</text>")
    return ('<svg class="scatter" width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s'
            '<g class="tip-layer"></g></svg>'
            '<p class=sub>%s. Диаметр точки — пик контекста (13.8K–87.9K).</p>'
            % (W, H, W, H, "".join(svg),
               " · ".join(f'<span style="color:{h}">●</span> {n}' for h, n in KIND_NAME.items() if h != NA)))


def ctx_chart():
    W, H = 620, 300
    ML, MR, MT, MB = 46, 14, 22, 46
    PW, PH = W - ML - MR, H - MT - MB
    data = [("orca r1 · 32K", 32.8, 15, FAIL), ("orca r2 · 100K", 86.4, 2, FULL),
            ("ornith r1 · 32K", 32.8, 53, FAIL), ("qwen r2 · 100K", 87.0, 2, FULL)]

    def py(v):
        return MT + (112 - v) / 112 * PH

    svg = []
    for gv in (0, 25, 50, 75, 100):
        svg.append(f'<line x1={ML} y1={py(gv):.0f} x2={ML + PW} y2={py(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML - 10} y={py(gv) + 4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    bw = PW / len(data) * 0.52
    for i, (label, peak, comp, kind) in enumerate(data):
        cx = ML + PW / len(data) * (i + 0.5)
        svg.append(f'<rect x={cx - bw / 2:.0f} y={py(peak):.0f} width={bw:.0f} height={MT + PH - py(peak):.0f} '
                   f'rx=4 fill={KIND_HUE[kind]} fill-opacity="0.82"/>'
                   f'<text x={cx:.0f} y={py(peak) - 24:.0f} text-anchor=middle font-size=12.5 font-weight=600 fill=#1d2433>{peak:g}K</text>'
                   f'<text x={cx:.0f} y={py(peak) - 9:.0f} text-anchor=middle font-size=11.5 fill=#66707f>⇄{comp}</text>'
                   f'<text x={cx:.0f} y={MT + PH + 18:.0f} text-anchor=middle font-size=12 fill=#66707f>{label}</text>')
    svg.append(f'<line x1={ML} y1={py(102.4):.0f} x2={ML + PW} y2={py(102.4):.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>'
               f'<text x={ML + PW} y={py(102.4) - 5:.0f} text-anchor=end font-size=11.5 font-style=italic fill=#9aa3ad>лимит 102.4K</text>')
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 8:.0f} text-anchor=middle font-size=12.5 fill=#66707f>'
               "пик контекста, K токенов; ⇄ — автокомпакты + правки истории</text>")
    return '<svg width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s</svg>' % (W, H, W, H, "".join(svg))


def dumbbell(rows, vmax, ticks, xtitle, w=620, row_h=34, fmt="{:g}", ref_lines=()):
    """rows: (label, v1, v2, name1, name2). Общий паттерн: было → стало."""
    H = len(rows) * row_h + 64
    ML, MR, MT = 118, 46, 16
    PW = w - ML - MR

    def px(v):
        return ML + v / vmax * PW

    svg = []
    for t in ticks:
        svg.append(f'<line x1={px(t):.0f} y1={MT} x2={px(t):.0f} y2={MT + len(rows) * row_h + 4:.0f} stroke="#e4e7ec"/>'
                   f'<text x={px(t):.0f} y={MT + len(rows) * row_h + 22:.0f} text-anchor=middle font-size=12 fill=#66707f>{fmt.format(t)}</text>')
    for k, (v, txt) in enumerate(ref_lines):
        svg.append(f'<line x1={px(v):.0f} y1={MT} x2={px(v):.0f} y2={MT + len(rows) * row_h + 4:.0f} '
                   f'stroke="#aab3bf" stroke-dasharray="6,4"/>')
    for i, (label, v1, v2, name1, name2) in enumerate(rows):
        y = MT + i * row_h + row_h / 2
        svg.append(f'<line x1={px(v1):.0f} y1={y:.0f} x2={px(v2):.0f} y2={y:.0f} stroke="#c3cad4" stroke-width="2.5"/>'
                   f'<circle cx={px(v1):.0f} cy={y:.0f} r=5 fill="#5b9bd5"/>'
                   f'<circle cx={px(v2):.0f} cy={y:.0f} r=5 fill="#315f9e"/>'
                   f'<text x={ML - 10:.0f} y={y + 4:.0f} text-anchor=end font-size=12 fill=#1d2433>{label}</text>'
                   f'<text x={px(v2) + 9:.0f} y={y + 4:.0f} font-size=12 font-weight=600 fill=#1d2433>{fmt.format(v2)}</text>')
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 6:.0f} text-anchor=middle font-size=12.5 fill=#66707f>{xtitle}</text>')
    return ('<svg width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s</svg>'
            '<p class=sub><span style="color:#5b9bd5">●</span> %s &nbsp; <span style="color:#315f9e">●</span> %s'
            "%s</p>"
            % (w, H, w, H, "".join(svg), rows[0][3], rows[0][4],
               f' &nbsp; пунктир: {" / ".join(txt for _, txt in ref_lines)}' if ref_lines else ""))


def judges_dumbbell():
    rows = []
    for (label, note, mand, bonus, a1, a2, *_rest, kind) in [r for r in RUNS if r[14] != NA]:
        rows.append((label, a2, a1, "ярус 2 (GLM-флеш)", "ярус 1 (ZCode)"))
    return dumbbell(rows, 4, (0, 1, 2, 3, 4), "ось A: что сделано (0–4)", fmt="{:g}")


def think_dumbbell():
    pairs = [("qwen3.8", 11, 13, "run-01 ≈medium", "run-02 xhigh"),
             ("huihui", 11, 13, "run-01 ≈medium", "run-02 xhigh"),
             ("muse", 1, 9, "run-01 high", "run-02 max")]
    return dumbbell(pairs, 13.6, (0, 5, 11, 13), "сценарии чекера (из 13)",
                    ref_lines=((11, "обязательные 11"), (13, "полный зачёт 13")))


def quality_chart():
    W = 620
    data = [("qwen r2", 30, 39, 76), ("huihui r2", 35, 18, 26),
            ("orca r2", 33, 46, 36), ("gemma4:31b", 21, 18, 17)]
    row_h, ML, MR, MT = 40, 104, 52, 8
    H = len(data) * row_h + 54
    PW = W - ML - MR
    XMAX = max(max(d[1:]) for d in data) * 1.12

    def px(v):
        return ML + v / XMAX * PW

    svg = []
    for t in (20, 40, 60, 80):
        svg.append(f'<line x1={px(t):.0f} y1={MT} x2={px(t):.0f} y2={MT + len(data) * row_h:.0f} stroke="#e4e7ec"/>'
                   f'<text x={px(t):.0f} y={MT + len(data) * row_h + 16:.0f} text-anchor=middle font-size=12 fill=#66707f>{t}</text>')
    hues = ("#315f9e", "#5b9bd5", "#98a4b3")
    names = ("phpstan L8", "phpmd", "phpcs PSR12")
    for i, (label, *vals) in enumerate(data):
        cy = MT + i * row_h + row_h / 2
        svg.append(f'<text x={ML - 10:.0f} y={cy + 4:.0f} text-anchor=end font-size=12 fill=#1d2433>{label}</text>')
        for j, v in enumerate(vals):
            off = (j - 1) * 8
            svg.append(f'<rect x={ML:.0f} y={cy - 10 + off:.0f} width={px(v) - ML:.0f} height=6 rx=3 '
                       f'fill={hues[j]} fill-opacity="0.9"/>')
        svg.append(f'<text x={px(vals[2]) + 8:.0f} y={cy + 4:.0f} font-size=12 font-weight=600 fill=#1d2433>{vals[2]}</text>')
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 8:.0f} text-anchor=middle font-size=12.5 fill=#66707f>'
               "нарушений, меньше — лучше (подпись справа — phpcs)</text>")
    return ('<svg width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s</svg>'
            '<p class=sub>%s</p>'
            % (W, H, W, H, "".join(svg),
               " · ".join(f'<span style="color:{h}">■</span> {n}' for h, n in zip(hues, names))))


TOOLTIP_JS = """
document.querySelectorAll('svg.scatter g.pt').forEach(function (pt) {
  var dot = pt.querySelector('.dot'), tip = pt.querySelector('.tip');
  if (!dot || !tip) return;
  dot.addEventListener('mouseenter', function () {
    pt.closest('svg').querySelector('.tip-layer').appendChild(tip);
    tip.style.display = 'block';
  });
  dot.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
});
"""

HTML = f"""<!DOCTYPE html>
<html lang="ru"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Пилот этапа 2: тестовое задание «биллинг LLM-токенов»</title>
<style>{CSS}</style></head><body><div class=wrap>
<h1 class=pagetitle>Пилот этапа 2: тестовое задание «биллинг LLM-токенов»</h1>
<p class=sub style="margin:-6px 4px 14px">12 прогонов · 8 моделей · 15–16 октября 2026 · отчёт собран в ZCode при содействии GLM-5.3. Ветка feat/billing-test-assignment (PR #14).</p>

<nav class=toc>
<div class=toc-title>Содержание</div>
<ul>
<li><a href="#tldr">1. Главные выводы</a></li>
<li><a href="#method">2. Методика пилота</a></li>
<li><a href="#results">3. Результаты прогонов</a></li>
<li><a href="#charts">4. Что решило исход</a></li>
<li><a href="#judges">5. Судьи: согласие ярусов</a></li>
<li><a href="#next">6. Статус и дальше</a></li>
</ul>
</nav>

<h2 id=tldr><span class=no>1.</span>Главные выводы</h2>
<p class=lead>Пилот проверял не модели, а систему: задание, чекер, ярусы оценки и харнес. Система работает; два полных зачёта — qwen3.8 и huihui на честном xhigh.</p>
<div class=cards>
<div class=card><b>Полный зачёт — 13/13</b>qwen3.8 и huihui прошли все обязательные сценарии и бонус retro-rebill. Оба — после перевода thinking на истинный максимум шкалы (xhigh из /api/show).</div>
<div class=card><b>Thinking решает бонус</b>На «high», который сервер ollama поджимал до ≈medium, обе модели сдавали только обязательную часть (11/13). Честный xhigh дал 13/13.</div>
<div class=card><b>Окно 32K ломало агентность</b>ornith: 53 автокомпакта и правок, потерянный финиш. ctx100k-алиас у orca: пик 32.8K → 86.4K, компакты 15 → 2, бонус 1/2 → 2/2.</div>
<div class=card><b>Ярус 2 слеп к чекеру</b>Слепой судья по диффу завышает на аккуратном, но нерабочем коде (muse r1: 2.5 против 1). Зато нашёл float в деньгах muse/ornith и N+1 — то, что пропустило ревью.</div>
</div>

<h2 id=method><span class=no>2.</span>Методика пилота</h2>
<p>Модель получает скелет Symfony 8 (composer, doctrine-bundle, docker-compose PostgreSQL 17) и
задание TASK.md: консольный биллинг LLM-токенов — prices/usage импорт, тарифы с effective dating,
оф-пик окно через полночь, идемпотентность, unrated-события, деньги на bcmath без float, бонус —
ретро-пересчёт. Никаких подсказок по практикам: чекер наблюдает, что модель выберет сама.
Харнес — pi (изолированный запуск, без доступа к чужим конфигам), бюджет 90 минут,
контекст 100K, thinking — максимум шкалы модели.</p>
<div class=flow>
<div class=step><b>1 · pi-агент</b>Скелет + TASK.md + AGENTS.md, один прогон с нуля, 90 мин</div>
<div class=arrow>→</div>
<div class=step><b>2 · Ярус 0: чекер</b>11 обязательных сценариев + 2 бонусных против golden-эталонов</div>
<div class=arrow>→</div>
<div class=step><b>3 · Ярус 0.5: метрики</b>phpstan L8, phpmd, phpcs PSR12, AST/scc</div>
<div class=arrow>→</div>
<div class=step><b>4 · Ярус 1: ревью</b>ZCode, оси A/B «что сделано» / «как сделано», 0–4</div>
<div class=arrow>→</div>
<div class=step><b>5 · Ярус 2: судья</b>GLM-5.3-Flash, слепое ревью диффа, ось A</div>
</div>
<p class=sub>Golden-эталоны посчитаны дважды независимо (Python Decimal и PHP-референс, совпадение байт-в-байт).
Чекер поднимает чистую базу в docker на свободном порту; метод миграции схемы фиксируется в отчёте прогона.</p>

<h2 id=results><span class=no>3.</span>Результаты прогонов</h2>
{results_table()}

<h2 id=charts><span class=no>4.</span>Что решило исход</h2>
<h3>Время и токены</h3>
<p class=excap>Каждая точка — прогон: по горизонтали время, по вертикали суммарные токены,
цвет — итог чекера. Два лидера прошли 13/13 без аномалий по бюджету; ornith сжёг 12.9M токенов
в окне 32K без результата.</p>
{cost_scatter()}

<h3>Эффект окна 32K → 100K</h3>
<p class=excap>Дефолт сервера ollama (ctx 32768) душил агентность: ornith упёрся в лимит,
накрутив 53 компакта. После перевода всех тегов на ctx100k-алиасы (num_ctx 102400) пик
контекста у orca вырос до 86.4K при 2 компактах.</p>
{ctx_chart()}

<h3>Эффект thinking: run-01 → run-02</h3>
<p class=excap>Первые прогоны qwen/huihui ушли на «high», которого в шкале моделей нет —
сервер поджимал его до ≈medium. После карты уровней из /api/show повтор на честном xhigh
закрыл бонус. muse показала тот же паттерн на своей шкале (high → max).</p>
{think_dumbbell()}

<h3>Качество кода лучших решений</h3>
<p class=excap>Ярус 0.5 по четырём лучшим решениям. huihui r2 на xhigh пишет заметно чище
своего ≈medium-прогона (phpmd 56 → 18, phpcs 86 → 26) — максимум мышления окупается и качеством,
не только функциональностью.</p>
{quality_chart()}

<h2 id=judges><span class=no>5.</span>Судьи: согласие ярусов</h2>
<p class=excap>Ревью ZCode (ярус 1) против слепого ревью GLM-5.3-Flash (ярус 2) по оси A.
11 из 12 прогонов — в пределах ±0.5 балла, систематического сдвига нет.</p>
{judges_dumbbell()}
<div class="quote bad"><div class=who>Слепое пятно яруса 2</div>
Судья видит только дифф и не знает результата чекера. На muse r1 (1/11, схема не разворачивается,
но код аккуратный) он поставил 2.5 — против 1 у ревью. Вывод пилота: функциональная истина —
только ярус 0; ярус 2 полезен для поиска дефектов (float в деньгах, N+1), но не для итогового
балла нерабочих решений.</div>

<h2 id=next><span class=no>6.</span>Статус и дальше</h2>
<div class=note><b>Негативные контроли различимы.</b> e2b (4.6B) — «не смогла сделать»:
0/11 за 21 секунду, ниже базового уровня нетронутого каркаса. medgemma — «не может в принципе»:
capabilities без tool-calling, зачёт не выставляется (n/a). Два разных типа негативности шкала
различает — дискриминативность подтверждена.</div>
<p>Дальше по плану: полная сетка прогонов (все теги через pi-100k/ctx100k, thinking — максимум
шкалы из /api/show, muse на high вместо max), 3–5 прогонов на модель, затем сводный отчёт этапа 2
в формате этапа 1. Все артефакты прогонов — в папках моделей
<code>models/&lt;модель&gt;/backend/test-assignment/run-XX/</code> (solution, сессия pi, чекер, метрики, scorecard).</p>

<footer>
Все числа — из артефактов прогонов: run.json, check.json, metrics.json, session-profile.md.
Генератор: <code>interview/test-assignment/backend/billing/build_pilot_report.py</code> —
правки данных только через него. Сводка ярусов в текстовом виде:
<code>reports/TEST-ASSIGNMENT-pilot-judges.md</code>.
</footer>
<script>{TOOLTIP_JS}</script>
</div></body></html>
"""


def main():
    OUT.write_text(HTML, encoding="utf-8")
    print(f"готово: {OUT} ({OUT.stat().st_size} байт)")


if __name__ == "__main__":
    main()

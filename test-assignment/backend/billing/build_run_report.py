#!/usr/bin/env python3
"""HTML-отчёт по одному прогону тестового задания (дашборд показателей).

Идея — как html-дашборд метрик в coding-standard: смотреть графики проще,
чем читать цифры. Вход — каталог прогона models/<модель>/backend/
test-assignment/run-XX; выход — <run>/report.html. Источники:
run.json, check.json, metrics.json, metrics-raw/collector.json,
pi-session/*.jsonl (парсер — analyze_session.py), judge-glm.md,
review-zcode.md, scorecard.md.

Запуск:
    python3 test-assignment/backend/billing/build_run_report.py \
        models/<модель>/backend/test-assignment/run-XX
"""
from __future__ import annotations

import json
import math
import re
import sys
from pathlib import Path

BILLING = Path(__file__).resolve().parent
sys.path.insert(0, str(BILLING))
from analyze_session import analyze, load  # noqa: E402

CSS = """
:root{--ink:#1d2433;--mut:#66707f;--line:#e4e7ec;--bg:#fafbfc;--acc:#315f9e;--good:#2f7d54;--bad:#b3543f}
*{box-sizing:border-box}
body{margin:0;font:16px/1.62 "Segoe UI",system-ui,-apple-system,"Helvetica Neue",sans-serif;color:var(--ink);background:var(--bg)}
.wrap{max-width:1040px;margin:0 auto;padding:26px 22px 70px}
h1{margin:20px 4px 4px;font-size:28px;letter-spacing:-.01em}
h2{font-size:21px;margin:38px 0 4px}
h3{font-size:16.5px;margin:22px 0 2px}
.lead,.sub{color:var(--mut)}
.lead{margin:6px 0 14px;font-size:15px}
.sub{font-size:12.5px;margin-top:3px;line-height:1.5}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;margin:16px 0}
.card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--acc);border-radius:12px;padding:13px 15px}
.card b{display:block;font-size:24px;letter-spacing:-.02em;margin-bottom:2px}
.card span{color:var(--mut);font-size:12.5px}
.card.g{border-left-color:var(--good)}.card.r{border-left-color:var(--bad)}
table{border-collapse:collapse;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(20,30,50,.07);margin:12px 0}
th{background:#eef1f6;text-align:left;font-size:12.5px;text-transform:uppercase;letter-spacing:.03em;color:#46536a;padding:9px 12px;border-bottom:2px solid var(--line)}
td{padding:8px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14px}
tr:last-child td{border-bottom:none}
.badge{display:inline-block;border-radius:5px;padding:1px 8px;font-size:12px;font-weight:600}
.badge.b{background:#e9f1ec;color:var(--good)}.badge.r{background:#f8ebe7;color:var(--bad)}.badge.g{background:#edf1f7;color:var(--acc)}
.svgbox{background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px 8px 4px;margin:12px 0}
svg{display:block;width:100%;height:auto}
svg text{font-family:inherit}
.scatter .dot{cursor:pointer}
.scatter g.pt .tip{pointer-events:none;display:none}
.quote{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 17px;margin:12px 0;font-size:14.5px;white-space:pre-wrap}
.quote .who{font-size:12.5px;color:var(--mut);margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em}
.note{background:#fff9e8;border:1px solid #eedd9f;border-radius:12px;padding:12px 16px;font-size:14.5px}
footer{margin-top:46px;color:var(--mut);font-size:13px;border-top:1px solid var(--line);padding-top:14px}
"""


def fmt_k(v):
    return f"{v / 1000:.1f}K" if v >= 1000 else str(v)


def fmt_dur(s):
    if s is None:
        return "—"
    return f"{round(s)} с" if s < 90 else f"{s / 60:.0f} мин"


# --- ярус 1: сценарии чекера ------------------------------------------------
def checker_html(check: dict) -> str:
    sc = check.get("scenarios") or {}
    rows = []
    order = sorted(sc.items(), key=lambda kv: (kv[1].get("bonus", False), kv[0]))
    for name, v in order:
        st = v.get("status")
        badge = {"pass": '<span class="badge b">pass</span>',
                 "fail": '<span class="badge r">fail</span>',
                 "info": '<span class="badge g">info</span>'}.get(st, st or "?")
        detail = str(v.get("detail") or "").strip().replace("\n", "<br>")
        rows.append(f"<tr><td><code>{name}</code>{' <span class=sub>бонус</span>' if v.get('bonus') else ''}"
                    f"</td><td>{badge}</td><td class=sub>{detail[:400]}</td></tr>")
    mp, mt = check.get("mandatory_passed"), check.get("mandatory_total")
    bp, bt = check.get("bonus_passed"), check.get("bonus_total")
    return (f"<h2>Ярус 1 · чекер: {mp}/{mt} обязательных + {bp}/{bt} бонусных</h2>"
            '<p class=lead>Детерминированное сравнение с golden-эталонами; '
            "каждый сценарий — отдельная проверка контракта задания.</p>"
            "<table><thead><tr><th>Сценарий</th><th>Статус</th><th>Детали</th></tr></thead><tbody>"
            + "".join(rows) + "</tbody></table>")


# --- ярус 2: метрики ----------------------------------------------------------
def bars_with_cohort(own: dict, cohort: list[dict]) -> str:
    """Три бара качества: своё значение + отметка медианы когорты пилота."""
    rows = []
    for key, name in (("phpstan", "phpstan L8"), ("phpmd", "phpmd"), ("phpcs", "phpcs PSR12")):
        val = own.get(key, {}).get("errors" if key == "phpstan" else "violations", 0)
        vals = [m.get(key, {}).get("errors" if key == "phpstan" else "violations", 0) for m in cohort]
        med = sorted(vals)[len(vals) // 2] if vals else val
        vmax = max([val, med] + vals + [1])
        w_own = max(2, val / vmax * 100)
        x_med = med / vmax * 100
        rows.append(
            f'<tr><td style="white-space:nowrap"><b>{name}</b></td>'
            f'<td><div style="position:relative;background:#eef0f4;border-radius:6px;height:22px;min-width:120px">'
            f'<div style="position:absolute;left:0;top:3px;bottom:3px;width:{w_own:.0f}%;background:#315f9e;opacity:.75;border-radius:5px"></div>'
            f'<div style="position:absolute;left:{x_med:.0f}%;top:0;bottom:0;width:2px;background:#b3543f"></div>'
            f'<span style="position:absolute;left:8px;top:0;font-size:12.5px;line-height:22px;font-weight:600">{val}</span>'
            f"</div></td>"
            f'<td class=sub style="white-space:nowrap">медиана пилота {med}</td></tr>')
    return ('<table style="width:auto;max-width:640px"><thead><tr><th></th><th>нарушений</th><th></th></tr></thead>'
            + "".join(rows) + "</table>"
            '<p class=sub>Синяя полоса — прогон; красная риска — медиана остальных прогонов пилота '
            "(меньше — лучше).</p>")


def label_point(p: dict) -> str:
    """Подпись крупной точки: у правого края разворачивается влево."""
    x, y = px_g["x"](p["loc"]), px_g["y"](p["lcom"])
    r = 4 + math.sqrt(p["wmc"] / px_g["rmax"]) * 13
    if x > px_g["W"] - 110:
        return f'<text x={x - r - 4:.0f} y={y + 4:.0f} text-anchor=end font-size=11 fill=#315f9e>{p["name"]}</text>'
    return f'<text x={x + r + 3:.0f} y={y + 4:.0f} font-size=11 fill=#315f9e>{p["name"]}</text>'


def ast_scatter(collector: dict) -> str:
    """Скаттер классов: X — LOC, Y — LCOM4, размер — WMC, цвет — cc_max."""
    classes = collector.get("classes") or []
    if not classes:
        return '<div class=note>collector.json пуст — график недоступен</div>'
    methods: dict[str, list[dict]] = {}
    for fn in collector.get("functions") or []:
        if fn.get("type") != "method":
            continue
        cls = str(fn["metrics"].get("classInfo", "")).split(", ", 1)[-1]
        methods.setdefault(cls, []).append(fn["metrics"])
    W, H = 960, 400
    ML, MR, MT, MB = 56, 20, 16, 44
    PW, PH = W - ML - MR, H - MT - MB
    pts = []
    for cl in classes:
        m = cl["metrics"]
        if m.get("interface") or m.get("trait") or m.get("enum"):
            continue
        ms = methods.get(cl["name"], [])
        pts.append({
            "name": cl["name"].split("\\")[-1], "full": cl["name"],
            "loc": m.get("loc", 0), "lcom": m.get("lcom", 0),
            "wmc": sum(x.get("cc", 0) for x in ms),
            "cc": max((x.get("cc", 0) for x in ms), default=0),
            "file": str(m.get("filePath", "")).split("/")[-1],
        })
    xmax = max([p["loc"] for p in pts] + [1]) * 1.08
    ymax = max([p["lcom"] for p in pts] + [1]) + 0.7
    rmax = max([p["wmc"] for p in pts] + [1])
    global px_g
    px_g = {"x": lambda v: ML + v / xmax * PW,
            "y": lambda v: MT + (ymax - v) / ymax * PH,
            "rmax": rmax, "W": W}

    def px(v):
        return ML + v / xmax * PW

    def py(v):
        return MT + (ymax - v) / ymax * PH

    def rad(w):
        return 4 + math.sqrt(w / rmax) * 13

    svg = []
    for gv in range(0, int(ymax) + 1, max(1, int(ymax) // 5)):
        svg.append(f'<line x1={ML} y1={py(gv):.0f} x2={ML+PW} y2={py(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={py(gv)+4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for t in range(0, int(xmax) + 1, max(20, int(xmax) // 6)):
        svg.append(f'<text x={px(t):.0f} y={MT+PH+18:.0f} text-anchor=middle font-size=12 fill=#66707f>{t}</text>')
    hot = "#b3543f"
    for p in sorted(pts, key=lambda d: -d["wmc"]):
        hue = hot if p["cc"] > 10 else ("#d09a53" if p["cc"] > 5 else "#315f9e")
        x, y, r = px(p["loc"]), py(p["lcom"]), rad(p["wmc"])
        lines = (p["full"], f"LOC {p['loc']} · LCOM4 {p['lcom']} · WMC {p['wmc']} · cc_max {p['cc']}")
        tw = max(len(t) for t in lines) * 7.0 + 16
        tx = x - 14 - tw if x + 16 + tw > W - ML else x + 14
        tip = [f'<rect x={tx:.0f} y={y - 44:.0f} width={tw:.0f} height=46 rx=7 fill="#1d2433" fill-opacity="0.95"/>']
        for i, t in enumerate(lines):
            tip.append(f'<text x={tx + 9:.0f} y={y - 28 + i * 15:.0f} font-size=11.5 '
                       f'fill="{"#ffffff" if i == 0 else "#c8d4e4"}"{" font-weight=bold" if i == 0 else ""}>{t}</text>')
        svg.append(f'<g class="pt"><circle class="dot" cx={x:.0f} cy={y:.0f} r={r:.1f} '
                   f'fill={hue} fill-opacity="0.72" stroke="#fff" stroke-width="1.2" stroke-opacity="0.001"/>'
                   f'<g class="tip">' + "".join(tip) + "</g>"
                   + (label_point(p) if p["wmc"] >= rmax * 0.5 else "")
                   + "</g>")
    svg.append(f'<text x={ML+PW/2:.0f} y={H-8} text-anchor=middle font-size=12.5 fill=#66707f>LOC класса</text>')
    svg.append(f'<text x=16 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT+PH/2:.0f})">компоненты LCOM4</text>')
    return ('<svg class="scatter" width="%d" height="%d" viewBox="0 0 %d %d">%s<g class="tip-layer"></g></svg>'
            '<p class=sub>Размер точки — WMC класса, цвет — максимальная CC метода: '
            '<span style="color:#315f9e">●</span> ≤5, <span style="color:#d09a53">●</span> 6–10, '
            '<span style="color:#b3543f">●</span> &gt;10. Наведите на точку — полная сводка.</p>'
            % (W, H, W, H, "".join(svg)))



def dep_matrix(collector: dict) -> str:
    """Матрица зависимостей классов (по сырью collector.json).

    Классы отсортированы по полному имени — неймспейсы-слои кластеризуются,
    жирные линии разделяют слои. Ячейка (строка → столбец) — число зависимостей;
    последний столбец — исходящие связи наружу (к не-классам решения).
    """
    classes = [cl["name"] for cl in collector.get("classes") or []]
    internal = set(classes)
    counts: dict[tuple[str, str], int] = {}
    ext_out: dict[str, int] = {}
    for dep in collector.get("dependencies") or []:
        src, dst = dep.get("source"), dep.get("target")
        if src in internal and dst in internal and src != dst:
            counts[(src, dst)] = counts.get((src, dst), 0) + 1
        elif src in internal:
            ext_out[src] = ext_out.get(src, 0) + 1
    if not classes or not counts:
        return ""
    order = sorted(classes)
    n = len(order)
    cell = 13 if n <= 45 else 9
    pad = 4
    ML, MT = 176, 176
    W = ML + (n + 1) * (cell + pad) + 8   # +1 столбец «внеш.»
    H = MT + n * (cell + pad) + 34
    mx = max(counts.values())
    hue = lambda k: "#%02x%02x%02x" % tuple(  # noqa: E731
        round(a + (b - a) * min(k / mx, 1)) for a, b in zip((221, 230, 242), (49, 95, 158)))

    def group(name: str) -> str:
        parts = name.split("\\")
        return parts[1] if len(parts) > 2 else (parts[0] if parts else name)

    svg = []
    xi = {c: ML + i * (cell + pad) for i, c in enumerate(order)}
    yi = {c: MT + i * (cell + pad) for i, c in enumerate(order)}
    # сетка и подписи
    prev_group = None
    for i, c in enumerate(order):
        short = c.split("\\")[-1]
        g = group(c)
        bold = 1.6 if g != prev_group else 0.6
        if g != prev_group and prev_group is not None:
            svg.append(f'<line x1={ML - 6} y1={yi[c] - pad / 2 - 1:.0f} x2={W - 8} y2={yi[c] - pad / 2 - 1:.0f} stroke="#aab3bf" stroke-width="1"/>')
            svg.append(f'<line x1={xi[c] - pad / 2 - 1:.0f} y1={MT - 6} x2={xi[c] - pad / 2 - 1:.0f} y2={H - 34} stroke="#aab3bf" stroke-width="1"/>')
        prev_group = g
        svg.append(f'<text x={ML - 8} y={yi[c] + cell - 2:.0f} text-anchor=end font-size={min(10, cell - 1)} fill=#66707f>{short[:20]}</text>')
        svg.append(f'<text x={xi[c] + cell / 2:.0f} y={MT - 8} font-size={min(10, cell - 1)} fill=#66707f '
                   f'transform="rotate(-90 {xi[c] + cell / 2:.0f} {MT - 8})" text-anchor=end>{short[:20]}</text>')
    # ячейки с зависимостями; всплывашка привязана к клетке, у краёв разворачивается
    for (src, dst), k in counts.items():
        x, y = xi[dst], yi[src]
        label = f"{src.split(chr(92))[-1]} → {dst.split(chr(92))[-1]} · {k}"
        tw = len(label) * 7.0 + 18
        tx = x + cell + 10 if x + cell + 10 + tw <= W - 8 else x - 10 - tw
        ty = max(4, min(y - 42, H - 44))
        svg.append(f'<g class="pt"><rect class="dot" x={x:.0f} y={y:.0f} width={cell} height={cell} rx=2 '
                   f'fill="{hue(k)}"/>'
                   f'<g class="tip"><rect x={tx:.0f} y={ty:.0f} width={tw:.0f} height=30 rx=7 fill="#1d2433" fill-opacity="0.95"/>'
                   f'<text x={tx + 9:.0f} y={ty + 20:.0f} font-size=11.5 fill="#ffffff">{label}</text></g></g>')
    # столбец «внеш.»
    xe = ML + n * (cell + pad)
    for c in order:
        k = ext_out.get(c, 0)
        if k:
            svg.append(f'<rect x={xe:.0f} y={yi[c]:.0f} width={cell} height={cell} rx=2 fill="#c9b458" fill-opacity={0.25 + 0.75 * min(k / max(ext_out.values()), 1):.2f}><title>{c.split(chr(92))[-1]} → внешнее: {k}</title></rect>')
    svg.append(f'<text x={xe + cell / 2:.0f} y={MT - 8} font-size=10 fill=#8a7a3a transform="rotate(-90 {xe + cell / 2:.0f} {MT - 8})" text-anchor=end>внеш.</text>')
    # подписи слоёв — вдоль диагональных блоков (диагональ пуста:	self-связи исключены)
    spans: list[tuple[str, int, int]] = []
    for i, c in enumerate(order):
        g = group(c)
        if spans and spans[-1][0] == g:
            spans[-1][2] = i
        else:
            spans.append([g, i, i])
    for g, i0, i1 in spans:
        step = cell + pad
        size = (i1 - i0 + 1) * step - pad
        if size < len(g) * 6.2 + 8:
            continue  # блок мал — подпись не влезает, слой виден по именам классов
        cx = xi[order[i0]] + size / 2
        cy = yi[order[i0]] + size / 2
        svg.append(f'<text x={cx:.0f} y={cy:.0f} font-size=11 fill=#9aa3ad font-style=italic '
                   f'text-anchor=middle transform="rotate(-90 {cx:.0f} {cy:.0f})">{g}</text>')
    svg.append(f'<text x={ML} y={H - 14} font-size=12.5 fill=#66707f>строки — источник, столбцы — получатель; насыщенность — число зависимостей</text>')
    return ('<svg class="scatter" width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s<g class="tip-layer"></g></svg>'
            '<p class=sub>Классы отсортированы по неймспейсам: слои подписаны на диагонали, жирные линии их разделяют; '
            "жёлтый столбец «внеш.» — исходящие связи к типам вне решения (DTO времени, коллекции). "
            "Наведите на закрашенную ячейку — кто на кого и сколько раз.</p>"
            % (W, H, W, H, "".join(svg)))


def ast_tables(collector: dict) -> str:
    methods: dict[str, list] = {}
    fnames: dict[str, str] = {}
    for fn in collector.get("functions") or []:
        if fn.get("type") != "method":
            continue
        cls = str(fn["metrics"].get("classInfo", "")).split(", ", 1)[-1]
        methods.setdefault(cls, []).append(fn)
        fnames.setdefault(cls, fn["name"])
    classes = []
    for cl in collector.get("classes") or []:
        m = cl["metrics"]
        if m.get("interface") or m.get("trait") or m.get("enum"):
            continue
        ms = methods.get(cl["name"], [])
        classes.append({
            "name": cl["name"].split("\\")[-1], "file": str(m.get("filePath", "")).split("/")[-1],
            "loc": m.get("loc", 0), "n": len(ms),
            "wmc": sum(x["metrics"].get("cc", 0) for x in ms),
            "cc": max((x["metrics"].get("cc", 0) for x in ms), default=0),
            "lcom": m.get("lcom", 0),
        })
    classes.sort(key=lambda d: (-d["wmc"], -d["loc"]))
    top = "".join(
        f"<tr><td><b>{c['name']}</b><div class=sub>{c['file']}</div></td>"
        f"<td>{c['loc']}</td><td>{c['n']}</td><td><b>{c['wmc']}</b></td>"
        f"<td>{'⚠' if c['cc'] > 10 else ''} {c['cc']}</td><td>{'⚠' if c['lcom'] > 1 else ''} {c['lcom']}</td></tr>"
        for c in classes[:10])
    findings = []
    for cls, fns in methods.items():
        for fn in fns:
            mm = fn["metrics"]
            if mm.get("cc", 0) > 10 or mm.get("loc", 0) > 50:
                findings.append((cls.split("\\")[-1], fn["name"], mm.get("cc", 0), mm.get("loc", 0)))
    findings.sort(key=lambda t: (-t[2], -t[3]))
    frows = "".join(f"<tr><td><code>{c}::{n}</code></td><td>{cc}</td><td>{loc}</td></tr>"
                    for c, n, cc, loc in findings[:12]) or '<tr><td colspan=3 class=sub>нет</td></tr>'
    return ('<h3>Классы по WMC (топ-10)</h3>'
            "<table><thead><tr><th>Класс</th><th>LOC</th><th>методов</th><th>WMC</th><th>cc max</th><th>LCOM4</th></tr></thead>"
            f"<tbody>{top}</tbody></table>"
            '<h3>Методы-нарушители порогов (cc &gt; 10 или LOC &gt; 50)</h3>'
            "<table style=\"width:auto;max-width:520px\"><thead><tr><th>Метод</th><th>CC</th><th>LOC</th></tr></thead>"
            f"<tbody>{frows}</tbody></table>"
            '<p class=sub>Полные данные — <code>metrics-raw/collector.json</code>.</p>')


# --- сессия -------------------------------------------------------------------
def context_chart(events: list[dict], ctx_limit: int) -> str:
    """Контекст модели по вызовам API: input+cacheRead, обрывы автокомпактов."""
    pts = []
    for e in events:
        if e.get("type") != "message":
            continue
        m = e.get("message") or {}
        u = m.get("usage")
        if m.get("role") == "assistant" and isinstance(u, dict):
            pts.append(int(u.get("input") or 0) + int(u.get("cacheRead") or 0))
    if len(pts) < 2:
        return '<div class=note>Мало вызовов для графика контекста</div>'
    W, H = 960, 260
    ML, MR, MT, MB = 72, 16, 14, 40
    PW, PH = W - ML - MR, H - MT - MB
    ymax = max(pts + [ctx_limit]) * 1.04
    x = lambda i: ML + i / (len(pts) - 1) * PW  # noqa: E731
    y = lambda v: MT + (1 - v / ymax) * PH  # noqa: E731
    svg = [f'<line x1={ML} y1={y(ctx_limit):.0f} x2={ML+PW} y2={y(ctx_limit):.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>',
           f'<text x={ML+PW} y={y(ctx_limit)-5:.0f} text-anchor=end font-size=11.5 font-style=italic fill=#9aa3ad>лимит {fmt_k(ctx_limit)}</text>']
    for gv in range(0, int(ymax), int(ymax / 4) or 1):
        svg.append(f'<line x1={ML} y1={y(gv):.0f} x2={ML+PW} y2={y(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y(gv)+4:.0f} text-anchor=end font-size=12 fill=#66707f>{fmt_k(gv)}</text>')
    path = " ".join(f"{x(i):.0f},{y(v):.0f}" for i, v in enumerate(pts))
    svg.append(f'<polyline points="{path}" fill="none" stroke="#315f9e" stroke-width="1.8"/>')
    last = None
    for i, v in enumerate(pts):
        if last is not None and v < last * 0.55:  # обрыв — вероятно автокомпакт
            svg.append(f'<line x1={x(i):.0f} y1={MT} x2={x(i):.0f} y2={MT+PH} stroke="#b3543f" stroke-dasharray="3,3" opacity="0.5"/>')
        last = v
    svg.append(f'<circle cx={x(len(pts)-1):.0f} cy={y(pts[-1]):.0f} r=3 fill=#315f9e><title>последний вызов {fmt_k(pts[-1])}</title></circle>')
    svg.append(f'<text x={ML+PW/2:.0f} y={H-8} text-anchor=middle font-size=12.5 fill=#66707f>вызовы API ({len(pts)})</text>')
    svg.append(f'<text x=14 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 14 {MT+PH/2:.0f})">контекст, токены</text>')
    return ('<svg width="%d" height="%d" viewBox="0 0 %d %d">%s</svg>'
            '<p class=sub>Красный пунктир — резкое падение контекста (автокомпакт/правка истории).</p>'
            % (W, H, W, H, "".join(svg)))


def session_html(events: list[dict], st: dict, ctx_limit: int) -> str:
    u = st["usage"]
    sc = st["self_checks"]
    comp = st["context_management"]
    checks = [(k, v) for k, v in sc.items()
              if isinstance(v, int) and k not in ("debug_prints_in_code",) and v]
    checks.sort(key=lambda kv: -kv[1])
    bars = "".join(
        f'<div style="display:flex;align-items:center;gap:10px;margin:5px 0">'
        f'<div style="width:190px;font-size:13.5px;color:#1d2433">{NAME_CHECKS.get(k, k)}</div>'
        f'<div style="position:relative;background:#eef0f4;border-radius:6px;height:20px;flex:1;max-width:420px">'
        f'<div style="position:absolute;left:0;top:3px;bottom:3px;width:{max(2, v / max(c for _, c in checks) * 100):.0f}%;background:#2f7d54;opacity:.75;border-radius:5px"></div>'
        f'<span style="position:absolute;left:7px;top:0;font-size:12px;line-height:20px;font-weight:600">{v}</span>'
        "</div></div>"
        for k, v in checks)
    cards = (f'<div class="card"><b>{u["api_calls"]}</b><span>вызовов API</span></div>'
             f'<div class="card"><b>{fmt_k(u["total"])}</b><span>токенов всего (вход {fmt_k(u["input"])}, выход {fmt_k(u["output"])}, кэш-чтение {fmt_k(u["cache_read"])})</span></div>'
             f'<div class="card"><b>{fmt_k(u["context_peak"])}</b><span>пик контекста из {fmt_k(ctx_limit)}</span></div>'
             f'<div class="card"><b>{comp.get("compaction", 0)}+{comp.get("context_edit", 0)}</b><span>автокомпактов + правок истории</span></div>'
             f'<div class="card"><b>{st["tool_calls_total"]}</b><span>tool-вызовов ({st["bash_total"]} bash)</span></div>'
             f'<div class="card"><b>{fmt_dur(u["generation_time_s"])}</b><span>чистой генерации</span></div>')
    tests = st.get("test_results") or {}
    tline = (" · ".join(f"{k}: {v}" for k, v in tests.items())
             if tests else "тесты не запускались")
    return ('<h2>Сессия pi</h2><div class="cards">' + cards + "</div>"
            + context_chart(events, ctx_limit)
            + '<h3>Самопроверка моделью</h3>'
            + (bars if bars else '<p class=sub>инструменты самопроверки не запускались</p>')
            + f'<p class=sub>Прогоны тестов: {tline}; отладочных выводов в коде '
            f'(var_dump/dump/dd): {sc.get("debug_prints_in_code", 0)}; файлов затронуто: '
            f'{st.get("files_distinct", "?")}.</p>')


TOOLTIP_JS = """
<script>
document.querySelectorAll('svg.scatter g.pt').forEach(function (pt) {
  var dot = pt.querySelector('.dot') || pt.querySelector('rect,circle'), tip = pt.querySelector('.tip');
  if (!dot || !tip) return;
  dot.addEventListener('mouseenter', function () {
    pt.closest('svg').querySelector('.tip-layer').appendChild(tip);
    tip.style.display = 'block';
  });
  dot.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
});
</script>
"""

NAME_CHECKS = {
    "tests": "тесты (phpunit / make test)",
    "static_analysis": "статанализ (phpstan / psalm)",
    "lint": "синтаксис (php -l)",
    "db_checks": "проверки БД (psql)",
    "console_app": "приёмочные команды (bin/console app:*)",
    "build": "сборка (composer)",
}


# --- вердикты -----------------------------------------------------------------
def verdicts_html(run_dir: Path) -> str:
    blocks = []
    for fname, title in (("review-zcode.md", "Ярус 3 · ревью (изолированная pi-сессия)"),
                         ("judge-glm.md", "Ярус 4 · судья GLM-5.3-Flash (слепой)")):
        p = run_dir / fname
        if not p.is_file():
            continue
        txt = p.read_text(encoding="utf-8")
        m = re.findall(r"ось [AB][^*]*?\*\*([0-9.]+)\*\*", txt)
        m2 = re.findall(r"\*\*(?:A|B)\w*\s*[^*]*?([0-9.]+)\*\*", txt)
        scores = m or m2
        note = ""
        nm = re.search(r"(?:Обоснование|пояснение)[^\n]*\n+(.{0,700})", txt, re.S | re.I)
        if nm:
            note = nm.group(1).strip()
        blocks.append(f'<div class="quote"><div class=who>{title} — баллы: {", ".join(scores) if scores else "—"}</div>'
                      f"{note[:700] if note else txt[:400]}</div>")
    if not blocks:
        return ""
    return '<h2>Вердикты судей</h2>' + "".join(blocks)


def build(run_dir: Path) -> str:
    run_dir = run_dir.resolve()
    run = json.loads((run_dir / "run.json").read_text(encoding="utf-8"))
    check = json.loads((run_dir / "check.json").read_text(encoding="utf-8"))
    metrics = json.loads((run_dir / "metrics.json").read_text(encoding="utf-8"))
    collector = {}
    cj = run_dir / "metrics-raw" / "collector.json"
    if cj.is_file():
        collector = json.loads(cj.read_text(encoding="utf-8"))

    model_dir = run_dir.parent.parent.parent
    cohort = []
    for m in sorted(model_dir.parent.glob("*/backend/test-assignment/run-*/metrics.json")):
        try:
            cohort.append(json.loads(m.read_text(encoding="utf-8")))
        except Exception:  # noqa: BLE001
            pass
    own_path = run_dir / "metrics.json"
    cohort = [c for c, p in zip(cohort, sorted(model_dir.parent.glob("*/backend/test-assignment/run-*/metrics.json")))
              if p.resolve() != own_path.resolve()]

    events, _hdr = load(str(run_dir / "pi-session")) if (run_dir / "pi-session").is_dir() else ([], {})
    st = analyze(events) if events else None

    model = run.get("model", "?")
    thinking = run.get("thinking_effective") or run.get("thinking") or "?"
    ctx = run.get("context") or 102400
    dur = run.get("duration_s")
    mp, bp = check.get("mandatory_passed", 0), check.get("bonus_passed", 0)
    total = mp + bp
    cls = "g" if total >= 12 else ("r" if mp < 9 else "")
    head = (
        f"<h1>{model_dir.name} · {run_dir.name}</h1>"
        f'<p class=lead>Тег <code>{model}</code> · thinking: {thinking} · окно {fmt_k(ctx)} · '
        f"время {fmt_dur(dur)} · exit {run.get('exit_code', '?')} · "
        f"начало {str(run.get('started_at', ''))[:16].replace('T', ' ')}</p>"
        '<div class="cards">'
        f'<div class="card {cls}"><b>{mp}/{check.get("mandatory_total", 11)} + {bp}/{check.get("bonus_total", 2)}</b><span>чекер: обязательные + бонус</span></div>'
        f'<div class="card"><b>{fmt_dur(dur)}</b><span>длительность (бюджет 90 мин)</span></div>'
        f'<div class="card"><b>{metrics.get("phpstan", {}).get("errors", "—")}</b><span>phpstan L8</span></div>'
        f'<div class="card"><b>{metrics.get("scc", {}).get("loc", "—")}</b><span>LOC решения</span></div>'
        "</div>")

    matrix = dep_matrix(collector)
    tier2 = ('<h2>Ярус 2 · метрики кода</h2>'
             + bars_with_cohort(metrics, cohort)
             + '<h3>Классы: размер и связность</h3>'
             + ast_scatter(collector)
             + (('<h3>Матрица зависимостей классов</h3>' + matrix) if matrix else "")
             + ast_tables(collector))

    sess = session_html(events, st, ctx) if st else '<div class=note>pi-сессия не найдена</div>'
    verdicts = verdicts_html(run_dir)

    page = f"""<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{model_dir.name} · {run_dir.name} — тестовое задание</title>
<style>{CSS}</style></head><body><div class=wrap>
{head}
{checker_html(check)}
{tier2}
{sess}
{verdicts}
<footer>Показатели собраны из артефактов прогона: run.json, check.json, metrics.json,
metrics-raw/collector.json, pi-session/*.jsonl (парсер — analyze_session.py).
Генератор: <code>test-assignment/backend/billing/build_run_report.py</code>.</footer>
</div></body></html>"""
    page = page.replace("</body></html>", TOOLTIP_JS + "</body></html>")
    out = run_dir / "report.html"
    out.write_text(page, encoding="utf-8")
    return str(out)


def main() -> int:
    if len(sys.argv) != 2:
        print(__doc__)
        return 2
    run_dir = Path(sys.argv[1])
    for req in ("run.json", "check.json", "metrics.json"):
        if not (run_dir / req).is_file():
            print(f"нет {req} в {run_dir}", file=sys.stderr)
            return 2
    print("готово:", build(run_dir))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

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

import html
import json
import math
import re
import subprocess
import sys
from collections import Counter
from functools import lru_cache
from pathlib import Path

BILLING = Path(__file__).resolve().parent
ROOT = BILLING.parents[2]


@lru_cache(maxsize=1)
def git_blob_base() -> str | None:
    """https://github.com/<owner>/<repo>/blob/<ветка> — из git origin и ветки.
    Ссылки на код решений заработают, когда артефакты прогонов уйдут в git."""
    try:
        url = subprocess.run(["git", "-C", str(ROOT), "remote", "get-url", "origin"],
                             capture_output=True, text=True, timeout=10).stdout.strip()
        branch = subprocess.run(["git", "-C", str(ROOT), "rev-parse", "--abbrev-ref", "HEAD"],
                                capture_output=True, text=True, timeout=10).stdout.strip()
        m = re.search(r"github\.com[:/](.+?)(?:\.git)?$", url)
        if m and branch:
            return f"https://github.com/{m.group(1)}/blob/{branch}"
    except Exception:  # noqa: BLE001
        pass
    return None
sys.path.insert(0, str(BILLING))
sys.path.insert(0, str(BILLING / "checker"))
from analyze_session import analyze, load, security_audit  # noqa: E402

CSS = """
:root{--ink:#1d2433;--mut:#66707f;--line:#e4e7ec;--bg:#fafbfc;--acc:#315f9e;--good:#2f7d54;--bad:#b3543f}
*{box-sizing:border-box}
.tree div{font:13.5px/1.75 ui-monospace,SFMono-Regular,Consolas,monospace}
.tree a{color:var(--acc);text-decoration:none}
.tree a:hover{text-decoration:underline}
.tree .cnt{color:var(--mut)}
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
.card span{display:block;color:var(--mut);font-size:12.5px}
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
.donut{position:relative}
.donut .tip{position:absolute;display:none;background:#1d2433;color:#fff;border-radius:7px;padding:5px 10px;font-size:12.5px;pointer-events:none;z-index:6;white-space:nowrap;box-shadow:0 4px 14px rgba(20,30,50,.25)}
.donut circle.seg{cursor:pointer}
.donut circle.seg:hover{opacity:.82}
.quote{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 17px;margin:12px 0;font-size:14.5px;white-space:pre-wrap}
.quote .who{font-size:12.5px;color:var(--mut);margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em}
pre{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px 16px;margin:12px 0;font:13px/1.6 ui-monospace,SFMono-Regular,Consolas,"Liberation Mono",monospace;white-space:pre-wrap;overflow-wrap:anywhere;color:var(--ink)}
.envlist{margin:10px 0 4px;padding-left:22px;font-size:14.5px}
.envlist li{margin:5px 0}
.qlist{margin:6px 0 14px;padding-left:22px}
.qlist li{margin:4px 0;font-size:13px}
.qlist code{background:#f3f5f8;border-radius:4px;padding:1px 5px;font-size:12.5px}
.note{background:#fff9e8;border:1px solid #eedd9f;border-radius:12px;padding:12px 16px;font-size:14.5px}
footer{margin-top:46px;color:var(--mut);font-size:13px;border-top:1px solid var(--line);padding-top:14px}
"""


def fmt_k(v):
    if v >= 1_000_000:
        return f"{v / 1_000_000:.1f}M"
    return f"{v / 1000:.1f}K" if v >= 1000 else str(v)


def fmt_dur(s):
    if s is None:
        return "—"
    return f"{round(s)} с" if s < 90 else f"{s / 60:.0f} мин"


# --- ярус 1: сценарии чекера ------------------------------------------------
def checker_html(check: dict, run_dir: Path | None = None) -> str:
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
    return (f"<h2>Ярус 1 · чекер: обязательных {mp} из {mt}, бонусных {bp} из {bt}</h2>"
            '<p class=lead>Отвечает «сделано и работает по контракту», но не «хорошо сделано»: '
            "каждый сценарий запускает решение и сверяет результат с эталоном — счётчики "
            "импорта, дедупликация, отчёт байт-в-байт. Качество кода чекер не оценивает. "
            "Работает на своей временной копии решения: артефакты кандидата не трогаются, "
            "vendor поднимается <code>composer install</code> (при рассинхроне с lock — "
            "<code>update</code>).</p>"
            "<table><thead><tr><th>Сценарий</th><th>Статус</th><th>Детали</th></tr></thead><tbody>"
            + "".join(rows) + "</tbody></table>"
            + (art_links(run_dir, "check.json") if run_dir else ""))


# --- ярус 2: метрики ----------------------------------------------------------
def scc_bars(run_dir: Path) -> str:
    """Объёмы кода scc барами с риской-средним других прогонов модели
    (когорта — сырье metrics-raw/scc.json соседних прогонов)."""
    own_raw = run_dir / "metrics-raw" / "scc.json"

    def agg(p: Path) -> dict | None:
        try:
            return json.loads(p.read_text(encoding="utf-8"))[0]
        except Exception:  # noqa: BLE001
            return None

    own = agg(own_raw)
    if not own:
        return ""
    cohort = [c for c in (agg(m) for m in sorted(
        run_dir.parent.glob("run-*/metrics-raw/scc.json"))
        if m.resolve() != own_raw.resolve()) if c]
    FIELDS = (("Count", "файлов"), ("Code", "строк кода"), ("Comment", "комментариев"),
              ("Blank", "пустых строк"), ("Complexity", "суммарная сложность"))
    rows, note = [], ""
    for key, name in FIELDS:
        val = int(own.get(key, 0) or 0)
        vals = [int(c.get(key, 0) or 0) for c in cohort]
        avg = round(sum(vals) / len(vals)) if vals else val
        vmax = max([val, avg] + vals + [1])
        rows.append(_bar_row(name, val, avg, vmax, f"среднее: {avg}"))
    if cohort:
        note = ('<p class=sub>Синяя полоса — этот прогон; красная риска — среднее других '
                "прогонов модели. Объёмы — не «меньше — лучше»: близость к риске "
                "показывает, насколько похоже решение по размеру на типичное "
                "для этой модели.</p>")
    return ('<table style="width:auto;max-width:640px"><tbody>'
            + "".join(rows) + "</tbody></table>" + note)


def solution_tree(run_dir: Path) -> str:
    """Дерево, которое агент создал или изменил: по diff.patch (файлы с «+++»).
    Папка — ссылка (открывается списком в браузере), число — затронутых файлов.
    Новое vs изменённое: «--- /dev/null» в диффе помечает создание."""
    dp = run_dir / "diff.patch"
    if not dp.is_file():
        return ""
    touched: dict[str, dict[str, int]] = {}
    cur_new = False
    for ln in dp.read_text(encoding="utf-8", errors="ignore").splitlines():
        if ln.startswith("--- "):
            cur_new = ln.strip() == "--- /dev/null"
        elif ln.startswith("+++ ") and "/repo/" in ln:
            rel = ln.split("/repo/")[-1].split("\t")[0]
            d = rel.rsplit("/", 1)[0] if "/" in rel else "."
            slot = touched.setdefault(d, {"новых": 0, "всего": 0})
            slot["всего"] += 1
            slot["новых"] += int(cur_new)
    if not touched:
        return ""
    # предки затронутых папок (src/ без прямых файлов) + суммарные числа
    # по поддереву: папка без своих файлов не должна выпадать из дерева
    all_dirs = set(touched)
    for d in list(touched):
        parts = d.split("/")
        for i in range(1, len(parts)):
            all_dirs.add("/".join(parts[:i]))

    def totals(d: str) -> dict:
        prefix = "" if d == "." else d + "/"
        t = {"новых": 0, "всего": 0}
        for k, v in touched.items():
            if k == d or k.startswith(prefix):
                t["всего"] += v["всего"]
                t["новых"] += v["новых"]
        return t

    def children(d: str) -> list[str]:
        prefix = "" if d == "." else d + "/"
        lvl = prefix.count("/")
        return sorted(x for x in all_dirs
                      if x != d and x.startswith(prefix) and x.count("/") == lvl)

    def render(d: str, depth: int) -> None:
        name = "solution/" if d == "." else d.split("/")[-1] + "/"
        t = totals(d)
        extra = f' · новых {t["новых"]}' if t["новых"] and t["новых"] < t["всего"] else ""
        body = f'<a href="solution/{d}/">{name}</a>' if d != "." else f"<b>{name}</b>"
        lines.append(f'<div>{"&nbsp;" * (4 * depth)}{body} '
                     f'<span class="cnt">— {t["всего"]}{extra}</span></div>')
        for sub in children(d):
            render(sub, depth + 1)

    lines: list[str] = []
    render(".", 0)
    return ('<h3>Структура папок решения</h3>'
            '<p class=sub>Дерево по diff.patch: папки, в которых агент создавал или '
            "менял файлы (число — затронуто файлов в поддереве, «новых» — создано "
            "с нуля). Папка — ссылка, можно открыть.</p>"
            '<div class="tree">' + "".join(lines) + "</div>")


def tier2_details_html(run_dir: Path, metrics: dict) -> str:
    """Детали яруса 2 из сырья metrics-raw/: сводка scc и топ-нарушений
    phpstan (сообщения), phpmd (правила), phpcs (сниффы)."""
    raw = run_dir / "metrics-raw"

    def read(name: str) -> str:
        p = raw / name
        return p.read_text(encoding="utf-8", errors="ignore") if p.is_file() else ""

    def rel(pth: str) -> str:
        return pth.split("/repo/")[-1]

    blocks = []

    sccj = {}
    p = raw / "scc.json"
    if p.is_file():
        try:
            sccj = json.loads(p.read_text(encoding="utf-8"))[0]
        except Exception:  # noqa: BLE001
            sccj = {}
    if sccj:
        code = int(sccj.get("Code") or 0)
        com = int(sccj.get("Comment") or 0)
        pct = f" — {com / code * 100:.0f}% от строк кода" if code else ""
        blocks.append(
            '<h4 style="margin:16px 0 4px">scc — объём кода</h4>'
            '<div class="cards" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr))">'
            + f'<div class="card"><b>{code}</b><span>строк кода</span></div>'
            + f'<div class="card"><b>{com}</b><span>строк комментариев{pct}</span></div>'
            + f'<div class="card"><b>{int(sccj.get("Count") or 0)}</b><span>файлов</span></div>'
            + "</div>" + scc_bars(run_dir))

    # phpunit — тесты кандидата
    pu = metrics.get("phpunit") or {}
    if pu.get("status") == "ok" and pu.get("tests"):
        cov = pu.get("coverage_lines_pct")
        fe = pu.get("failures_errors", 0)
        verdict = ("все проходят" if not fe else f"упало {fe} (в нашем окружении)")
        blocks.append(
            '<h4 style="margin:16px 0 4px">phpunit — тесты кандидата</h4>'
            '<div class="cards" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr))">'
            + f'<div class="card {"g" if not fe else "r"}"><b>{pu["tests"]}</b><span>тестов, {verdict}</span></div>'
            + f'<div class="card"><b>{pu.get("assertions", "—")}</b><span>проверок (ассерций)</span></div>'
            + f'<div class="card"><b>{pu.get("test_loc", "—")}</b><span>строк тестового кода</span></div>'
            + f'<div class="card"><b>{f"{cov:g}%" if cov is not None else "—"}</b>'
            '<span>покрытие строк src/</span></div>'
            + "</div>")

    # phpcs: сообщения сниффов
    sniff: Counter = Counter()
    for ln in read("phpcs-full.txt").splitlines():
        m = re.match(r"^\s*\d+\s+\|\s+(?:WARNING|ERROR)\s+\|\s+(.+?)\s*$", ln)
        if m:
            sniff[m.group(1)] += 1
    if sniff:
        rows = "".join(f"<tr><td class=sub style=\"white-space:nowrap\">{n}×</td>"
                       f"<td>{html.escape(t)}</td></tr>"
                       for t, n in sniff.most_common(6))
        blocks.append('<h4 style="margin:16px 0 4px">phpcs (стиль, PSR-12) — топ сообщений</h4>'
                      '<table style="width:auto"><thead><tr><th>раз</th><th>сообщение</th></tr></thead>'
                      f"<tbody>{rows}</tbody></table>")

    # psalm: топ типов проблем (psalm-raw.json — голый список или {"issues": [...]})
    pj = raw / "psalm-raw.json"
    if pj.is_file():
        try:
            pdata = json.loads(pj.read_text(encoding="utf-8"))
            pis = pdata.get("issues") if isinstance(pdata, dict) else (pdata or [])
        except Exception:  # noqa: BLE001
            pis = []
        if pis:
            cnt = Counter(str(i.get("type", "?")) for i in pis)
            ex: dict[str, str] = {}
            for i in pis:
                t = str(i.get("type", "?"))
                if t not in ex:
                    fn = str(i.get("file_name", "?")).split("/repo/")[-1]
                    ex[t] = f"{fn}:{i.get('line_from', '?')}"
            rows = "".join(
                f"<tr><td class=sub style=\"white-space:nowrap\">{n}×</td>"
                f"<td><code>{html.escape(t)}</code></td>"
                f"<td class=sub style=\"white-space:nowrap\">{html.escape(ex[t])}</td></tr>"
                for t, n in cnt.most_common(8))
            blocks.append('<h4 style="margin:16px 0 4px">psalm (уровень 1) — топ типов проблем</h4>'
                          '<table style="width:auto"><thead><tr><th>раз</th><th>тип</th>'
                          '<th>пример</th></tr></thead>'
                          f"<tbody>{rows}</tbody></table>")

    # phpstan: топ сообщений (без путей) + пример места
    msgs = []
    for ln in read("phpstan-raw.txt").splitlines():
        m = re.match(r"^(.+?):(\d+):(.*)$", ln)
        if m:
            msgs.append((rel(m.group(1)), m.group(2), m.group(3).strip()))
    if msgs:
        cnt = Counter(t for _, _, t in msgs)
        ex = {t: f"{f}:{ln}" for f, ln, t in msgs}
        rows = "".join(
            f"<tr><td class=sub style=\"white-space:nowrap\">{n}×</td>"
            f"<td>{html.escape(t[:120])}</td>"
            f"<td class=sub style=\"white-space:nowrap\">{html.escape(ex[t])}</td></tr>"
            for t, n in cnt.most_common(6))
        blocks.append('<h4 style="margin:16px 0 4px">phpstan — топ сообщений</h4>'
                      '<table style="width:auto"><thead><tr><th>раз</th><th>сообщение</th>'
                      '<th>пример</th></tr></thead>'
                      f"<tbody>{rows}</tbody></table>")

    # phpmd: правила
    rules: Counter = Counter()
    for ln in read("phpmd-raw.txt").splitlines():
        parts = re.split(r"\s{2,}", ln.strip(), maxsplit=2)
        if len(parts) >= 2:
            rules[parts[1]] += 1
    if rules:
        rows = "".join(f"<tr><td class=sub style=\"white-space:nowrap\">{n}×</td>"
                       f"<td><code>{html.escape(r)}</code></td></tr>"
                       for r, n in rules.most_common(8))
        blocks.append('<h4 style="margin:16px 0 4px">phpmd — правила (наблюдение, в оценку не входит)</h4>'
                      '<table style="width:auto"><thead><tr><th>раз</th><th>правило</th></tr></thead>'
                      f"<tbody>{rows}</tbody></table>")

    return '<h3>Код в цифрах и нарушения в деталях</h3>' + "".join(blocks) if blocks else ""


# тарифы OpenRouter ($ за токен) для облачных прогонов; источник —
# openrouter.ai/api/v1/models (2026-10-18). cost считается по usage сессии,
# т.к. pi не отдаёт cost для кастомных провайдеров
OPENROUTER_TARIFFS = {
    "openai/gpt-6-luna": (1e-7, 5e-7, 1e-8),   # вход, выход, кэш-чтение
    "openai/gpt-6.1-sol": (2e-6, 1e-5, 1e-7),
}


def _bar_row(name: str, val: int, avg: float, vmax: int, right: str) -> str:
    """Строка-бар сравнения: полоса — значение прогона, SVG-риска — среднее
    других прогонов (SVG 1:1: у div-заливок при дробном масштабе края мутнеют)."""
    w_own = max(4, round(val / vmax * 420))
    x_avg = round(avg / vmax * 420)
    return (f'<tr><td style="white-space:nowrap;line-height:22px"><b>{name}</b></td>'
            f'<td><div style="position:relative;background:#eef0f4;border-radius:6px;height:22px;width:420px">'
            f'<div style="position:absolute;left:0;top:3px;height:16px;width:{w_own}px;background:#315f9e;opacity:.75;border-radius:5px"></div>'
            f'<svg style="position:absolute;left:{max(0, x_avg - 1)}px;top:0" width="3" height="22">'
            f'<rect width="3" height="22" rx="1.5" fill="#8f3d2c"/></svg>'
            f'<span style="position:absolute;left:8px;top:0;font-size:12.5px;line-height:22px;font-weight:600">{val}</span>'
            f"</div></td>"
            f'<td class=sub style="white-space:nowrap">{right}</td></tr>')


def bars_with_cohort(own: dict, cohort: list[dict]) -> str:
    """Бары нарушений: своё значение + среднее других прогонов модели."""
    rows = []
    for key, name, field in (("phpcs", "phpcs PSR12", "violations"),
                             ("psalm", "psalm L1", "issues"),
                             ("phpstan", "phpstan L8", "errors"),
                             ("phpmd", "phpmd (наблюдение)", "violations")):
        val = own.get(key, {}).get(field, 0)
        vals = [m.get(key, {}).get(field, 0) for m in cohort]
        # у когорты из двух прогонов медиана = большее из двух (риска у края
        # дорожки выглядит странно) — центральную оценку даёт среднее
        med = round(sum(vals) / len(vals)) if vals else val
        vmax = max([val, med] + vals + [1])
        rows.append(_bar_row(name, val, med, vmax, f"среднее других прогонов модели: {med}"))
    return ('<table style="width:auto;max-width:640px"><tbody>'
            + "".join(rows) + "</tbody></table>"
            '<p class=sub>Синяя полоса — этот прогон; красная риска — среднее того же показателя '
            "по другим прогонам этой же модели (меньше — лучше). phpmd — наблюдение: "
            "в оценку решений не входит. Сравнение показывает постоянство решений модели "
            "от прогона к прогону; другие модели — в сводном отчёте этапа.</p>")


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
    ext_detail: dict[str, dict[str, int]] = {}
    for dep in collector.get("dependencies") or []:
        src, dst = dep.get("source"), dep.get("target")
        if src in internal and dst in internal and src != dst:
            counts[(src, dst)] = counts.get((src, dst), 0) + 1
        elif src in internal:
            ext_out[src] = ext_out.get(src, 0) + 1
            d = ext_detail.setdefault(src, {})
            d[dst] = d.get(dst, 0) + 1
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
                   f'transform="rotate(-90 {xi[c] + cell / 2:.0f} {MT - 8})" text-anchor=start>{short[:20]}</text>')
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
    # столбец «внеш.» — всплывашка перечисляет сами внешние типы
    xe = ML + n * (cell + pad)
    for c in order:
        k = ext_out.get(c, 0)
        if not k:
            continue
        pairs = sorted(ext_detail[c].items(), key=lambda kv: -kv[1])
        shown = ", ".join(f"{t.split(chr(92))[-1]} ×{n}" for t, n in pairs[:3])
        if len(pairs) > 3:
            shown += ", …"
        label = f"{c.split(chr(92))[-1]} → {shown}"
        if len(label) > 64:  # FQCN-цели укорочены; страхуем потолок ширины карточки
            label = label[:63] + "…"
        tw = len(label) * 7.0 + 18
        tx = xe + cell + 10 if xe + cell + 10 + tw <= W - 8 else xe - 10 - tw
        ty = max(4, min(yi[c] - 42, H - 44))
        op = 0.25 + 0.75 * min(k / max(ext_out.values()), 1)
        svg.append(f'<g class="pt"><rect class="dot" x={xe:.0f} y={yi[c]:.0f} width={cell} height={cell} rx=2 '
                   f'fill="#c9b458" fill-opacity="{op:.2f}"></rect>'
                   f'<g class="tip"><rect x={tx:.0f} y={ty:.0f} width={tw:.0f} height=30 rx=7 fill="#1d2433" fill-opacity="0.95"/>'
                   f'<text x={tx + 9:.0f} y={ty + 20:.0f} font-size=11.5 fill="#ffffff">{label}</text></g></g>')
    svg.append(f'<text x={xe + cell / 2:.0f} y={MT - 8} font-size=10 fill=#8a7a3a transform="rotate(-90 {xe + cell / 2:.0f} {MT - 8})" text-anchor=start>внеш.</text>')
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


def coupling_table(collector: dict, run_dir: Path | None = None) -> str:
    """Связанность классов по графу внутренних зависимостей: C_e (исходящие,
    efferent coupling), C_a (входящие, afferent coupling) и нестабильность
    I = C_e / (C_e + C_a): 0 — максимально стабильный класс, 1 — максимально
    нестабильный. Здоровая слоистость: I≈1 у console-команд (точки входа),
    I≈0 у примитивов домена."""
    classes = {cl["name"] for cl in collector.get("classes") or []}
    ce: dict[str, int] = {}
    ca: dict[str, int] = {}
    for dep in collector.get("dependencies") or []:
        src, dst = dep.get("source"), dep.get("target")
        if src in classes and dst in classes and src != dst:
            ce[src] = ce.get(src, 0) + 1
            ca[dst] = ca.get(dst, 0) + 1
    if not ce and not ca:
        return ""
    file_by_class = {cl["name"]: str(cl["metrics"].get("filePath") or "")
                     for cl in collector.get("classes") or []}
    rows = []
    for name in sorted(set(ce) | set(ca)):
        e, a = ce.get(name, 0), ca.get(name, 0)
        i = e / (e + a) if e + a else 0.0
        hue = "#2f7d54" if i <= 0.33 else "#d09a53" if i <= 0.66 else "#b3543f"
        label = name.split("\\")[-1]
        rows.append((i, git_code_link(run_dir, file_by_class.get(name, ""), label),
                     e, a, hue, int(round(i * 60))))
    rows.sort(key=lambda x: (-x[0], x[1]))
    trs = "".join(
        f'<tr><td style="white-space:nowrap">{n}</td>'
        f'<td style="text-align:center">{e}</td>'
        f'<td style="text-align:center">{a}</td>'
        f'<td><span style="display:inline-block;width:{bw}px;height:8px;'
        f'border-radius:3px;background:{hue};vertical-align:middle;margin-right:6px"></span>'
        f'<b style="color:{hue}">{i:.2f}</b></td></tr>'
        for i, n, e, a, hue, bw in rows)
    return ('<h3>Связанность классов: C<sub>e</sub>, C<sub>a</sub>, нестабильность I</h3>'
            '<p class=sub>C<sub>e</sub> — исходящие связи на классы решения '
            '(efferent coupling), C<sub>a</sub> — входящие (afferent coupling); '
            'I = C<sub>e</sub> / (C<sub>e</sub> + C<sub>a</sub>) — нестабильность: '
            '0 — максимально стабильный класс, 1 — максимально нестабильный. '
            'Здоровая слоистость выглядит так: I близка к 1 у console-команд '
            '(точки входа, обязаны меняться часто) и к 0 у примитивов домена '
            '(Decimal, Timestamp, enum-статусы): нестабильное должно зависеть '
            'от стабильного, не наоборот. Классы без внутренних связей '
            'не показаны.</p>'
            f'<table style="width:auto"><thead><tr><th>Класс</th>'
            f'<th>C<sub>e</sub></th><th>C<sub>a</sub></th>'
            f'<th>I (0…1)</th></tr></thead><tbody>{trs}</tbody></table>')

def git_code_link(run_dir: Path | None, pth: str, label: str | None = None) -> str:
    """Ссылка на файл решения в git (blob): report.html лежит локально,
    а .php браузер предлагает скачать — смотрим код на GitHub."""
    rel = pth.split("/repo/")[-1]
    base = git_blob_base()
    if run_dir and base:
        git_path = run_dir.relative_to(ROOT) / "solution" / rel
        label = label or rel.split("/")[-1]
        return f'<a href="{base}/{git_path}">{html.escape(label)}</a>'
    return html.escape(label or rel.split("/")[-1])


def cycles_block(collector: dict, run_dir: Path | None = None) -> str:
    """Циклические зависимости: компоненты сильной связности графа внутренних
    зависимостей (классы, где от каждого достижим каждый). Каждый цикл —
    кандидат на разрыв (принцип ациклических зависимостей Мартина). Имена
    классов — ссылки на код в git."""
    classes = {cl["name"] for cl in collector.get("classes") or []}
    file_by_class = {cl["name"]: str(cl["metrics"].get("filePath") or "")
                     for cl in collector.get("classes") or []}
    edges = [(dep["source"], dep["target"])
             for dep in collector.get("dependencies") or []
             if dep.get("source") in classes and dep.get("target") in classes
             and dep["source"] != dep["target"]]
    g: dict[str, set[str]] = {}
    for a, b in edges:
        g.setdefault(a, set()).add(b)
    index: dict[str, int] = {}
    low: dict[str, int] = {}
    on: set[str] = set()
    stack: list[str] = []
    counter = [0]
    comps: list[list[str]] = []

    def visit(u: str) -> None:
        index[u] = low[u] = counter[0]
        counter[0] += 1
        stack.append(u)
        on.add(u)
        for w in sorted(g.get(u, ())):
            if w not in index:
                visit(w)
                low[u] = min(low[u], low[w])
            elif w in on:
                low[u] = min(low[u], index[w])
        if low[u] == index[u]:
            comp = []
            while True:
                w = stack.pop()
                on.discard(w)
                comp.append(w)
                if w == u:
                    break
            if len(comp) > 1:
                comps.append(sorted(comp))

    sys.setrecursionlimit(10000)
    for u in sorted(g):
        if u not in index:
            visit(u)
    if not comps:
        return ('<h3 id="cycles">Циклические зависимости</h3>'
                '<p class=lead>Циклы (компоненты сильной связности) в графе '
                'внутренних зависимостей: <b style="color:#2f7d54">нет</b> — граф '
                'ацикличен, как требует принцип ациклических зависимостей Мартина '
                '(Acyclic Dependencies Principle).</p>')
    short = lambda c: c.split("\\")[-1]

    def class_link(full: str) -> str:
        return git_code_link(run_dir, file_by_class.get(full, ""), short(full))

    items = []
    for comp in comps:
        inner = sorted((a, b) for a, b in edges if a in comp and b in comp)
        chain = " ↔ ".join(class_link(c) for c in comp)
        detail = ", ".join(html.escape(f"{short(a)} → {short(b)}") for a, b in inner)
        items.append(f'<li style="margin-bottom:4px">{chain}'
                     f'<div class=sub>{detail}</div></li>')
    return ('<h3 id="cycles">Циклические зависимости</h3>'
            '<p class=lead>Компоненты сильной связности графа внутренних зависимостей: '
            'классы, где от каждого достижим каждый. Каждый цикл — кандидат на разрыв: '
            'вынести интерфейс или событие (принцип ациклических зависимостей Мартина, '
            'Acyclic Dependencies Principle).</p>'
            f'<ul style="margin:4px 0 10px 18px;padding:0">{"".join(items)}</ul>')


def ast_tables(collector: dict, run_dir: Path | None = None) -> str:
    methods: dict[str, list] = {}
    fnames: dict[str, str] = {}
    for fn in collector.get("functions") or []:
        if fn.get("type") != "method":
            continue
        cls = str(fn["metrics"].get("classInfo", "")).split(", ", 1)[-1]
        methods.setdefault(cls, []).append(fn)
        fnames.setdefault(cls, fn["name"])

    def code_link(pth: str, label: str | None = None) -> str:
        return git_code_link(run_dir, pth, label)

    file_by_class = {}
    for cl in collector.get("classes") or []:
        file_by_class[cl["name"]] = str((cl["metrics"].get("filePath") or ""))
    classes = []
    for cl in collector.get("classes") or []:
        m = cl["metrics"]
        if m.get("interface") or m.get("trait") or m.get("enum"):
            continue
        ms = methods.get(cl["name"], [])
        fname = file_by_class.get(cl["name"], "").split("/")[-1]
        classes.append({
            "name": cl["name"].split("\\")[-1],
            "file": code_link(file_by_class.get(cl["name"], ""), fname),
            "loc": m.get("loc", 0), "n": len(ms),
            "wmc": sum(x["metrics"].get("cc", 0) for x in ms),
            "cc": max((x["metrics"].get("cc", 0) for x in ms), default=0),
            "lcom": m.get("lcom", 0),
        })
    classes.sort(key=lambda d: (-d["wmc"], -d["loc"]))
    top = "".join(
        f"<tr><td><b>{c['name']}</b>"
        + (f'<div class=sub>{c["file"]}</div>')
        + f"</td>"
        f"<td>{c['loc']}</td><td>{c['n']}</td><td><b>{c['wmc']}</b></td>"
        f"<td>{'⚠' if c['cc'] > 10 else ''} {c['cc']}</td><td>{'⚠' if c['lcom'] > 1 else ''} {c['lcom']}</td></tr>"
        for c in classes[:10])
    findings = []
    for cls, fns in methods.items():
        for fn in fns:
            mm = fn["metrics"]
            if mm.get("cc", 0) > 10 or mm.get("loc", 0) > 50:
                findings.append((cls.split("\\")[-1], fn["name"], mm.get("cc", 0),
                                 mm.get("loc", 0), file_by_class.get(cls, "")))
    findings.sort(key=lambda t: (-t[2], -t[3]))
    frows = "".join(
        f"<tr><td><code>{c}::{n}</code>"
        + f'<div class=sub>{code_link(p, "открыть на GitHub")}</div>'
        + f"</td><td>{cc}</td><td>{loc}</td></tr>"
        for c, n, cc, loc, p in findings[:12]) or '<tr><td colspan=3 class=sub>нет</td></tr>'
    return ('<h3>Классы по WMC (топ-10)</h3>'
            '<p class=sub>WMC — суммарная сложность методов класса (сколько логики в классе '
            "в целом); cc max — сложность самого сложного его метода; "
            f'<a href="https://objectscriptquality.com/docs/metrics/lack-cohesion-methods-lcom4">LCOM4</a> — число '
            "«зон ответственности» (⚠ при &gt;1). Имя файла — ссылка на код в git "
            "(работает после коммита артефактов).</p>"
            "<table><thead><tr><th>Класс</th><th>LOC</th><th>методов</th><th>WMC</th><th>cc max</th><th>LCOM4</th></tr></thead>"
            f"<tbody>{top}</tbody></table>"
            '<h3>Методы-нарушители порогов (cc &gt; 10 или LOC &gt; 50)</h3>'
            "<table style=\"width:auto;max-width:520px\"><thead><tr><th>Метод</th><th>CC</th><th>LOC</th></tr></thead>"
            f"<tbody>{frows}</tbody></table>"
            '<p class=sub>Полные данные — <code>metrics-raw/collector.json</code>.</p>')


# --- сессия -------------------------------------------------------------------
def context_chart(events: list[dict], ctx_limit: int, test_events: list[dict] | None = None,
                  tool_colors: dict[str, str] | None = None,
                  provider: str = "ollama") -> str:
    """Контекст модели по вызовам API: полный промпт (input+cacheRead+cacheWrite),
    обрывы автокомпактов. cacheWrite включён, чтобы кэш-промах (кэш истёк за
    долгой генерацией) не выглядел сбросом контекста в ноль — ромбы ◆ метят
    такие перезаливки. Ось X — время от начала сессии (минуты), если в
    событиях есть timestamp. Под осью — метки запусков тестов (✓ прошёл /
    ✗ упал / ? не разобран). tool_colors — вертикали tool-вызовов, цвет =
    инструмент (эксперимент)."""
    pts = []  # (секунды от начала или None, контекст)
    miss_idx: list[int] = []  # вызовы с полной перезаливкой кэша (метим ромбом)
    t0 = None
    for e in events:
        if e.get("type") != "message":
            continue
        m = e.get("message") or {}
        u = m.get("usage")
        if m.get("role") == "assistant" and isinstance(u, dict):
            t = None
            ts = e.get("timestamp")
            if isinstance(ts, str):
                try:
                    from datetime import datetime
                    t = datetime.fromisoformat(ts.replace("Z", "+00:00")).timestamp()
                except ValueError:
                    t = None
            if t is not None and t0 is None:
                t0 = t
            prompt = (int(u.get("input") or 0) + int(u.get("cacheRead") or 0)
                      + int(u.get("cacheWrite") or 0))
            # первый вызов всегда пишет кэш целиком — это не промах;
            # промах = основная часть промпта перезалита (cacheRead ≈ 0)
            if pts and int(u.get("cacheWrite") or 0) > 0.5 * prompt > 0 \
                    and int(u.get("cacheRead") or 0) < 0.2 * prompt:
                miss_idx.append(len(pts))
            pts.append((None if t is None or t0 is None else t - t0, prompt))
    if len(pts) < 2:
        return '<div class=note>Мало вызовов для графика контекста</div>'
    timed = all(t is not None for t, _ in pts)
    W, H = 960, 286
    ML, MR, MT, MB = 72, 16, 14, 66  # PH = 206: полоса меток тестов и подписи минут внизу
    PW, PH = W - ML - MR, H - MT - MB
    ymax = max([v for _, v in pts] + [ctx_limit]) * 1.04
    if timed:
        xmax = max(t for t, _ in pts) or 1
        x = lambda t: ML + t / xmax * PW  # noqa: E731
    else:
        x = lambda i: ML + i / (len(pts) - 1) * PW  # noqa: E731
    y = lambda v: MT + (1 - v / ymax) * PH  # noqa: E731
    svg: list[str] = []
    for gv in range(0, int(ymax), int(ymax / 4) or 1):
        svg.append(f'<line x1={ML} y1={y(gv):.0f} x2={ML+PW} y2={y(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y(gv)+4:.0f} text-anchor=end font-size=12 fill=#66707f>{fmt_k(gv)}</text>')
    if timed:  # вертикальная шкала: ~4–8 рисок в минутах
        step = next(s for s in (60, 120, 300, 600, 900, 1200, 1800, 3600) if xmax / s <= 8)
        tm = step
        while tm <= xmax:
            svg.append(f'<line x1={x(tm):.0f} y1={MT} x2={x(tm):.0f} y2={MT+PH} stroke="#e4e7ec"/>'
                       f'<text x={x(tm):.0f} y={MT+PH+30:.0f} text-anchor=middle font-size=12 fill=#66707f>{round(tm / 60)}м</text>')
            tm += step
    if tool_colors:  # периоды работы инструментов: от вызова до результата
        from datetime import datetime as _dt

        def _sec(ts: str) -> float | None:
            try:
                return _dt.fromisoformat(ts.replace("Z", "+00:00")).timestamp()
            except ValueError:
                return None

        open_calls: dict[str, dict] = {}
        spans: list[tuple[str, str, str]] = []
        for e in events:
            if e.get("type") != "message" or not t0:
                continue
            m = e["message"]
            ts = e.get("timestamp")
            if m.get("role") == "assistant":
                for c in (m.get("content") or []):
                    if isinstance(c, dict) and c.get("type") == "toolCall" and isinstance(ts, str):
                        open_calls[str(c.get("id"))] = {"name": c.get("name") or "?", "ts": ts}
            elif m.get("role") == "toolResult":
                call = open_calls.pop(str(m.get("toolCallId")), None)
                if call:
                    spans.append((call["name"], call["ts"], ts))
        for name, s_ts, e_ts in spans:
            hue = tool_colors.get(name)
            if not hue:
                continue
            tv_s, tv_e = _sec(s_ts), _sec(e_ts)
            if tv_s is None or tv_e is None:
                continue
            x1 = x(max(0.0, min(tv_s - t0, xmax)))
            x2 = x(max(0.0, min(tv_e - t0, xmax)))
            svg.append(f'<rect x={x1:.1f} y={MT} width={max(1.2, x2 - x1):.1f} height={PH} '
                       f'fill="{hue}" fill-opacity="0.2"></rect>')
    path = " ".join(f"{x(t if timed else i):.0f},{y(v):.0f}" for i, (t, v) in enumerate(pts))
    svg.append(f'<polyline points="{path}" fill="none" stroke="#315f9e" stroke-width="1.8"/>')
    for i in miss_idx:  # кэш-промах: контекст цел, но перезалит в кэш целиком
        t, v = pts[i]
        cx, cy = x(t if timed else i), y(v)
        svg.append(f'<path d="M {cx:.0f} {cy-5:.0f} L {cx+5:.0f} {cy:.0f} L {cx:.0f} {cy+5:.0f} '
                   f'L {cx-5:.0f} {cy:.0f} Z" fill="#d09a53" fill-opacity="0.9"></path>')
    lt, lv = pts[-1]
    svg.append(f'<circle cx={x(lt if timed else len(pts)-1):.0f} cy={y(lv):.0f} r=3 fill="#315f9e"></circle>')
    tmarks = 0
    if timed and test_events and t0:
        seen_x: dict[int, int] = {}
        for te in test_events:
            ts = te.get("ts")
            if not isinstance(ts, str):
                continue
            try:
                from datetime import datetime as _dt
                tsec = _dt.fromisoformat(ts.replace("Z", "+00:00")).timestamp() - t0
            except ValueError:
                continue
            tsec = max(0.0, min(tsec, xmax))
            hue = {"pass": "#2f7d54", "fail": "#b3543f"}.get(te.get("result"), "#9aa3ad")
            xi = round(x(tsec))
            k = seen_x.get(xi, 0)  # запуски в один момент — микро-разъезд, чтобы не слипались
            seen_x[xi] = k + 1
            svg.append(f'<line x1={xi + k * 3} y1={MT+PH+4} x2={xi + k * 3} y2={MT+PH+12} '
                       f'stroke="{hue}" stroke-width="2.5"></line>')
            tmarks += 1
    # лимит рисуем последним — поверх прямоугольников инструментов
    svg.append(f'<line x1={ML} y1={y(ctx_limit):.0f} x2={ML+PW} y2={y(ctx_limit):.0f} '
               f'stroke="#aab3bf" stroke-dasharray="6,4"></line>')
    lbl = f"лимит {fmt_k(ctx_limit)}"
    lw = len(lbl) * 6.6 + 10
    svg.append(f'<rect x={ML+PW-lw:.0f} y={y(ctx_limit)-16:.0f} width={lw:.0f} height=15 '
               f'fill="#fafbfc"></rect>')
    svg.append(f'<text x={ML+PW} y={y(ctx_limit)-5:.0f} text-anchor=end font-size=11.5 '
               f'font-style=italic fill=#9aa3ad>{lbl}</text>')
    if timed:
        cap = f"время от начала сессии ({len(pts)} вызовов API)"
    else:
        cap = f"вызовы API ({len(pts)})"
    svg.append(f'<text x={ML+PW/2:.0f} y={H-8} text-anchor=middle font-size=12.5 fill=#66707f>{cap}</text>')
    svg.append(f'<text x=14 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 14 {MT+PH/2:.0f})">контекст, токены</text>')
    note = "Контекст — полный промпт вызова (вход + кэш-чтение + кэш-запись). " \
           "Резкое падение линии — автокомпакт или правка истории. " \
           '<span style="color:#d09a53">◆</span> — кэш-промах: контекст цел, ' \
           "но перезалит в кэш целиком (TTL кэша истёк за долгой генерацией)."
    if provider == "gigachat":
        note += (" У GigaChat падения без компакций — артефакт учёта провайдера: usage отдаёт "
                 "только некэшированную часть промпта, кэшированный префикс не показывает.")
    if tmarks:
        note += (" Полосы под осью — запуски тестов: "
                 '<span style="color:#2f7d54">зелёная прошёл</span>, '
                 '<span style="color:#b3543f">красная упал</span>, серая — вывод не разобран.')
    return ('<svg width="%d" height="%d" viewBox="0 0 %d %d">%s</svg>'
            '<p class=sub>%s</p>'
            % (W, H, W, H, "".join(svg), note))


TOOL_HUES = ["#315f9e", "#2f7d54", "#d09a53", "#b3543f", "#6b5b9e", "#3f8f9e"]


def donut_card(title: str, items: list[tuple[str, int]], center_word: str,
               rest_note: str = "", name_w: int = 58, lead: str = "") -> str:
    """Пончик с легендой и тултипами на сегментах (data-tip + DONUT_TIP_JS)."""
    total = sum(n for _, n in items)
    R, SW, CX, CY = 70, 32, 110, 110
    C = 2 * math.pi * R
    segs, cum, legend = [], 0.0, []
    for i, (name, n) in enumerate(items):
        is_rest = name == "остальное"
        hue = "#9aa3ad" if is_rest else TOOL_HUES[i % len(TOOL_HUES)]
        seg = n / total * C
        pct = round(n / total * 100)
        tip = f"<b>{html.escape(name)}</b> — {n} из {total} ({pct}%)"
        segs.append(f'<circle class="seg" data-tip="{tip}" r={R} cx={CX} cy={CY} fill="none" '
                    f'stroke="{hue}" stroke-width={SW} stroke-dasharray="{seg:.1f} {C - seg:.1f}" '
                    f'stroke-dashoffset="{-cum:.1f}" transform="rotate(-90 {CX} {CY})"></circle>')
        cum += seg
        label = name if is_rest else f"<code>{name}</code>"
        legend.append(f'<div style="display:flex;align-items:center;gap:9px;margin:5px 0;font-size:14px">'
                      f'<span style="width:14px;height:14px;border-radius:4px;background:{hue};flex:none"></span>'
                      f'<span style="width:{name_w}px">{label}</span>'
                      f'<b>{n}</b><span class=sub>{pct}%</span></div>')
    segs.append(f'<text x={CX} y={CY - 2} text-anchor=middle font-size=30 font-weight=bold '
                f'fill="#1d2433">{total}</text>'
                f'<text x={CX} y={CY + 20} text-anchor=middle font-size=12 fill="#66707f">{center_word}</text>')
    return (f'<div style="flex:1 1 400px;min-width:360px">'
            f"<h3 style=\"margin-top:12px\">{title}</h3>"
            + (f'<p class=sub>{lead}</p>' if lead else "")
            + f'<div class="donut" style="display:flex;gap:26px;align-items:center;flex-wrap:wrap;'
            f'position:relative;margin:10px 0">'
            f'<svg width="{CY * 2}" height="{CX * 2}" viewBox="0 0 {CX * 2} {CX * 2}" '
            f'style="max-width:250px">{"".join(segs)}</svg>'
            f'<div>{"".join(legend)}</div><div class="tip"></div></div>'
            + rest_note + "</div>")


def donuts_row(st: dict, events: list[dict]) -> str:
    """Обе круговые в один ряд: tool-вызовы и утилиты bash."""
    tools = sorted((st.get("tool_calls_by_name") or {}).items(), key=lambda kv: -kv[1])
    utils = bash_utils(events)
    if not tools and not utils:
        return ""
    left = (donut_card("Tool-вызовы по инструментам", tools, "tool-вызовов",
                       lead="Обращения агента к инструментам pi: чтение файлов, "
                            "правки, запись, шелл.", name_w=58)
            if tools else "")
    right = ""
    if utils:
        top, rest = utils[:8], utils[8:]
        items = top + ([("остальное", sum(n for _, n in rest))] if rest else [])
        bash_n = (st.get("tool_calls_by_name") or {}).get("bash", 0)
        rest_note = ('<p class=sub>«Остальное» — вызовы утилит вне топ-8: '
                     + ", ".join(f"<code>{n}</code>" for n, _ in rest) + ".</p>" if rest else "")
        right = donut_card(
            "Утилиты в bash", items, "вызовов", rest_note, name_w=80,
            lead=f"Запуски программ внутри {bash_n} вызовов инструмента bash: один "
                 "вызов — обычно цепочка команд через ; / && / |, считаются первые слова "
                 "каждого сегмента.")
    return (f'<div style="display:flex;gap:40px;flex-wrap:wrap;align-items:flex-start">'
            f"{left}{right}</div>")


def bash_utils(events: list[dict]) -> list[tuple[str, int]]:
    """Утилиты, которые агент запускал в bash: первые слова сегментов команд
    (составные команды через ;/&&/| считаются по частям; чистая навигация
    и состояние шелла — cd, export и т.п. — не считаются работой)."""
    noise = {"cd", "pushd", "popd", "export", "source", "set", "unset", ":",
             "true", "false", "exit", "wait", "alias",
             # ключевые слова шелла/SQL/PHP, прилипшие из многострочных команд
             "do", "done", "if", "then", "fi", "else", "elif", "for", "while",
             "case", "esac", "in", "function", "require", "include", "end",
             "select", "declare", "local", "return",
             "foreach", "const", "new", "class", "use", "var", "public",
             "private", "protected", "static", "string", "int", "bool", "float",
             "array", "null", "and", "or", "xor", "isset", "instanceof", "as",
             "fn", "match", "enum", "extends", "implements", "throw", "try",
             "catch", "finally", "insert", "update", "delete", "create", "values",
             "where", "from", "into"}
    cnt: dict[str, int] = {}
    for e in events:
        if e.get("type") != "message":
            continue
        for c in (e.get("message") or {}).get("content") or []:
            if not isinstance(c, dict) or c.get("type") != "toolCall" or c.get("name") != "bash":
                continue
            cmd = str((c.get("arguments") or {}).get("command")
                      or (c.get("arguments") or {}).get("content") or "")
            # тела heredoc-блоков — это код, а не команды шелла; кавычки — аргументы
            # (альтернации grep, php -r '…'), из них утилиты тоже не извлекаем
            cmd = re.sub(r"<<-?\s*['\"]?(\w+)['\"]?\b.*?^\1\b", "", cmd,
                         flags=re.M | re.S)
            cmd = re.sub(r"'[^']*'", " ", cmd)
            cmd = re.sub(r'"[^"]*"', " ", cmd)
            for seg in re.split(r";|&&|\|\||\|", cmd):
                tok = seg.strip().split()
                while tok and re.match(r"^[A-Za-z_][A-Za-z0-9_]*=", tok[0]):
                    tok = tok[1:]  # присваивания FOO=bar перед командой
                if not tok or tok[0].split("/")[-1] in noise:
                    continue
                w = tok[0].split("/")[-1]  # /usr/bin/php -> php
                if w.isupper() and len(w) > 1:
                    continue  # SQL-ключевые слова CAPSOM из psql-сценариев
                if re.fullmatch(r"[a-zA-Z][\w.-]*", w):
                    cnt[w] = cnt.get(w, 0) + 1
    return sorted(cnt.items(), key=lambda kv: -kv[1])


def debug_prints_in_code(run_dir: Path) -> tuple[int, int]:
    """Реальные вызовы печатной отладки в финальном коде решения (не в истории
    правок): var_dump / dd / dump по границам слов, solution/**/*.php."""
    rx = re.compile(r"\b(?:var_dump|dd|dump)\s*\(")
    n, files = 0, set()
    sol = run_dir / "solution"
    for p in sol.rglob("*.php") if sol.is_dir() else []:
        try:
            hits = rx.findall(p.read_text(encoding="utf-8", errors="ignore"))
        except OSError:
            continue
        if hits:
            n += len(hits)
            files.add(str(p.relative_to(sol)))
    return n, len(files)


def session_html(events: list[dict], st: dict, ctx_limit: int, run_dir: Path) -> str:
    u = st["usage"]
    sc = st["self_checks"]
    comp = st["context_management"]
    comp_n = comp.get("compaction", 0) + comp.get("context_edit", 0)
    try:
        prov = json.loads((run_dir / "run.json").read_text(encoding="utf-8")).get("provider", "ollama")
    except (OSError, json.JSONDecodeError):
        prov = "ollama"
    # бары самопроверки: инструменты из require-dev скелета — всегда
    # (пустой бар = не запускал), плюс всё, что агент запускал сверх них
    given_keys = ("phpunit", "phpcs", "psalm")
    extra = [k for k, v in sc.items()
             if isinstance(v, int) and v and k not in ("debug_prints_in_code",) and k not in given_keys]
    checks = [(k, sc.get(k, 0)) for k in (*given_keys, *extra)]
    checks.sort(key=lambda kv: -kv[1])
    cmax = max([v for _, v in checks] + [1])
    bars = "".join(
        f'<div style="display:flex;align-items:center;gap:10px;margin:5px 0">'
        f'<div style="width:190px;font-size:13.5px;color:#1d2433">{NAME_CHECKS.get(k, k)}</div>'
        f'<div style="position:relative;background:#eef0f4;border-radius:6px;height:20px;flex:1;max-width:420px">'
        + (f'<div style="position:absolute;left:0;top:3px;bottom:3px;width:{max(2, v / cmax * 100):.0f}%;background:#2f7d54;opacity:.75;border-radius:5px"></div>'
           if v else "")
        + f'<span style="position:absolute;left:7px;top:0;font-size:12px;line-height:20px;font-weight:600;color:{"#1d2433" if v else "#9aa3ad"}">{v}</span>'
        "</div></div>"
        for k, v in checks)
    pct = round(u["cache_read"] / u["total"] * 100) if u["total"] else 0
    cards = (f'<div class="card"><b>{u["api_calls"]}</b><span>вызовов API</span></div>'
             f'<div class="card"><b>{fmt_k(u["total"])}</b><span>токенов всего (вход {fmt_k(u["input"])}, выход {fmt_k(u["output"])}, кэш-чтение {fmt_k(u["cache_read"])} — {pct}%)</span></div>'
             f'<div class="card"><b>{fmt_k(u["context_peak"])}</b><span>пик контекста из {fmt_k(ctx_limit)}</span></div>'
             f'<div class="card"><b>{comp_n}</b><span>автокомпактов и правок истории '
             f'(компактов {comp.get("compaction", 0)}, правок {comp.get("context_edit", 0)})</span></div>'
             f'<div class="card"><b>{st["tool_calls_total"]}</b><span>tool-вызовов ({st["bash_total"]} bash)</span></div>'
             f'<div class="card"><b>{fmt_dur(u["generation_time_s"])}</b><span>чистой генерации</span></div>')
    tests = st.get("test_results") or {}
    n_runs = sum(v for k, v in tests.items() if isinstance(v, int))
    tail = ""
    if n_runs:
        tail = (f"Тесты: {n_runs} запусков — прошли {tests.get('pass', 0)}, "
                f"упали {tests.get('fail', 0)}"
                + (f", вывод не разобран {tests.get('?', 0)}" if tests.get("?") else "")
                + ". ")
    dbg, dbg_files = debug_prints_in_code(run_dir)
    tail += (f"Вызовов печатной отладки (var_dump / dump / dd) в финальном коде: {dbg}"
             + (f" в {dbg_files} файлах — остался отладочный вывод." if dbg else ".")
             + f" Уникальных файлов создано или отредактировано: {st.get('files_distinct', '?')}.")
    seq = sc.get("test_outcomes_sequence") or []
    if seq:
        sym = {"pass": "✓ прошёл", "fail": "✗ упал", "?": "? не разобран"}
        tail += " Хронология запусков тестов: " + " → ".join(sym.get(s, s) for s in seq) + "."
    tool_colors = {name: TOOL_HUES[i % len(TOOL_HUES)]
                   for i, (name, _) in enumerate(sorted((st.get("tool_calls_by_name") or {}).items(),
                                                        key=lambda kv: -kv[1]))}
    return ('<h2>Выполнение · сессия pi</h2>'
            '<p class=lead>Ход работы агента-кандидата: рост контекста по вызовам API, '
            "автокомпакты, чем самопроверялся.</p>"
            '<div class="cards">' + cards + "</div>"
            + solution_tree(run_dir)
            + donuts_row(st, events)
            + (lambda tc: (
                ('<p class=sub>Наложены периоды работы tool-вызовов — прямоугольник от '
                 "вызова до результата, ширина = длительность, цвет = инструмент: "
                 + ", ".join(f'<span style="color:{h}">■ {html.escape(n)}</span>' for n, h in tc.items())
                 + ".</p>") if tc else ""
            )
            + f'<div class="svgbox">{context_chart(events, ctx_limit, st.get("test_events"), tc, provider=prov)}</div>'
            )(tool_colors)
            + '<h3>Самопроверка моделью</h3>'
            + '<p class=lead>Сколько раз за прогон агент сам проверял свою работу, '
            "по инструментам. Кандидату в require-dev даны тесты phpunit, phpcs и psalm "
            "(пустой бар — не запускал); phpstan и phpmd — только внешние ярусы.</p>"
            + bars
            + '<h3>Косяки и переделки</h3>'
            + f'<div class="cards">'
            + f'<div class="card"><b>{st.get("bash_first_try_pct", "—")}%</b>'
            f'<span>команд bash прошли с первого раза (без ошибок в выводе)</span></div>'
            + f'<div class="card {"r" if st.get("bash_errors") else ""}"><b>{st.get("bash_errors", "—")}</b>'
            f'<span>вызовов завершились ошибкой (Fatal error, SQLSTATE, not found…)</span></div>'
            + f'<div class="card"><b>{st.get("bash_retries_after_error", "—")}</b>'
            f'<span>раз после ошибки агент правил и перезапускал команду</span></div>'
            + f'<div class="card"><b>{st.get("test_fail_to_pass", 0)} из {st.get("test_fails_total", 0)}</b>'
            f'<span>падений тестов доведены до прохода (агент правил код, тесты потом прошли)</span></div>'
            + "</div>"
            + '<p class=sub>Показатель качества: чем меньше ошибок и переделок, тем ближе '
            "прогон к «ваншоту» и тем быстрее получен результат. Перекликается с показателем "
            "галлюцинаций из интервью.</p>"
            + f'<p class=sub>{tail}</p>'
            + art_links(run_dir, "pi-session", "pi-run.log", "diff.patch",
                        "session-profile.md", "scorecard.md", "summary.md"))


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

DONUT_TIP_JS = """
<script>
document.querySelectorAll('.donut').forEach(function (d) {
  var tip = d.querySelector('.tip');
  if (!tip) return;
  d.querySelectorAll('circle.seg').forEach(function (s) {
    s.addEventListener('mousemove', function (ev) {
      var r = d.getBoundingClientRect();
      tip.innerHTML = s.getAttribute('data-tip');
      tip.style.display = 'block';
      tip.style.left = Math.min(ev.clientX - r.left + 14, r.width - 60) + 'px';
      tip.style.top = (ev.clientY - r.top - 12) + 'px';
    });
    s.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
  });
});
</script>
"""

NAME_CHECKS = {
    "phpunit": "тесты (phpunit / make test)",
    "phpcs": "стиль (phpcs / phpcbf)",
    "psalm": "psalm",
    "phpstan": "phpstan (в скелете не давался)",
    "phpmd": "phpmd (в скелете не давался)",
    "lint": "синтаксис (php -l)",
    "db_checks": "проверки БД (psql)",
    "acceptance_commands": "приёмка (bin/console app:*)",
    "build": "сборка (composer)",
}


# --- прогон и окружение -------------------------------------------------------
def deepseek_version(model: str, run: dict) -> str:
    """Алиас deepseek-flash указывает на актуальный снапшот: фиксируем,
    какой был жив на дату прогона (обновление до -1017 — 2026-10-17)."""
    if model != "deepseek-flash":
        return ""
    date = str(run.get("started_at") or "")[:10]
    if not date:
        return " · снапшот неизвестен (нет даты в run.json)"
    return (" · DeepSeek-V4.1-Flash-1017" if date >= "2026-10-17"
            else " · DeepSeek-V4.1-Flash (снапшот до обновления 2026-10-17)")


def thinking_level(run: dict) -> tuple[str, str]:
    """Уровень рассуждения модели: значение, реально ушедшее в запрос
    (аргумент pi маппится на шкалу модели в runner_pi.thinking_map_for)."""
    thinking = run.get("thinking") or "?"
    scale = run.get("thinking_scale") or []
    if not scale:
        # облачные провайдеры: шкалы нет, уровень pi и есть фактический effort
        return (thinking if thinking != "?" else "—",
                "; thinking моделью не управляется" if thinking == "?" else "")
    from runner_pi import thinking_map_for
    mapped = thinking_map_for(scale)[0].get(thinking)
    levels = [str(v) for v in scale if isinstance(v, str)]
    # логическая шкала — только если строковых уровней нет вовсе (вкл/выкл);
    # False среди уровней — просто положение «выкл» в обычной шкале
    has_bool = not levels and any(isinstance(v, bool) for v in scale)
    eff = "включён" if has_bool else (mapped or thinking)
    return eff, ("; шкала логическая (вкл/выкл)" if has_bool else "")


def art_links(run_dir: Path, *names: str) -> str:
    """Строка ссылок на артефакты раздела (лежат рядом с report.html)."""
    links = [f'<a href="{n}">{n}</a>' for n in names if (run_dir / n).exists()]
    return ('<p class=sub style="margin:6px 0 0">Артефакты: ' + " · ".join(links) + "</p>"
            ) if links else ""


def env_html(run: dict, model: str, outcome: str = "", usage: dict | None = None,
             run_dir: Path | None = None) -> str:
    """Раздел «Прогон и окружение»: итог прогона + как запускался агент."""
    thinking = run.get("thinking") or "?"
    eff, scale_note = thinking_level(run)
    ctx = run.get("context") or 102400
    mt = run.get("max_tokens") or 8192
    mts = f"{mt:,}".replace(",", " ")
    sha = run.get("task_sha256") or "—"
    dl, sf = run.get("diff_lines"), run.get("solution_files")
    cj = json.loads((BILLING / "skeleton" / "composer.json").read_text(encoding="utf-8"))
    dev = ", ".join(f"<code>{k} {v}</code>" for k, v in sorted((cj.get("require-dev") or {}).items()))
    php = (cj.get("require") or {}).get("php", "≥8.4")
    cmd = html.escape(
        "PI_CODING_AGENT_DIR=<tmp>/pi-home PI_OFFLINE=1 \\\n"
        "pi -p --session-dir <прогон>/pi-session \\\n"
        f"    --model {model} --thinking {thinking} \\\n"
        '    "Прочитай TASK.md в корне репозитория и выполни задание."')
    tok_cards = ""
    if usage and usage.get("total"):
        pct = round(usage["cache_read"] / usage["total"] * 100)
        tok_cards = (f'<div class="card"><b>{fmt_k(usage["total"])}</b>'
                     f"<span>токенов, кэш {pct}%</span></div>")
    cards = (
        f'<div class="card"><b>{fmt_dur(run.get("duration_s"))}</b><span>длительность прогона</span></div>'
        + tok_cards
        + (f'<div class="card"><b>{dl}</b><span>строк</span></div>'
           f'<div class="card"><b>{sf}</b><span>файлов в решении (без vendor)</span></div>'
           if dl is not None else ""))
    if run.get("provider") == "openrouter" and usage:
        t = OPENROUTER_TARIFFS.get(model)
        if t:
            pin, pout, pcache = t
            cost = (usage["input"] * pin + usage["output"] * pout
                    + usage["cache_read"] * pcache)
            cards += (f'<div class="card"><b>${cost:.4f}</b>'
                      "<span>стоимость API по тарифу OpenRouter</span></div>")
    return (
        "<h2>Прогон и окружение</h2>"
        '<p class=lead>Агент-кандидат работает в изолированном pi на временной копии '
        "скелета задания; проверяющие ярусы подключаются после выполнения и каждый "
        "работает на своей копии решения.</p>"
        '<div class="cards" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr))">'
        + outcome + "</div>"
        '<div class="cards" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr))">'
        + cards + "</div>"
        "<h3>Как запускался агент</h3>"
        f"<pre>{cmd}</pre>"
        '<ul class="envlist">'
        "<li><code>PI_CODING_AGENT_DIR</code> — временный pi-home: <code>models.json</code> "
        "собран на лету под одну модель; адрес сервера — env <code>OLLAMA_BASE_URL</code> "
        "(в git не попадает).</li>"
        "<li><code>PI_OFFLINE=1</code> — служебный режим pi: он сам не ходит в сеть "
        "(проверка версии, каталог моделей, телеметрия). Командам агента сеть "
        "при этом доступна.</li>"
        "<li>агент работает в одноразовой копии <code>skeleton/</code>: каркас-эталон "
        "не загрязняется прогоном; <code>.env</code> раннер кладёт из "
        "<code>.env.example</code>, чтобы Symfony был рабочим с первой секунды. "
        "После прогона копия удаляется — остаются только артефакты "
        "(<code>solution/</code>, <code>diff.patch</code>, сессия).</li>"
        f"<li>версия задания — <code>TASK.md</code>, sha256 <code>{html.escape(str(sha))}</code> "
        "— одинакова во всех прогонах.</li>"
        f"<li>лимиты: таймаут {fmt_dur(run.get('timeout_s'))} на весь прогон; ответ "
        f"модели в одном вызове — не больше {mts} токенов, считая размышление "
        "(поднято со стандартных 8192: длинное размышление иначе съедает бюджет "
        "ещё до ответа).</li>"
        "</ul>"
        "<h3>Что дано кандидату</h3>"
        f"<p>Скелет <code>skeleton/</code> — рабочий Symfony-каркас (PHP {html.escape(php)}, "
        "Doctrine, bcmath) с <code>TASK.md</code> и <code>AGENTS.md</code>. Инструменты "
        f"самопроверки в require-dev: {dev} — только это кандидату доступно; "
        "phpstan и phpmd в скелет не кладём (phpmd слишком жёсток для самопроверки, "
        "phpstan дублирует psalm) — меряем ими только внешние ярусы. Само задание — "
        "<code>TASK.md</code>; агенту оно передаётся одной фразой-промптом "
        "(в блоке выше), без уточнений и наводок.</p>"
        + (art_links(run_dir, "run.json") if run_dir else ""))


# --- вердикты: ярус 3 и ярус 4 -------------------------------------------------
def _sidecar(run_dir: Path, name: str) -> dict:
    p = run_dir / name
    return json.loads(p.read_text(encoding="utf-8")) if p.is_file() else {}


def _axis_card(v, name: str) -> str:
    vs = f"{v:g} из 4" if v is not None else "—"
    cls = "g" if v is not None and v >= 3 else ("r" if v is not None and v < 2.5 else "")
    return f'<div class="card {cls}"><b>{vs}</b><span>{name}</span></div>'


def tier3_html(run_dir: Path) -> str:
    t3 = _sidecar(run_dir, "tier3.json")
    p = run_dir / "review-zcode.md"
    if not t3 and not p.is_file():
        return ""
    cards = (_axis_card(t3.get("axis_a"), "ось A «код и архитектура»")
             + _axis_card(t3.get("axis_b"), "ось B «процесс и коммуникация»")
             + f'<div class="card"><b>{fmt_dur(t3.get("duration_s"))}</b><span>чистое время ревью</span></div>'
             + f'<div class="card"><b>{fmt_k(t3.get("tokens_total") or 0)}</b>'
             f'<span>токенов (вход {fmt_k(t3.get("tokens_in") or 0)}, ${t3.get("cost_usd", 0):.4f})</span></div>')
    from judge_tier3 import build_facts  # той же функцией собирается боевой промпт ревьюера
    facts = html.escape("\n".join(build_facts(run_dir)))
    note = ""
    if p.is_file():
        txt = p.read_text(encoding="utf-8")
        nm = re.search(r"## Обоснование\s*\n+(.+?)\n<details>", txt, re.S)
        note = nm.group(1).strip() if nm else ""
    quote = (f'<h3>Обоснование</h3><div class="quote"><div class=who>ревьюер яруса 3</div>'
             f"{html.escape(note)}</div>") if note else ""
    return ("<h2>Ярус 3 · ревью: изолированная pi-сессия glm-5.3-flash</h2>"
            '<p class=lead>Модельная проверка решения: отдельная pi-сессия '
            "(<code>zai/glm-5.3-flash</code>, thinking low) оценивает по рубрике обе оси. "
            "На вход — TASK.md, дифф (первые 2600 строк) и блок ФАКТЫ: детерминированные "
            "итоги ярусов 1–2.</p>"
            '<div class="cards">' + cards + "</div>"
            "<h3>Вход ревьюера: блок ФАКТЫ</h3>"
            f"<pre>{facts}</pre>" + quote
            + art_links(run_dir, "review-zcode.md", "tier3.json"))


def tier4_html(run_dir: Path) -> str:
    t4 = _sidecar(run_dir, "tier4.json")
    t3 = _sidecar(run_dir, "tier3.json")
    p = run_dir / "judge-glm.md"
    if not t4 and not p.is_file():
        return ""
    a = t4.get("axis_a")
    delta = t3.get("axis_a") - a if a is not None and t3.get("axis_a") is not None else None
    cards = (_axis_card(a, "ось A «код и архитектура», вслепую")
             + (f'<div class="card"><b>{delta:+g}</b><span>расхождение осей A: ярус 3 − ярус 4</span></div>'
                if delta is not None else "")
             + f'<div class="card"><b>{fmt_dur(t4.get("duration_s"))}</b><span>чистое время судьи</span></div>'
             + f'<div class="card"><b>{fmt_k(t4.get("tokens_total") or 0)}</b>'
             f'<span>токенов (вход {fmt_k(t4.get("tokens_in") or 0)}, ${t4.get("cost_usd", 0):.4f})</span></div>')
    note, quotes = "", []
    if p.is_file():
        txt = p.read_text(encoding="utf-8")
        nm = re.search(r"^- (?:заметка|пояснение): (.+)$", txt, re.M)
        note = nm.group(1).strip() if nm else ""
        quotes = re.findall(r"^\s+- цитата: `(.+?)`\s*$", txt, re.M)
    quote = (f'<div class="quote"><div class=who>заметка судьи</div>{html.escape(note)}</div>'
             if note else "")
    qlist = (("<p class=sub><b>Цитаты-доказательства из диффа:</b></p><ul class=qlist>"
              + "".join(f"<li><code>{html.escape(q)}</code></li>" for q in quotes) + "</ul>")
             if quotes else "")
    return ("<h2>Ярус 4 · судья: слепое второе мнение</h2>"
            '<p class=lead>Та же модель судьи (<code>zai/glm-5.3-flash</code>, thinking low), '
            "но вслепую: только TASK.md и дифф, без фактов чекера и метрик. Зачем: ревьюер "
            "яруса 3 видит числа прохождений и может опереться на них сильнее, чем они того "
            "стоят (или довериться плохой метрике) — слепой судьи ловит это смещение. "
            "Расхождение осей A в пределах ±1 — согласие; больше — слепая зона, смотреть "
            "вручную.</p>"
            '<div class="cards">' + cards + "</div>" + quote + qlist
            + art_links(run_dir, "judge-glm.md", "tier4.json"))


def audit_html(events: list[dict], cwd: str | None, run_dir: Path) -> str:
    """Аудит безопасности: детерминированные сигналы (сеть, пути вне cwd)
    + вердикт судьи-аудитора из audit.json."""
    sec = security_audit(events, cwd)
    signals = sec.get("signals") or []
    aud = _sidecar(run_dir, "audit.json")
    overall = aud.get("overall")
    hue = {"чисто": "#2f7d54", "пограничное": "#8a6d1a", "нарушение": "#b3543f"}.get(overall, "#66707f")
    if signals:
        rows = "".join(
            f"<tr><td style=\"white-space:nowrap\">{s['kind']}</td>"
            f"<td><code>{html.escape(s.get('detail') or '')}</code></td>"
            f"<td class=sub>{html.escape(s['cmd'][:110])}</td></tr>"
            for s in signals[:8])
        body = ('<table style="width:auto"><thead><tr><th>тип</th><th>объект</th><th>команда</th></tr></thead>'
                f"<tbody>{rows}</tbody></table>"
                + (f'<p class=sub>…всего сигналов {len(signals)}; полный список — '
                   f"<code>security.json</code>, построчный разбор — <code>audit-verdict.md</code>.</p>"
                   if len(signals) > 8 else ""))
    else:
        body = '<p class=sub>Сеть не использовалась, за пределы рабочего каталога агент не выходил.</p>'
    verdict = ""
    if aud:
        verdict = (f'<p><b style="color:{hue}">Вердикт аудитора: {html.escape(str(overall))}</b>'
                   f"<span class=sub> — {html.escape(aud.get('note', ''))}</span></p>")
    return ('<h2>Аудит безопасности</h2>'
            '<p class=lead>Наблюдение за поведением агента: детектор помечает обращение '
            "к сети и пути вне рабочего каталога прогона. Сигнал — не вердикт: каждый "
            "разбирает судья-аудитор (изолированная pi-сессия glm-5.3-flash, "
            "<code>checker/judge_audit.py</code>). Полный список сигналов — "
            "<code>security.json</code>, построчный разбор — <code>audit-verdict.md</code> "
            "рядом с отчётом.</p>"
            + body + verdict
            + art_links(run_dir, "security.json", "audit-verdict.md", "audit.json"))


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
    own_metrics = (run_dir / "metrics.json").resolve()
    cohort = []  # только прогоны этой же модели: сравнение постоянства решений
    for m in sorted(model_dir.glob("backend/test-assignment/run-*/metrics.json")):
        if m.resolve() == own_metrics:
            continue
        try:
            cohort.append(json.loads(m.read_text(encoding="utf-8")))
        except Exception:  # noqa: BLE001
            pass

    events, _hdr = load(str(run_dir / "pi-session")) if (run_dir / "pi-session").is_dir() else ([], {})
    st = analyze(events) if events else None

    model = run.get("model", "?")
    thinking = run.get("thinking_effective") or run.get("thinking") or "?"
    ctx = run.get("context") or 102400
    dur = run.get("duration_s")
    mp, bp = check.get("mandatory_passed", 0), check.get("bonus_passed", 0)
    mt, bt = check.get("mandatory_total", 11), check.get("bonus_total", 2)
    total = mp + bp
    cls = "g" if total >= 12 else ("r" if mp < 9 else "")
    cls_b = "g" if bt and bp == bt else ""
    outcome = (
        f'<div class="card {cls}"><b>{mp} из {mt}</b>'
        f"<span>обязательные сценарии</span></div>"
        f'<div class="card {cls_b}"><b>{bp} из {bt}</b>'
        f"<span>бонусные сценарии</span></div>"
        + _axis_card(_sidecar(run_dir, "tier3.json").get("axis_a"), "код и архитектура")
        + _axis_card(_sidecar(run_dir, "tier3.json").get("axis_b"), "процесс и коммуникация")
        + _axis_card(_sidecar(run_dir, "tier4.json").get("axis_a"), "слепое ревью"))
    lv, _ = thinking_level(run)
    us = st["usage"] if st else {}
    head = (
        f"<h1>{model_dir.name} · {run_dir.name}</h1>"
        f'<p class=lead>Тег <code>{model}</code>{deepseek_version(model, run)} · '
        f'рассуждение {html.escape(str(lv))} · '
        f"окно {fmt_k(ctx)} · начало {str(run.get('started_at', ''))[:16].replace('T', ' ')}</p>")

    matrix = dep_matrix(collector)
    tier2 = ('<h2>Ярус 2 · метрики кода</h2>'
             '<p class=lead>Насколько хорошо написан код: предупреждения статанализа '
             "(phpstan, psalm, phpcs) и структурные метрики (сложность, связность). Числа не про "
             "поведение — его проверил чекер, и не про смысл решения — его оценивают судьи "
             "ярусов 3–4.</p>"
             + bars_with_cohort(metrics, cohort)
             + tier2_details_html(run_dir, metrics)
             + '<h3>Классы: размер и связность</h3>'
             '<p class=lead>Каждая точка — один класс решения. Горизонталь — LOC, строк '
             "кода. Вертикаль — LCOM4: сколько у класса несвязанных «зон ответственности» "
             "(1 — класс про одно; 2+ — делает несколько дел сразу, кандидат на разнос). "
             "Размер точки — WMC: суммарная сложность всех методов класса. Цвет — CC самого "
             "сложного метода (цикломатическая сложность: сколько в нём путей исполнения, "
             "грубо — ветвлений if/for/case): синий ≤5 — спокойно, оранжевый 6–10 — "
             "тяжеловато, красный &gt;10 — метод-спагетти.</p>"
             + ast_scatter(collector)
             + (('<h3>Матрица зависимостей классов</h3>' + matrix) if matrix else "")
             + coupling_table(collector, run_dir)
             + cycles_block(collector, run_dir)
             + ast_tables(collector, run_dir)
             + art_links(run_dir, "metrics.json", "metrics-raw"))

    env = env_html(run, model, outcome, usage=us, run_dir=run_dir)
    sess = session_html(events, st, ctx, run_dir) if st else '<div class=note>pi-сессия не найдена</div>'
    audit = audit_html(events, _hdr.get("cwd"), run_dir)
    t3s = tier3_html(run_dir)
    t4s = tier4_html(run_dir)

    page = f"""<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{model_dir.name} · {run_dir.name} — тестовое задание</title>
<style>{CSS}</style></head><body><div class=wrap>
{head}
{env}
{sess}
{audit}
{checker_html(check, run_dir)}
{tier2}
{t3s}
{t4s}
<footer>Отчёт идёт по шагам прогона: подготовка окружения → выполнение → аудит → ярусы 1–4.
Показатели собраны из артефактов: run.json, diff.patch, check.json, metrics.json,
metrics-raw/collector.json, pi-session/*.jsonl (парсер — analyze_session.py),
security.json, audit.json, tier3.json, tier4.json.
Генератор: <code>test-assignment/backend/billing/build_run_report.py</code>.</footer>
</div></body></html>"""
    page = page.replace("</body></html>", TOOLTIP_JS + DONUT_TIP_JS + "</body></html>")
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

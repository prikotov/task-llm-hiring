#!/usr/bin/env python3
"""Сборка отчёта карты знаний PHP: HTML-карта + summary.md.

Режимы:
    --run-dir models/<модель>/knowledge-map/run-01
        Карта одного прогона: зелёный — верно, красный — неверно,
        серый — ответ не разобран. Пишет report.html и summary.md
        в папку прогона.

    --model-dir models/<модель>/knowledge-map
        Тепловая карта по всем прогонам (этап «5 прогонов»): клетка —
        доля верных ответов, маркер — нестабильные (флипующие) клетки.
        Пишет compare.html и summary.md в папку модели.

Серверы, адреса и ключи в отчёт не попадают (они в session.json).
"""

import argparse
import html
import re
from collections import defaultdict
import json
import statistics
import sys
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent

COLOR_OK = "#3aa25c"     # верно
COLOR_BAD = "#d75f5f"    # неверно
COLOR_NA = "#a9b0ba"     # не разобрано
COLOR_MID = "#e8b64c"    # тепловая середина


def esc(s) -> str:
    return html.escape(str(s), quote=True)


def cell_text(q: dict) -> str:
    """Текст клетки: утверждение (verify) или вопрос (recall)."""
    return q["text"] if "text" in q else q["prompt"]


def load_questions(path: Path) -> tuple[int | None, list]:
    data = json.loads(path.read_text(encoding="utf-8"))
    if isinstance(data, dict):
        return data.get("version"), data["questions"]
    return None, data


def load_run(run_dir: Path) -> dict:
    session = json.loads((run_dir / "session.json").read_text(encoding="utf-8"))
    by_id = {t["id"]: t for t in session.get("turns", [])}
    return session, by_id


def is_correct(turn: dict) -> bool:
    if "is_correct" in turn:
        return bool(turn["is_correct"])
    a = turn.get("answer")
    return a is not None and a == turn.get("correct_answer")


def given_text(turn: dict) -> str:
    """Ответ модели в читаемом виде: да/нет для verify-клеток,
    значение для recall-клеток."""
    a = turn.get("answer")
    if a is None:
        return "— (нет ответа)"
    if isinstance(a, bool):
        return "да" if a else "нет"
    return norm_display(str(a))


def norm_display(s: str | None) -> str:
    """Единый вид значения: нижний регистр, без кавычек, сжатые пробелы."""
    if s is None:
        return "—"
    s = re.sub(r"\s+", " ", s.strip().lower())
    return s.strip("«»\"' .") or (s or "—")


def correct_text(q: dict, turn: dict | None) -> str:
    """Эталон клетки: для recall — нормализованное значение, для да/нет — да/нет."""
    if q.get("type") == "answer":
        return norm_display(turn.get("correct_answer")) if turn else "—"
    return "да" if q["answer"] else "нет"


def cell_class(turn: dict | None) -> str:
    if turn is None:
        return "na"
    if turn.get("answer") is None:
        return "na"
    return "ok" if is_correct(turn) else "bad"


def pct(n: int, d: int) -> float:
    return round(100 * n / d, 1) if d else 0.0


def heat_color(p: float) -> str:
    """Непрерывная шкала красный → янтарный → зелёный по доле верных."""
    a, b = (COLOR_BAD, COLOR_MID) if p < 0.5 else (COLOR_MID, COLOR_OK)
    t = (p * 2) % 1 if p < 0.5 else (p - 0.5) * 2
    rgb = []
    for i, name in enumerate(("r", "g", "b")):
        x = int(a[1 + 2 * i:3 + 2 * i], 16)
        y = int(b[1 + 2 * i:3 + 2 * i], 16)
        rgb.append(round(x + (y - x) * t))
    return "#" + "".join(f"{v:02x}" for v in rgb)


def tooltip(turn: dict | None, q: dict, runs: list[tuple] | None = None) -> str:
    """Всплывающая подсказка клетки: утверждение, ответы, обоснование."""
    parts = [f"<b>{esc(q['id'])} · {esc(q['topic'])} · L{q['level']}</b>",
             esc(cell_text(q))]
    if runs is not None:  # тепловая карта: ответ каждого прогона
        lines = []
        for run_name, ans in runs:
            mark = "— ⬜" if ans is None else ("да ✅" if ans else "нет ❌")
            lines.append(f"{esc(run_name)}: {mark}")
        parts.append("<br>" + "<br>".join(lines))
    elif turn is not None:
        given = given_text(turn)
        if turn.get("p") is not None:
            given += f" (p={turn['p']})"
        parts.append(f"Модель: «{esc(given)}»")
    right = correct_text(q, turn)
    parts.append(f"Верный ответ: <b>{esc(right)}</b>")
    return "<br>".join(parts)


def run_label(session: dict, run_dir: Path) -> str:
    tag = esc(session.get("model", "?"))
    no = session.get("run")
    no = f", run-{no:02d}" if isinstance(no, int) else f", {esc(run_dir.name)}"
    think = session.get("think")
    think_s = f" · think: {esc(think)}" if think and not \
        str(think).startswith("—") else ""
    return tag + no + think_s


def page_css() -> str:
    css = """
    :root { --fg:#1c2130; --mut:#6b7280; --line:#e3e6ea; }
    * { box-sizing: border-box; }
    body { font: 15px/1.5 system-ui, sans-serif; color: var(--fg);
           background: #f6f7f9; margin: 0; padding: 28px 18px 60px; }
    main { max-width: 1120px; margin: 0 auto; }
    h1 { font-size: 24px; margin: 0 0 4px; }
    h2 { font-size: 18px; margin: 34px 0 10px; }
    .proto { color: var(--mut); margin: 0 0 18px; }
    .chips { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0 6px; }
    .chip { background: #fff; border: 1px solid var(--line); border-radius: 9px;
            padding: 7px 12px; font-size: 14px; }
    .chip b { font-size: 16px; }
    .legend { display: flex; gap: 16px; align-items: center; margin: 10px 0 4px;
              font-size: 13.5px; flex-wrap: wrap; }
    .legend .hint { color: var(--mut); font-size: 12.5px; }
    .sw { display: inline-block; width: 14px; height: 14px; border-radius: 4px;
          margin-right: 6px; vertical-align: -2px; }
    .mapwrap { overflow: visible; background: #fff; border: 1px solid var(--line);
               border-radius: 12px; padding: 16px; width: fit-content;
               max-width: 100%; }
    .map { display: grid; gap: 3px;
           grid-template-columns: minmax(190px, 250px) repeat(8, 46px) 84px; }
    .hd { text-align: center; align-self: end; font-weight: 600; font-size: 13px;
          color: var(--mut); padding-bottom: 4px; }
    .rh { align-self: center; font-size: 13.5px; padding: 4px 8px 4px 0; }
    .cell { height: 46px; border-radius: 7px; position: relative; }
    a.cell { display: block; color: inherit; text-decoration: none; }
    tr:target { outline: 2px solid #1c2130; background: #fffbe8; }
    .cell.ok { background: @OK@; }
    .cell.bad { background: @BAD@; }
    .cell.na { background: @NA@; }
    .cell:hover { outline: 2px solid #1c2130; }
    .cell .tip { display: none; position: absolute; bottom: 108%; left: 50%;
                 transform: translateX(-50%); width: 320px; z-index: 20;
                 background: #202634; color: #eef1f6; padding: 10px 12px;
                 border-radius: 9px; font-size: 12px; line-height: 1.45;
                 text-align: left; white-space: normal;
                 box-shadow: 0 6px 18px rgba(0,0,0,.35); }
    .cell:hover .tip { display: block; }
    .cell .num { position: absolute; inset: 0; display: flex; align-items: center;
                 justify-content: center; font-weight: 700; font-size: 13px;
                 color: rgba(20,24,32,.75); }
    .flip { outline: 2px dashed #8a2d2d; outline-offset: -2px; }
    .agg { align-self: center; text-align: center; font-weight: 700;
           font-size: 13px; color: var(--mut); }
    table.data { border-collapse: collapse; background: #fff; width: 100%;
                 border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
    table.data th, table.data td { border-bottom: 1px solid var(--line);
                 padding: 8px 12px; text-align: left; vertical-align: top; }
    table.data th { background: #f0f2f5; font-size: 13px; color: var(--mut); }
    .bar { background: #ececef; height: 12px; border-radius: 6px; width: 100%;
           max-width: 260px; }
    .bar > div { height: 100%; border-radius: 6px; background: var(--fg); }
    .ok-t { color: #22834a; font-weight: 600; }
    .bad-t { color: #b03a3a; font-weight: 600; }
    td.bad-t small { color: var(--fg); font-weight: 400; }
    .na-t { color: var(--mut); }
    td.assert { font-size: 13.5px; }
    code.id { font-size: 12.5px; background: #eef1f4; border-radius: 5px;
              padding: 1px 6px; white-space: nowrap; }
    footer { color: var(--mut); font-size: 12.5px; margin-top: 40px; }
    .raw-details summary { cursor: pointer; color: var(--mut);
                           font-size: 11.5px; user-select: none; }
    .raw-details[open] summary { margin-bottom: 4px; }
    .raw-sample { background: #f4f6f8; border: 1px solid var(--line);
                  border-radius: 6px; padding: 6px 8px; margin: 4px 0 0;
                  font: 11.5px/1.45 ui-monospace, SFMono-Regular, Menlo, monospace;
                  white-space: pre-wrap; word-break: break-word; }
    .map.compact .cell { height: 24px; border-radius: 4px; }
    .map.compact .rh { font-size: 11px; }
    .map.compact .hd { font-size: 10.5px; padding-bottom: 2px; }
    .map.compact .agg { font-size: 10.5px; }
    .map.compact .zonehdr { font-size: 11px; padding: 3px 8px; margin-top: 4px; }
    .map.compact .cell .num { font-size: 9.5px; }
    .seriesrow { display: flex; flex-wrap: wrap; gap: 22px;
                 align-items: flex-start; margin: 8px 0 30px; }
    .series { min-width: 300px; }
    .serhead { font-weight: 600; margin-bottom: 2px; }
    .sermeta { color: var(--mut); font-size: 12.5px; margin-bottom: 8px; }
    .zonehdr { grid-column: 1 / -1; font-weight: 600; font-size: 13px;
               color: var(--fg); background: #eef1f4; border-radius: 6px;
               padding: 5px 10px; margin-top: 6px; }
    .reqhead { color: var(--fg); font-size: 15px; margin: 14px 0 8px; }
    .msg { margin: 0 0 10px; }
    .msg:last-child { margin-bottom: 0; }
    .role { display: inline-block; font-size: 11px; font-weight: 600;
            letter-spacing: .04em; padding: 2px 8px; border-radius: 5px;
            margin-bottom: 4px; background: #eef1f4; color: var(--mut); }
    .prompt-sample { background: #f4f6f8; border: 1px solid var(--line);
                     border-radius: 8px; padding: 10px 12px;
                     font: 12.5px/1.55 ui-monospace, SFMono-Regular, Menlo, monospace;
                     white-space: pre-wrap; margin: 0 0 6px; }
    """
    return (css.replace("@OK@", COLOR_OK)
               .replace("@BAD@", COLOR_BAD)
               .replace("@NA@", COLOR_NA))


def chips_row_single(session: dict, by_id: dict, questions: list) -> str:
    qs = {q["id"]: q for q in questions}
    turns = [by_id[q["id"]] for q in questions if q["id"] in by_id]
    n = len(questions)
    correct = sum(1 for t in turns if is_correct(t))
    parsed = [t for t in turns if t.get("answer") is not None]
    late = sum(1 for t in turns if t.get("parsed") == "late")
    grey = n - len(parsed)
    bool_parsed = [t for t in parsed if isinstance(t.get("answer"), bool)]
    yes_n = sum(1 for t in bool_parsed if t["answer"])
    yes_share = pct(yes_n, len(bool_parsed) or 1)
    strict_n = sum(1 for t in parsed if t.get("strict"))
    total_s = sum(t.get("duration_s") or 0 for t in turns)
    if total_s < 90:
        time_s = f"{total_s:.0f} c"
    else:
        time_s = f"{total_s / 60:.1f} мин"
    totals = session.get("totals") or {
        "prompt_tokens": sum(t.get("prompt_eval_count") or 0 for t in turns),
        "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
        "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0 for t in turns),
    }
    ver = session.get("questionnaire")
    ver_s = f"вопросник v{ver}" if ver else "вопросник"
    # общая оценка: два числа — без новых фич (зона «База») и по всей матрице
    base_q = [q for q in questions if q.get("zone", "base") == "base"]
    base_c = sum(1 for q in base_q
                 if q["id"] in by_id and is_correct(by_id[q["id"]]))
    base_pct = pct(base_c, len(base_q)) if base_q else pct(correct, n)
    score_chip = (f'<div class="chip">Оценка: <b>{base_pct}%</b> '
                  f'(с новыми фичами: <b>{pct(correct, n)}%</b>)</div>')
    zone_stats = []
    for zl, zk in (("База", "base"), ("PHP 8.4–8.5", "current"),
                   ("Будущее", "future")):
        if zk not in {q.get("zone", "base") for q in questions}:
            continue
        zq = [q for q in questions if q.get("zone", "base") == zk]
        zc = sum(1 for q in zq if q["id"] in by_id and is_correct(by_id[q["id"]]))
        zone_stats.append(f"{zl} {pct(zc, len(zq))}%")
    if bool_parsed:
        bias_chip = (f'<div class="chip">Ответов «да»: <b>{yes_share}%</b>\n'
                     '           (баланс вопросника 50/50, базлайн константного '
                     'ответа — 50%)</div>')
    else:
        bias_chip = ('<div class="chip">Формат: <b>recall</b> — эталон входит в '
                     'ответ (нормализация без кавычек/регистра); строгих '
                     f'совпадений: <b>{strict_n} / {len(parsed)}</b></div>')
    # отклонения показываем только когда они есть
    parse_extras = " · ".join(f"{lbl}: {cnt}" for lbl, cnt in
                              (("не по JSON-формату", late), ("без вердикта", grey))
                              if cnt)
    parse_extra_s = f" ({parse_extras})" if parse_extras else ""
    return f"""
    <div class="chips">
      {score_chip}
      <div class="chip">По зонам: <b>{esc(" · ".join(zone_stats))}</b></div>
      <div class="chip">Точность: <b>{correct} / {n}</b></div>
      <div class="chip">Разобрано ответов: <b>{len(parsed)} / {n}</b>{parse_extra_s}</div>
      {bias_chip}
      <div class="chip">Время: <b>{time_s}</b>
           ({statistics.median(t.get('duration_s') or 0 for t in turns):.2f} c на клетку — медиана)</div>
      <div class="chip">Токены: <b>{totals.get('prompt_tokens', 0)} вх /
           {totals.get('output_tokens', 0)} вых</b> (кэш: {totals.get('cached_tokens', 0)})</div>
      <div class="chip">{esc(ver_s)}</div>
    </div>"""


def map_grid_single(by_id: dict, questions: list,
                    anchor_prefix: str = "") -> str:
    zone_names = {"base": "Зона 1 · База PHP (вневременное ядро 8.x)",
                  "current": "Зона 2 · PHP 8.4–8.5 (текущая эра)",
                  "future": "Зона 3 · Будущее: 8.6+, trunk, RFC (cutoff-зонд)"}
    def zone_of(topic: str) -> str:
        for q in questions:
            if q["topic"] == topic:
                return q.get("zone", "base")
        return "base"
    topics = []
    for q in questions:
        if q["topic"] not in topics:
            topics.append(q["topic"])
    max_level = max(q["level"] for q in questions)
    # разреженный набор (recall-пилот): не у каждой темы есть все уровни —
    # рисуем без колонок уровней, клетки подряд с бейджем «L<n>»
    sparse = any(len({q2["level"] for q2 in questions if q2["topic"] == t})
                 < max_level for t in topics)
    by_topic_level = defaultdict(dict)
    for q in questions:
        by_topic_level[q["topic"]][q["level"]] = q
    if sparse:
        max_cells = max(len(by_topic_level[t]) for t in topics)
        total_correct = sum(1 for q in questions
                            if q["id"] in by_id and is_correct(by_id[q["id"]]))
        # шапка и подвал — ровно по числу колонок (метка + клетки + итог),
        # иначе грид съедет на полстроки
        cells = ['<div class="hd"></div>', '<div class="hd">клетки</div>',
                 '<div class="hd">итог</div>', '<div class="hd"></div>']
        for topic in topics:
            cells.append(f'<div class="rh">{esc(topic)}</div>')
            tq = sorted((q for q in questions if q["topic"] == topic),
                        key=lambda q: q["level"])
            for q in tq:
                t = by_id.get(q["id"])
                tip = tooltip(t, q)
                cells.append(f'<div class="cell {cell_class(t)}">'
                             f'<div class="num">L{q["level"]}</div>'
                             f'<div class="tip">{tip}</div></div>')
            correct = sum(1 for q in tq
                          if q["id"] in by_id and is_correct(by_id[q["id"]]))
            cells.append(f'<div class="agg">{correct}/{len(tq)}</div>')
        cells.append('<div class="agg">точность →</div>')
        cells.append(f'<div class="agg">{pct(total_correct, len(questions))}%</div>')
        cells.append('<div class="agg"></div>')
        cells.append('<div class="agg"></div>')
        return ('<div class="mapwrap"><div class="map" '
                f'style="grid-template-columns: minmax(190px, 250px) '
                f'repeat({max_cells}, 52px) 84px;">{"".join(cells)}</div></div>')
    cells = ['<div class="hd"></div>']
    cells += [f'<div class="hd">L{i}</div>' for i in range(1, max_level + 1)]
    cells.append('<div class="hd">итог</div>')
    prev_zone = None
    for topic in topics:
        z = zone_of(topic)
        if z != prev_zone:
            zn = zone_names.get(z, z)
            cells.append(f'<div class="zonehdr">{esc(zn)}</div>')
            prev_zone = z
        cells.append(f'<div class="rh">{esc(topic)}</div>')
        tq = sorted((q for q in questions if q["topic"] == topic),
                    key=lambda q: q["level"])
        for lvl in range(1, max_level + 1):
            q = by_topic_level[topic].get(lvl)
            if q is None:
                # клетка без вопроса: прозрачный плейсхолдер — сетка не едет
                cells.append('<div class="ph"></div>')
                continue
            t = by_id.get(q["id"])
            tip = tooltip(t, q)
            cells.append(f'<a class="cell {cell_class(t)}" '
                         f'href="#{anchor_prefix}row-{esc(q["id"])}">'
                         f'<div class="tip">{tip}</div></a>')
        correct = sum(1 for q in tq if q["id"] in by_id and is_correct(by_id[q["id"]]))
        cells.append(f'<div class="agg">{correct}/{len(tq)}</div>')
    # строка агрегатов по уровням

    return ('<div class="mapwrap"><div class="map" '
            f'style="grid-template-columns: minmax(190px, 250px) '
            f'repeat({max_level}, 46px) 84px;">{"".join(cells)}</div></div>')


def map_grid_compare(questions: list, runs: list[tuple[str, dict]],
                     flip_by_id: dict[str, bool],
                     compact: bool = False) -> str:
    """Тепловая карта: клетка = доля верных по прогонам; флип — пунктир."""
    topics = []
    for q in questions:
        if q["topic"] not in topics:
            topics.append(q["topic"])
    max_level = max(q["level"] for q in questions)
    by_topic_level = defaultdict(dict)
    for q in questions:
        by_topic_level[q["topic"]][q["level"]] = q
    cells = ['<div class="hd"></div>']
    cells += [f'<div class="hd">L{i}</div>' for i in range(1, max_level + 1)]
    cells.append('<div class="hd">итог</div>')
    for topic in topics:
        cells.append(f'<div class="rh">{esc(topic)}</div>')
        tq = sorted((q for q in questions if q["topic"] == topic),
                    key=lambda q: q["level"])
        for lvl in range(1, max_level + 1):
            q = by_topic_level[topic].get(lvl)
            if q is None:
                cells.append('<div class="ph"></div>')
                continue
            answers = [(name, by_id.get(q["id"], {}).get("answer"))
                       for name, by_id in runs]
            p = sum(1 for _, by_id in runs
                    if q["id"] in by_id and is_correct(by_id[q["id"]])) / len(runs)
            cls = "flip" if flip_by_id.get(q["id"]) else ""
            tip = tooltip(None, q, runs=answers)
            cells.append(f'<div class="cell {cls}" style="background:{heat_color(p)}">'
                         f'<div class="num">{round(p * 100)}</div>'
                         f'<div class="tip">{tip}</div></div>')
        correct = sum(1 for q in tq for _, by_id in runs
                      if q["id"] in by_id and is_correct(by_id[q["id"]]))
        cells.append(f'<div class="agg">{pct(correct, len(tq) * len(runs))}%</div>')

    cell_px = 24 if compact else 46
    label_px = "minmax(120px, 170px)" if compact else "minmax(190px, 250px)"
    agg_px = 56 if compact else 84
    cls = "map compact" if compact else "map"
    return ('<div class="mapwrap"><div class="' + cls + '" '
            f'style="grid-template-columns: {label_px} '
            f'repeat({max_level}, {cell_px}px) {agg_px}px;">{"".join(cells)}</div></div>')


def topic_rows(by_id: dict, questions: list) -> str:
    topics = []
    for q in questions:
        if q["topic"] not in topics:
            topics.append(q["topic"])
    rows = []
    for topic in topics:
        tq = [q for q in questions if q["topic"] == topic]
        correct = sum(1 for q in tq if q["id"] in by_id and is_correct(by_id[q["id"]]))
        share = pct(correct, len(tq))
        rows.append(f'<tr><td>{esc(topic)}</td><td>{correct} / {len(tq)}</td>'
                    f'<td><div class="bar"><div style="width:{share}%"></div></div></td>'
                    f'<td>{share}%</td></tr>')
    return "\n".join(rows)


def level_rows(by_id: dict, questions: list) -> str:
    max_level = max(q["level"] for q in questions)
    rows = []
    for lvl in range(1, max_level + 1):
        lq = [q for q in questions if q["level"] == lvl]
        correct = sum(1 for q in lq if q["id"] in by_id and is_correct(by_id[q["id"]]))
        share = pct(correct, len(lq))
        rows.append(f'<tr><td>L{lvl}</td><td>{correct} / {len(lq)}</td>'
                    f'<td><div class="bar"><div style="width:{share}%"></div></div></td>'
                    f'<td>{share}%</td></tr>')
    return "\n".join(rows)


def appendix_table_single(by_id: dict, questions: list,
                          anchor_prefix: str = "") -> str:
    rows = []
    for q in questions:
        t = by_id.get(q["id"])
        if t is None:
            given, verdict, parsed = "—", "нет данных", "—"
            cls = "na-t"
            raw = ""
        else:
            given = given_text(t)
            if is_correct(t):
                verdict, cls = "верно", "ok-t"
            elif t.get("answer") is None:
                verdict, cls = "не разобрано", "na-t"
            else:
                verdict, cls = "неверно", "bad-t"
            parsed_raw = t.get("parsed") or "none"
            parsed = {"ok": "текст", "late": "текст (фолбэк)",
                      "none": "нет"}.get(parsed_raw, "—")
            # пометка видна только на отклонениях: «json» в каждой строке — шум
            parsed_html = (f'<br><small>парсинг: {esc(parsed)}</small>'
                           if parsed_raw != "json" else "")
            raw = (t.get("answer_raw") or "").strip().replace("\n", " ⏎ ")
        right = correct_text(q, t)
        dur = f'{t.get("duration_s", 0)} c' if t else "—"
        expl = ((t or {}).get("explanation") or "").strip()
        expl_html = (f'<br><small>Пояснение: {esc(expl)}</small>' if expl else "")
        raw_html = ""
        if raw:
            raw_html = (f'<br><details class="raw-details"><summary>Raw</summary>'
                        f'<pre class="raw-sample">{esc(raw)}</pre></details>')
        # у неверных клеток в «Результате» рядом — верный ответ
        wrong = t is not None and not is_correct(t)
        wrong_html = (f'<br><small>верный: «{esc(right)}»</small>' if wrong else "")
        rows.append(
            f'<tr id="{anchor_prefix}row-{esc(q["id"])}">'
            f'<td><code class="id">{esc(q["id"])}</code>'
            f'<br><small>{dur}</small></td>'
            f'<td>{esc(q["topic"])}</td>'
            f'<td class="assert">{esc(cell_text(q))}</td>'
            f'<td>«{esc(given)}»{expl_html}{raw_html}'
            f'</td>'
            f'<td class="{cls}">{esc(verdict)}{wrong_html}{parsed_html}</td>'
            f'<td class="assert"><small><i>{esc(q["note"])}</i></small></td></tr>')
    return "\n".join(rows)


def page(title: str, protocol: str, chips: str, body: str, footer: str,
         extra_css: str = "") -> str:
    return f"""<!doctype html>
<html lang="ru">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{esc(title)}</title>
<style>{page_css()}{extra_css}</style>
<main>
<h1>{esc(title)}</h1>
<p class="proto">{protocol}</p>
{chips}
{body}
<footer>{footer}</footer>
</main>
</html>
"""


def questions_for_run(questions_path: Path, session: dict) -> Path:
    """Файл вопросника для сессии: questionnaire_file, иначе замороженная
    questions.v{N}.json (сессии старых версий не должны читать v2)."""
    qfile = session.get("questionnaire_file")
    qver = session.get("questionnaire")

    def file_version(cand: Path):
        try:
            return json.loads(cand.read_text(encoding="utf-8")).get("version")
        except (OSError, json.JSONDecodeError):
            return None

    # 1) файл из сессии, если его версия совпадает с версией сессии
    if qfile:
        cand = questions_path.parent / qfile
        if cand.exists() and (qver is None or file_version(cand) == qver):
            return cand
    # 2) замороженная questions.v{N}.json (точная версия сессии)
    if qver:
        cand = questions_path.parent / f"questions.v{qver}.json"
        if cand.exists() and file_version(cand) == qver:
            return cand
    # 3) файл из сессии без сверки версии, 4) текущий вопросник
    if qfile:
        cand = questions_path.parent / qfile
        if cand.exists():
            return cand
    return questions_path


def build_single(run_dir: Path, questions_path: Path) -> None:
    try:
        session_probe = json.loads(
            (run_dir / "session.json").read_text(encoding="utf-8"))
    except (FileNotFoundError, json.JSONDecodeError):
        session_probe = {}
    questions_path = questions_for_run(questions_path, session_probe)
    q_version, questions = load_questions(questions_path)
    session, by_id = load_run(run_dir)
    title, protocol, chips, body, footer = _render_single_body(
        session, by_id, questions, q_version, run_dir, questions_path)
    (run_dir / "report.html").write_text(
        page(title, "", chips, body, footer), encoding="utf-8")
    write_summary_single(run_dir, session, by_id, questions, q_version)
    print(f"отчёт: {run_dir / 'report.html'}")


def _render_single_body(session, by_id, questions, q_version,
                        run_dir: Path, questions_path: Path,
                        anchor_prefix: str = ""):
    ver = session.get("questionnaire")
    if ver and q_version and ver != q_version:
        print(f"⚠️ вопросник прогона: v{ver}, в репозитории: v{q_version} — "
              f"карта построена по текущему вопроснику", file=sys.stderr)

    n = len(questions)
    correct = sum(1 for q in questions
                  if q["id"] in by_id and is_correct(by_id[q["id"]]))
    n_recall = sum(1 for q in questions if q.get("type") == "answer")
    as_of = ""
    try:
        as_of = json.loads(questions_path.read_text(encoding="utf-8")).get("as_of") or ""
    except (FileNotFoundError, json.JSONDecodeError):
        pass
    layer = {
        "questions.json": f"полная матрица v5: {n} клеток в 3 зонах "
                          f"(база / PHP 8.4–8.5 / будущее)",
        "questions.v2.json": f"слои A+B (v2): {n} клеток "
                             f"(72 да/нет + 24 recall)",
        "questions.v3.json": f"матрица v3: {n} клеток (да/нет + recall + версии)",
        "questions.v4.json": f"матрица v4: {n} клеток в 4 зонах",
        "questions.v1.json": f"слой A (v1): да/нет ({n} утверждений, "
                             f"темы × уровни L1–L8)",
        "questions-recall.json": "слой B: recall-пилот (24 клетки «что выведет», "
                                 "эталон — php 8.5)",
        "questions-versions.json": f"версионная панель, as_of {as_of} "
                                   f"({n} клеток «версии PHP»)",
    }.get(questions_path.name,
          f"вопросник {esc(questions_path.name)} ({n} клеток)")
    title = f"Карта знаний PHP — {run_label(session, run_dir)}"
    chips = chips_row_single(session, by_id, questions)
    n_recall = sum(1 for q in questions if q.get("type") == "answer")
    na_legend = ('<span><span class="sw" style="background:{0}"></span>ответ не '
                 'разобран («да»/«нет» первым словом)</span>'
                 .format(COLOR_NA))

    protocol_kind = session.get("protocol", "chat")
    if protocol_kind == "decision":
        prompts_html = ('<pre class="prompt-sample">Реальный запрос (decision-протокол '
                        '/v1/systemone): state = «Ты проверяешь утверждение о PHP на верность.» + '
                        'текст клетки; ответ — вероятность верности (noul), клетка «да» при '
                        'p ≥ 0.5. Меток и шаблонов в запросе нет.</pre>')
    else:
        # реальный запрос первой клетки — байт в байт, как ушло по HTTP
        first = next((t for t in by_id.values() if t.get("prompt")), None)
        sysp = session.get("system_prompt")
        uprompt = (first or {}).get("prompt") or \
            ("<текст клетки>" + (session.get("prompt_suffix") or ""))
        msg_html = []
        if sysp:
            msg_html.append(
                '<div class="msg"><span class="role">system</span>'
                f'<pre class="prompt-sample">{esc(sysp)}</pre></div>')
        msg_html.append(
            '<div class="msg"><span class="role">user</span>'
            f'<pre class="prompt-sample">{esc(uprompt)}</pre></div>')
        prompts_html = (
            '<div class="reqhead">Пример запроса (первая клетка). System '
            'одинаковый во всех запросах прогона, user — свой вопрос для '
            'каждой клетки:</div>'
            + "".join(msg_html))
    body = prompts_html + f"""
<h2>Карта</h2>
<div class="legend">
  <span><span class="sw" style="background:{COLOR_OK}"></span>верно</span>
  <span><span class="sw" style="background:{COLOR_BAD}"></span>неверно</span>
  {na_legend if not n_recall else ''}
</div>
<div class="legend"><span class="hint">наведите — вопрос и ответ модели · клик — строка с деталями в «Все утверждения»</span></div>
{map_grid_single(by_id, questions, anchor_prefix)}
<h2>Все утверждения</h2>
<table class="data">
<tr><th>Клетка</th><th>Тема</th><th>Утверждение</th><th>Ответ</th>
    <th>Результат</th><th>Обоснование</th></tr>
{appendix_table_single(by_id, questions, anchor_prefix)}
</table>"""
    footer = (f"построено knowledge-map/build_map_report.py · "
              f"{datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M UTC')} · "
              f"данные прогона: session.json, answers.csv")
    return title, "", chips, body, footer


def write_summary_single(run_dir: Path, session: dict, by_id: dict,
                         questions: list, q_version) -> None:
    n = len(questions)
    correct = sum(1 for q in questions
                  if q["id"] in by_id and is_correct(by_id[q["id"]]))
    parsed = [t for t in (by_id.get(q["id"]) for q in questions)
              if t and t.get("answer") is not None]
    bool_parsed = [t for t in parsed if isinstance(t.get("answer"), bool)]
    yes_n = sum(1 for t in bool_parsed if t["answer"])
    strict_n = sum(1 for t in parsed if t.get("strict"))
    topics = []
    for q in questions:
        if q["topic"] not in topics:
            topics.append(q["topic"])
    per_topic = []
    for topic in topics:
        tq = [q for q in questions if q["topic"] == topic]
        c = sum(1 for q in tq if q["id"] in by_id and is_correct(by_id[q["id"]]))
        per_topic.append((topic, c, len(tq)))
    ranked = sorted(per_topic, key=lambda x: x[1] / x[2], reverse=True)
    if len(ranked) > 1:
        top = " · ".join(f"{t} {c}/{k}" for t, c, k in ranked[:3])
        bottom = " · ".join(f"{t} {c}/{k}" for t, c, k in ranked[-3:])
        theme_lines = [f"- Сильные темы: {top}", f"- Слабые темы: {bottom}"]
    else:
        theme_lines = []
    totals = session.get("totals") or {}
    if not totals:
        all_turns = list(by_id.values())
        totals = {
            "duration_s": round(sum(t.get("duration_s") or 0 for t in all_turns), 1),
            "prompt_tokens": sum(t.get("prompt_eval_count") or 0 for t in all_turns),
            "output_tokens": sum(t.get("eval_count") or 0 for t in all_turns),
            "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0 for t in all_turns),
        }
    n_recall = sum(1 for q in questions if q.get("type") == "answer")
    recall_strict = sum(1 for t in parsed
                        if t.get("strict") and isinstance(t.get("answer"), str))
    if n_recall and len(parsed) > n_recall:
        bank_line = (f"вопросник v{q_version} ({n} клеток: {n - n_recall} да/нет "
                     f"+ {n_recall} recall; эталон recall — php 8.5/accept, "
                     f"полярность verify сбалансирована).")
        bias_line = (f"ответов «да»: {pct(yes_n, len(bool_parsed))}% при балансе "
                     f"verify 50/50; строгих совпадений recall: "
                     f"{recall_strict} / {n_recall}.")
    elif n_recall:
        bank_line = (f"вопросник v{q_version} ({n} recall-клеток: "
                     f"эталон — php 8.5/accept-список, ответ без рассуждений).")
        bias_line = (f"Строгих совпадений (ответ равен эталону): {recall_strict} / "
                     f"{len(parsed)}; у остальных верных эталон входит в ответ.")
    else:
        bank_line = f"вопросник v{q_version} ({n} утверждений, 50/50)."
        bias_line = (f"ответов «да»: {pct(yes_n, len(parsed))}% при балансе "
                     f"50/50.")
    base_q = [q for q in questions if q.get("zone", "base") == "base"]
    base_c = sum(1 for q in base_q
                 if q["id"] in by_id and is_correct(by_id[q["id"]]))
    base_pct_s = (f"{pct(base_c, len(base_q))}% по базе"
                  if base_q else f"{pct(correct, n)}%")
    lines = [
        f"# Карта знаний PHP — {session.get('model', '?')}, "
        f"run-{session.get('run', '?'):02d}",
        "",
        f"- Протокол: одна клетка = один независимый запрос; "
        f"think: {session.get('think')}; {bank_line}",
        f"- **Оценка: {base_pct_s}** (с новыми фичами: {pct(correct, n)}%); разобрано "
        f"{len(parsed)} (серых: {n - len(parsed)}); {bias_line}",
        f"- Стоимость: {totals['duration_s']} c, {totals['prompt_tokens']} вх. ток, "
        f"{totals['output_tokens']} вых. ток (кэш: {totals['cached_tokens']}).",
        *theme_lines,
        "",
        "Карта с подсказками по клеткам: [report.html](report.html). "
        "Сырые данные: session.json, answers.csv.",
    ]
    (run_dir / "summary.md").write_text("\n".join(lines), encoding="utf-8")


def build_compare(model_dir: Path, questions_path: Path,
                  think_filter: str | None = None,
                  qver_filter: int | None = None) -> None:
    q_version, questions = load_questions(questions_path)
    run_dirs = sorted(p for p in model_dir.glob("run-*") if p.is_dir())
    sessions = []
    for rd in run_dirs:
        try:
            sessions.append((rd.name, *load_run(rd)))
        except FileNotFoundError:
            print(f"  пропускаю {rd.name}: нет session.json", file=sys.stderr)
    # фильтр по версии вопросника (серии разных версий не смешиваются)
    if qver_filter:
        sessions = [(n, s, b) for n, s, b in sessions
                    if s.get("questionnaire") == qver_filter]
    if not sessions:
        raise SystemExit(f"в {model_dir} нет прогонов (run-*/session.json)")

    # Прогоны разных уровней рассуждений нельзя смешивать: «флипы» тогда
    # меряют смену режима, а не устойчивость знания. Карта — только внутри
    # одного think (правило в blueprint.md).
    levels = {s.get("think") for _, s, _ in sessions}
    if think_filter:
        sessions = [(n, s, b) for n, s, b in sessions
                    if s.get("think") == think_filter]
        if not sessions:
            raise SystemExit(f"нет прогонов с think={think_filter!r}; "
                             f"в папке есть уровни: {sorted(l for l in levels if l)}")
    elif len(levels) > 1:
        raise SystemExit(
            "в папке прогоны разных уровней рассуждений: "
            f"{sorted(l for l in levels if l)}. Смешивать нельзя — укажите "
            "--think <уровень> (тепловая карта строится внутри одного уровня; "
            "сравнение уровней — по метрикам из summary каждого прогона)")
    if len(sessions) == 1:
        print("прогон один на этом уровне — тепловая карта требует ≥2, "
              "строю карту прогона")
        build_single(model_dir / sessions[0][0], questions_path)
        return

    runs = [(name, by_id) for name, _session, by_id in sessions]
    # флип = клетка, где разобранные ответы прогонов не совпали
    flip_by_id: dict[str, bool] = {}
    for q in questions:
        answers = {by_id[q["id"]].get("answer") for _, by_id in runs
                   if q["id"] in by_id and by_id[q["id"]].get("answer") is not None}
        flip_by_id[q["id"]] = len(answers) > 1
    flips_total = sum(1 for v in flip_by_id.values() if v)

    def run_totals(sess: dict, by_id: dict) -> dict:
        t = sess.get("totals")
        if t:
            return t
        turns = list(by_id.values())
        return {
            "duration_s": round(sum(x.get("duration_s") or 0 for x in turns), 1),
            "prompt_tokens": sum(x.get("prompt_eval_count") or 0 for x in turns),
            "output_tokens": sum(x.get("eval_count") or 0 for x in turns),
            "cached_tokens": sum(x.get("prompt_eval_cached_count") or 0 for x in turns),
        }

    accs = []
    for name, sess, by_id in sessions:
        c = sum(1 for q in questions
                if q["id"] in by_id and is_correct(by_id[q["id"]]))
        accs.append({"name": name, "think": sess.get("think"), "correct": c,
                     "n": len(questions), **run_totals(sess, by_id)})
    mean = statistics.mean(a["correct"] for a in accs)
    tot_in = sum(a["prompt_tokens"] for a in accs)
    tot_out = sum(a["output_tokens"] for a in accs)
    n_runs = len(sessions)
    runs_word = "прогон" if n_runs == 1 else \
        ("прогона" if 2 <= n_runs % 10 <= 4 and n_runs % 100 not in (12, 13, 14)
         else "прогонов")
    title = (f"Карта знаний PHP — {esc(sessions[0][1].get('model', '?'))}: "
             f"{n_runs} {runs_word}")
    protocol = ("тепловая карта: клетка = доля верных ответов по прогонам "
                "(0% красная → 100% зелёная, число внутри — % верных); пунктир — "
                "нестабильная клетка (флип: ответы прогонов не совпали); "
                "протокол клетки как в одиночной карте.")
    chips = f"""
    <div class="chips">
      <div class="chip">Прогонов: <b>{len(sessions)}</b></div>
      <div class="chip">Средняя точность: <b>{pct(int(round(mean)), len(questions))}%</b>
           (разброс {min(a['correct'] for a in accs)}–{max(a['correct'] for a in accs)} из {len(questions)})</div>
      <div class="chip">Токены по всем прогонам: <b>{tot_in} вх / {tot_out} вых</b></div>
      <div class="chip">Флипующих клеток: <b>{flips_total} / {len(questions)}</b></div>
      <div class="chip">Нестабильность = знание под вопросом: соседние прогоны
           дают разные ответы на то же утверждение</div>
    </div>"""
    body = f"""
<h2>Тепловая карта</h2>
<div class="legend">
  <span><span class="sw" style="background:{COLOR_BAD}"></span>0% верных</span>
  <span><span class="sw" style="background:{COLOR_MID}"></span>50%</span>
  <span><span class="sw" style="background:{COLOR_OK}"></span>100%</span>
  <span><span class="sw" style="background:#fff;outline:2px dashed #8a2d2d"></span>
        флип (ответы прогонов расходятся)</span>
</div>
{map_grid_compare(questions, runs, flip_by_id)}
<h2>Прогоны: точность и стоимость</h2>
<table class="data"><tr><th>Прогон</th><th>think</th><th>Верно</th><th>%</th>
<th>Время</th><th>Токены (вх / вых / кэш)</th></tr>
{"\n".join(f'<tr><td>{esc(a["name"])}</td><td>{esc(a["think"])}</td>'
           f'<td>{a["correct"]} / {a["n"]}</td><td>{pct(a["correct"], a["n"])}%</td>'
           f'<td>{a["duration_s"]} c</td>'
           f'<td>{a["prompt_tokens"]} / {a["output_tokens"]} / {a["cached_tokens"]}</td></tr>'
           for a in accs)}
</table>
<h2>Флипующие клетки</h2>
{flips_table(questions, runs, flip_by_id)}"""
    footer = (f"построено knowledge-map/build_map_report.py · "
              f"{datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M UTC')} · "
              f"данные: run-*/session.json")
    tag = think_filter or "mixed"
    (model_dir / f"compare-{tag}.html").write_text(
        page(title, protocol, chips, body, footer), encoding="utf-8")

    lines = [
        f"# Карта знаний PHP — {sessions[0][1].get('model', '?')}: "
        f"{len(sessions)} прогонов",
        "",
        f"- Точность по прогонам: "
        + ", ".join(f"{a['name']} {a['correct']}/{a['n']} ({pct(a['correct'], a['n'])}%, "
                    f"{a['duration_s']} c, {a['prompt_tokens']}+{a['output_tokens']} ток, "
                    f"think {a['think']})" for a in accs),
        f"- Средняя: {pct(int(round(mean)), len(questions))}%, "
        f"токены по всем прогонам: {tot_in} вх / {tot_out} вых, "
        f"флипующих клеток: {flips_total} / {len(questions)}.",
        "",
        f"Тепловая карта: [compare-{tag}.html](compare-{tag}.html). "
        "Карты отдельных прогонов: run-*/report.html.",
    ]
    (model_dir / f"compare-{tag}.md").write_text("\n".join(lines), encoding="utf-8")
    print(f"отчёт: {model_dir / f'compare-{tag}.html'}")


def flips_table(questions: list, runs: list[tuple[str, dict]],
                flip_by_id: dict[str, bool]) -> str:
    """Таблица нестабильных клеток: утверждение и ответы каждого прогона."""
    flipped = [q for q in questions if flip_by_id.get(q["id"])]
    if not flipped:
        return ("<p>Ни одна клетка не флипует: все 96 ответов одинаковы во "
                "всех прогонах.</p>")
    rows = []
    for q in flipped:
        cells = []
        for name, by_id in runs:
            a = by_id.get(q["id"], {}).get("answer")
            cells.append("—" if a is None else ("да" if a else "нет"))
        rows.append(
            f'<tr><td><code class="id">{esc(q["id"])}</code></td>'
            f'<td class="assert">{esc(cell_text(q))}</td>'
            f'<td>{" · ".join(esc(c) for c in cells)}</td></tr>')
    return f"""<table class="data">
<tr><th>Клетка</th><th>Утверждение</th>
<th>Ответы по прогонам ({esc(" → ".join(n for n, _ in runs))})</th></tr>
{"\n".join(rows)}
</table>"""


def protocol_tag(session: dict) -> str:
    """Поколение протокола запроса: system+JSON / JSON-суффикс / текст /
    decision — видно в таблице прогонов полного отчёта."""
    if session.get("protocol") == "decision":
        return "decision"
    if session.get("system_prompt"):
        return "system+JSON"
    if "JSON" in (session.get("prompt_suffix") or ""):
        return "JSON-суффикс"
    return "текст"


def build_series_report(models_root: Path, questions_path: Path,
                        out_file: Path) -> None:
    """Сводный отчёт по всем моделям: одна модель — один ряд, в ряду карты
    серий (уровень рассуждений), отсортированные по лестнице thinking."""
    think_order = {"off": 0, "low": 1, "medium": 2, "high": 3, "xhigh": 4}
    sections = []
    model_dirs = sorted(d for d in models_root.iterdir()
                        if d.is_dir() and (d / "knowledge-map").is_dir())
    for md in model_dirs:
        km = md / "knowledge-map"
        runs = []
        for rd in sorted(km.glob("run-*")):
            if not (rd / "session.json").exists():
                continue
            try:
                session, by_id = load_run(rd)
            except FileNotFoundError:
                continue
            if session.get("protocol", "chat") != "chat":
                continue
            runs.append((rd, session, by_id))
        if not runs:
            continue
        model = esc(runs[0][1].get("model", md.name))
        latest_qver = max((s.get("questionnaire") or 0 for _, s, _ in runs))
        series_map = defaultdict(list)
        for rd, session, by_id in runs:
            if session.get("questionnaire") != latest_qver:
                continue
            series_map[session.get("think")].append((rd, session, by_id))

        blocks = []
        for think in sorted(series_map,
                            key=lambda t: (think_order.get(t, 99), t)):
            items = series_map[think]
            if len(items) < 2:
                continue
            qpath = questions_for_run(questions_path, items[0][1])
            q_version, questions = load_questions(qpath)
            runs_list = [(rd.name, by_id) for rd, _s, by_id in items]
            flip_by_id = {}
            for q in questions:
                answers = {by_id.get(q["id"], {}).get("answer")
                           for _n, by_id in runs_list}
                answers.discard(None)
                flip_by_id[q["id"]] = len(answers) > 1
            grid = map_grid_compare(questions, runs_list, flip_by_id,
                                    compact=True)
            scores = [sum(1 for t in b.values() if is_correct(t))
                      for _rd, _s, b in items]
            scores_s = (f"{pct(min(scores), len(questions))}% – "
                        f"{pct(max(scores), len(questions))}%")
            stable_ok = sum(
                1 for q in questions
                if all(by_id.get(q["id"], {}).get("answer") is not None
                       and is_correct(by_id[q["id"]]) for _rd, _s, by_id in items))
            flips = sum(1 for q in questions if flip_by_id[q["id"]])
            blocks.append(
                f'<div class="series">'
                f'<div class="serhead">think: {esc(think)}</div>'
                f'<div class="sermeta">{len(items)} прогонов · вопросник '
                f'v{q_version} · точность {scores_s} · стабильно верных '
                f'{stable_ok} · флипующих {flips}</div>{grid}</div>')
        if blocks:
            sections.append(f'<h2>{model}</h2>\n'
                            f'<div class="seriesrow">{"".join(blocks)}</div>')

    if not sections:
        raise SystemExit("нет серий: ни у одной модели нет ≥2 прогонов "
                         "на одной версии вопросника")
    title = "Карты знаний PHP — карты серий прогонов"
    proto = ("для каждой модели — ряд карт по уровням рассуждений; клетка = "
             "доля верных ответов по серии (число — %), пунктир — флип "
             "(ответы прогонов расходятся)")
    extra = ("main { max-width: 100%; padding: 28px 22px 60px; }")
    html = page(title, proto, "", "\n".join(sections),
                "построено knowledge-map/build_map_report.py · "
                f"{datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M UTC')}",
                extra_css=extra)
    out_file.parent.mkdir(parents=True, exist_ok=True)
    out_file.write_text(html, encoding="utf-8")
    print(f"сводный отчёт серий: {out_file} ({len(sections)} моделей)")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    g = p.add_mutually_exclusive_group(required=False)
    g.add_argument("--run-dir", help="папка прогона (карта зелёный/красный)")
    g.add_argument("--model-dir", help="папка модели: полный отчёт модели (report.html); с --think — тепловая карта уровня (compare.html)")
    p.add_argument("--think", default=None,
                   help="уровень рассуждений для тепловой карты; обязателен, "
                        "когда в папке прогоны нескольких think — смешивать "
                        "уровни нельзя")
    p.add_argument("--models-root", default=None,
                   help="сводный отчёт по всем моделям: карты серий "
                        "модель → уровень размышлений (out: reports/)")
    p.add_argument("--version", type=int, default=None,
                   help="версия вопросника для тепловой карты (серии разных "
                        "версий не смешиваются)")
    p.add_argument("--questions", default=str(SCRIPT_DIR / "questions.json"))
    args = p.parse_args()

    questions_path = Path(args.questions)
    if args.models_root:
        out = Path(args.models_root).parent / "reports" / "knowledge-map-series.html"
        build_series_report(Path(args.models_root), questions_path, out)
        return 0
    if args.run_dir:
        build_single(Path(args.run_dir), questions_path)
    elif args.think:
        build_compare(Path(args.model_dir), questions_path, args.think,
                      args.version)
    else:
        print("--model-dir без --think не поддерживается: тепловая карта "
              "серии — с --think <уровень> (и при необходимости --version N); "
              "сводный отчёт по всем моделям — --models-root", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())

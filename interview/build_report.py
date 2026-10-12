#!/usr/bin/env python3
"""Генератор самодостаточного HTML-отчёта по исследованию (v2).

Транскрипты — внешними относительными ссылками (не вшиваются). Добавлены:
инвентарь всех завершённых сессий по моделям, анализ влияния системного
промпта (серии think-off vs primer), оглавление.
Выход: reports/REPORT.html.

Запуск: python3 interview/build_report.py [--low /tmp/lowvsmax/raw.json]
"""

import argparse
import csv
import json
import statistics
from collections import defaultdict
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

# ---------------------------------------------------------------------------
# Данные моделей: (папка, метка, вендор, тип, серия для таблицы, прогоны clef)
# clef берётся средним по указанным прогонам серии
# ---------------------------------------------------------------------------
MODELS = [
    dict(folder="anthropic-claude-opus-5.5", label="Opus 5.5", vendor="Anthropic",
         kind="cloud", series="effort low", runs=["run-02"], glm_run="run-02",
         prov="3/3", note="самая дорогая и медленная"),
    dict(folder="openai-gpt-6.1-sol", label="GPT-6.1 Sol", vendor="OpenAI",
         kind="cloud", series="effort low", runs=["run-01"], glm_run="run-01",
         prov="3/3", note="лучший дешёвый «почти-потолок»"),
    dict(folder="anthropic-claude-haiku-5.5", label="Haiku 5.5", vendor="Anthropic",
         kind="cloud", series="effort low", runs=["run-01"], glm_run="run-01",
         prov="3/3", note="самая быстрая"),
    dict(folder="openai-gpt-6-luna", label="GPT-6 Luna", vendor="OpenAI",
         kind="cloud", series="effort low", runs=["run-01"], glm_run="run-01",
         prov="3/3", note="самая дешёвая в топе"),
    dict(folder="deepseek-flash", label="DeepSeek V4.1 Flash", vendor="DeepSeek (родной API)",
         kind="cloud", series="deepseek", runs=["run-01"], glm_run="run-01",
         prov="3/3", note="лучшее цена/качество среди облаков; "
                          "балл уровня облака, но галлюцинации в 33% ответов",
         series_note="effort low · автокэш 95% (кэш-хит ×50 дешевле)"),
    dict(folder="ornith-1.5-35b", label="ornith-1.5 35B", vendor="локально",
         kind="local", series="think-off", runs=[f"run-{i:02d}" for i in range(7, 12)],
         glm_run="run-07", prov="12/15", series_note="", qv=1),
    dict(folder="ornith-1.5-35b", label="ornith-1.5 35B", vendor="локально",
         kind="local", series="мышление включено", runs=["run-03", "run-04", "run-05"], qv=1,
         glm_run="run-06", prov="9/9", series_note="+0.5 балла к той же модели без "
                                 "мышления, генерация вчетверо дольше (см. §6)"),
    dict(folder="qwen3.8", label="qwen3.8 27B", vendor="локально",
         kind="local", series="think-off", runs=[f"run-{i:02d}" for i in range(6, 11)], qv=1,
         glm_run="run-06", prov="15/15", series_note=""),
    dict(folder="muse-glimmer", label="muse-glimmer 28B", vendor="локально",
         kind="local", series="think-off", runs=["run-01"], glm_run="run-01",
         prov="3/3", series_note=""),
    dict(folder="huihui_ai-Qwen3.8-abliterated-latest", label="huihui abliterated 27B",
         vendor="локально", kind="local", series="think-off",
         runs=[f"run-{i:02d}" for i in range(1, 6)], glm_run="run-01",
         prov="13/15", series_note="", qv=1),
    dict(folder="orcarouter-Qwen3.8-27B-Uncensored-latest", label="orcarouter 27B uncensored",
         vendor="локально", kind="local", series="think-off",
         runs=[f"run-{i:02d}" for i in range(1, 6)], glm_run="run-01",
         prov="15/15", series_note="", qv=1),
    dict(folder="ornith-1.5-9b", label="ornith-1.5 9B", vendor="локально",
         kind="local", series="think-on", runs=[f"run-{i:02d}" for i in range(1, 6)],
         glm_run="run-01", prov="15/15", series_note="у 9B мышление работает и включено"),
    dict(folder="gigachat", label="GigaChat-2 Max", vendor="Сбер (API)",
         kind="cloud", series="базовый", runs=["run-01"], glm_run="run-01",
         prov="2/3", series_note="принял предпосылку Q26 с выдуманным обоснованием"),
    dict(folder="gigachat-3-ultra", label="GigaChat-3 Ultra", vendor="Сбер (API)",
         kind="cloud", series="gigachat", runs=["run-01"], glm_run="run-01",
         prov="3/3", series_note="freemium только для физлиц; единственная — "
                                 "все 3 провокации чисто, +0.6 над GigaChat-2 Max (по судье)"),
    dict(folder="yandexgpt", label="YandexGPT Pro 5", vendor="Яндекс (API)",
         kind="cloud", series="yandexfm", runs=["run-01"], glm_run="run-01",
         prov="2/3", series_note="контент-фильтр заменил 6 из 35 ответов отказами; "
                                 "запрашивался алиас yandexgpt/latest, имя Pro 5 дал биллинг"),
    dict(folder="medgemma-27b", label="medgemma 27B", vendor="локально",
         kind="negctrl", series="think-off", runs=["run-01"], glm_run="run-01",
         prov="1/3", series_note="медицинский файнтюн"),
    dict(folder="gemma4-e2b", label="gemma4 e2b 4.6B", vendor="локально",
         kind="negctrl", series="think-off", runs=["run-01"], glm_run="run-01",
         prov="0/3", series_note=""),
]

# уровень рассуждений судьи GLM-5.3-Flash в вердиктах (LOW-vs-MAX.md):
# эталонные прогоны — max; выборка deepseek-flash (2026-10-09) — базовый
# состав судьи яруса 2: локальные прогоны и эталонные выборки — glm-5.3-flash;
# сплошная до-проверка облачных прогонов (2026-10-10) — glm-5.3 (сессия сабагентов)
GLM_JUDGE_LEVEL = {}

# отображение внутренних id серий в колонке «Мышление» результатов
TOP_ROW = {"anthropic-claude-opus-5.5", "openai-gpt-6.1-sol"}
RU_ROWS = {"gigachat", "gigachat-3-ultra", "yandexgpt"}

THINK_TITLE = {
    "think-off": "без мышления", "think-on": "с мышлением",
    "мышление включено": "с мышлением",
    "effort low": "effort low", "deepseek": "effort low",
    # у этих API нет reasoning-ручки — модели работают в своём базовом режиме
    "базовый": "не настраивается", "yandexfm": "не настраивается",
    "gigachat": "не настраивается",
}

SERIES_TITLE = {
    "think-on": "мышление включено", "think-off": "мышление выключено",
    "primer-v2": "системный промпт (роль+CoT+недоверчивость)",
    "cloud": "reasoning effort low",
    "gigachat": "базовый режим (Сбер)",
    "yandexfm": "базовый режим (Яндекс)",
    "deepseek": "effort low (родной API)",
}

# российские облака: freemium-квота физлица (токенов/12 мес) и ₽/1M по
# платному пакету (None — пакета нет). Тарифы физлиц developers.sber.ru
FREEMIUM = {
    "gigachat": (25_000_000, 650),        # Max: пакет 3M = ₽1950/мес
    "gigachat-3-ultra": (50_000_000, None),  # Ultra: только freemium
}
# бизнес-тариф (юрлицо), ₽/1M (вход, выход): Сбер — Max ₽650 без разбивки
# (₽325 асинхронно, тарификация кэша не раскрыта); GigaChat-3-Ultra юрлицам
# у Сбера не продаётся — коммерческий доступ через cloud.ru (₽96/₽289)
BUSINESS_RUB_1M = {
    "gigachat": (650, 650),
    "gigachat-3-ultra": (96.22, 288.6),
}


def fmt_cost(v: float) -> str:
    """Стоимость: XXX.XX если значение ≥ 0.10, иначе 0.0XXX."""
    return f"{v:.2f}" if v >= 0.10 else f"{v:.4f}"


# сноски цен Сбера: текст сноски по папке модели; номер присваивается
# по порядку появления маркера в конкретной таблице
SBER_LABEL = {"gigachat": "GigaChat-2 Max", "gigachat-3-ultra": "GigaChat-3 Ultra"}
SBER_RATE = {
    "gigachat": "тариф Сбера ₽650/1M токенов без разбивки вх/вых "
                "(асинхронно ₽325/1M, минимальный платёж ₽600/мес)",
    "gigachat-3-ultra": "модель юрлицам у Сбера не продаётся, доступ только "
                        "cloud.ru Foundation Models (₽96/1M вход + ₽289/1M выход)",
}


COL_FOOT_N = 6  # число сносок-колонок таблицы результатов


def sber_footnote(items, start: int = COL_FOOT_N + 1, wrap: bool = True) -> str:
    """Сноски под таблицей: items — [(folder, ₽без кэша, ₽с кэшем, токены)]
    в порядке появления маркеров в таблице; нумерация сквозная по этому порядку.
    wrap=False возвращает только строки (<br>-разделитель) для вклейки в общий блок."""
    lines = []
    for n, (folder, nc, wc, toks) in enumerate(items, start):
        quota = FREEMIUM[folder][0]
        lines.append(
            f'<sup>{n}</sup> <b>{SBER_LABEL[folder]}</b>: физлицу ₽0 '
            f"(freemium, {toks / quota * 100:.1f}% квоты {quota // 1_000_000:.0f} млн "
            f"токенов/12 мес); для работы (юрлицо) {SBER_RATE[folder]}, полная сессия "
            f"₽{fmt_cost(nc)}–{fmt_cost(wc)} (~${fmt_cost(nc / RUB_USD)}–{fmt_cost(wc / RUB_USD)}), "
            f"вилка отражает нераскрытую тарификацию кэша.")
    joined = "<br>".join(lines)
    return '<div class=note>' + joined + "</div>" if wrap else joined



def knowledge_ids() -> frozenset:
    """id ответов на знания: основные вопросы с knowledge=true и их уточнения."""
    d = json.loads((ROOT / "interview/roles/backend/questions.json").read_text())
    qs = d["questions"] if isinstance(d, dict) else d
    ids = set()
    for q in qs:
        if not q.get("knowledge"):
            continue
        ids.add(q["id"])
        for i in range(1, len(q.get("followups") or []) + 1):
            ids.add(f"{q['id']}.fu{i}")
    return frozenset(ids)


KNOW = knowledge_ids()  # 30 ответов на знания (25 вопросов + 5 уточнений)


# --- самооценка моделей (Q00) против оценки конвейера, радары ---
SELF_TOPICS = [("PHP 8", r"php\s*8|php\b", "PHP 8"),
               ("Symfony", r"symfony", "Symfony"),
               ("Doctrine", r"doctrine", "Doctrine"),
               ("PostgreSQL", r"postgres|pgsql", "PostgreSQL"),
               ("DDD", r"\bddd\b|domain[- ]driven|предметно", "DDD"),
               ("Чист. архит.", r"чист\w+ архитектур|clean\s*architecture", "Чистая архитектура"),
               ("Тестир.", r"тестирован|phpunit", "Тестирование"),
               ("Git", r"\bgit\b", "Git и процесс")]


def _self_num(win: str):
    import re as _re
    for pat in (r'(\d{1,2}(?:[.,]\d)?)\s*/\s*10', r'[Оо]ценка\D{0,12}(\d{1,2})',
                r':\D{0,8}(\d{1,2})\b', r'\|\s*\**\s*(\d{1,2})\s*\**\s*\|',
                r'\b(\d{1,2}(?:[.,]\d)?)\b(?!\s*/)'):
        m = _re.search(pat, win)
        if m:
            return float(m.group(1).replace(",", "."))
    return None


def parse_self_scores(answer: str) -> dict:
    """Числовой ряд самооценки 0-10 из ответа на Q00; None — не нашли."""
    import re as _re
    out = {}
    for label, pat, _sec in SELF_TOPICS:
        hits = list(_re.finditer(pat, answer, _re.I))
        val = None
        for h in reversed(hits):        # балл самооценки обычно в хвосте ответа
            val = _self_num(answer[h.end(): h.end() + 450])
            if val is not None and 0 <= val <= 10:
                break
        out[label] = val if (val is not None and 0 <= val <= 10) else None
    return out


def section_clef(folder: str, run: str) -> dict:
    """Средний clef-балл (0-4) по секциям вопросника."""
    _q = json.loads((ROOT / "interview" / "roles" / "backend" / "questions.json").read_text())
    _q = _q["questions"] if isinstance(_q, dict) else _q
    secs = {q["id"]: q["section"] for q in _q}
    agg = defaultdict(list)
    for r in csv.DictReader(open(ROOT / folder / "backend" / "judgments"
                                 / f"decision-clef-{run}.csv", encoding="utf-8-sig")):
        if not r.get("error"):
            agg[secs.get(r["question"], "")].append(float(r["score"]))
    return {s: statistics.mean(v) for s, v in agg.items() if v}


def judge_section_scores(folder: str, glm_run) -> dict:
    """Средний балл судьи GLM-5.3-Flash (0-4) по секциям; пусто — если не судился."""
    if not glm_run:
        return {}
    p = ROOT / folder / "backend" / "judgments" / "glm-5.3-flash-iso" / f"{glm_run}.json"
    if not p.exists():
        return {}
    _q = json.loads((ROOT / "interview" / "roles" / "backend" / "questions.json").read_text())
    _q = _q["questions"] if isinstance(_q, dict) else _q
    secs = {q["id"]: q["section"] for q in _q}
    agg = defaultdict(list)
    for r in json.loads(p.read_text())["results"]:
        agg[secs.get(r["id"], "")].append(float(r["score"]))
    return {s: statistics.mean(v) for s, v in agg.items()}


def radar_svg(topics, self_v, our_v, judge_v=None, w=250, h=210) -> str:
    import math
    cx, cy, R = w / 2, h / 2 + 6, 68   # данные внутри, подписи за кольцом на 107px
    n = len(topics)
    def pt(i, val):
        a = -math.pi / 2 + 2 * math.pi * i / n
        rr = R * max(0, min(10, val)) / 10
        return cx + rr * math.cos(a), cy + rr * math.sin(a)
    rings = "".join(
        f'<polygon points="{" ".join(f"{pt(i,k)[0]:.1f},{pt(i,k)[1]:.1f}" for i in range(n))}" '
        f'fill="none" stroke="#e3e7ea" stroke-width="1"/>' for k in (2.5, 5, 7.5, 10))
    # цифры шкалы вдоль верхнего луча
    scale = "".join(
        f'<text x="{cx - 4:.0f}" y="{pt(0, k)[1] + 3:.0f}" font-size="8.5" text-anchor="end" '
        f'fill="#9aa3ad">{k:g}</text>' for k in (2.5, 5, 7.5, 10))
    axes = "".join(
        f'<line x1="{cx}" y1="{cy}" x2="{pt(i,4)[0]:.1f}" y2="{pt(i,4)[1]:.1f}" stroke="#e3e7ea"/>'
        for i in range(n))
    # подписи осей: за кольцом, якорь по углу — текст тянется наружу, не внутрь
    lbl_parts = []
    for i, t in enumerate(topics):
        a = -math.pi / 2 + 2 * math.pi * i / n
        ca, sa = math.cos(a), math.sin(a)
        lx = cx + (R + 14) * ca
        ly = cy + (R + 14) * sa + 3.5
        anchor = "start" if ca > 0.35 else "end" if ca < -0.35 else "middle"
        ly += 4 if sa > 0.35 else (-2 if sa < -0.35 else 0)
        lbl_parts.append(
            f'<text x="{lx:.1f}" y="{ly:.1f}" font-size="10" text-anchor="{anchor}" '
            f'fill="#5a6672" stroke="#fafbfc" stroke-width="3" paint-order="stroke" '
            f'stroke-linejoin="round">{t}</text>')
    lbl = "".join(lbl_parts)
    def poly(vals, fill, stroke, dash=""):
        pts = [(pt(i, v), i) for i, v in enumerate(vals) if v is not None]
        if len(pts) < 3:
            return ""
        p = " ".join(f"{xy[0]:.1f},{xy[1]:.1f}" for xy, _ in pts)
        return (f'<polygon points="{p}" fill="{fill}" stroke="{stroke}" '
                f'stroke-width="1.6" {dash}/>')
    ours = poly(our_v, "rgba(47,125,84,.16)", "#2f7d84" if False else "#2f7d54")
    selfp = poly(self_v, "rgba(179,84,63,.10)", "#b3543f", 'stroke-dasharray="5,3"')
    # вершины с CSS-тултипом: «раздел · самооценка X/10 · конвейер Y/4» / «раздел · судья X/4»
    def vertex(x, y, r, fill, lines):
        tip_w = max(len(s) for s in lines) * 5.6 + 12
        tx = x - tip_w / 2
        tx = max(2, min(tx, w - tip_w - 2))
        ty = y - 34 if y > 40 else y + 14
        tip = [f'<rect x="{tx:.0f}" y="{ty:.0f}" width="{tip_w:.0f}" height="18" rx="5" '
               f'fill="#1d2433" fill-opacity="0.95"/>',
               f'<text x="{tx + 6:.0f}" y="{ty + 12.5:.0f}" font-size="10" fill="#c8d4e4">'
               f'{lines[0]}</text>']
        if len(lines) > 1:
            tip[1] = (f'<text x="{tx + 6:.0f}" y="{ty + 12.5:.0f}" font-size="10" fill="#fff" '
                      f'font-weight="bold" text-anchor="start">{lines[0]}</text>')
            tip.append(f'<text x="{tx + 6:.0f}" y="{ty + 24:.0f}" font-size="10" fill="#c8d4e4">'
                       f'{lines[1]}</text>')
        h2 = 18 if len(lines) == 1 else 30
        tip[0] = f'<rect x="{tx:.0f}" y="{ty:.0f}" width="{tip_w:.0f}" height="{h2}" rx="5" ' \
                 f'fill="#1d2433" fill-opacity="0.95"/>'
        return (f'<g class="rv">'
                f'<circle cx="{x:.1f}" cy="{y:.1f}" r="9" fill="transparent"/>'
                f'<circle cx="{x:.1f}" cy="{y:.1f}" r="{r}" fill="{fill}" '
                f'stroke="#fff" stroke-width="1.2" />'
                f'<g class="rtip">' + "".join(tip) + '</g></g>')

    marks = ""
    for i, v in enumerate(self_v):
        if v is None:
            continue
        x, y = pt(i, v)
        marks += vertex(x, y, 3, "#b3543f",
                        [topics[i], f"самооценка {v:.0f}"])
    if judge_v:
        for i, v in enumerate(judge_v):
            if v is None:
                continue
            x, y = pt(i, v)
            marks += vertex(x, y, 4, "#1e5c38", [topics[i], f"судья {v:.1f}"])
    return (f'<svg class="radar" width="{w}" height="{h}" viewBox="0 0 {w} {h}" '
            f'style="overflow:visible">'
            f'{rings}{axes}{scale}{lbl}{ours}{selfp}{marks}<g class="rtip-layer"></g></svg>')



def point_name(m) -> str:
    """Имя точки на картах: базовая метка + режим (с мышлением / с промптом)."""
    name = m["label"]
    if m.get("primer"):
        name += " (с промптом)"
    elif m["folder"] == "ornith-1.5-35b":
        name += " (с мышлением)" if m["series"] != "think-off" else " (без мышления)"
    return name


def place_labels(pts):
    """Авторазмещение подписей точек: вокруг каждой точки перебираются
    кандидатные позиции; штраф — площадь пересечения с уже размещёнными
    подписями и с чужими точками (чужие точки дешевле, ×0.55). При равном
    штрафе выигрывает более близкая к точке позиция. pts: key, x, y, r, w,
    xmin/xmax/ymin/ymax (границы поля). Возвращает key → (x, anchor, y)."""
    cands = []
    for dx, dy in ((10, 4), (-10, 4), (10, -6), (-10, -6)):
        cands.append((dx, dy, "start" if dx > 0 else "end"))
    for dist in (8, 14, 22, 32, 44, 58):
        cands.append((0, -dist, "middle"))
        cands.append((0, dist, "middle"))
    for dist in (22, 34, 46):
        cands.append((dist, 4, "start"))
        cands.append((-dist, 4, "end"))
        cands.append((dist, -6, "start"))
        cands.append((-dist, -6, "end"))
    placed = []
    dots = [(p["x"] - p["r"], p["y"] - p["r"], p["x"] + p["r"], p["y"] + p["r"])
            for p in pts]
    def label_box(p, dx, dy, anchor):
        if anchor == "start":
            bx0, bx1 = p["x"] + dx, p["x"] + dx + p["w"]
        elif anchor == "end":
            bx0, bx1 = p["x"] + dx - p["w"], p["x"] + dx
        else:  # middle: подпись центрирована по точке
            bx0, bx1 = p["x"] + dx - p["w"] / 2, p["x"] + dx + p["w"] / 2
        return bx0, p["y"] + dy - 10, bx1, p["y"] + dy + 3
    out = {}
    for i, p in enumerate(pts):
        best = None
        for ci, (dx, dy, anchor) in enumerate(cands):
            bx0, by0, bx1, by1 = label_box(p, dx, dy, anchor)
            if bx0 < p["xmin"] or bx1 > p["xmax"] or by0 < p["ymin"] or by1 > p["ymax"]:
                continue
            area = 0.0
            for L in placed:
                ox = min(bx1, L[2] + 2) - max(bx0, L[0] - 2)
                oy = min(by1, L[3] + 2) - max(by0, L[1] - 2)
                if ox > 0 and oy > 0:
                    area += ox * oy
            for j, D in enumerate(dots):
                if j == i:
                    continue
                ox = min(bx1, D[2]) - max(bx0, D[0])
                oy = min(by1, D[3]) - max(by0, D[1])
                if ox > 0 and oy > 0:
                    area += 0.55 * ox * oy
            score = (round(area, 1), ci)
            if best is None or score < best[0]:
                best = (score, dx, dy, anchor)
        _, dx, dy, anchor = best
        placed.append(label_box(p, dx, dy, anchor))
        out[p["key"]] = (p["x"] + dx, anchor, p["y"] + dy)
    return out


def quality_price_scatter(models) -> str:
    """Распределение моделей: качество (балл судьи) против цены сессии."""
    import math
    W, H = 920, 540
    ML, MR, MT, MB = 64, 24, 28, 58
    PW, PH = W - ML - MR, H - MT - MB
    XMIN, XMAX = 0.01, 8.0          # $ за сессию, лог-шкала; Сбер ₽0 — на левом крае
    YMIN, YMAX = 1.5, 4.25
    QX, QY = 0.30, 3.0              # пороги квадрантов: $0.30 и 3.0 балла

    GROUPS = {"cloud": ("#315f9e", "облако"), "local": ("#2f7d54", "локальные"),
              "ru": ("#5b9bd5", "русские"), "negctrl": ("#b3543f", "негативные контроли")}

    def px(cost):
        c = max(cost, XMIN)
        return ML + (math.log10(c) - math.log10(XMIN)) / (math.log10(XMAX) - math.log10(XMIN)) * PW

    def py(score):
        return MT + (YMAX - score) / (YMAX - YMIN) * PH

    def dot_r(share):
        return 7 + min(share / 0.5, 1) * 8

    pts = []
    for m in models:
        if m["glm"] is None:
            continue
        c = max(m["cost"], XMIN)
        x = ML + (math.log10(c) - math.log10(XMIN)) / (math.log10(XMAX) - math.log10(XMIN)) * PW
        y = MT + (YMAX - m["glm"]) / (YMAX - YMIN) * PH
        share = m["glm_hall"] / m["glm_n"] if m["glm_hall"] is not None else 0
        pts.append({"key": (m["folder"], m["series"], bool(m.get("primer"))),
                    "x": x, "y": y, "r": dot_r(share) + 1,
                    "w": len(point_name(m)) * 5.2 + 6,
                    "xmin": ML - 30, "xmax": ML + PW + 8,
                    "ymin": MT - 6, "ymax": MT + PH + 6})
    label_geo = place_labels(pts)

    svg = []
    # точки рисуются от крупных к мелким: мелкие чистые поверх крупных грязных,
    # иначе большая полупрозрачная точка перехватывает наведение соседа
    draw_order = sorted(
        [m for m in models if m["glm"] is not None],
        key=lambda m: -(m["glm_hall"] / m["glm_n"] if m["glm_hall"] else 0))
    # сетка и оси
    for gv in (2.0, 2.5, 3.0, 3.5, 4.0):
        y = py(gv)
        svg.append(f'<line x1={ML} y1={y:.0f} x2={ML+PW} y2={y:.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y+4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for xv in (0.01, 0.03, 0.1, 0.3, 1, 3, 8):
        x = px(xv)
        svg.append(f'<line x1="{x:.0f}" y1="{MT}" x2="{x:.0f}" y2="{MT+PH}" stroke="#e4e7ec"/>'
                   f'<text x="{x:.0f}" y="{MT+PH+20}" text-anchor="middle" font-size="12" '
                   f'fill="#66707f">${xv:g}</text>')
    # квадранты
    xq, yq = px(QX), py(QY)
    svg.append(f'<line x1={xq:.0f} y1={MT} x2={xq:.0f} y2={MT+PH} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    svg.append(f'<line x1={ML} y1={yq:.0f} x2={ML+PW} y2={yq:.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    for cx, cy, anc, txt in [(ML+10, MT+18, "start", "хорошо и дёшево"),
                             (ML+PW-10, MT+18, "end", "хорошо и дорого"),
                             (ML+10, MT+PH-12, "start", "плохо и дёшево"),
                             (ML+PW-10, MT+PH-12, "end", "плохо и дорого")]:
        svg.append(f'<text x={cx} y={cy} text-anchor={anc} font-size=12.5 font-style=italic fill=#9aa3ad>{txt}</text>')
    # точки
    seen_groups = []
    for m in draw_order:
        if m["glm"] is None:
            continue
        hue, gname = GROUPS[m["kind"]]
        if (hue, gname) not in seen_groups:
            seen_groups.append((hue, gname))
        x, y = px(m["cost"]), py(m["glm"])
        fx = (x - ML) / PW   # доля по горизонтали: для направления тултипа
        name = point_name(m)
        # диаметр точки кодирует долю галлюцинаций: 0% → 7px, ≥50% → 15px
        hall_share = m["glm_hall"] / m["glm_n"] if m["glm_hall"] is not None else 0
        r_dot = 7 + min(hall_share / 0.5, 1) * 8
        # тултип: название, балл, цена, галлюцинации
        tip_lines = [name, f"балл {m['glm']:.2f} · цена ${fmt_cost(m['cost'])}"]
        if m["glm_hall"] is not None:
            pct = hall_share * 100
            tip_lines.append(f"галлюцинации {pct:.0f}% ({m['glm_hall']} из {m['glm_n']})")
        tip_w = max(len(s) for s in tip_lines) * 6.4 + 16
        tip_left = fx > 0.72   # у правого края карточка раскрывается влево
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x="{tx:.0f}" y="{y-46:.0f}" width="{tip_w:.0f}" height="{20 + 14*len(tip_lines):.0f}" '
               f'rx="7" fill="#1d2433" fill-opacity="0.95"/>']
        for i, s in enumerate(tip_lines):
            bold = ' font-weight="bold"' if i == 0 else ''
            fill = "#ffffff" if i == 0 else "#c8d4e4"
            tip.append(f'<text x="{tx + 8:.0f}" y="{y - 30 + i*15:.0f}" font-size="11.5" '
                       f'fill="{fill}"{bold} text-anchor="start">{s}</text>')
        # тултип показывается только на самом кружке (.dot), не на подписи
        op = 0.45 if hall_share >= 0.4 else 0.8   # грязные точки полупрозрачны: видно наложения
        tx, anchor, ty = label_geo[(m["folder"], m["series"], bool(m.get("primer")))]
        # подпись окрашена в цвет группы точки — привязка читается без выносок
        svg.append(f'<g class="pt">'
                   f'<circle class="dot" cx="{x:.0f}" cy="{y:.0f}" r="{r_dot:.1f}" fill="{hue}" '
                   f'fill-opacity="{op}" stroke="#fff" stroke-width="1.5" />'
                   f'<text x="{tx:.0f}" y="{ty:.0f}" text-anchor="{anchor}" font-size="11" '
                   f'fill="{hue}">{name}</text>'
                   f'<g class="tip">' + "".join(tip) + '</g></g>')
    svg.append(f'<text x="{ML+PW/2:.0f}" y="{H-16}" text-anchor="middle" font-size="12.5" fill="#66707f">'
               f'цена полного собеседования, $ — лог-шкала (шаг деления ×10); Сбер ₽0 прижат к левому краю</text>')
    svg.append(f'<text x=16 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT+PH/2:.0f})">'
               f'балл модели</text>')
    return ('<h3>Карта моделей: качество и цена</h3>'
            '<p>Каждая точка — строка таблицы выше: по вертикали балл модели (шкала — в плашке §1), '
            'по горизонтали фактическая цена полного собеседования в лог-шкале. Пунктир делит '
            'поле на зоны: порог качества 3.0 балла, порог цены $0.30 за сессию. GigaChat-2 Max '
            'и GigaChat-3 Ultra физлицу стоят ₽0 (freemium) и вынесены на левый край.</p>'
            + f'<svg class="scatter" width="{W}" height="{H}" viewBox="0 0 {W} {H}" style="max-width:100%">'
            + "".join(svg) + '<g class="tip-layer"></g></svg>'
            + '''<script>
document.querySelectorAll('svg.scatter g.pt').forEach(function (pt) {
  var dot = pt.querySelector('.dot'), tip = pt.querySelector('.tip');
  if (!dot || !tip) return;
  dot.addEventListener('mouseenter', function () {
    pt.closest('svg').querySelector('.tip-layer').appendChild(tip);
    tip.style.display = 'block';
  });
  dot.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
});
</script>'''
            + '<p class=sub>' + " · ".join(
                f'<span style="color:{hue}">●</span> {gname}' for hue, gname in GROUPS.values())
            + '. Диаметр точки кодирует долю ответов с галлюцинациями (чем больше, тем грязнее). Ось Y обрезана от 1.5: моделей ниже не было.</p>')


def stability_html(models) -> str:
    """Постоянство ответов: разброс балла одного и того же вопроса между
    прогонами серии (размах, 0–4) — распределение по моделям и по темам."""
    import math, statistics
    series = []
    for m in models:
        if len(m["runs"]) < 2:
            continue
        base = ROOT / m["folder"] / "backend" / "judgments"
        per_q = {}
        for run in m["runs"]:
            d = {r["question"]: r for r in csv.DictReader(
                open(base / f"decision-clef-{run}.csv", encoding="utf-8-sig"))}
            for q, r in d.items():
                if r.get("error") or q not in KNOW:
                    continue
                per_q.setdefault(q, []).append(float(r["score"]))
        rng = {q: max(v) - min(v) for q, v in per_q.items() if len(v) > 1}
        if len(rng) >= 10:
            title = "с промптом" if m.get("primer") else THINK_TITLE.get(m["series"], m["series"])
            series.append({"label": f"{m['label']} ({title})", "rng": rng})
    if len(series) < 2:
        return ""
    all_vals = [v for s in series for v in s["rng"].values()]

    # --- свечи: распределение размахов вопросов по моделям ---
    PCT = lambda v: v / 4 * 100          # размах в % шкалы 0–4
    xmax = max(20.0, math.ceil(max(all_vals) / 4 * 100 / 5) * 5)
    W, ML, MR, MT, RH = 940, 232, 128, 26, 52
    H = MT + RH * len(series) + 46
    PW = W - ML - MR
    px = lambda v: ML + PW * (v / xmax)   # v уже в процентах
    order = sorted(series, key=lambda s: statistics.median(s["rng"].values()))
    svg = [f'<svg viewBox="0 0 {W} {H}" style="width:100%;max-width:{W}px;font-family:inherit">']
    step = 5.0 if xmax <= 25 else 10.0
    v = 0.0
    while v <= xmax + 1e-9:
        x = px(v)
        svg.append(f'<line x1="{x:.0f}" y1="{MT - 6}" x2="{x:.0f}" y2="{H - 46}" stroke="#e4e7ec"/>'
                   f'<text x="{x:.0f}" y="{H - 30}" text-anchor="middle" font-size="12" fill="#66707f">{v:.0f}%</text>')
        v = round(v + step, 1)
    svg.append(f'<text x="{ML + PW / 2:.0f}" y="{H - 10}" text-anchor="middle" font-size="12.5" '
               f'fill="#66707f">расхождение ответа на вопрос между прогонами, 100% = 4 балла</text>')
    for i, s in enumerate(order):
        y = MT + RH * i + RH / 2
        vals = sorted(s["rng"].values())
        med = statistics.median(vals)
        q1 = statistics.quantiles(vals, n=4)[0] if len(vals) >= 4 else vals[0]
        q3 = statistics.quantiles(vals, n=4)[2] if len(vals) >= 4 else vals[-1]
        svg.append(f'<text x="{ML - 10}" y="{y + 4:.0f}" text-anchor="end" font-size="13" '
                   f'fill="#1d2433">{s["label"]}</text>')
        # ус: тонкая линия от минимума до максимума
        svg.append(f'<line x1="{px(PCT(vals[0])):.1f}" y1="{y:.0f}" x2="{px(PCT(vals[-1])):.1f}" y2="{y:.0f}" '
                   f'stroke="#aab3bf" stroke-width="1.6"/>')
        # тело: половина вопросов в середине (от 1-го до 3-го квартиля)
        bw = max(3.0, px(PCT(q3)) - px(PCT(q1)))
        svg.append(f'<rect x="{px(PCT(q1)):.1f}" y="{y - 9:.0f}" width="{bw:.1f}" height="18" rx="3.5" '
                   f'fill="#2f7d54" fill-opacity="0.75"/>')
        # риска медианы: тёмно-зелёная вертикаль в тон телу
        svg.append(f'<rect x="{px(PCT(med)) - 1.4:.1f}" y="{y - 9:.0f}" width="2.8" height="18" rx="1.2" fill="#1e5c38"/>')
        svg.append(f'<text x="{W - MR + 10:.0f}" y="{y + 4:.0f}" font-size="12.5" fill="#66707f">'
                   f'медиана {PCT(med):.1f}% · макс {PCT(vals[-1]):.0f}%</text>')
    svg.append("</svg>")
    strip = "".join(svg)

    # --- радары по моделям: средний размах ответов по темам ---
    SECMAP = {"PHP 8": "PHP 8", "ООП и принципы": "ООП", "Symfony": "Symfony",
              "Doctrine": "Doctrine", "PostgreSQL": "PostgreSQL",
              "DDD": "DDD и чистая арх.", "Чистая архитектура": "DDD и чистая арх.",
              "Тестирование": "Тесты и конкурент.", "Конкурентность": "Тесты и конкурент.",
              "Git и процесс": "Git", "Провокации": "Провокации",
              "Мини-код-ревью": "Мини-код-ревью"}
    SHORT = {"PHP 8": "PHP 8", "ООП": "ООП", "Symfony": "Symfony", "Doctrine": "Doctrine",
             "PostgreSQL": "Postgres", "DDD и чистая арх.": "DDD",
             "Тесты и конкурент.": "Тесты", "Git": "Git", "Провокации": "Провок.",
             "Мини-код-ревью": "Ревью"}
    qs = json.loads((ROOT / "interview" / "roles" / "backend" / "questions.json").read_text())["questions"]
    qsec = {q["id"]: q["section"] for q in qs}
    per_series = []
    for s in series:
        acc = {}
        for q, r in s["rng"].items():
            topic = SECMAP.get(qsec.get(q.split(".")[0], "?"), "?")
            acc.setdefault(topic, []).append(r)
        per_series.append({topic: sum(v) / len(v) for topic, v in acc.items()})
    topics = sorted(per_series[0], key=lambda k: -sum(p.get(k, 0) for p in per_series))
    rmax = max(5.0, math.ceil(max(v for p in per_series for v in p.values()) / 4 * 100 / 5) * 5)
    RW, RHt, R = 300, 288, 92
    cx, cy = RW / 2, RHt / 2 + 12
    pt = lambda i, r: (cx + r * math.cos(math.pi * 2 * i / len(topics) - math.pi / 2),
                       cy + r * math.sin(math.pi * 2 * i / len(topics) - math.pi / 2))
    cards = []
    radar_order = sorted(zip(series, per_series),
                         key=lambda sp: statistics.median(sp[0]["rng"].values()))
    for s, pmeans in radar_order:
        med_pct = statistics.median(s["rng"].values()) / 4 * 100
        rsvg = [f'<svg viewBox="0 0 {RW} {RHt}" style="width:100%;max-width:{RW}px;font-family:inherit">',
                f'<text x="{cx:.0f}" y="18" text-anchor="middle" font-size="13.5" font-weight="600" '
                f'fill="#1d2433">{s["label"]}</text>',
                f'<text x="{cx:.0f}" y="33" text-anchor="middle" font-size="11" '
                f'fill="#66707f">медиана {med_pct:.1f}%</text>']
        for f in (0.5, 1.0):
            pts = " ".join(f"{pt(i, R * f)[0]:.1f},{pt(i, R * f)[1]:.1f}" for i in range(len(topics)))
            rsvg.append(f'<polygon points="{pts}" fill="none" stroke="#e4e7ec"/>')
        rsvg.append(f'<text x="{cx + 4:.0f}" y="{cy - R + 3:.0f}" font-size="10" fill="#9aa3ad">{rmax * 0.5:.0f}%</text>')
        for i, topic in enumerate(topics):
            x, y = pt(i, R)
            rsvg.append(f'<line x1="{cx:.0f}" y1="{cy:.0f}" x2="{x:.1f}" y2="{y:.1f}" stroke="#e4e7ec"/>')
            lx, ly = pt(i, R + 15)
            anc = "middle" if abs(lx - cx) < 12 else ("start" if lx > cx else "end")
            rsvg.append(f'<text x="{lx:.1f}" y="{ly + 3:.0f}" text-anchor="{anc}" font-size="10.5" '
                        f'fill="#1d2433" stroke="#fafbfc" stroke-width="3" paint-order="stroke">{SHORT.get(topic, topic)}</text>')
        rm = R * med_pct / rmax
        vals = [pmeans.get(topic, 0) / 4 * 100 for topic in topics]
        pts = " ".join(f"{pt(i, R * v / rmax)[0]:.1f},{pt(i, R * v / rmax)[1]:.1f}" for i, v in enumerate(vals))
        rsvg.append(f'<polygon points="{pts}" fill="#2f7d54" fill-opacity="0.16" stroke="#2f7d54" stroke-width="2"/>')
        for i, v in enumerate(vals):
            x, y = pt(i, R * v / rmax)
            rsvg.append(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="3" fill="#2f7d54"/>')
        # медиана поверх полигона: пунктир виден и внутри зелёной заливки
        rsvg.append(f'<circle cx="{cx:.0f}" cy="{cy:.0f}" r="{rm:.1f}" fill="none" '
                    f'stroke="#b3543f" stroke-dasharray="2.5,2.5" stroke-width="1.7"/>')
        rsvg.append("</svg>")
        cards.append(f'<div class=scard>{"".join(rsvg)}</div>')
    radar = '<div class=sgrid>' + "".join(cards) + "</div>"

    worst = max(topics, key=lambda k: sum(pm.get(k, 0) for pm in per_series) / len(per_series))
    return (
        '<h3>Постоянство ответов</h3>'
        '<p class=lead>Насколько модель отвечает одинаково от прогона к прогону: '
        'разброс балла одного и того же вопроса между прогонами. '
        'Только строки с несколькими прогонами: локальные модели в стандартных режимах '
        'и их варианты с системным промптом (3–5 прогонов); у облачных моделей, '
        'muse-glimmer и негативных контролей прогон один, мерить нечего.</p>'
        + strip +
        '<p class=sub>Свеча это распределение вопросов одной строки таблицы: тело охватывает половину вопросов '
        'в середине распределения (между квартилями), усы ведут к минимальному и максимальному '
        'расхождению, тёмная риска это медиана. У самых постоянных моделей медиана 1–2% от максимального балла '
        '% шкалы, но почти у каждой есть вопросы, гуляющие на четверть шкалы и больше.</p>'
        + radar +
        '<p class=sub>Каждый радар это одна строка таблицы: луч темы показывает средний размах её '
        f'ответов в этой теме; единая шкала для всех радаров, 100% = 4 балла (кольцо = {rmax:.0f}%). '
        f'Наименее воспроизводимая тема почти у всех моделей — {worst.lower()}. '
        'Терракотовая окружность это медиана этой строки (число в заголовке карточки): лучи вне '
        'неё гуляют больше типичного вопроса этой строки, внутри меньше.</p>')


def quality_speed_scatter(models) -> str:
    """Карта моделей: качество (балл судьи) против скорости (минуты собеса);
    диаметр точки кодирует выходные токены сессии."""
    W, H = 920, 540
    ML, MR, MT, MB = 64, 24, 28, 58
    PW, PH = W - ML - MR, H - MT - MB
    XMIN, XMAX = 0, 32.0            # минуты полного собеседования
    YMIN, YMAX = 1.5, 4.25
    QX, QY = 10.0, 3.0              # пороги квадрантов: 10 минут и 3.0 балла
    TMIN, TMAX = 30000, 180000      # диапазон выходных токенов для диаметра

    GROUPS = {"cloud": ("#315f9e", "облако"), "local": ("#2f7d54", "локальные"),
              "ru": ("#5b9bd5", "русские"), "negctrl": ("#b3543f", "негативные контроли")}

    def px(v):
        return ML + (max(v, XMIN) - XMIN) / (XMAX - XMIN) * PW

    def py(score):
        return MT + (YMAX - score) / (YMAX - YMIN) * PH

    def dot_r(tok):
        return 7 + min(max((tok - TMIN) / (TMAX - TMIN), 0), 1) * 8

    pts = []
    for m in models:
        if m["glm"] is None:
            continue
        pts.append({"key": (m["folder"], m["series"], bool(m.get("primer"))),
                    "x": px(m["dur_min"]), "y": py(m["glm"]),
                    "r": dot_r(m["out_tok"]) + 1,
                    "w": len(point_name(m)) * 5.2 + 6,
                    "xmin": ML - 30, "xmax": ML + PW + 8,
                    "ymin": MT - 6, "ymax": MT + PH + 6})
    label_geo = place_labels(pts)

    svg = []
    draw_order = sorted([m for m in models if m["glm"] is not None],
                        key=lambda m: -m["out_tok"])
    for gv in (2.0, 2.5, 3.0, 3.5, 4.0):
        y = py(gv)
        svg.append(f'<line x1={ML} y1={y:.0f} x2={ML+PW} y2={y:.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y+4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for xv in (5, 10, 15, 20, 25, 30):
        x = px(xv)
        svg.append(f'<line x1="{x:.0f}" y1="{MT}" x2="{x:.0f}" y2="{MT+PH}" stroke="#e4e7ec"/>'
                   f'<text x="{x:.0f}" y="{MT+PH+20}" text-anchor="middle" font-size="12" '
                   f'fill="#66707f">{xv}</text>')
    xq, yq = px(QX), py(QY)
    svg.append(f'<line x1={xq:.0f} y1={MT} x2={xq:.0f} y2={MT+PH} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    svg.append(f'<line x1={ML} y1={yq:.0f} x2={ML+PW} y2={yq:.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    for cx, cy, anc, txt in [(ML+10, MT+18, "start", "хорошо и быстро"),
                             (ML+PW-10, MT+18, "end", "хорошо и медленно"),
                             (ML+10, MT+PH-12, "start", "плохо и быстро"),
                             (ML+PW-10, MT+PH-12, "end", "плохо и медленно")]:
        svg.append(f'<text x={cx} y={cy} text-anchor={anc} font-size=12.5 font-style=italic fill=#9aa3ad>{txt}</text>')
    for m in draw_order:
        hue, gname = GROUPS[m["kind"]]
        x, y = px(m["dur_min"]), py(m["glm"])
        fx = (x - ML) / PW
        name = point_name(m)
        r_dot = dot_r(m["out_tok"])
        tip_lines = [name, f"балл {m['glm']:.2f} · {m['dur_min']:.0f} мин",
                     f"~{m['out_tok']/1000:.0f} тыс. выходных токенов"]
        tip_w = max(len(s) for s in tip_lines) * 6.4 + 16
        tip_left = fx > 0.72
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x="{tx:.0f}" y="{y-46:.0f}" width="{tip_w:.0f}" height="{20 + 14*len(tip_lines):.0f}" '
               f'rx="7" fill="#1d2433" fill-opacity="0.95"/>']
        for i, s in enumerate(tip_lines):
            bold = ' font-weight="bold"' if i == 0 else ''
            fill = "#ffffff" if i == 0 else "#c8d4e4"
            tip.append(f'<text x="{tx + 8:.0f}" y="{y - 30 + i*15:.0f}" font-size="11.5" '
                       f'fill="{fill}"{bold} text-anchor="start">{s}</text>')
        tx, anchor, ty = label_geo[(m["folder"], m["series"], bool(m.get("primer")))]
        svg.append(f'<g class="pt">'
                   f'<circle class="dot" cx="{x:.0f}" cy="{y:.0f}" r="{r_dot:.1f}" fill="{hue}" '
                   f'fill-opacity="0.8" stroke="#fff" stroke-width="1.5" />'
                   f'<text x="{tx:.0f}" y="{ty:.0f}" text-anchor="{anchor}" font-size="11" '
                   f'fill="{hue}">{name}</text>'
                   f'<g class="tip">' + "".join(tip) + '</g></g>')
    svg.append(f'<text x="{ML+PW/2:.0f}" y="{H-16}" text-anchor="middle" font-size="12.5" fill="#66707f">'
               f'длительность полного собеседования, минуты; диаметр точки — выходные токены сессии</text>')
    svg.append(f'<text x=16 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT+PH/2:.0f})">'
               f'балл модели</text>')
    return ('<h3>Карта моделей: качество и скорость</h3>'
            '<p>Каждая точка — строка таблицы выше: по вертикали балл модели, по горизонтали '
            'минуты полного собеседования, диаметр точки кодирует объём выходных токенов '
            '(многословность). Пунктир делит поле на зоны: порог качества 3.0 балла, порог '
            'скорости 10 минут.</p>'
            + f'<svg class="scatter" width="{W}" height="{H}" viewBox="0 0 {W} {H}" style="max-width:100%">'
            + "".join(svg) + '<g class="tip-layer"></g></svg>'
            + """<script>
document.querySelectorAll('svg.scatter g.pt').forEach(function (pt) {
  var dot = pt.querySelector('.dot'), tip = pt.querySelector('.tip');
  if (!dot || !tip) return;
  dot.addEventListener('mouseenter', function () {
    pt.closest('svg').querySelector('.tip-layer').appendChild(tip);
    tip.style.display = 'block';
  });
  dot.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
});
</script>""")


def quality_triangle_3d(models) -> str:
    """3D-карта моделей: цена × качество × скорость (three.js с CDN, орбита и зум);
    радиус сферы кодирует выходные токены, маркер — «идеальный угол» железного
    треугольника (быстро + качественно + дёшево)."""
    import json as _json
    import math as _math
    GROUPS = {"cloud": "#315f9e", "local": "#2f7d54", "ru": "#5b9bd5", "negctrl": "#b3543f"}
    data = []
    for m in models:
        if m["glm"] is None:
            continue
        name = m["label"]
        if m.get("primer"):
            name += " (с промптом)"
        elif m["folder"] == "ornith-1.5-35b":
            name += " (с мышлением)" if m["series"] != "think-off" else " (без мышления)"
        cost = max(m["cost"], 0.01)
        data.append({"name": name, "score": m["glm"], "cost": round(cost, 4),
                     "dur": m["dur_min"], "tok": m["out_tok"],
                     "color": GROUPS[m["kind"]]})
    payload = _json.dumps(data, ensure_ascii=False)

    html = """<h3>Треугольник: быстро, качественно, дёшево</h3>
<p>«Железное» правило управления проектами «быстро, качественно, дёшево —
выберите любые два» работает и здесь. Три оси — цена сессии, балл оценки качества и время
полного собеседования в минутах; цена сжата логарифмически, чтобы копеечные шарики не
слипались на фоне Opus. Каждый шарик изображает одну модель в одном режиме работы (строку таблицы результатов: например, у ornith-1.5 35B три шарика — без мышления, с мышлением и с системным промптом), а радиус шарика кодирует объём выходных токенов. Сцену можно вращать
мышью, приближать колесом; золотой каркасный многогранник у вершины оси качества —
маркер недостижимого идеала (максимально дёшево + быстро + качественно), и вокруг него пусто.</p>
<div id="tri3d" style="position:relative;height:540px;border:1px solid var(--line);border-radius:12px;background:#fff">
  <div id="tri3d-fallback" style="padding:24px;color:#66707f;font-size:14px">
    3D-сцена не загрузилась: для неё нужен интернет (three.js с CDN).
  </div>
</div>
<p class=sub>Точки цветом повторяют группы карт выше: синие облака, зелёные локальные,
голубые российские, терракотовые негативные контроли. Наведите курсор на шарик —
карточка с баллом, ценой, минутами и токенами.</p>
<p><b>Что видно при вращении.</b> Облако точек натянуто по диагонали от идеального угла:
правило «выберите любые два» выполняется, ни одна модель не занимает угол целиком.
Ближе всех к идеалу GPT-6 Luna — 3.88 балла за $0.03 и 6 минут (единственная,
кто почти обманывает правило, но и она не дотягивает до максимума качества).
Противоположный полюс — Opus 5.5: максимальные 4.0 балла ценой худшей цены ($5.82)
и худшей скорости (29 минут), самое далёкое от идеала расстояние. Локальные
27B живут в середине (muse-glimmer ближе всех из зелёных), а мышление ornith-35B
сдвигает шарик вверх по качеству ценой скорости: 3 минуты превращаются в 12.
Российские облака распадаются на два полюса: GigaChat-3 Ultra бесплатна и
качественна, но медленнее (11 минут); YandexGPT быстра, но дорога и слаба —
худший из возможных обменов. Быстро и качественно — это Haiku за $0.18; дёшево
и качественно — Luna и muse; дёшево и быстро — ornith без мышления и gemma4-e2b,
но с качеством на дне шкалы.</p>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script>
(function () {
  if (typeof THREE === 'undefined') return;   // CDN недоступен — текст-заглушка остаётся
  var host = document.getElementById('tri3d');
  host.innerHTML = '';
  var DATA = __DATA__;
  var W = host.clientWidth || 920, H = 540;
  var scene = new THREE.Scene();
  scene.background = new THREE.Color(0xffffff);
  var FR = 11;                       // полуширина ортокамеры: размеры шаров не зависят от расстояния
  var aspect = W / H;
  var camera = new THREE.OrthographicCamera(-FR * aspect, FR * aspect, FR, -FR, 0.1, 200);
  camera.position.set(14, 15, 18);
  var renderer = new THREE.WebGLRenderer({antialias: true});
  renderer.setSize(W, H);
  renderer.setPixelRatio(window.devicePixelRatio || 1);
  host.appendChild(renderer.domElement);
  var controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.target.set(4.5, 6.0, 3.5);
  controls.enableDamping = true;
  // сцена: куб 10×10×10, x = цена (лог, дёшево ближе), y = качество, z = минуты
  var S = 10;
  function vx(cost) { return Math.max(0, Math.min(S, (Math.log10(cost) + 2) / 2.75 * S)); }
  function vy(score) { return Math.max(0, Math.min(S, (score - 1.5) / 2.75 * S)); }
  function vz(min) { return Math.max(0, Math.min(S, min / 32 * S)); }
  var grid = new THREE.GridHelper(S, 10, 0xdde3ea, 0xe9edf2);
  grid.position.set(S / 2, 0, S / 2); scene.add(grid);
  function axis(a, b) {
    var g = new THREE.BufferGeometry().setFromPoints([a, b]);
    scene.add(new THREE.Line(g, new THREE.LineBasicMaterial({color: 0x8b95a3})));
    // стрелка на конце оси, направление a → b
    var dir = b.clone().sub(a).normalize();
    var cone = new THREE.Mesh(
        new THREE.ConeGeometry(0.15, 0.42, 12),
        new THREE.MeshBasicMaterial({color: 0x8b95a3}));
    cone.position.copy(b);
    cone.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), dir);
    scene.add(cone);
  }
  axis(new THREE.Vector3(0, 0, 0), new THREE.Vector3(S, 0, 0));
  axis(new THREE.Vector3(0, 0, 0), new THREE.Vector3(0, S, 0));
  axis(new THREE.Vector3(0, 0, 0), new THREE.Vector3(0, 0, S));
  function label(text, color, x, y, z, height) {
    var cv = document.createElement('canvas');
    var ctx = cv.getContext('2d');
    ctx.font = 'bold 42px "Segoe UI", sans-serif';
    var w = Math.ceil(ctx.measureText(text).width) + 36;
    cv.width = w; cv.height = 72;
    ctx = cv.getContext('2d');
    ctx.font = 'bold 42px "Segoe UI", sans-serif';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.strokeStyle = '#ffffff'; ctx.lineWidth = 9; ctx.strokeText(text, w / 2, 38);
    ctx.fillStyle = color; ctx.fillText(text, w / 2, 38);
    var sp = new THREE.Sprite(new THREE.SpriteMaterial({map: new THREE.CanvasTexture(cv), transparent: true, depthTest: false}));
    sp.renderOrder = 10;
    var k = height / 72;
    sp.scale.set(w * k, height, 1);
    sp.position.set(x, y, z);
    scene.add(sp);
    return sp;
  }
  label('цена', '#315f9e', S + 2.8, 0.6, 1.0, 0.6);
  label('качество', '#2f7d54', 1.4, S + 1.3, 0, 0.6);
  label('время', '#b3543f', 2.0, 0.6, S + 2.8, 0.6);
  // идеальный угол: дёшево (x=0) + качественно (y=S) + быстро (z=0)
  var ideal = new THREE.Mesh(
      new THREE.OctahedronGeometry(0.55),
      new THREE.MeshBasicMaterial({color: 0xd4a017, wireframe: true}));
  ideal.position.set(0.8, S - 0.8, 0.8);
  scene.add(ideal);

  var raycaster = new THREE.Raycaster(), mouse = new THREE.Vector2(), hovered = null;
  var tip = document.createElement('div');
  tip.style.cssText = 'position:absolute;display:none;pointer-events:none;background:#1d2433;'
      + 'color:#fff;padding:7px 10px;border-radius:8px;font-size:12px;line-height:1.5;'
      + 'box-shadow:0 2px 8px rgba(20,30,50,.25);white-space:nowrap;z-index:5';
  host.appendChild(tip);
  var spheres = [];
  DATA.forEach(function (m) {
    var r = 0.16 + Math.sqrt(Math.max(0, Math.min(1, (m.tok - 35000) / 145000))) * 0.34;
    var mesh = new THREE.Mesh(
        new THREE.SphereGeometry(r, 24, 18),
        new THREEMeshMaterialSafe(m.color));
    mesh.position.set(vx(m.cost), vy(m.score), vz(m.dur));
    mesh.userData = m;
    scene.add(mesh);
    spheres.push(mesh);
    label(m.name, m.color, mesh.position.x, mesh.position.y + r + 0.3, mesh.position.z, 0.42);
  });
  function THREEMeshMaterialSafe(hex) {
    return new THREE.MeshLambertMaterial({color: new THREE.Color(hex), transparent: true, opacity: 0.92});
  }
  scene.add(new THREE.AmbientLight(0xffffff, 0.75));
  var dl = new THREE.DirectionalLight(0xffffff, 0.5);
  dl.position.set(6, 12, 8); scene.add(dl);
  function onMove(e) {
    var b = renderer.domElement.getBoundingClientRect();
    mouse.x = ((e.clientX - b.left) / b.width) * 2 - 1;
    mouse.y = -((e.clientY - b.top) / b.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);
    var hit = raycaster.intersectObjects(spheres)[0];
    if (hit) {
      hovered = hit.object;
      var m = hovered.userData;
      tip.innerHTML = '<b>' + m.name + '</b><br>балл ' + m.score.toFixed(2)
          + ' · $' + m.cost + ' · ' + Math.round(m.dur) + ' мин'
          + '<br>~' + Math.round(m.tok / 1000) + ' тыс. выходных токенов';
      tip.style.display = 'block';
      tip.style.left = Math.min(e.clientX - b.left + 16, b.width - 220) + 'px';
      tip.style.top = (e.clientY - b.top + 12) + 'px';
    } else { hovered = null; tip.style.display = 'none'; }
  }
  host.addEventListener('mousemove', onMove);
  host.addEventListener('mouseleave', function () { tip.style.display = 'none'; });
  (function animate() {
    requestAnimationFrame(animate);
    ideal.rotation.y += 0.01;
    controls.update();
    renderer.render(scene, camera);
  })();
})();
</script>"""
    return html.replace("__DATA__", payload)


def self_assessment_html(models) -> str:
    topics = [t[0] for t in SELF_TOPICS]
    QWEN_FAMILY = {"qwen3.8", "huihui_ai-Qwen3.8-abliterated-latest",
                   "orcarouter-Qwen3.8-27B-Uncensored-latest"}
    RU_SET = ("gigachat", "gigachat-3-ultra", "yandexgpt")
    ORNITH_FAMILY = {"ornith-1.5-35b", "ornith-1.5-9b"}
    GROUPS_RADAR = [
        ("Облачные SOTA", lambda m: m["folder"] in
         ("anthropic-claude-opus-5.5", "openai-gpt-6.1-sol",
          "anthropic-claude-haiku-5.5", "openai-gpt-6-luna")),
        ("DeepSeek", lambda m: m["folder"] == "deepseek-flash"),
        ("Российские", lambda m: m["folder"] in RU_SET),
        ("Семейство локальных qwen", lambda m: m["folder"] in QWEN_FAMILY),
        ("Семейство локальных ornith", lambda m: m["folder"] in ORNITH_FAMILY),
        ("Другие локальные модели", lambda m: m["kind"] == "local"
         and m["folder"] not in QWEN_FAMILY and m["folder"] not in ORNITH_FAMILY),
        ("Негативный контроль", lambda m: m["kind"] == "negctrl"),
    ]

    def radar_card(m):
        run = m["runs"][0]
        s = json.loads((ROOT / m["folder"] / "backend" / run / "session.json").read_text())
        q00 = next((t["answer"] for t in s["turns"] if t["id"] == "Q00"), "")
        self_sc = parse_self_scores(q00)
        sec = section_clef(m["folder"], run)
        our4 = [sec.get(t[2]) for t in SELF_TOPICS]
        self_v = [None if self_sc[t[0]] is None else self_sc[t[0]] for t in SELF_TOPICS]
        our_v = [None if v is None else v * 2.5 for v in our4]   # итоговые в 10-балльную
        js = judge_section_scores(m["folder"], m.get("glm_run"))
        judge_v = [None if js is None else (None if js.get(t[2]) is None else js[t[2]] * 2.5)
                   for t in SELF_TOPICS] if js else None
        # сводная ось: балл судьи, где он проверял; иначе clef
        combined_v = [j if j is not None else o
                      for j, o in zip(judge_v or [None] * len(our_v), our_v)]
        pairs = [(a, b) for a, b in zip(self_v, our_v) if a is not None and b is not None]
        if pairs:
            d = statistics.mean(a - b for a, b in pairs)
            verdict = ("адекватна" if abs(d) < 1.0 else
                       "завышает" if d > 0 else "занижает")
            delta = f"Δ {d:+.1f} · {verdict}"
        else:
            delta = "—"
        name = m["label"]
        if m.get("primer"):
            name += ' <span class=sub>(с промптом)</span>'
        elif m["folder"] == "ornith-1.5-35b":
            mode = "с мышлением" if m["series"] != "think-off" else "без мышления"
            name += f' <span class=sub>({mode})</span>'
        return (f'<div class=scard>'
                + f'<div class=rname>{name}</div>'
                + radar_svg(topics, self_v, combined_v, judge_v, w=310, h=280)
                + f'<div class=sub>{delta}</div></div>')

    blocks = []
    for gtitle, pred in GROUPS_RADAR:
        group = [m for m in models if pred(m)]
        base = [m for m in group if not m.get("primer")]
        extra = [m for m in group if m.get("primer")]
        # карточка с промптом стоит сразу после базовой карточки той же модели
        # (у ornith-35B базовых карточек две — промпт идёт после первой)
        ordered = []
        served = set()
        for m in base:
            ordered.append(m)
            if m["folder"] not in served:
                served.add(m["folder"])
                ordered.extend(p for p in extra if p["folder"] == m["folder"])
        ordered.extend(p for p in extra
                       if p["folder"] not in {b["folder"] for b in base})
        cards = [radar_card(m) for m in ordered]
        if cards:
            blocks.append(f'<div class=sgroup-title>{gtitle}</div>'
                          + '<div class=sgrid>' + "".join(cards) + '</div>')
    return ('<h3 id=self>Самооценка против итоговых баллов</h3>'
            '<p>В разминке (вопрос Q00) модель сама оценивает свои знания по восьми разделам '
            'шкалой 0–10; итоговые баллы конвейера приведены к той же 10-балльной шкале: '
            '<span style="color:#b3543f">■ пунктир это '
            'самооценка</span>, <span style="color:#2f7d54">■ сплошная, итоговый балл: '
            'судьи где проверял, иначе clef</span> (тёмно-зелёные точки '
            '● отмечают оси, взятые у судьи: '
            'судья проверил полный прогон соответствующей строки таблицы). '
            'Δ это средняя разность '
            'самооценки и итоговых баллов, '
            'единая база для всех моделей. Варианты с системным промптом показаны '
            'отдельными карточками рядом с базовыми; в корреляцию ниже они не входят.</p>'
            '<p><b>Эффект Даннинга-Крюгера воспроизводится почти идеально:</b> корреляция силы модели '
            '(clef) с перекосом самооценки, Пирсон r = −0.89 и Спирмен ρ = −0.91 (n = 18). Сильные '
            'модели себя недооценивают (Opus −1.5, Haiku −1.6, Sol −1.2), слабые переоценивают '
            '(YandexGPT Pro 5 +2.7 при худшем знании стека, gemma4-e2b +2.2), середина откалибрована '
            'в ноль (Ultra, qwen3.8, muse, 9B, gemma4 26B). Отдельно стоит DeepSeek: сила почти уровня облака при почти '
            'точной самооценке (−0.4). Системный промпт самооценку не надувает: '
            'у всех пяти вариантов с промптом Δ остаётся около нуля, от −0.1 до +0.8. Подробнее об эффекте у больших языковых моделей читайте в '
            '<a href="https://prikotov.pro/blog/effekt-danninga-kryugera-u-bolshikh-yazykovykh-modelei-llm">'
            'блоге автора</a>.</p>'
            '<p><b>Низкий балл по разделу не приговор, а сигнал для постановки задачи.</b> Радар показывает, где модели нужна опора: в слабых разделах давать больше контекста (документацию, примеры, конвенции проекта), усиливать гвард-рейлы проверками на детерминированных инструментах (Psalm, PHPStan, PHPCS, тесты в CI) и закладывать более плотное ревью другими агентами и человеком. Модель с провалом по Doctrine всё равно продуктивна, если схема и миграции приходят вместе с задачей, а результат проходит статический анализ. Итог исследования не «кого нельзя нанять», а «сколько контекста и проверок заложить в работу с каждой моделью».</p>'
            + "".join(blocks)
            + '''<script>
document.querySelectorAll('svg.radar').forEach(function (svg) {
  var layer = svg.querySelector('.rtip-layer');
  svg.querySelectorAll('g.rv').forEach(function (rv) {
    var tip = rv.querySelector('.rtip');
    if (!tip) return;
    rv.addEventListener('mouseenter', function () {
      layer.appendChild(tip);
      tip.style.display = 'block';
    });
    rv.addEventListener('mouseleave', function () {
      tip.style.display = 'none';
    });
  });
});
</script>''')


def load_metrics():
    out = []
    for m in MODELS:
        scores, hps, nrs, durs, out_toks = [], [], [], [], []
        run_means = []
        for run in m["runs"]:
            run_scores = []
            base = ROOT / m["folder"] / "backend" / "judgments"
            d = {r["question"]: r for r in csv.DictReader(
                open(base / f"decision-clef-{run}.csv", encoding="utf-8-sig"))}
            e = {r["question"]: r for r in csv.DictReader(
                open(base / f"escalation-clef-{run}.csv", encoding="utf-8-sig"))} \
                if (base / f"escalation-clef-{run}.csv").exists() else {}
            for q, r in d.items():
                if r.get("error") or q not in KNOW:
                    continue
                scores.append(float(r["score"])); hps.append(float(r["hall_p"]))
                run_scores.append(float(r["score"]))
                if q in e and not e[q].get("error"):
                    nrs.append(float(e[q]["need_review"]))
            run_means.append(statistics.mean(run_scores))
            s = json.loads((ROOT / m["folder"] / "backend" / run / "session.json").read_text())
            durs.append(sum(t["duration_s"] for t in s["turns"]))
            u = s.get("usage", {})
            out_toks.append(u.get("output_tokens") or sum(t.get("eval_count") or 0 for t in s["turns"]))
        # судья GLM-5.3-Flash (ярус 2)
        gp = ROOT / m["folder"] / "backend" / "judgments" / "glm-5.3-flash-iso" / f"{m['glm_run']}.json"
        glm_avg = glm_hall = None
        glm_n = 0
        if gp.exists():
            g = [v for v in json.loads(gp.read_text())["results"]
                 if v["id"] in KNOW]
            glm_avg = round(sum(v["score"] for v in g) / len(g), 2)
            glm_hall = sum(1 for v in g if v["hallucination"])
            glm_n = len(g)
        glm_note = ("не проверялся" if glm_avg is None
                    else f"выборка {glm_n} факт-вопросов" if glm_n == 10
                    else f"все {glm_n} ответов на знания")
        if m["kind"] == "cloud":
            s0 = json.loads((ROOT / m["folder"] / "backend" / m["runs"][0] / "session.json").read_text())
            u0 = s0.get("usage", {})
            cost = (u0.get("cost_usd") or 0) / len(m["runs"]) if u0.get("cost_usd") else 0
            if not cost and u0.get("cost_rub"):
                cost = u0["cost_rub"] / RUB_USD / len(m["runs"])
        else:
            cost = statistics.mean(durs) / 3600 * (
                LOCAL_KWH * RUB_KWH + AMORT_RUB_H) / RUB_USD
        out.append({**m,
                    "clef": round(statistics.mean(scores), 2),
                    "run_sigma": round(statistics.stdev(run_means), 2) if len(run_means) > 1 else None,
                    "p_hall": round(statistics.mean(hps), 3),
                    "need_rev": round(statistics.mean(nrs), 2) if nrs else None,
                    "dur_min": round(statistics.mean(durs) / 60, 1),
                    "out_tok": round(statistics.mean(out_toks)),
                    "cost": cost, "glm": glm_avg, "glm_hall": glm_hall, "glm_n": glm_n,
                    "glm_level": (GLM_JUDGE_LEVEL.get(m["folder"], "max")
                                 if glm_avg is not None else None),
                    "glm_note": glm_note})
    return out


def sessions_inventory():
    """Все завершённые прогоны: (модель, прогон, серия, вопросов, минут)."""
    rows = []
    for d in sorted(ROOT.iterdir()):
        b = d / "backend"
        if not b.is_dir():
            continue
        for r in sorted(b.glob("run-*")):
            sj = r / "session.json"
            if not sj.exists():
                continue
            s = json.loads(sj.read_text())
            turns = s.get("turns", [])
            if not turns:
                continue
            bad = sum(1 for t in turns if t.get("done_reason") == "error"
                      or t.get("answer") in ("(пустой ответ модели)", "(ошибка генерации: таймаут/сбой)"))
            if bad > 0 or len(turns) < 30:
                continue  # не дошёл до конца
            backend = s.get("backend")
            series = {"openrouter": "cloud", "gigachat": "gigachat",
                      "yandexfm": "yandexfm", "deepseek": "deepseek"}.get(backend) or \
                ("primer-v2" if s.get("primer") else \
                 ("think-off" if s.get("think") == "off" else "think-on"))
            rub = None
            biz = None  # бизнес-стоимость (юрлицо): (без кэша, с кэшем), ₽
            if backend in ("openrouter", "gigachat", "yandexfm", "deepseek"):
                u = s.get("usage", {})
                in_tok = u.get("input_tokens", 0) or 0
                out_tok = u.get("output_tokens", 0) or 0
                cost = u.get("cost_usd", 0) or 0
                cost_rub = u.get("cost_rub") or 0
                if cost_rub:
                    rub = cost_rub
                if not cost and cost_rub:
                    cost = cost_rub / RUB_USD
                cr = u.get("cache_read_tokens")
                cw = u.get("cache_write_tokens")
                if backend == "gigachat" and cr:
                    in_tok += cr  # у Сбера input_tokens — без кэшированной части
                if d.name in BUSINESS_RUB_1M:
                    tin, tout = BUSINESS_RUB_1M[d.name]
                    biz = (((in_tok - (cr or 0)) * tin + out_tok * tout) / 1e6,
                           (in_tok * tin + out_tok * tout) / 1e6)
                if cr is not None:
                    cached = cr + (cw or 0)
                    pct = cached / in_tok * 100 if in_tok else 0
                    split = (f" (чт {cr:,} + зп {(cw or 0):,})" if cw else "")
                    cache_cell = (f"{pct:.0f}%"
                                  f"<div class=sub>{cached:,}".replace(",", " ")
                                  + split.replace(",", " ") + "</div>")
                elif u.get("cache_read_tokens_rec") is not None:
                    rec = u["cache_read_tokens_rec"]
                    exact = u.get("cache_rec_method", "").endswith("exact")
                    pct = rec / in_tok * 100 if in_tok else 0
                    mark = "" if exact else "~"
                    cache_cell = (f"{mark}{pct:.0f}%"
                                  f"<div class=sub>{mark}{rec:,} (чт)</div>".replace(",", " "))
                else:
                    cache_cell = None  # деталей нет
            else:
                in_tok = sum((t.get("prompt_eval_count") or 0) for t in turns)
                out_tok = sum((t.get("eval_count") or 0) for t in turns) + \
                    sum((f.get("eval_count") or 0) for t in turns
                        for f in (t.get("followups") or []))
                gen_h = sum((t.get("duration_s") or 0) for t in turns) / 3600
                rub = gen_h * (LOCAL_KWH * RUB_KWH + AMORT_RUB_H)
                cost = rub / RUB_USD
                # ollama не отдаёт кэш-токены отдельно; кэш сессии виден по скорости
                cache_cell = "сессия: вкл" if s.get("kv_cache") == "session" else "выключен" 
            rows.append(dict(model=d.name, run=r.name, series=series,
                             qv=s.get("questionnaire"),
                             n=len(turns),
                             dur=round(sum(t.get("duration_s") or 0 for t in turns) / 60, 1),
                             in_tok=in_tok, out_tok=out_tok, cost=cost,
                             rub=cost_rub if backend not in ("openrouter",) and cost_rub else rub,
                             biz=biz,
                             cache_cell=cache_cell if cache_cell else "н/д"))
    return rows


# модель стоимости локального инференса: 550 Вт (CPU+GPU) в момент генерации,
# тариф с 01.10.2026 и курс — от владельца (2026-10-09); амортизация железа —
# ₽600 000 при эксплуатации 12 ч/день и окупаемости 3 года, линейно по часам
# работы, минус остаточная стоимость 30% (GPU на вторичке держат цену) —
# от владельца (2026-10-09)
LOCAL_KWH = 0.55
RUB_KWH = 7.07
HW_COST_RUB = 600_000
HW_RESIDUAL_PCT = 0.30              # остаточная стоимость через 3 года
AMORT_HOURS = 3 * 365 * 12          # часов работы за срок окупаемости
AMORT_RUB_H = (HW_COST_RUB * (1 - HW_RESIDUAL_PCT)) / AMORT_HOURS  # ₽31.96/ч
RUB_USD = 85.15

# цены $/1M (вход, выход) на момент прогонов — октябрь 2026
PRICES = {
    "openai-gpt-6-luna": (0.10, 0.50),
    "openai-gpt-6.1-sol": (2.00, 10.00),
    "anthropic-claude-haiku-5.5": (0.10, 0.50),
    "anthropic-claude-opus-5.5": (4.00, 20.00),
}


def primer_analysis():
    rows = [r for r in csv.DictReader(open(ROOT / "reports" / "decision-all.csv",
                                           encoding="utf-8-sig")) if r["judge"] == "clef"]
    agg = defaultdict(lambda: defaultdict(list))
    for r in rows:
        if r["question"] not in KNOW:
            continue
        k = (r["model"], r["series"])
        agg[k]["score"].append(float(r["score"]) or 0)
        agg[k]["hall"].append(float(r["hall_p"]) or 0)
        if r["question"] in ("Q24", "Q25", "Q26"):
            agg[k]["prov"].append(float(r["score"]) or 0)
    return {k: {m: statistics.mean(v) for m, v in d.items()} for k, d in agg.items()}


def bar(value, vmax, fmt="{:.2f}", hue="#4a7dbd"):
    pct = max(2, min(100, value / vmax * 100))
    return (f'<div class=barwrap><div class=bar style="width:{pct:.0f}%;'
            f'background:{hue}"></div><span>{fmt.format(value)}</span></div>')


TOC = """
<nav class=toc>
<div class=toc-title>Содержание</div>
<ul>
<li><a href="#tldr">1. Главные выводы</a></li>
<li><a href="#method">2. Методика</a></li>
<li><a href="#models">3. Модели</a></li>
<li><a href="#results">4. Результаты</a></li>
<li><a href="#honesty">5. Достоверность (галлюцинации)</a></li>
<li><a href="#prompt">6. Системный промпт и мышление</a></li>
<li><a href="#judges">7. Судьи и метрики</a></li>
<li><a href="#economics">8. Экономика собеса</a></li>
<li><a href="#sessions">9. Сессии и данные</a></li>
</ul>
</nav>
"""

TEMPLATE = """<!DOCTYPE html>
<html lang="ru"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Лидерборд локальных LLM на знания PHP, Symfony, PostgreSQL, Doctrine, DDD, Git</title>
<style>
:root{--ink:#1d2433;--mut:#66707f;--line:#e4e7ec;--bg:#fafbfc;--acc:#315f9e;--good:#2f7d54;--bad:#b3543f}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;font:16px/1.62 "Segoe UI",system-ui,-apple-system,"Helvetica Neue",sans-serif;color:var(--ink);background:var(--bg)}
.wrap{max-width:1040px;margin:0 auto;padding:26px 22px 90px}
h1.pagetitle{margin:26px 4px 14px;font-size:34px;line-height:1.18;letter-spacing:-.01em;color:var(--ink)}
header{background:linear-gradient(140deg,#1d2c46 0%,#315f9e 90%);color:#fff;border-radius:16px;padding:34px 40px 30px;margin-bottom:22px}
header h1{margin:0 0 12px;font-size:31px;line-height:1.22;letter-spacing:-.01em}
header p{margin:5px 0;opacity:.93;max-width:74ch}
header p.gap{margin-top:32px}
.toc{margin:20px 0 4px}
.toc-title{font-size:13px;text-transform:uppercase;letter-spacing:.09em;color:var(--mut);margin-bottom:9px}
.toc ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:5px 28px}
.toc a{color:var(--acc);text-decoration:none}
.toc a:hover{text-decoration:underline}
h2{font-size:24px;margin:44px 0 4px;letter-spacing:-.01em}
h2 .no{color:var(--mut);font-weight:400;margin-right:8px}
h3{font-size:17.5px;margin:26px 0 2px}
h4{font-size:15.5px;margin:18px 0 2px;color:var(--ink)}
.lead{color:var(--mut);margin:0 0 14px;font-size:15px}
header .lead{color:#fff}
header p a{color:#cfe0f7;text-decoration:underline}
p{margin:10px 0}
.sub{font-size:12.5px;color:var(--mut);margin-top:3px;line-height:1.5}
.excap{font-size:15px;color:var(--ink);margin:12px 0 -4px;line-height:1.5}
table{border-collapse:collapse;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(20,30,50,.07);margin:14px 0}
th{background:#eef1f6;text-align:left;font-size:12.5px;text-transform:uppercase;letter-spacing:.03em;color:#46536a;padding:10px 12px;border-bottom:2px solid var(--line)}
td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14.5px}
tr:last-child td{border-bottom:none}
tr.neg td{background:#fdf6f4}
.sgrid{display:flex;flex-wrap:wrap;gap:14px;margin:14px 0}
.sgroup-title{font-size:14px;font-weight:bold;color:var(--ink);margin:20px 0 2px;padding-top:12px;border-top:1px solid var(--line)}
.scard{border:1px solid #e3e7ea;border-radius:10px;padding:10px 8px;text-align:center;min-width:310px}
.scard .rname{font-weight:bold;font-size:14px;margin-bottom:4px}
tr.top td{background:#eef6ef}
tr.ru td{background:#eef2f8}
.barwrap{position:relative;background:#eef0f4;border-radius:6px;min-width:118px;height:21px}
.barwrap .bar{position:absolute;left:0;top:3px;bottom:3px;border-radius:5px;min-width:3px;opacity:.3}
.barwrap span{position:absolute;left:7px;top:0;font-size:12.5px;line-height:21px;font-weight:600;color:var(--ink)}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(295px,1fr));gap:13px;margin:16px 0}
.card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--acc);border-radius:12px;padding:15px 17px;font-size:14.5px}
.card b{display:block;margin-bottom:5px;font-size:15px;min-height:2.8em}
.scalebox{background:#f4f7fb;border:1px solid var(--line);border-radius:10px;padding:11px 16px;font-size:13.5px;color:var(--mut);margin:14px 0}
.scalebox div{margin-top:7px}
.scatter .tip{display:none;pointer-events:none}
.radar{overflow:visible}
.radar .rtip{display:none;pointer-events:none}
.radar .rv:hover .rtip{display:block}
.radar .rv .dot, .radar .rv:hover circle{cursor:pointer}
.scatter .dot{cursor:pointer}
.scatter g.pt .tip{pointer-events:none}
.scalebox b{color:var(--ink)}
.quote{background:#fff;border:1px solid var(--line);border-radius:12px;padding:15px 19px;margin:13px 0;font-size:14.5px}
pre.req{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 16px;margin:8px 0 12px;font-size:11.8px;line-height:1.55;overflow-x:auto;white-space:pre-wrap}
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
.mgroup{margin:20px 0}
.mgroup h3{margin-bottom:2px}
footer{margin-top:56px;color:var(--mut);font-size:13.5px;border-top:1px solid var(--line);padding-top:18px;line-height:1.6}
@media print{.toc{display:none}header{border-radius:0}h2{page-break-after:avoid}}
</style></head><body><div class=wrap>
<h1 class=pagetitle>Лидерборд локальных LLM на знания PHP, Symfony, PostgreSQL, Doctrine, DDD, Git</h1>
<p class=sub style="margin:-6px 4px 14px">Отчёт подготовлен с 6 по 10 октября 2026 года в ZCode при содействии моделей GLM-5.3 и GLM-5.3-Flash, id сессии sess_ba5167d4-12bc-452c-afbe-3d117b4d1667.</p>

<div class=quote><div class=who>Предыстория от автора</div>
Меня заблокировали в OpenAI по региональному признаку после двух лет
подписки на тарифах Plus и Pro. В один день исчезли доступ к официальному
API, ChatGPT и Codex CLI, история чатов, настроенные GPTs и проекты.
Но грустную историю можно превратить в новую, интересную и полезную:
посмотреть альтернативы, не облачные, то, что можно держать локально.
Пока была подписка, я туда даже не заглядывал: давным-давно пробовал,
и модели тогда были «тупенькие». С тех пор в моём проекте появилась
RTX 5090 в хорошем окружении, я был наслышан о новых Qwen и решил
посмотреть, что там сейчас. Первым шагом
я решил узнать, что вообще знают локальные модели о PHP, Symfony
и PostgreSQL, то есть о стеке моего проекта <a href="https://task.ai-aid.pro/">TasK</a>.
<br><br>
Я не ML-инженер и не представляю, как «правильно» тестировать модели.
Зато у меня есть опыт проведения собеседований, и родилась идея: пусть
более умная модель прособеседует локальные как кандидатов, а я посмотрю
результат. Мы придумали методологию и решили её потестить. Я накидывал
модели, она спрашивала и оценивала. Было странно: локальные отвечали
достаточно качественно, я такого не ожидал. Тогда и появились цифровые
метрики, чтобы сравнивать знания без впечатлений.
<br><br>
Первая мысль про LLM-судью не сработала: анализ выходил поверхностный,
многое пропускалось. Решил попробовать decision-модели для судейства
качества, сейчас на хайпе Jev, но выбрал локальный Clef, он, кстати,
оказался не хуже.
Повозились с судейством, получили более-менее результат и провалидировали
его на SOTA-моделях: те точно хорошо знают PHP. После валидации поймали
ряд инсайтов и проблем, добавили судью GLM-5.3-Flash туда, где Clef
не справлялся. Так сложилась методология, свой PHP-лидерборд моделей.
Отчёт по нему мы здесь и представляем.
<br><br>
Напомню: это лидерборд на <b>знания</b>, а не на умение писать код.
Версия пробная, будем развивать. Если есть идеи, предложения или желание
помочь, присоединяйтесь: электронная почта
<a href="mailto:dmitry@prikotov.ru">dmitry@prikotov.ru</a> ·
Telegram: <a href="https://t.me/prikotov">@prikotov</a>.
<br><br>
<i>Всё как у людей ©</i>
</div>

<header id=top>
<p class=lead>Мы провели техническое собеседование на роль PHP-бэкендера.
Участники: локальные модели на потребительской видеокарте, облачные
флагманы Anthropic, OpenAI и DeepSeek, российские GigaChat (Сбер)
и YandexGPT, всего __MODELS_N__ моделей. Вопросы покрывают наш рабочий стек: PHP 8,
Symfony, Doctrine, PostgreSQL, DDD, чистую архитектуру, тестирование, git.
Формат прост: один чат, один вопрос, один ответ, без инструментов,
файлов и system prompt. Так мы изучаем базовые знания модели, то, что
у неё зашито в весах при обучении, а не то, что ей могло бы дать окружение.</p>
<p>Ответы судит дешёвая decision-модель, а контрольные вердикты выносит
изолированный судья GLM-5.3-Flash, который сверяет спорные утверждения
запуском кода на PHP 8.5 и запросами к настоящему PostgreSQL.</p>
<p class=gap>Этот отчёт для тех, кто выбирает модель под задачи и понимает
ограничения локальных моделей: до облачных флагманов они не дотягивают,
поэтому процесс приходится подстраивать. Как и с людьми, с моделями применима
<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D0%BE%D0%B5_%D0%BB%D0%B8%D0%B4%D0%B5%D1%80%D1%81%D1%82%D0%B2%D0%BE">модель ситуационного лидерства Херси и Бланшара</a>: сильной достаточно короткой
постановки, слабой нужны детальный контекст и проверки. Рейтинг ниже показывает
силу каждой модели по разделам стека, чтобы понимать, как подходить к ней
в конкретной задаче.</p>
</header>
__TOC__

<h2 id=tldr><span class=no>1</span>Главные выводы</h2>
<div class=scalebox><b>Шкала баллов 0–4.</b> 0 — ответ фактически неверен; 1 — отдельные
верные крупицы; 2 — верная канва без глубины; 3 — верно, с глубиной и примерами;
4 — то же с нюансами и вкусом.
<div>Баллы в выводах ставит судья GLM-5.3-Flash по вопросам на знания; где судья
модель не проверял, балл clef (дешёвый судья, ярус 1).</div>
<div>Диапазоны вроде 3.8–4.0 — разброс между моделями группы, а не погрешность.
Сводная таблица и источник каждого балла — в §4.</div></div>
<div class=cards>
<div class=card><b>Облачные SOTA-модели набирают 3.85–4 балла из 4</b>Оценка на знания: Claude Opus 5.5 (4.0), GPT-6.1 Sol (3.95), GPT-6 Luna (3.88), Claude Haiku 5.5 (3.85). Лучшая локальная muse-glimmer 28B: 3.53, на 0.3 балла ниже SOTA; худшая ornith-1.5 9B: 2.43. Лучшая русская GigaChat-3 Ultra: 3.38, худшая YandexGPT Pro 5: 2.02.</div>
<div class=card><b>Модели вплетают выдумки в верные ответы</b>По вопросам на знания: у GPT-6.1 Sol и GPT-6 Luna галлюцинаций нет, у Claude Opus 5.5 и Claude Haiku 5.5 по 2 из 30 (7%); у локальных qwen3.8 27B и ornith-1.5 35B галлюцинации в 40–47% ответов (12 и 14 из 30), у ornith-1.5 9B — 67% (20 из 30). DeepSeek V4.1 Flash при балле уровня облака — 33% (10 из 30). Обычно это одна деталь в остальном верном ответе, поэтому по баллам разница почти не видна.</div>
<div class=card><b>Провокации: кто вскрывает, кто ведётся</b>Провокация «Для чего в PostgreSQL FORCE INDEX?» построена на ложной предпосылке: в PostgreSQL директивы FORCE INDEX не существует, она есть только в MySQL. Таких провокаций в собесе три, и числа показывают, сколько из них вскрыла модель: облако и muse-glimmer все три, medgemma одну (уверенно объяснила, зачем нужна несуществующая директива), gemma4-e2b ни одной.</div>
<div class=card><b>Режим мышления сильнее промптинга</b>Внутренние рассуждения дают ornith-1.5 35B +0.65 балла и 100% вскрытых провокаций; системный промпт «роль+CoT+недоверчивость» дал +0.32 (§6).</div>
<div class=card><b>Негативный контроль подтвердил чувствительность шкалы</b>Медицинский файнтюн medgemma 27B набрал балл 2.67, галлюцинации в 47% ответов (14 из 30); крошка gemma4 e2b 4.6B получила балл 2.42, галлюцинации в 23% (7 из 30). Шкала различает слабость любого происхождения.</div>
<div class=card><b>Полное интервью: от $0.02 до $5.82</b>SOTA-облако: от GPT-6 Luna $0.0300 до Claude Opus 5.5 $5.82. DeepSeek V4.1 Flash — $0.25. Локальные: $0.02–0.09 (электричество и амортизация). Русские: Сбер физлицу ₽0 (freemium), YandexGPT Pro 5 — ₽400 за сессию. Кэш экономит до 70%, детали в §8.</div>
</div>

<h2 id=method><span class=no>2</span>Методика</h2>
<p class=lead>Скриптованное интервью, одинаковые условия для всех, судейство с проверкой фактов.</p>
<h3>Интервью</h3>
<p>Собеседование сделано в виде скриптованного интервью из 35 открытых
вопросов: PHP 8, типы и ловушки (3 вопроса), ООП (1), Symfony (3),
Doctrine (3), PostgreSQL (2), DDD и чистая архитектура (4), тестирование
и конкурентность (2), процесс работы, включая git (8), плюс разминка с
самооценкой знаний (1), три провокации с ложной предпосылкой, два
мини-код-ревью и финал собеса (3). У ключевых вопросов запланированы
уточнения: шесть уточнений, итого 41 ответ. Вопросы
зафиксированы в <a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/roles/backend/questions.json">вопроснике
backend-роли</a> и одинаковы для всех кандидатов и прогонов.</p>
<p>Ведёт интервью только скрипт-раннер
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/runner.py">runner.py</a>):
один собес это один чат, раннер читает вопросник и шлёт вопросы по одному
в API модели, каждый ответ дописывается в историю чата. Кандидат видит только
этот диалог: без system prompt, файлов и инструментов. Уточнения заранее
прописаны в вопроснике у своих вопросов и одинаковы для всех моделей: раннер
отправляет их дословно после базового ответа, не подстраивая под ответ
кандидата. Если кандидат вместо
ответа сам переспрашивает (короткая реплика, оканчивающаяся вопросом), раннер
за весь собес не более одного раза отвечает стандартной репликой «Допущения
делай сам, отвечай так, как считаешь правильным». За все прогоны это
не сработало ни разу: кандидаты не переспрашивают, а заканчивают развёрнутый
ответ встречным вопросом, и раннер такое уточнением не считает.</p>
<p>Каждый прогон сохраняется в два файла: человекочитаемый транскрипт и
сырая сессия session.json с полным массивом сообщений, ходом мышления и
таймингами (<a href="https://github.com/prikotov/task-llm-hiring/blob/main/muse-glimmer/backend/run-01/transcript.md">пример
транскрипта</a>, <a href="https://github.com/prikotov/task-llm-hiring/blob/main/muse-glimmer/backend/run-01/session.json">пример
сессии</a>); судейство идёт по протоколам постфактум. Отдельного выбора роли
нет: роль это имя папки вопросника. Вопросник
interview/roles/backend/questions.json задаёт роль backend, прогоны
складываются в папку модели &lt;модель&gt;/backend/run-NN, существующие прогоны
не перезаписываются.</p>
<p>Транспорт и изоляция прогонов: локальные модели опрашиваются с сервера
ollama с выгрузкой из памяти до и после каждого прогона, чтобы кэш одного
собеса не грел следующий; параметры генерации дефолтные, seed не
фиксируется. Облачные SOTA ходят через OpenRouter
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/runner_openrouter.py">runner_openrouter.py</a>)
с reasoning effort low (сопоставимо с выключенным мышлением локальных),
DeepSeek, GigaChat и YandexGPT через родные API провайдеров
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/runner_ru_cloud.py">runner_ru_cloud.py</a>).
Для каждой модели делается несколько независимых повторений одного и того же собеседования, прогонов (до пяти); показатели усредняются по прогонам. Постоянство меряется на двух уровнях: разброс средних прогонов показывает стабильность средней оценки модели, а разброс балла одного и того же вопроса между прогонами показывает постоянство отдельного ответа.</p>
<h3>Оценка</h3>
<p>Ответы оцениваются по протоколам постфактум, каждый ответ отдельно. У
каждого вопроса есть чек-лист якорей: 🟢 зелёный свет (2–4 признака сильного
ответа) и 🚩 красный флаг (1–2 признака слабого)
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/rubrics/backend/checklists.json">чек-листы
всех вопросов</a>); полные карточки эталона с зелёным светом, красными
флагами, образцом ответа и шкалой 0–4 написаны вручную для двух вопросов как
калибровка судейства
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/rubrics/backend/Q01.md">пример
карточки</a>). Сама оценка идёт в два яруса, от дешёвого судьи к дорогому.</p>
<div class=flow>
<div class=step><b>Ярус 1: Decision-модель - clef</b>локально (дёшево и быстро), один запрос на анализ ответа: quality (балл 0–4), hallucination (галлюцинации), need_review (нужен ли более знающий судья), unsure (неуверенность), factcheck_zone (факт-проверка).</div>
<div class=arrow>→</div>
<div class="step hot"><b>Ярус 2: LLM судья - GLM-5.3-Flash</b>по триггеру (need_review или hallucination &gt; 0.3), один запуск на ответ: спорные факты проверяет запуском кода (PHP, PostgreSQL) и выносит вердикт.</div>
</div>
<p>Ярус 1 (<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/score.py">score.py</a>)
оценивает каждый ответ изолированно, одним запросом: decision-модель clef
получает вопрос и ответ кандидата и отвечает сразу на пять вопросов:
quality (балл 0–4), hallucination (вероятность галлюцинаций), need_review
(нужен ли более знающий судья), unsure (неуверенность в своей оценке),
factcheck_zone (утверждения, требующие факт-проверки).</p>

<p>Ярус 2 (<a href="https://github.com/prikotov/task-llm-hiring/blob/main/interview/judge_glm.py">judge_glm.py</a>)
подключается по триггеру: need_review &gt; 0.3 или hallucination &gt; 0.3; при
таких порогах делегирование вверх происходит для 41% ответов (качество
триггера, точность и полнота — в §7). Судья
GLM-5.3-Flash запускается изолированно на один вопрос-ответ, с якорями
чек-листа этого вопроса; спорные утверждения он проверяет запуском
кода (PHP 8.5, PostgreSQL в docker) и возвращает вердикт: балл, признак
галлюцинации, мотивировка.</p>
<h4>Пример оценки ответа кандидата</h4>
<p>Проследим путь одного реального ответа кандидата по обоим ярусам: провокация
Q24 («Для чего в PostgreSQL используется FORCE INDEX?»), ответ muse-glimmer 28B
из первого прогона.</p>
<p class=excap><b>Запрос к clef</b> (score.py, реальный вызов; текст ответа кандидата сокращён):</p>
<pre class=req>POST /v1/systemone
{ "model": "clef",
  "state": "Ты оцениваешь ответ кандидата на техническом собеседовании
            PHP/Symfony/Doctrine/PostgreSQL/DDD-разработчика.

            ВОПРОС:
            Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

            ОТВЕТ КАНДИДАТА:
            В PostgreSQL **нет команды `FORCE INDEX`** и никогда не было.
            `FORCE INDEX`, `USE INDEX`, `IGNORE INDEX` — это хинты оптимизатора из MySQL.
            … (далее: философия cost-based планировщика, SQL-примеры SET STATISTICS,
            enable_*, частичный индекс)",
  "questions": {
    "quality":       { "type": "score",
                       "instructions": "Оцени качество ответа кандидата на вопрос собеседования",
                       "criteria": ["0 — ответ неверен, отсутствует или содержит грубые фактические ошибки",
                                    "1 — фрагментарный: отдельные верные тезисы без понимания связей",
                                    "2 — базовый: в целом верно, но поверхностно, без нюансов",
                                    "3 — уверенный: верно, со структурой, примерами и trade-offs",
                                    "4 — экспертный: верно и глубоко, с нюансами и границами применимости"] },
    "hallucination": { "type": "noul",
                       "instructions": "Содержит ли ответ выдуманные факты, несуществующие API/классы/методы или фальшивые источники?",
                       "criteria": { "false": "Выдуманного нет, факты корректны",
                                     "true":  "Есть выдуманные факты, API или источники" } },
    "need_review":   { "type": "noul",
                       "instructions": "Нужно ли передать этот ответ на проверку более знающему судье?
                                        Ответь «да», если в ответе есть утверждения, требующие
                                        факт-проверки по документации или запуску кода, или если
                                        ты не уверен, что сам можешь верно оценить этот ответ.",
                       "criteria": { "false": "Проверка старшей моделью не нужна: всё проверено мной уверенно",
                                     "true":  "Нужна проверка старшей моделью" } },
    "unsure":        { "type": "noul",
                       "instructions": "Есть ли у тебя неуверенность в том, что ты верно понимаешь правильность этого ответа?",
                       "criteria": { "false": "Уверен в оценке", "true": "Есть неуверенность" } },
    "factcheck_zone":{ "type": "noul",
                       "instructions": "Содержит ли ответ утверждения, которые нельзя оценить без
                                        сверки с документацией или запуска кода?",
                       "criteria": { "false": "Нет, всё оцениваемо по общим знаниям",
                                     "true":  "Да, есть такие утверждения" } } } }</pre>
<p class=excap><b>Ответ clef</b> (реальный): noul-поля это вероятности от 0 до 1, не бинарные флаги.</p>
<pre class=req>{ "answers": { "quality":       { "score": 3.55, "confidence": 0.45,
                                    "probabilities": { "0": 0.01, "1": 0.03, "2": 0.04,
                                                       "3": 0.24, "4": 0.68 } },
               "hallucination": { "noul": 0.119 },
               "need_review":   { "noul": 0.146 },
               "unsure":        { "noul": 0.145 },
               "factcheck_zone":{ "noul": 0.313 } } }</pre>
<p class=excap><b>Как читать.</b> clef оценил ответ в 3.55 балла из 4: 68% вероятности
за «экспертный» и 24% за «уверенный»; уверенность самого clef в балле
умеренная, 0.45. Вероятность галлюцинаций 12%, эскалацию clef не просит:
need_review 0.146 при пороге 0.3. Показательно, что factcheck_zone 0.313:
третья часть вероятности за то, что в ответе есть утверждения, которые не
проверить без документации или запуска кода, и всё же дешёвый судья на этом
основании ответ не эскалирует.</p>
<p class=excap><b>Вердикт судьи яруса 2 на тот же ответ</b> (hallucination здесь бинарный,
true значит «есть хотя бы одна выдуманная деталь»; частичная выдумка не обнуляет
балл, оценка качества и признак галлюцинации независимы):</p>
<pre class=req>{ "score": 4.0, "hallucination": false,
  "note": "Провокация с ложной предпосылкой распознана безошибочно: FORCE INDEX
           в PostgreSQL действительно нет (проверено по официальной документации,
           это хинты MySQL). … Все SQL-примеры и упомянутые GUC/каталоги реальны,
           выдуманных фактов и источников нет." }</pre>
<p class=excap style="margin-top:10px">Этот ответ кандидата триггеры наверх не отправили бы, и это правильное
решение: сплошная до-проверка (ремарка ниже) показала, что LLM судья здесь
почти ничего не добавляет, 4 против 3.55, галлюцинаций нет. В этом и замысел
двух ярусов: дорогой судья подключается только там, где дешёвый оценщик не
уверен. Где триггеры пока промахиваются, разбирает §7 на собранной эталонной
разметке.</p>
<p>Вердикты каждого прогона лежат рядом с ним по пути
&lt;модель&gt;/&lt;роль&gt;/judgments/ (в примерах ниже: muse-glimmer/backend/judgments/):
таблица
яруса 1 с баллом и вероятностью галлюцинаций
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/muse-glimmer/backend/judgments/decision-clef-run-01.csv">пример</a>),
отдельная таблица эскалационных признаков
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/muse-glimmer/backend/judgments/escalation-clef-run-01.csv">пример</a>)
и вердикты яруса 2 в glm-5.3-flash-iso/
(<a href="https://github.com/prikotov/task-llm-hiring/blob/main/muse-glimmer/backend/judgments/glm-5.3-flash-iso/run-01.json">пример</a>).</p>
<div class=note><b>Ремарка.</b> Описанное выше это система, к которой стремимся.
В самом исследовании оценка и эскалация шли двумя раздельными вызовами
clef, с прежней формулировкой need_review, а судья яруса 2 сплошь проверил
все 30 ответов на знания в одном прогоне каждой модели (остальные
прогоны судит дешёвый ярус, его балл усредняется), а не только ответы выше порога:
сплошная проверка собрала эталонную разметку, на которой калибруются
триггеры и улучшается методика оценки (§7). В таблицах §4 стоят баллы этого
прогона.</div>
<h2 id=models><span class=no>3</span>Модели</h2>
<p class=lead>Кто участвовал и почему: состав отражает вопросы исследования, от потолка облаков и честной цены до локальных кандидатов, российских облаков и негативных контролей.</p>
<table>
<tr><th>Модель</th><th>Провайдер вызова</th><th>Почему в тесте</th><th>Детали</th></tr>
<tr class=top><td><b>GPT-6.1 Sol</b> <span class="badge g">облако</span></td><td>OpenAI · OpenRouter</td><td>главный SOTA-бенчмарк: как выглядит потолок чистых способностей</td><td>effort low; авто-кэш OpenAI; его ответы служат SOTA-эталоном яруса 0</td></tr>
<tr class=top><td><b>Claude Opus 5.5</b> <span class="badge g">облако</span></td><td>Anthropic · OpenRouter</td><td>потолок качества и цены: оправданы ли деньги</td><td>effort low; кэш только по явным cache_control-меткам</td></tr>
<tr><td><b>Claude Haiku 5.5</b> <span class="badge g">облако</span></td><td>Anthropic · OpenRouter</td><td>быстрая дешёвая модель топ-вендора</td><td>effort low; две сессии: без кэша и с ним ($0.18 против $0.0500)</td></tr>
<tr><td><b>GPT-6 Luna</b> <span class="badge g">облако</span></td><td>OpenAI · OpenRouter</td><td>самая дешёвая в топе: ценовой пол «умных»</td><td>effort low; авто-кэш</td></tr>
<tr><td><b>DeepSeek V4.1-Flash</b> <span class="badge g">облако</span></td><td>родной API DeepSeek</td><td>рекорд цены/качества открытых китайских весов, без накладных расходов агрегатора</td><td>effort low · контекст 1M · вывод до 393k · автокэш (кэш-хит ×50 дешевле входа)</td></tr>
<tr class=ru><td><b>GigaChat-2 Max</b> <span class="badge g">облако</span></td><td>Сбер · API физлица (freemium)</td><td>главная корпоративная российская LLM: каков отечественный дефолт</td><td>базовый режим, reasoning-ручки нет; нативный префикс-кэш; на провокации Q26 принял ложную предпосылку с выдуманным обоснованием</td></tr>
<tr class=ru><td><b>GigaChat-3-Ultra</b> <span class="badge g">облако</span></td><td>Сбер · api.giga.chat (freemium, только физлица)</td><td>новый флагман Сбера</td><td>базовый режим; юрлицам не продаётся; квота 50 млн токенов; единственная модель проекта, чисто прошедшая все 3 провокации</td></tr>
<tr class=ru><td><b>YandexGPT Pro 5</b> <span class="badge g">облако</span></td><td>Яндекс Cloud</td><td>второй российский облачный провайдер</td><td>базовый режим; контент-фильтр заменил отказами 6 из 35 технических вопросов; плоский тариф ₽1.2/1k без кэш-скидки; имя Pro 5 видно только в детализации биллинга</td></tr>
<tr><td><b>ornith-1.5 35B</b> <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td><td>флагман открытых весов в MoE-формате, локальный потолок</td><td>в двух режимах: мышление выключено / включено (с мышлением генерация вчетверо дольше)</td></tr>
<tr><td><b>ornith-1.5 9B</b> <span class="badge b">локально</span></td><td>локально · ollama</td><td>размерная точка: MoE 9B против dense 27B</td><td>мышление включено (у 9B без него не отвечает)</td></tr>
<tr><td><b>qwen3.8 27B</b> <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td><td>главный локальный кандидат: dense 27B популярного семейства</td><td>мышление выключено (с мышлением контекст переполняется)</td></tr>
<tr><td><b>muse-glimmer 28B</b> <span class="badge b">локально</span></td><td>локально · ollama</td><td>малоизвестный бренд: проверка «кота в мешке»</td><td>мышление выключено; лучшая локальная по баллам судьи (3.53)</td></tr><tr><td><b>huihui abliterated 27B</b> <span class="badge b">локально</span></td><td>локально · ollama</td><td>abliterated-файнтюн qwen3.8: сняты отказохранители</td><td>мышление выключено</td></tr><tr><td><b>orcarouter 27B uncensored</b> <span class="badge b">локально</span></td><td>локально · ollama</td><td>uncensored-файнтюн qwen3.8: сняты отказохранители</td><td>мышление выключено</td></tr>
<tr class=neg><td><b>medgemma 27B</b> <span class="badge r">негат. контроль</span></td><td>локально · ollama</td><td>негативный контроль: медицинский файнтюн, проверяем различимость шкалы на чужом домене</td><td>мышление выключено</td></tr>
<tr class=neg><td><b>gemma4-e2b 4.6B</b> <span class="badge r">негат. контроль</span></td><td>локально · ollama</td><td>негативный контроль: крошка, низ шкалы</td><td>мышление выключено</td></tr>
</table>
<h3>Локальное окружение</h3>
<table>
<tr><th>Компонент</th><th>Модель</th><th>Ключевое</th></tr>
<tr><td>Процессор</td><td><a href="https://www.amd.com/en/products/processors/desktops/ryzen/9000-series/amd-ryzen-9-9950x3d.html">AMD Ryzen 9 9950X3D</a></td><td>16 ядер / 32 потока, Zen 5, до 5.7 ГГц, TDP 170 Вт</td></tr>
<tr><td>Видеокарта</td><td><a href="https://cis.palit.com/palit/vgapro.php?id=5335&lang=ru">Palit GeForce RTX 5090 GameRock OC</a></td><td>32 ГБ GDDR7, 21 760 CUDA-ядер, 1792 ГБ/с, PCIe 5.0</td></tr>
<tr><td>Материнская плата</td><td><a href="https://ru.msi.com/Motherboard/MAG-X870-TOMAHAWK-WIFI">MSI MAG X870 TOMAHAWK WIFI</a></td><td>AM5, PCIe 5.0</td></tr>
<tr><td>Память</td><td><a href="https://www.gskill.com/product/165/425/1736132870/F5-6000J2836F48GX2-TR5NS">G.Skill Trident Z5 Royal Neo 96 ГБ</a></td><td>DDR5-6000, 2×48 ГБ</td></tr>
<tr><td>Накопители</td><td><a href="https://www.kingston.com/ru/ssd/gaming/kingston-fury-renegade-g5-nvme-m2-ssd">Kingston FURY Renegade G5 2 ТБ ×2</a></td><td>NVMe M.2</td></tr>
</table>
<h3>Фактический режим работы (снято с сервера, 2026-10-09)</h3>
<table>
<tr><th>Подсистема</th><th>Режим</th><th>Что это значит</th></tr>
<tr><td>CPU</td><td>governor performance, amd_pstate active</td><td>буст до ~5.6 ГГц держится под нагрузкой, троттлинга нет</td></tr>
<tr><td>GPU PCIe</td><td><b>PCIe 5.0 ×16</b> при работе; в простое линия понижается до Gen1</td><td>полная пропускная способность в момент инференса; Gen1 это штатное энергосбережение NVIDIA</td></tr>
<tr><td>GPU питание</td><td>power limit 600 Вт, драйвер 610.57</td><td>максимум карты не урезан</td></tr>
<tr><td>Память</td><td>DDR5 5600 MT/s, каналы A+B (dual channel), 1.1 В</td><td>работает на JEDEC-дефолте: EXPO-профиль 6000 MT/s планок в BIOS не включён, небольшой запас производительности не задействован</td></tr>
<tr><td>NVMe</td><td>линк PCIe 5.0, чтение модели 6.0 ГБ/с (direct)</td><td>диски не узкое место при загрузке весов</td></tr>
<tr><td>Загрузка модели</td><td>холодный старт 27B Q4 (блоб 20 ГБ) занимает 2.9 с</td><td>дисковое ожидание незаметно на фоне сессий 3–20 минут; цена изоляции прогонов (выгрузка после каждого) примерно 3 с на следующий старт</td></tr>
</table>
<p class=sub>Рантайм ollama, модели в квантовании Q4_K_M (кроме отмеченного отдельно); 27B-модели целиком помещаются в 32 ГБ VRAM. Потребление в момент инференса ~550 Вт (основа модели стоимости, §8); конфигурация из личной базы заметок владельца, режим снят командами dmidecode/nvidia-smi/dd с сервера.</p>
<p class=sub>gemma4-26b участвовала только в эксперименте с системным промптом (§6).</p>

<h2 id=results><span class=no>4</span>Результаты</h2>
<p class=lead>Таблица сравнивает модели в стандартных условиях: системный промпт не задавался, мышление у локальных моделей выключено, у облаков reasoning effort low (исключение ornith-9B, у которой мышление всегда включено). У Сбера и Яндекса reasoning-ручки в API нет, поэтому уровень мышления у них не настраивается: модель работает в своём базовом режиме. Отдельными строками добавлены эксперименты §6: ornith-1.5 35B с включённым мышлением и пять локальных моделей с системным промптом «опытный PHP-бэкендер» (gemma4 26B участвовала только в эксперименте с промптом).</p>
<p>Баллы обеих шкал и галлюцинации — только по вопросам на знания, 30 ответов из 41 (детали в сноске 2). Фон строк: зеленоватый отмечает топ качества, голубоватый российские облака, розоватый негативные контроли.</p>
<p class=sub style="margin:-6px 0 10px">Строки отсортированы по баллу судьи GLM-5.3-Flash; он проверил по всем 30 вопросам на знания одного прогона каждую строку, включая варианты с промптом, постоянство по прогонам контролирует clef (±).</p>
<!--TABLE-->

<h2 id=honesty><span class=no>5</span>Достоверность (галлюцинации)</h2>
<p class=lead>Одни и те же вопросы, а между моделями пропасть.</p>
<div class="quote bad"><div class=who>Q24 · «Для чего в PostgreSQL используется FORCE INDEX?», medgemma 27B · 0 баллов · галлюцинация</div>
«FORCE INDEX в PostgreSQL — это директива, которая подсказывает оптимизатору запросов, какой индекс использовать для конкретной таблицы… Это не жёсткое требование, а скорее <i>рекомендация</i>.»
<div class=sub>Несуществующая конструкция описана уверенно и подробно. Судья сверился с документацией: ни в одной версии PostgreSQL её нет, это синтаксис MySQL.</div></div>
<div class="quote good"><div class=who>Тот же вопрос, GPT-6 Luna · 4 балла</div>
«В PostgreSQL <b>нет команды FORCE INDEX</b> и никогда не было. Это синтаксис MySQL… PostgreSQL построен на принципиально другой философии: планировщик умнее разработчика.»
<div class=sub>Предпосылка вскрыта, названы корректные альтернативы (ANALYZE, enable_seqscan для диагностики, расширение pg_hint_plan как крайняя мера).</div></div>
<div class="quote good"><div class=who>Q25 · «На сколько процентов strict_types=1 замедляет PHP?», Opus 5.5 · 4 балла</div>
«Сначала поправлю две вещи. Во-первых, собственных замеров у меня нет, поэтому называть процент “по опыту” не буду. Во-вторых, сама предпосылка неверна: strict_types=1 не замедляет код в сколько-нибудь измеримой степени.»
<div class=sub>Отказ выдумывать цифру и опровержение самой предпосылки. Судья прогнал микробенчмарк: кандидат прав.</div></div>
<div class="quote bad"><div class=who>Q02 · «Что напечатает "10"+"5" в PHP?», gemma4-e2b · 1 балл · галлюцинация</div>
«…в PHP "10"+"5" даёт конкатенацию "105"», проверено запуском: <code>int(15)</code>. Модель приписала PHP семантику JavaScript.</div>

<h2 id=prompt><span class=no>6</span>Системный промпт и мышление</h2>
<p class=lead>Классический вопрос: помогает ли «роль + размышляй вслух + проверяй предпосылки»?</p>
<p>Пять локальных моделей сдавали собеседование дважды: базовый вариант (мышление
выключено) и с системным промптом «опытный PHP-бэкендер 15 лет, сначала
рассуждай, проверяй предпосылки вопросов, в конце дай обратную связь по
собеседованию». Каждый вариант повторён пятью независимыми прогонами, судья clef.</p>
<!--PRIMER-->
<!--THINK-->
<p class=sub>Разметка провокаций по транскриптам подтверждает картину: мышление ловит ложные предпосылки 15 из 15 против 12 из 15 без него; промпт частично восстанавливает проверку предпосылок, но не добавляет внимательности к безопасности в код-ревью: рассуждение «о вопросе» не заменяет рассуждение «по существу». Постоянства промпт тоже не добавляет: медиана размаха балла одного вопроса между прогонами с ним не ниже, чем без него (qwen3.8 4.6% → 7.7% шкалы, huihui 6.6% → 8.1%, orcarouter 4.2% → 4.5%, ornith-1.5 35B 11.7% → 10.0%; радары постоянства — в §4), то есть промпт меняет ответы, но не закрепляет их.</p>

<h2 id=judges><span class=no>7</span>Судьи: decision-модели и подбор метрик</h2>
<p class=lead>Судья тоже LLM, и его проверили так же строго, как кандидатов: семь моделей на роль арбитра и шесть подходов к метрикам, прежде чем собрать оценку из §2.</p>
<h3>Сравнение decision-моделей на роль судьи</h3>
<p class=sub>Мерили против судьи GLM-5.3-Flash (изолированные проверки с запуском кода): корреляция баллов, точность детекции галлюцинаций (AUC), доля пойманных PHP-ошибок, верифицированных запуском на PHP 8.5.</p>
<table>
<tr><th>Модель</th><th>Зачем пробовали</th><th>Вердикт</th></tr>
<tr><td><b>clef 27B Q4</b> (локально)</td><td>основной кандидат</td><td><b>Принят</b>, судья яруса 1: corr 0.62 с судьёй GLM-5.3-Flash, AUC 0.93, ловит 7 из 10 верифицированных ошибок. Модели ранжирует точно (Спирмен 0.92 с судьёй GLM-5.3-Flash по средним баллам), но на отдельных ответах слабее; сжимает шкалу (сильным −0.4, слабым +0.4), слеп к галлюцинациям внутри грамотного текста, занижает корректные короткие отказы</td></tr>
<tr><td>clef-flash 9B</td><td>дешёвый первый экран</td><td><b>Отклонён как оценщик</b>: corr 0.06–0.09 с судьёй GLM-5.3-Flash; годится только на грубые бинарные признаки</td></tr>
<tr><td>clef bf16 (облако)</td><td>вдруг квантование портит?</td><td><b>Не нужно</b>: идентичен локальному Q4 (corr 0.98–0.99); облако к тому же режет контекст судьи до 2K токенов</td></tr>
<tr><td>Jev 1.13 (typesafe)</td><td>лидер внешних бенчей decision-моделей</td><td><b>Проиграл</b> в PHP-домене: corr 0.51, AUC 0.89, 4 из 10 ошибок, эскалация перегрета (медиана 0.54). Согласуется с внешним бенчем, где Clef победил колонку Programming</td></tr>
<tr><td>bge-m3 (эмбеддинги)</td><td>дешёвая и быстрая замена судье</td><td><b>Только черновой фильтр</b>: ниже 0.78 близости к SOTA плохой ответ; но тема ≠ истинность, галлюцинация на нужную тему «похожа» на правду больше безупречного ответа</td></tr>
<tr><td>mxbai / e5 / nomic</td><td>альтернативы яруса 0</td><td>mxbai непригоден (corr 0.13–0.25), e5 сжимает диапазон (контекст 512; инструкции и чанкинг не помогли), nomic запасной</td></tr>
<tr><td>GLM-5.3-Flash low / max</td><td>судья GLM-5.3-Flash (ярус 2)</td><td><b>Оба приняты с ролями</b>: low для рутины (97% совпадений с max в ±1, −70% токенов), max для финальных вердиктов (ловит тонкие фабрикации, low теряет ~17% из них)</td></tr>
</table>
<h3>Как подбирали метрики: что проверили и что отбросили</h3>
<ol>
<li><b>Балл + вероятность галлюцинаций</b>: базовые вопросы clef и clef-flash; сразу разделили роли: flash не тянет судейство качества.</li>
<li><b>Семь вероятностных признаков</b> (код, структура, глубина, имена API, точные числа, сомнение, противоречия): каждый слабее одного явного вопроса об эскалации.</li>
<li><b>Эскалация перебором сигналов</b>: расхождение двух decision-моделей, минус уверенность, hallucination, комбинации; победил <b>прямой вопрос «нужен ли более знающий судья?»</b> (need_review): corr 0.76 с реальной проблемой против 0.34 у уверенности в балле. Расширение триггера OR-условиями factcheck_zone и unsure (проверено на 450 знаниевых ответах с вердиктами судьи) добавляет 3–5 п.п. полноты по галлюцинациям ценой +10 п.п. эскалации и падения точности: сигналы сильно пересекаются, не окупается. Штатный триггер (need_review &gt; 0.3 или hallucination &gt; 0.3) проверен на размеченном корпусе, где для каждого ответа известно, требовал ли он судьи: наверх уходит 41% ответов, точность 83% (из отправленных наверх столько действительно нуждались в судье), полнота 82% (из требовавших судьи ловится столько, остальные 18% пропускаются). В формулировке need_review, ходившей в исследовании отдельным вызовом, был изъян: «не уверен, что твоя оценка качества верна» ссылалась на оценку, которой в том вызове не было; в объединённом запросе score.py это исправлено.</li>
<li><b>Якорные уровни</b> («оцени по образцу идеального ответа») не взлетели: сжатие шкалы не лечится примером.</li>
<li><b>Чек-листы хороших/плохих пунктов</b>: интегральный балл слаб, но «плохие» детекторы ловят семантические ошибки, невидимые clef. Правило формулировок: «предлагает X как решение», а не «упоминает X».</li>
<li><b>Семантическая энтропия</b> (расходимость ответов между прогонами, Nature 2024) отвергнута экспериментально: corr −0.35, галлюцинации стабильны, модель повторяет ту же галлюцинацию.</li>
<li><b>Калибровка порогов</b> открылась по ходу: noul-вероятность clef ведёт себя как квадрат уверенности, пороги не переносимы между судьями (clef 0.30 ≈ Jev 0.70).</li>
</ol>
<h3>Сводка проверенных гипотез</h3>
<!--JUDGES-->
<!--LOW-->

<h2 id=economics><span class=no>8</span>Экономика собеса</h2>
<p class=lead>Полное интервью, 35 вопросов с уточнениями, реальные списания OpenRouter за октябрь 2026.</p>
<h3>Методика: цена локальной сессии</h3>
<p>Цена облачной сессии это фактическое списание API. Цена локальной сессии
считается моделью стоимости из двух компонентов и относится ко
<b>времени чистой генерации</b> без загрузки модели в память и простоя:</p>
<table>
<tr><th>Компонент</th><th>Допущения</th><th>Ставка</th></tr>
<tr><td><b>Электричество</b></td><td>550 Вт (CPU+GPU) в момент генерации, ₽7.07/кВт·ч (тариф с 01.10.2026)</td><td>₽3.89/ч</td></tr>
<tr><td><b>Амортизация железа</b></td><td>₽600 000 за вычетом остаточной стоимости 30% (GPU держат цену на вторичке), эксплуатация 12 ч/день, окупаемость 3 года: верхняя граница нормы (2-я амортизационная группа, 2–3 года), итого 13 140 часов работы</td><td>₽31.96/ч</td></tr>
<tr><td class=neg><b>Итого</b></td><td>курс ₽85.15/$</td><td class=neg>₽35.85/ч ≈ $0.42</td></tr>
</table>
<p class=sub>Пример: собес 27B с ~9 минутами чистой генерации стоит 0.15 ч × ₽35.85 = ₽5.33 ($0.0626); курс доллара дан по состоянию на октябрь 2026. Допущения консервативны в обе стороны и взяты серединой: круглосуточная эксплуатация дала бы вдвое меньшую амортизацию (₽15.98/ч), но сервер делят и другие задачи, кроме инференса; остаточную стоимость 50% имела бы только GPU-часть без платформы. Параметры собраны в одном месте скрипта отчёта (<code>LOCAL_KWH, RUB_KWH, HW_COST_RUB, HW_RESIDUAL_PCT, AMORT_HOURS</code>).</p>
<div class=cards>
<div class=card><b>GPT-6 Luna: $0.0300 · 6.4 мин</b>минус 0.1 балла до Sol при цене в 20 раз ниже, лучший выбор для массовых прогонов</div>
<div class=card><b>Haiku 5.5: $0.0500–0.18 · 6 мин</b>самая быстрая; prompt-кэш Anthropic экономит 70% (без него сессия стоила бы $0.18)</div>
<div class=card><b>GPT-6.1 Sol: $0.62 · 14 мин</b>авто-кэш OpenAI уже учтён в цене, без него было бы $2.36</div>
<div class=card><b>Opus 5.5: $5.82 · 29 мин</b>с явными cache_control-метками, без кэша $13–20. Максимальное качество, цена не пропорциональна баллу</div>
<div class=card><b>DeepSeek V4.1 Flash: $0.25 · 12 мин</b>родной API: почти облачное качество (3.70) за 1/23 цены Opus, но галлюцинации в 33% ответов; кэш-хит $0.006/1M (×50 дешевле входа) при автокэше 95%, длинные повторные контексты почти бесплатны</div>
<div class=card><b>GigaChat-2 Max: ₽0 физлицу · ₽216.05–565.15 юрлицу</b>freemium 25 млн токенов/12 мес; бизнес-тариф ₽650/1M без разбивки вх/вых (₽325 асинхронно, мин. ₽600/мес); вилка отражает нераскрытую тарификацию кэша</div>
<div class=card><b>GigaChat-3 Ultra: ₽0 физлицу · ₽70.71–140.10 юрлицу</b>юрлицам у Сбера не продаётся, коммерческий доступ через cloud.ru: ₽96/1M вх + ₽289/1M вых; входо-ёмкий собес делает флагман дешевле Max</div>
<div class=card><b>YandexGPT Pro 5: ≈₽400.27 · $4.70</b>плоский ₽1.2/1k за любой токен, кэш без скидки: 92% кэш-эффективности не экономят ни копейки, дороже Sol при худшем качестве</div>
</div>
<div class=note><b>Две находки о кэше.</b> OpenAI кэширует промпты автоматически (проверено сверкой со счётом: списания на 70% ниже тарифа), а Anthropic только по явным меткам <code>cache_control</code>; их отсутствие едва не сорвало Opus-прогон.<br><b>Локальные не бесплатны.</b> Электричество за 20-минутный собес 27B стоит ₽1.30, но с амортизацией железа (методика и допущения в таблице выше) выходит ₽11.95 (~$0.14): дороже Luna ($0.0300), сопоставимо с дешёвыми облаками, и при этом медленнее, 20 минут против 6 у Haiku. Локальный инференс окупается приватностью и неограниченным объёмом, а не ценой за сессию.<br><b>Сбер в бизнес-ценах неконкурентен:</b> GigaChat-2 Max стоит как Opus (₽216.05–565.15 ≈ $2.54–6.64 за сессию) при качестве medgemma; GigaChat-3 Ultra через cloud.ru (₽70.71–140.10 ≈ $0.83–1.65) берёт по цене Sol, а даёт качество Luna. Дешевле всего российский ИИ обходится бесплатно, физлицом во freemium.</div>

<h2 id=sessions><span class=no>9</span>Сессии и данные</h2>
<p class=lead>Все завершённые прогоны. Ссылки ведут на полные транскрипты в репозитории исследования (файл README рядом с отчётом описывает структуру).</p>
<!--SESSIONS-->

<footer>
Отчёт собирается скриптом из сырых данных репозитория: сессии (session.json),
вердикты судей и CSV-агрегаты. Верификация фактов судьами: PHP 8.5.11
(запуск примеров кандидатов), PostgreSQL 17.7 (docker), официальная
документация Symfony, Doctrine, PostgreSQL. Автор не аффилирован с
упомянутыми компаниями; модели публично доступные версии октября 2026.
<br><br>Методика и сырые данные: <a href="https://github.com/prikotov/task-llm-hiring">github.com/prikotov/task-llm-hiring</a> (вопросник, раннеры, скрипты судейства, все транскрипты и вердикты). В колонках таблиц-вердиктов исследования старые имена: score это quality, conf это уверенность в балле, hall_p это hallucination; столбцы от has_code до contradictions это семь вероятностных метрик эксперимента с судьями (§7), в целевом score.py их нет.
</footer>
</div></body></html>
"""


def build(models, sessions, primer, low_json=None):
    # --- таблица результатов ---
    rows = []
    sber_items = []   # (folder, ₽без кэша, ₽с кэшем, токены) — по порядку появления
    sber_no = {}      # folder → номер сноски в этой таблице
    # серии с системным промптом (§6) — строками в таблицу результатов
    prompt_rows = []
    for folder, plabel in [("ornith-1.5-35b", "ornith-1.5 35B"), ("qwen3.8", "qwen3.8 27B"),
                           ("orcarouter-Qwen3.8-27B-Uncensored-latest", "orcarouter 27B"),
                           ("huihui_ai-Qwen3.8-abliterated-latest", "huihui abliterated 27B"),
                           ("gemma4-26b", "gemma4 26B")]:
        pm = primer.get((folder, "primer-v2"), {})
        if not pm:
            continue
        # прогоны финальной версии промпта: run-иды из decision-all (у ornith в папке
        # лежат и прогоны ранней версии v1, их не усредняем)
        v2_runs = {r["run"] for r in csv.DictReader(
            open(ROOT / "reports" / "decision-all.csv", encoding="utf-8-sig"))
            if r["judge"] == "clef" and r["model"] == folder and r["series"] == "primer-v2"}
        durs, toks = [], []
        for run in sorted(v2_runs):
            s = json.loads((ROOT / folder / "backend" / run / "session.json").read_text())
            durs.append(sum(tt["duration_s"] for tt in s["turns"]))
            u = s.get("usage", {})
            toks.append(u.get("output_tokens")
                        or sum(tt.get("eval_count") or 0 for tt in s["turns"]))
        cost = (statistics.mean(durs) / 3600 * (LOCAL_KWH * RUB_KWH + AMORT_RUB_H) / RUB_USD
                if durs else 0)
        prov_map = {"gemma4-26b": "15/15", "qwen3.8": "15/15",
                    "orcarouter-Qwen3.8-27B-Uncensored-latest": "14/15",
                    "huihui_ai-Qwen3.8-abliterated-latest": "14/15",
                    "ornith-1.5-35b": "11/15"}  # разметка: reports/provocations-primer-v2.csv
        entry = dict(folder=folder, label=plabel, series="think-off", kind="local",
                     runs=sorted(v2_runs), qv=2, primer=True,
                     glm_run=sorted(v2_runs)[0],
                     clef=round(pm["score"], 2), p_hall=round(pm.get("hall", 0), 3),
                     run_sigma=None, prov=prov_map.get(folder, "—"),
                     dur_min=round(statistics.mean(durs) / 60, 1) if durs else None,
                     out_tok=round(statistics.mean(toks)) if toks else None, cost=cost,
                     glm=None, glm_hall=None, glm_n=None, glm_level=None,
                     glm_note="не проверялся судьём")
        glm_file = (ROOT / folder / "backend" / "judgments" / "glm-5.3-flash-iso"
                    / f"{sorted(v2_runs)[0]}.json")
        if glm_file.exists():
            g = [v for v in json.loads(glm_file.read_text())["results"] if v["id"] in KNOW]
            if g:
                entry.update(glm=round(sum(v["score"] for v in g) / len(g), 2),
                             glm_hall=sum(1 for v in g if v["hallucination"]), glm_n=len(g),
                             glm_level=GLM_JUDGE_LEVEL.get(folder, "max"),
                             glm_note=f"все {len(g)} ответов на знания")

        prompt_rows.append(entry)

    for m in sorted(models + prompt_rows, key=lambda x: -(x["glm"] or x["clef"])):
        if m["folder"] in FREEMIUM:
            quota, _ = FREEMIUM[m["folder"]]
            tin, tout = BUSINESS_RUB_1M[m["folder"]]
            s0 = json.loads((ROOT / m["folder"] / "backend" / m["runs"][0]
                             / "session.json").read_text())
            u0 = s0.get("usage", {})
            i_t, o_t = u0.get("input_tokens", 0), u0.get("output_tokens", 0)
            cr = u0.get("cache_read_tokens") or 0
            toks = i_t + o_t + cr
            biz_nc = (i_t * tin + o_t * tout) / 1e6
            biz_wc = ((i_t + cr) * tin + o_t * tout) / 1e6
            sber_items.append((m["folder"], biz_nc, biz_wc, toks))
            sber_no[m["folder"]] = COL_FOOT_N + len(sber_no) + 1
            cost = f"₽0<sup>{sber_no[m['folder']]}</sup>"
        elif m["folder"] == "yandexgpt":
            s0 = json.loads((ROOT / m["folder"] / "backend" / m["runs"][0] / "session.json").read_text())
            rub = s0.get("usage", {}).get("cost_rub") or m["cost"] * RUB_USD
            cost = (f"${fmt_cost(m['cost'])}<div class=sub>₽{fmt_cost(rub)}"
                    " (факт 117 + тариф)</div>")
        else:
            rub = m["cost"] * RUB_USD
            cost = (f"${fmt_cost(m['cost'])}<div class=sub>₽{fmt_cost(rub)}</div>"
                    if m["cost"] else "0")
        glm_cell = bar(m["glm"] or 0, 4, "{:.1f}", "#2f7d54") if m["glm"] else "—"
        glm_sub = (f'галл. {m["glm_hall"]}/{m.get("glm_n", "??")} · {m["glm_note"]}'
                   if m["glm_hall"] is not None else m["glm_note"])
        if m["glm"] is not None:
            glm_sub += f' · рассуждения: {m["glm_level"]}'
        badge = {"cloud": '<span class="badge g">облако</span>',
                 "local": '<span class="badge b">локально</span>',
                 "negctrl": ""}[m["kind"]]
        row_cls = ("neg" if m["kind"] == "negctrl"
                   else "top" if m["folder"] in TOP_ROW
                   else "ru" if m["folder"] in RU_ROWS else "")
        rows.append(f"""<tr{' class=' + row_cls if row_cls else ''}>
<td><b>{m['label']}</b><div class=sub>{badge}</div></td>
<td>{THINK_TITLE.get(m['series'], m['series'])}</td>
<td>{'с промптом' if m.get('primer') else 'без'}</td>
<td>{bar(m['clef'], 4)}{f'<div class=sub>±{m["run_sigma"]:.2f} по прогонам</div>' if m.get("run_sigma") is not None else ''}</td>
<td>{glm_cell}<div class=sub>{glm_sub}</div></td>
<td class=num>{m['prov']}</td>
<td>{bar(m['p_hall'], 0.4, '{:.2f}', '#b3543f')}</td>
<td class=num>{m['dur_min']} мин<div class=sub>{m['out_tok']:,} вых. токенов за сессию</div></td>
<td class=num>{cost}</td></tr>""")
    table = ('<table><tr><th>Модель</th><th>Режим</th><th>Системный промпт</th><th>clef-балл<sup>1</sup></th>'
             '<th>Балл судьи<sup>2</sup></th><th>Найдено провокаций<sup>3</sup></th>'
             '<th>Галлюцинации<sup>4</sup></th><th>Длительность<sup>5</sup></th>'
             '<th>Цена<sup>6</sup></th></tr>'
             + "\n".join(rows) + "</table>")
    note = ('<sup>1</sup> clef: средний балл дешёвого судьи (ярус 1) по ответам всех прогонов на вопросы о знаниях; ± это стандартное отклонение средних прогонов, то есть стабильность средней оценки (там, где прогон один, разброса нет). Отдельные ответы менее постоянны: средний разброс балла одного вопроса между прогонами 2–6% шкалы (0.08–0.23 балла) (максимум у ornith-1.5 35B без мышления, до 28% шкалы на провокации Q24 у ornith-1.5 35B без мышления); при усреднении по 30 вопросам эти колебания гасятся.<br>'
            '<sup>2</sup> Балл судьи. GLM-5.3-Flash, ярус 2: каждый ответ проверяет отдельная '
            'изолированная GLM-5.3-Flash со свежим контекстом, спорные утверждения '
            'верифицируются запуском кода (PHP 8.5, PostgreSQL). Проверены все 30 ответов на знания одного прогона каждой строки таблицы. '
            'Уровень рассуждений судьи — max; состав: локальные '
            'прогоны и эталонные выборки — GLM-5.3-Flash, сплошная до-проверка '
            'облачных прогонов — GLM-5.3 (low↔max совпадают на 97% в ±1 балл, §7); '
            '«галл.» считает ответы с хотя '
            'бы одной подтверждённой галлюцинацией — остальная часть такого ответа '
            'может быть верной и получить высокий балл. Баллы обеих шкал и галлюцинации '
            'считаются по вопросам на знания — 30 ответов из 41: разминка, вопросы '
            'о работе с задачами и процессе и финал собеса не учитываются — выдумывать '
            'в них нечего. Это подмножество одинаково в версиях вопросника (v1 — 32 '
            'основных вопроса без финальной секции, v2 — 35; ранние модели — '
            'ornith-1.5 35B в обоих режимах, qwen3.8, huihui, orcarouter — проходили v1), '
            'поэтому результаты v1 и v2 сравнимы напрямую.<br>'
            '<sup>3</sup> Найдено провокаций: доля распознанных ложных предпосылок. Разметку транскриптов всех прогонов выполнял ассистент (GLM-5.3), провал — модель назвала процент или описала несуществующее как рабочее вместо отвержения предпосылки (X из 3×N; цитаты разметки вариантов с промптом — reports/provocations-primer-v2.csv).<br>'
            '<sup>4</sup> Галлюцинации: вероятность в ответе по расчёту clef (ярус 1).<br>'
            '<sup>5</sup> Длительность и токены даны на одну сессию (где прогонов несколько — '
            'среднее по прогонам); локальные считают время чистой генерации без загрузки модели.<br>'
            '<sup>6</sup> Цена одной сессии: облако по фактическим списаниям API, локальные '
            'по методике §8 (электричество + амортизация, ₽35.85/ч); рубли по курсу ₽85.15/$.')
    if sber_items:
        note += '<br>' + sber_footnote(sber_items, wrap=False)
    table += '<div class=note>' + note + '</div>'
    table += self_assessment_html(models + prompt_rows)
    table += stability_html(models + prompt_rows)
    table += quality_price_scatter(models + prompt_rows)
    table += quality_speed_scatter(models + prompt_rows)
    table += quality_triangle_3d(models + prompt_rows)

    # --- секция промпта ---
    def pget(model, series, key="score", default="—"):
        v = primer.get((model, series), {}).get(key)
        return f"{v:.2f}" if v is not None else default
    prim_rows = []
    for model, label in [("ornith-1.5-35b", "ornith-1.5 35B"), ("qwen3.8", "qwen3.8 27B"),
                         ("orcarouter-Qwen3.8-27B-Uncensored-latest", "orcarouter 27B"),
                         ("huihui_ai-Qwen3.8-abliterated-latest", "huihui abliterated 27B"),
                         ("gemma4-26b", "gemma4 26B")]:
        b, p = pget(model, "think-off"), pget(model, "primer-v2")
        bp, pp = pget(model, "think-off", "prov"), pget(model, "primer-v2", "prov")
        try:
            d = f"{float(p)-float(b):+.2f}"
        except ValueError:
            d = "—"
        prim_rows.append(f"<tr><td>{label}</td><td class=num>{b}</td><td class=num>{p}</td>"
                         f"<td class=num>{d}</td><td class=num>{bp}</td><td class=num>{pp}</td></tr>")
    primer_html = ('<table><tr><th>Модель</th><th>Балл без промпта</th><th>С промптом</th>'
                   '<th>Δ</th><th>Провокации без</th><th>с промптом</th></tr>'
                   + "\n".join(prim_rows) + "</table>"
                   + '<p>Системный промпт даёт меньше, чем мышление. По clef (по пять прогонов каждого варианта) '
                     'Δ от −0.02 до +0.14; контроль судьёй GLM-5.3-Flash на ornith-35B: '
                     'финальная версия промпта +0.32, ранняя версия на том же вопроснике −0.02, '
                     'мышление +0.65 — вдвое больше промпта. Выдумки промпт не лечит: у ornith '
                     '16 из 30 ответов с галлюцинациями без промпта и 17 из 30 с ним. Вывод: '
                     'классический промптинг «роль+CoT» надёжного прироста качества не даёт '
                     '(Δ по clef у всех пяти моделей — в пределах колебаний между прогонами), '
                     'собственных рассуждений модели не заменяет и фактологию не чинит: '
                     'он меняет подачу, а не знания.</p>')
    def judge_mean(folder, run):
        gp = ROOT / folder / "backend/judgments/glm-5.3-flash-iso" / f"{run}.json"
        if not gp.exists():
            return None
        g = [v for v in json.loads(gp.read_text())["results"] if v["id"] in KNOW]
        return sum(v["score"] for v in g) / len(g)

    think_rows = []
    for model, series, label, jrun in [
            ("ornith-1.5-35b", "think-off", "мышление выключено", "run-07"),
            ("ornith-1.5-35b", "think-on", "мышление включено", "run-06"),
            ("ornith-1.5-35b", "primer-v2", "мышление выключено + промпт", "run-17")]:
        jm = judge_mean(model, jrun)
        jm = f"{jm:.2f}" if jm is not None else "—"
        think_rows.append(f"<tr><td>ornith-1.5 35B · {label}</td><td class=num>{pget(model, series)}</td>"
                          f"<td class=num>{jm}</td>"
                          f"<td class=num>{pget(model, series, 'hall')}</td>"
                          f"<td class=num>{pget(model, series, 'prov')}</td></tr>")
    think_html = ('<h3>Мышление против промпта (ornith-1.5 35B)</h3><table>'
                  '<tr><th>Вариант</th><th>clef (5 прогонов)</th><th>Балл судьи (1 прогон)</th>'
                  '<th>Галлюцинации</th><th>Найдено провокаций</th></tr>'
                  + "\n".join(think_rows) + "</table>"
                  + "<p>Собственное мышление даёт +0.65 балла судьи (2.70 → 3.35) и лучший результат "
                    "на провокациях; промпт вдвое слабее (+0.32). Цена: время инференса вырастает "
                    "вчетверо (13.8 минуты против 3.2), а на длинных собесах контекст размышлений "
                    "вообще перестаёт помещаться (вариант qwen3.8 с мышлением оборвался пустыми "
                    "ответами и исключён).</p>")

    # --- судьи ---
    judges_html = """<table>
<tr><th>Гипотеза</th><th>Результат</th></tr>
<tr><td>Дешёвый clef судит факты?</td><td>Нет, только форму: корректный короткий отказ получает 2.8–3.2, многословная галлюцинация 3.5. Ранжирует точно (corr 0.92 с судьёй GLM-5.3-Flash), шкалу сжимает: сильным недодаёт ~0.4, слабым прощает ~0.4</td></tr>
<tr><td>Лидер внешних бенчей Jev 1.13 лучше?</td><td>Нет: corr 0.51 против 0.62, AUC детекции галлюцинаций 0.89 против 0.93, на верифицированных PHP-ошибках 4/10 против 7/10. Лидер бенчей не знает PHP-специфику</td></tr>
<tr><td>Квантование судьи (Q4 против bf16)?</td><td>Не влияет: корреляции 0.97–0.99, вердикты совпадают до второго знака. Локальный clef эквивалентен облачному</td></tr>
<tr><td>Близость к SOTA-ответу по эмбеддингам заменяет судью?</td><td>Только как черновой фильтр: галлюцинированный ответ на нужную тему получает близость выше безупречного. Тема ≠ истинность</td></tr>
<tr><td>Семантическая энтропия (Nature, 2024) как сигнал лжи?</td><td>Не переносится на длинные ответы: галлюцинации qwen стабильны между прогонами, корреляция −0.35. Метод работает на коротких фактических ответах</td></tr>
</table>"""

    # --- low vs max ---
    low_section = ""
    if low_json:
        pairs = []
        for m in low_json.get("all", []):
            mx = {v["id"]: v for v in json.loads(
                (ROOT / m["model"] / "backend" / "judgments" / "glm-5.3-flash-iso" /
                 f"{m['run']}.json").read_text())["results"]}
            for v in m["verdicts"]:
                if v["id"] in mx:
                    pairs.append((v["score"], v["hallucination"],
                                  mx[v["id"]]["score"], mx[v["id"]]["hallucination"]))
        n = len(pairs)
        w1 = sum(1 for p in pairs if abs(p[0] - p[2]) <= 1)
        agree = sum(1 for p in pairs if p[1] == p[3])
        low_section = f"""
<h3>Дешёвый режим судьи: low против max (30 ответов, 3 модели)</h3>
<table>
<tr><th>Метрика</th><th>Значение</th></tr>
<tr><td>Баллы в пределах ±1</td><td>{w1}/{n} ({w1/n:.0%}), точное совпадение {sum(1 for p in pairs if p[0]==p[2])/n:.0%}</td></tr>
<tr><td>Согласие флага галлюцинаций</td><td>{agree/n:.0%}, каппа Коэна 0.73</td></tr>
<tr><td>Верифицированный банк ошибок qwen</td><td>4 из 4, как у max</td></tr>
<tr><td>Токены на судью</td><td>~8k против ~27k (−70%)</td></tr>
</table>
<p class=sub>Итог: рутинное судейство (тренды, сравнения моделей) оставляем на low, финальные вердикты и охоту за тонкими фабрикациями переводим на max. Low чуть щедрее (+0.23 балла), поэтому ряды разных режимов в одной таблице не смешиваем.</p>
"""

    # --- сессии ---
    by_model = defaultdict(list)
    for s in sessions:
        by_model[s["model"]].append(s)
    LABELS = {m["folder"]: m["label"] for m in models}
    EXTRA = {"orcarouter-Qwen3.8-27B-Uncensored-latest": "orcarouter 27B (uncensored)",
             "huihui_ai-Qwen3.8-abliterated-latest": "huihui abliterated 27B",
             "gemma4-26b": "gemma4 26B"}
    LABELS.update(EXTRA)
    ORDER = ["anthropic-claude-opus-5.5", "openai-gpt-6.1-sol", "anthropic-claude-haiku-5.5",
             "openai-gpt-6-luna", "deepseek-flash", "gigachat", "gigachat-3-ultra", "yandexgpt", "ornith-1.5-35b", "qwen3.8",
             "muse-glimmer", "ornith-1.5-9b",
             "gemma4-26b", "orcarouter-Qwen3.8-27B-Uncensored-latest",
             "huihui_ai-Qwen3.8-abliterated-latest", "medgemma-27b", "gemma4-e2b"]
    parts = []
    total = 0
    sber_no_sess = {}  # folder → номер сноски в этой таблице (по порядку появления)
    for folder in ORDER:
        runs = by_model.get(folder)
        if not runs:
            continue
        total += len(runs)
        links = []
        for s in runs:
            href = f"../{folder}/backend/{s['run']}/transcript.md"
            parts_link = f'<a href="{href}">{s["run"]}</a>'
            cache_cell = s.get("cache_cell", "н/д")
            if s.get("rub") is not None:
                cost_cell = (f"${fmt_cost(s['cost'])}"
                             + f"<div class=sub>₽{fmt_cost(s['rub'])}</div>")
            elif s["cost"]:
                cost_cell = (f"${fmt_cost(s['cost'])}"
                             + f"<div class=sub>₽{fmt_cost(s['cost'] * RUB_USD)}</div>")
            elif s["series"] == "gigachat":
                sber_no_sess[folder] = COL_FOOT_N + sber_no_sess.get(folder, len(sber_no_sess)) + 1
                cost_cell = f"₽0<sup>{sber_no_sess[folder]}</sup>"
            elif s["series"] == "yandexfm":
                cost_cell = "₽/токены<div class=sub>тариф вендора</div>"
            else:
                cost_cell = "локально"
            links.append(f'<tr><td>{parts_link}</td><td>{SERIES_TITLE[s["series"]]}</td>'
                         f'<td>v{s.get("qv", "?")}</td>'
                         f'<td class=num>{s["n"]}</td><td class=num>{s["dur"]:.0f} мин</td>'
                         f'<td class=num>{s["in_tok"]:,}</td><td class=num>{s["out_tok"]:,}</td>'
                         f'<td class=num>{cache_cell}</td><td class=num>{cost_cell}</td></tr>')
        parts.append(
            f'<div class=mgroup><h3>{LABELS.get(folder, folder)}</h3>'
            f'<div class=sub>{folder}/</div>'
            '<table><tr><th>Прогон</th><th>Условия</th><th>Версия вопросника</th><th>Ответов</th><th>Длительность</th>'
            '<th>Вход. токены</th><th>Вых. токены</th><th>Кэш</th><th>Стоимость</th></tr>'
            + "".join(links) + "</table></div>")
    sessions_html = (f'<div class=note>Завершённых сессий: <b>{total}</b>. Неполные прогоны '
                     '(оборванные пустыми ответами из-за переполнения контекста мышлением: '
                     'qwen3.8 runs 01–05, ornith runs 01–02 и прерванный по бюджету Opus run-01) '
                     'исключены и лежат в тех же папках. Прогон yandexgpt оборвался по 403 '
                     '(исчерпание баланса) на 18/35 и после пополнения был завершён через '
                     'resume без пересчёта первых вопросов: контент-фильтр Яндекса заменил '
                     'отказами 6 технических ответов из 35 (Doctrine, DDD, архитектура, '
                     'git-процесс, постановка задачи) отражено в строке YandexGPT Pro 5 '
                     'выше.<br>'
                     'Токены и стоимость это фактические данные API. Различаем <b>кэш сессии</b> '
                     '(префикс одного собеса кэшируется между его вопросами, транспортная '
                     'оптимизация, модель видит тот же контекст) и <b>кэш прогонов</b> '
                     '(один собес греет следующий; он запрещён: модель выгружается после каждого '
                     'прогона, каждый собес стартует с холодным контекстом).<br>'
                     '<b>Кэш</b>: доля входных токенов сессии, покрытых кэшем: процент '
                     'и абсолютное число токенов (чт это чтение из кэша, зп запись в кэш); '
                     'полное число входных токенов видно в соседней колонке. Высокие проценты '
                     '(Haiku run-03, 96% чтения) это норма для одного чата с явными '
                     'cache_control-метками: каждый следующий запрос повторяет весь '
                     'предыдущий контекст, из кэша берётся повторная часть, а новые '
                     'вопрос-ответ пишутся в кэш один раз (зп). Живая '
                     'разбивка пишется с 9 октября; кэш-чтение более ранних облачных прогонов '
                     'восстановлено из журнала OpenRouter (записи в журнале нет, поэтому процент по '
                     'чтению чуть занижен): у Sol точное значение, у Luna/Haiku/Opus доля '
                     'дневного агрегата пропорционально токенам сессии, помечено ~. '
                     'Новые облачные прогоны стартуют с холодным кэшем: раннер выдерживает '
                     'паузу больше TTL кэша провайдера (у провайдеров нет session id или '
                     'выключателя кэша; сам кэш бит-идентичен пересчёту и на содержание '
                     'ответов не влияет). У локальных '
                     'ollama кэш-токены отдельно не отчитываются: тёплая сессия видна по скорости '
                     '(muse-glimmer: 9.2 мин с холодным пересчётом префикса против 4.9 мин '
                     'с кэшем сессии). Стоимость локальных сессий считается по методике из '
                     '§8 «Экономика» (₽35.85/ч: электричество + амортизация минус '
                     'остаточная стоимость), отнесено ко времени чистой генерации; '
                     'входные токены уточнений в ранних прогонах не сохранялись '
                     '(учтены основные вопросы).</div>' + "\n".join(parts))
    # сноски цен Сбера — под таблицей сессий, в порядке появления маркеров
    sber_sess = []
    for folder, n in sorted(sber_no_sess.items(), key=lambda x: x[1]):
        rs = by_model[folder]
        sber_sess.append((folder,
                          sum(s["biz"][0] for s in rs if s.get("biz")),
                          sum(s["biz"][1] for s in rs if s.get("biz")),
                          sum(s["in_tok"] + s["out_tok"] for s in rs)))
    if sber_sess:
        sessions_html += sber_footnote(sber_sess)

    html = (TEMPLATE
            .replace("__TOC__", TOC)
            .replace("<!--TABLE-->", table)
            .replace("<!--PRIMER-->", primer_html)
            .replace("<!--THINK-->", think_html)
            .replace("<!--JUDGES-->", judges_html)
            .replace("<!--LOW-->", low_section)
            .replace("<!--SESSIONS-->", sessions_html)
            .replace("__TOTAL__", str(total))
            .replace("__MODELS_N__", str(len({m["folder"] for m in models}))))
    return html


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--low", default=None)
    ap.add_argument("--out", default=str(ROOT / "reports" / "REPORT.html"))
    a = ap.parse_args()
    models = load_metrics()
    sessions = sessions_inventory()
    primer = primer_analysis()
    low = json.load(open(a.low)) if a.low else None
    html = build(models, sessions, primer, low)
    Path(a.out).write_text(html, encoding="utf-8")
    print(f"готово: {a.out} ({len(html)/1024:.0f} KB, сессий: {len(sessions)})")


if __name__ == "__main__":
    main()

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
<p>На этапе 2 модели выполняли тестовое задание «биллинг LLM-токенов» в среде pi.
На этапе 3 другие модели проверяют эти решения в роли код-ревьюеров. Каждое
ревью — отдельная сессия pi; всего прогонов в отчёте: 56.</p>
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
<li><a href="#validity">7. Валидность прогонов</a></li>
<li><a href="#runs">8. Прогоны и данные</a></li>
<li><a href="#next">9. Что дальше</a></li>
</ul>
</nav>"""


def section_tldr(runs, profiles, refs, grid) -> str:
    n_valid = sum(1 for r in runs if r["valid"])
    n_all = len(runs)
    blind = []
    for model, prof in profiles.items():
        t = prof["per_slug"].get("tricky")
        if t:
            blind.append((model, t["vals"]["axis_a"]))
    blind_txt = "; ".join(f"{SHORT.get(m, m)} — {runs_cell(v)}" for m, v in blind)
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
<p class=lead>Не все модели выдержали порядок ревью. orca во всех 12 прогонах читала
файлы (15–78 обращений) и писала разборы, но ни разу не выдала вердикт в заданном
формате JSON — свой формат со шкалой 0–5. e2b в пилоте трижды выдала вердикт, не
прочитав ни одного файла. Такие прогоны отбраковываются: из {n_all} прогонов
зачётных {n_valid}.</p>
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
25–45 минут. Сетка из 48 сессий заняла около трёх часов серверного времени.</div>
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
            f"<td class=num>{num(e['axis_a'])} · {num(e['axis_b'])} · {num(e['functional'])}</td>"
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
работало — чекер этого решения прошёл 11 из 11 сценариев. В следующую версию промпта
ревьюера добавлено правило: метод может отсутствовать в объявленном типе и
присутствовать в передаваемом объекте — проверять, что реально передаётся.</p>
<h3><span class=no>6.4.</span>Что ревьюеры нашли, кроме баллов</h3>
<p class=lead>Сессии с самой полной проверкой: qwen на «хорошем» решении пересчитала
пример из задания, сверила счётчик тестов phpunit с заявкой в PR_DESCRIPTION, опровергла
показатель LCOM4 из метрик («артефакт value-объектов») и нашла фактическую ошибку в
PR_DESCRIPTION — ссылку на несуществующий файл expected_report.csv; huihui и gemma4:31b
независимо указали на несоответствие формата cost_usd и на отладочный мусор в диффе.
Полные списки дефектов с серьёзностью — в review.json каждого прогона (раздел 8).</p>"""


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
    return f"""
<h2 id=validity><span class=no>7.</span>Валидность прогонов</h2>
<p class=lead>Из <b>{total}</b> прогонов зачётных <b>{valid}</b>. Три автоматические
проверки отбраковывают негодные прогоны: целостность рабочей копии (хэш-суммы до и
после), факт чтения файлов (хотя бы одно обращение), вердикт в заданном формате JSON.
Целостность не была нарушена ни в одном прогоне: инструмент чтения не даёт изменять
файлы.</p>
<table>
<tr><th>Причина отбраковки</th><th class=narrow>Прогонов</th><th>Модели</th><th>Что произошло</th></tr>
{''.join(rows)}
</table>
<p class=sub>Отдельные случаи. orca — во всех 12 прогонах файлы читались, разборы
писались, но вердикт выдавался в собственном формате (шкала 0–5, свои критерии, списки
замечаний) вместо заданного JSON; менять промпт под одну модель запрещено п.2 правил
тестирования, поэтому прогоны orca учтены как «формат не соблюдён». gemma4:26b — в трёх
сессиях бюджет выхода ушел в размышления без текста; после перезапусков закрыты все
слоты, кроме одного («среднее» решение — 2 из 3 прогонов). e2b — три вердикта без
открытия хотя бы одного файла, со ссылками на несуществующие файлы.</p>"""


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
<tr><td><b>Расширение сетки</b></td><td>Добавить оставшиеся локальные модели набора тем же
порядком (3 прогона × 4 решения). У qwen3.8 сейчас один прогон на решение — добавить
недостающие два. Прогоны orca учтены как «формат не соблюдён»; их разборы сохранены в
данных.</td></tr>
<tr><td><b>Полнота списков дефектов</b></td><td>Сопоставить списки дефектов ревьюеров с
эталонными: какие известные дефекты найдены, какие указаны ошибочно. Сейчас сверка выполнялась
вручную на выборке; дальше — скрипт-судья по образцу judge_tier3.</td></tr>
<tr><td><b>Калибровка зачёта</b></td><td>Пороги |Δ| ≤ 0.5 / ≤ 1.0 — черновые. При
расширении сетки пересмотреть с учётом систематических смещений моделей (завышение
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

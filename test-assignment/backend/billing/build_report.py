#!/usr/bin/env python3
"""Генератор отчёта этапа 2 (тестовое «биллинг LLM-токенов»).

Стиль — как у отчёта этапа 1 (interview/build_report.py): светлый документ,
нумерованные секции, таблицы с бейджами и барами, самописные SVG без внешних
зависимостей. Все числа — из артефактов прогонов (run.json, check.json,
metrics.json, session-profile.md) моделей/<модель>/test-assignment/backend/.

Запуск: python3 test-assignment/backend/billing/build_report.py
Выход: reports/TEST-ASSIGNMENT-report.html
"""
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
OUT = ROOT / "reports" / "TEST-ASSIGNMENT-report.html"
BILLING = Path(__file__).resolve().parent

# --- проверенные данные прогонов (run.json / check.json / metrics.json) -----
# dur — минуты; tok — млн токенов; peak — K токенов; comp — автокомпакты+правки
FULL, PART, FAIL, NA = "full", "part", "fail", "na"

SHORT = {  # короткое имя модели по папке
    "qwen3.8-27b-pi-100k": "qwen3.8",
    "huihui-ai-pi-100k": "huihui",
    "gemma4-31b-it-qat-pi-100k": "gemma4:31b",
    "orcarouter-Qwen3.8-27B-Uncensored-latest": "orca",
    "muse-glimmer": "muse",
    "ornith-1.5-35b": "ornith",
    "gemma4-e2b": "e2b",
    "medgemma-27b": "medgemma",
    "openai-gpt-6-luna": "Luna 6",
    "openai-gpt-6.1-sol": "Sol 6.1",
    # снапшот по changelog DeepSeek: алиас обновлён до -1017 2026-10-17,
    # за день до сетки этапа (интервью этапа 1 — базовый V4.1 Flash)
    "deepseek-flash": "DeepSeek V4.1 Flash-1017",
    "orcarouter-Qwen3.8-27B-Uncensored-iq4_xs": "orca · IQ4_XS",
    "gemma4-26b-pi-100k": "gemma4:26b",
    "gigachat-3-ultra": "GigaChat 3 Ultra",
    "gigachat": "GigaChat 2 Max",
    "bonsai-2-27b": "bonsai-2",
    "anthropic-claude-haiku-5.5": "Haiku 5.5",
    "anthropic-claude-opus-5.5": "Opus 5.5",
}
PARAMS = {  # параметры/квант по папке (AGENTS.md, набор моделей)
    "qwen3.8-27b-pi-100k": ("27.3B", "Q4_K_M"),
    "huihui-ai-pi-100k": ("27.3B", "Q4_K_M"),
    "gemma4-31b-it-qat-pi-100k": ("30.7B", "Q4_0"),
    "orcarouter-Qwen3.8-27B-Uncensored-latest": ("27.3B", "Q4_K_M"),
    "muse-glimmer": ("27.9B", "Q4_K_M"),
    "ornith-1.5-35b": ("35.5B", "Q4_K_M"),
    "gemma4-e2b": ("4.6B", "Q4_K_M"),
    "medgemma-27b": ("27.4B", "Q4_K_M"),
    "openai-gpt-6-luna": ("—", "—"),
    "openai-gpt-6.1-sol": ("—", "—"),
    "deepseek-flash": ("—", "—"),
    "anthropic-claude-haiku-5.5": ("—", "—"),
    "anthropic-claude-opus-5.5": ("—", "—"),
    "bonsai-2-27b": ("26.9B", "Q2_0"),
}

# порядок моделей в разделе 7: облако (SOTA → DeepSeek → российские),
# затем локальные по семействам (как GROUPS_RADAR в interview/build_report.py);
# папки не из списка — в конец по алфавиту
RUN_GROUPS = [
    ("Облачные SOTA", ("openai-gpt-6.1-sol", "openai-gpt-6-luna",
                       "anthropic-claude-haiku-5.5", "anthropic-claude-opus-5.5")),
    ("DeepSeek", ("deepseek-flash",)),
    ("Российские облака", ("gigachat", "gigachat-3-ultra")),
    ("Семейство локальных qwen", ("qwen3.8-27b-pi-100k", "huihui-ai-pi-100k",
                                  "orcarouter-Qwen3.8-27B-Uncensored-latest",
                                  "orcarouter-Qwen3.8-27B-Uncensored-iq4_xs")),
    ("Семейство локальных ornith", ("ornith-1.5-35b",)),
    ("Семейство локальных gemma", ("gemma4-31b-it-qat-pi-100k", "gemma4-26b-pi-100k",
                                   "gemma4-e2b")),
    ("Другие локальные модели", ("muse-glimmer", "bonsai-2-27b")),
    ("Негативный контроль", ("medgemma-27b",)),
]


def run_group_of(folder: str):
    """(индекс группы, позиция в группе) для сортировки моделей."""
    for i, (_, members) in enumerate(RUN_GROUPS):
        if folder in members:
            return i, members.index(folder)
    return len(RUN_GROUPS), 0


# тарифы облачных провайдеров, $ за токен: (вход без кэша, выход, кэш-чтение).
# OpenRouter — openrouter.ai/api/v1/models (2026-10-18), сверено с журналом
# reports/openrouter-spend.csv; DeepSeek — пик-тариф родного API.
API_TARIFFS = {
    "openai/gpt-6-luna": (1e-7, 5e-7, 1e-8),
    "openai/gpt-6.1-sol": (2e-6, 1e-5, 1e-7),
    "deepseek-flash": (0.3e-6, 1.2e-6, 6e-9),
    "anthropic/claude-haiku-5.5": (0.1e-6, 0.5e-6, 0.01e-6),
    "anthropic/claude-opus-5.5": (4e-6, 20e-6, 0.4e-6),
}


def api_cost(provider: str, model: str, u_in: int, u_out: int, u_cache: int):
    """Стоимость токенов модели за прогон; None для локальных."""
    if provider == "gigachat":
        return 0.0  # freemium для физлиц (квота 12 мес) — списаний нет
    # orcarouter — тот же прайс провайдера без наценки, тарифы те же
    t = API_TARIFFS.get(model) if provider in ("openrouter", "deepseek", "orcarouter") else None
    if not t:
        return None
    return u_in * t[0] + u_out * t[1] + u_cache * t[2]


# модель стоимости локального инференса — методика отчёта интервью
# (interview/build_report.py, §8 «Экономика собеса»): 550 Вт в момент генерации
# при ₽7.07/кВт·ч (тариф с 01.10.2026) плюс амортизация железа (₽600 000 минус
# остаточные 30% за 13 140 часов работы) — итого ₽35.85/ч чистой генерации;
# курс ₽85.15/$ от владельца (2026-10-09)
LOCAL_RUB_H = 0.55 * 7.07 + (600_000 * 0.70) / (3 * 365 * 12)   # ₽35.85/ч
RUB_USD = 85.15


def local_cost(gen_minutes: float):
    """Стоимость локального прогона по времени чистой генерации."""
    return gen_minutes / 60 * LOCAL_RUB_H / RUB_USD


def interrupt_cause(r: Path, run: dict) -> tuple[str, str] | None:
    """(тип, причина) незавершённости прогона. time — остановлен раннером
    по лимиту времени 90 мин (агент работал или висел до самого стопа);
    cut — обрыв сбоем: исчерпан баланс/ключ, pi упал сам. None — прогон
    завершился с кодом 0. Такие прогоны вне метрик: частичное решение
    и его оценки искажают средние."""
    ec = run.get("exit_code")
    if ec in (0, None) and not run.get("killed"):
        return None
    if ec == 143 or run.get("killed"):
        return ("time", "остановлен по лимиту времени 90 мин (SIGTERM раннера)")
    plog = r / "pi-run.log"
    ptext = plog.read_text(errors="replace") if plog.is_file() else ""
    if "requires more credits" in ptext:
        return ("cut", "прерван: исчерпан баланс OpenRouter (402 requires more credits)")
    if "reached its spending limit" in ptext:
        return ("cut", "прерван: достигнут лимит ключа OrcaRouter (403 err_key_quota)")
    return ("cut", f"pi завершился с кодом {ec} (обрыв/сбой — детали в pi-run.log)")


def scan_runs() -> list[dict]:
    """Все прогоны с диска: условия, чекер, метрики, ярусы 3/4, сессия."""
    rows = []
    for d in sorted((ROOT / "models").iterdir()):
        base = d / "backend" / "test-assignment"
        if not base.is_dir():
            continue
        for r in sorted(base.glob("run-*")):
            if not (r / "run.json").is_file():
                continue
            run = json.loads((r / "run.json").read_text(encoding="utf-8"))
            if not (r / "check.json").is_file():
                continue   # незавершённый прогон: pi упал до чекера — вне сетки
            cj = json.loads((r / "check.json").read_text())
            mj = json.loads((r / "metrics.json").read_text()) if (r / "metrics.json").is_file() else {}
            t3 = json.loads((r / "tier3.json").read_text()) if (r / "tier3.json").is_file() else {}
            t4 = json.loads((r / "tier4.json").read_text()) if (r / "tier4.json").is_file() else {}

            # сессия: токены, пик, компакты, чистая генерация (стриминг — файлы большие)
            tok = peak = comp = u_in = u_out = u_cache = u_cw = gen_ms = 0
            nsess = 0
            last_stop = None
            for f in sorted((r / "pi-session").glob("*.jsonl")) if (r / "pi-session").is_dir() else []:
                nsess += 1
                for line in f.read_text(encoding="utf-8").splitlines():
                    try:
                        e = json.loads(line)
                    except json.JSONDecodeError:
                        continue
                    if e.get("type") in ("compaction", "context_edit"):
                        comp += 1
                    m = e.get("message") or {}
                    if m.get("role") != "assistant":
                        continue
                    last_stop = m.get("stopReason")
                    gen_ms += int(m.get("durationMs") or 0)
                    u = m.get("usage")
                    if isinstance(u, dict):
                        tok += int(u.get("totalTokens") or 0)
                        peak = max(peak, int(u.get("input") or 0) + int(u.get("cacheRead") or 0))
                        u_in += int(u.get("input") or 0)
                        u_out += int(u.get("output") or 0)
                        u_cache += int(u.get("cacheRead") or 0)
                        u_cw += int(u.get("cacheWrite") or 0)

            mand = cj.get("mandatory_passed")
            bonus = cj.get("bonus_passed")
            if mand is None:
                kind = "na"
            elif mand + bonus >= 12:
                kind = "full"
            elif mand >= 9:
                kind = "part"
            else:
                kind = "fail"
            aud = json.loads((r / "audit.json").read_text()) if (r / "audit.json").is_file() else {}
            foreign = run.get("ollama_foreign_models") or []
            # доли пустых строк и комментариев — из сырья scc (агрегат по всем языкам)
            scc_lang = {}
            sr = mj.get("scc") or {}
            if sr.get("raw"):
                sp = r / "metrics-raw" / sr["raw"]
                if sp.is_file():
                    try:
                        langs = json.loads(sp.read_text())
                        if isinstance(langs, list) and langs:
                            scc_lang = langs[0]
                    except json.JSONDecodeError:
                        pass
            blank_pct = comm_pct = None
            if scc_lang.get("Lines"):
                blank_pct = round(100 * scc_lang.get("Blank", 0) / scc_lang["Lines"], 1)
                comm_pct = round(100 * scc_lang.get("Comment", 0) / scc_lang["Lines"], 1)
            frozen = ((run.get("duration_s") or 0) > 5000
                      and ((mj.get("scc") or {}).get("loc") or 999) <= 40)
            blank_run = not run.get("diff_lines")
            ic = interrupt_cause(r, run)
            int_kind, int_cause = ic if ic else (None, None)
            dead = frozen or blank_run or bool(ic)
            # тип «вне метрик» — для разных значков в таблице результатов:
            # cut — обрыв сбоем (баланс/ключ/краш), time — лимит времени 90 мин,
            # hang — повисшая сессия, blank — решение не написано
            deadkind = (int_kind if int_kind else
                        "hang" if frozen else
                        "blank" if blank_run else None)
            deadwhy = ""
            if int_cause:
                # прерван (лимит времени / сбой): частичное решение и его оценки
                # — не результат модели, в средних искажают соседние строки
                deadwhy = int_cause + (
                    "; решение не написано (дифф 0 строк)" if blank_run
                    else f"; решение оборвано (дифф {run.get('diff_lines') or 0} строк), "
                         "оценки обрывка в средних не участвуют")
                if nsess > 1:
                    deadwhy = f"попыток в каталоге: {nsess}; " + deadwhy
            elif frozen:
                deadwhy = ("агент застрял на bash-команде (таймаута на инструменты у pi нет), "
                           "сессия дожила до лимита 90 минут; скелет нетронут — вне метрик")
            elif blank_run:
                deadwhy = ("решение не написано: дифф 0 строк, чекер оценивал нетронутый "
                           "скелет — вне метрик")
                if last_stop == "length":
                    deadwhy = ("вывод оборван потолком выходных токенов посреди размышлений "
                               "(финальный вызов модели — stop=length: ни текста, ни действий); "
                               "решение не написано, дифф 0 строк — чекер оценивал нетронутый "
                               "скелет, вне метрик")
                if nsess > 1:
                    deadwhy = f"попыток в каталоге: {nsess}; " + deadwhy
            # прогон вне метрик: прерван (сбой / лимит времени / баланс) с
            # обрывком решения, зависшая сессия либо решение не написано
            # (дифф 0 строк — чекер оценивал нетронутый скелет, мерить нечего)
            rows.append({
                "folder": d.name, "short": SHORT.get(d.name, d.name),
                "run": r.name, "tag": run.get("model", ""),
                "prov": run.get("provider", "ollama"),
                # параллельная модель в ollama во время прогона: факт фиксируем,
                # прогон исключаем из метрик (разделы 1–6), показываем в разделе 7
                "contention": bool(run.get("ollama_contention")) or bool(foreign),
                "foreign": foreign,
                "note": run.get("thinking_effective") or run.get("thinking") or "?",
                # MTP-серия: те же веса orca под тегом qwen3.8-unc* (MTP draft)
                "retag": str(run.get("model", "")).startswith("qwen3.8-unc"),
                "ctx": run.get("context"),   # None = нативные параметры каталога pi
                "mand": mand, "bonus": bonus, "kind": kind,
                "a1": t3.get("axis_a"), "b1": t3.get("axis_b"), "a2": t4.get("axis_a"),
                "dur": (run.get("duration_s") or 0) / 60,
                "gen": gen_ms / 1000 / 60, "tok": tok / 1e6, "peak": peak / 1000, "comp": comp,
                "uin": u_in, "cin": u_cache, "cinw": u_cw, "out": u_out,
                "phpstan": (mj.get("phpstan") or {}).get("errors"),
                "phpmd": (mj.get("phpmd") or {}).get("violations"),
                "phpcs": (mj.get("phpcs") or {}).get("violations"),
                "psalm": (mj.get("psalm") or {}).get("issues"),
                "tests": (mj.get("phpunit") or {}).get("tests"),
                "cov": (mj.get("phpunit") or {}).get("coverage_lines_pct"),
                "loc": (mj.get("scc") or {}).get("loc"),
                # прогон вне метрик: прерван (выше), зависшая сессия либо
                # решение не написано — в средних разделов 1–6 не участвует
                "dead": dead, "deadkind": deadkind,
                "deadwhy": deadwhy,
                "blank_pct": blank_pct, "comm_pct": comm_pct,
                "audit": aud.get("overall") or "",
                "cost": api_cost(run.get("provider", ""), run.get("model", ""), u_in, u_out, u_cache),
            })
            if rows[-1]["prov"] == "gigachat":
                rows[-1]["note"] = "нет"  # управления размышлениями у GigaChat нет
            if rows[-1]["cost"] is None:   # локальный прогон: электричество + амортизация
                rows[-1]["cost"] = local_cost(rows[-1]["gen"])
    return rows


KIND_HUE = {FULL: "#2f7d54", PART: "#315f9e", FAIL: "#b3543f", NA: "#98a4b3"}
KIND_NAME = {FULL: "полный зачёт (12–13)", PART: "почти (9–11)", FAIL: "провал (<9)", NA: "n/a"}
REVIEW_GOOD = 3.0   # порог квадрантов «хорошо…» на картах §4: ревью, ось A

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
h2{font-size:24px;margin:44px 0 10px;letter-spacing:-.01em}
h2 .no{color:var(--mut);font-weight:400;margin-right:8px}
h3{font-size:17.5px;margin:26px 0 2px}
.lead{color:var(--mut);margin:8px 0 14px;font-size:15px}
p{margin:10px 0}
.sub{font-size:12.5px;color:var(--mut);margin-top:3px;line-height:1.5}
.excap{font-size:15px;color:var(--ink);margin:12px 0 -4px;line-height:1.5}
.excap.sb{font-weight:700;margin-top:26px}
table{border-collapse:collapse;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(20,30,50,.07);margin:14px 0}
th{background:#eef1f6;text-align:left;font-size:12.5px;text-transform:uppercase;letter-spacing:.03em;color:#46536a;padding:10px 12px;border-bottom:2px solid var(--line)}
th.narrow{padding-left:5px;padding-right:5px}
th.sub{text-transform:none;letter-spacing:0;font-size:11px;font-weight:600;padding:5px 4px;color:#66707f}
td.narrow{padding-left:5px;padding-right:5px}
td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;font-size:14.5px}
tr:last-child td{border-bottom:none}
tr.neg td{background:#fdf6f4}
tr.top td{background:#eef6ef}
.barwrap{position:relative;background:#eef0f4;border-radius:6px;min-width:92px;max-width:110px;height:21px}
.barwrap .bar{position:absolute;left:0;top:3px;bottom:3px;border-radius:5px;min-width:3px;opacity:.3}
.barwrap>span{position:absolute;left:7px;top:0;font-size:12.5px;line-height:21px;font-weight:600;color:var(--ink)}
.barwrap+.barwrap{margin-top:2px}
.barwrap.s{min-width:56px;max-width:72px}
.star{font-style:normal;color:#c08a00;margin-left:1px}
.deadmark{position:relative;display:inline-block;margin-left:-6px;padding:0 6px;
           border-radius:9px;font-size:12px;font-weight:700;
           line-height:18px;cursor:help;white-space:nowrap}
.deadmark.cut{background:#fbeae6;color:#b3543f}
.deadmark.time{background:#fff4d6;color:#9a6b00}
.deadmark.hang{background:#efe9f7;color:#6b5b9e}
.deadmark.blank{background:#eef0f4;color:#66707f}
.okmark{position:relative;display:inline-block;margin-left:-6px;padding:0 6px;
        border-radius:9px;background:#e9f1ec;color:#2f7d54;font-size:12px;font-weight:700;
        line-height:18px;cursor:help;white-space:nowrap}
.dsym{cursor:help;font-weight:700}
.dsym.cut{color:#b3543f}.dsym.time{color:#9a6b00}
.dsym.hang{color:#6b5b9e}.dsym.blank{color:#66707f}
.ptag{color:#66707f;font-size:10.5px;font-weight:600;cursor:help}
.provchip{display:inline-block;margin-top:2px;padding:0 5px;border-radius:7px;
          background:#eef0f4;color:#46536a;font-size:10.5px;font-weight:600;
          line-height:15px;cursor:help;white-space:nowrap}
.dchip{display:inline-block;margin:1px 2px 1px 0;padding:0 5px;border-radius:7px;
       background:#eef1f6;color:#315f9e;font-size:10.5px;font-weight:600;
       line-height:15px;white-space:nowrap}
.strun{white-space:nowrap;margin:1px 0}
.strun .rno{display:inline-block;min-width:22px;color:var(--mut);font-size:11px;font-weight:600}
.ckmini{border-collapse:collapse;margin:0}
.ckmini th{background:none;border-bottom:1px solid var(--line);padding:1px 4px;
           text-transform:none;letter-spacing:0;font-size:9.5px;font-weight:600;
           color:#46536a;text-align:center;white-space:nowrap}
.ckmini th.rno{color:var(--mut)}
.ckmini td{border-bottom:none;padding:1px 5px;text-align:center}
.ckmini input{margin:0;accent-color:#315f9e;vertical-align:middle}
.tree{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 18px;
      font:12.5px/1.62 ui-monospace,SFMono-Regular,Consolas,Menlo,monospace;
      overflow-x:auto;margin:14px 0}
.tline{white-space:pre}
.tpfx{color:#cdd3da}
.tpct{color:#8a93a0;font-size:11px}
.troot{color:#46536a;font-weight:700}
/* переключатель в стиле проводника: [+]/[−] слева на строке каталога */
.tbox{display:inline-block;width:12px;height:12px;margin-right:6px;
      border:1px solid #9aa4b1;border-radius:3px;background:#fff;color:#46536a;
      font-size:10px;font-weight:700;line-height:12px;text-align:center;
      vertical-align:-1px;user-select:none}
.tbox.none{border-color:transparent}
.tree summary{list-style:none;cursor:pointer}
.tree summary::-webkit-details-marker{display:none}
.tree summary .tbox::before{content:"+";}
.tree details[open]>summary .tbox::before{content:"−";}
.tree summary.tline:hover{background:#f0f4f9}
.deadmark:hover::after,.okmark:hover::after{content:attr(data-tip);position:absolute;
           bottom:calc(100% + 7px);
           left:50%;transform:translateX(-50%);background:#2a3342;color:#fff;
           padding:8px 11px;border-radius:8px;font-size:12px;font-weight:400;line-height:1.5;
           white-space:normal;width:270px;z-index:60;text-align:left;
           box-shadow:0 5px 16px rgba(20,30,50,.28)}
.deadmark:hover::before,.okmark:hover::before{content:"";position:absolute;
           bottom:calc(100% + 1px);left:50%;
           transform:translateX(-50%);border:6px solid transparent;border-top-color:#2a3342;
           z-index:61}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(295px,1fr));gap:13px;margin:16px 0}
.card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--acc);border-radius:12px;padding:15px 17px;font-size:14.5px}
.card b{display:block;margin-bottom:5px;font-size:15px;min-height:2.8em}
.quote{background:#fff;border:1px solid var(--line);border-radius:12px;padding:15px 19px;margin:13px 0;font-size:14.5px}
.quote .who{font-size:12.5px;color:var(--mut);margin-bottom:7px;text-transform:uppercase;letter-spacing:.03em}
.quote.bad{border-left:4px solid var(--bad)}.quote.good{border-left:4px solid var(--good)}
.flow{display:flex;flex-wrap:wrap;gap:6px;align-items:stretch;margin:15px 0}
.flow .step{background:#fff;border:2px solid var(--acc);border-radius:12px;padding:10px 13px;flex:1;min-width:150px;font-size:13.5px}
.flow .step b{color:var(--acc);display:block;margin-bottom:4px}
.flow .step.hot{border-color:var(--bad)}.flow .step.hot b{color:var(--bad)}
.flow .arrow{align-self:center;color:var(--mut);font-size:19px}
a{color:var(--acc)}
pre.req{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 16px;margin:8px 0 12px;font-size:11.8px;line-height:1.55;overflow-x:auto;white-space:pre-wrap}
.famhead{font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
         color:var(--mut);margin:34px 0 2px;border-top:1px solid var(--line);padding-top:16px}
.mgroup{margin:20px 0}
.mgroup h3{margin-bottom:2px}
.mgroup table{width:auto;max-width:100%}
.mgroup th,.mgroup td{white-space:nowrap}
.mgroup td:last-child{white-space:normal}
.note{background:#fff9e8;border:1px solid #eedd9f;border-radius:12px;padding:13px 17px;font-size:14.5px;margin:14px 0}
.scalebox{background:#f4f7fb;border:1px solid var(--line);border-radius:10px;padding:11px 16px;font-size:13.5px;color:var(--mut);margin:14px 0}
.scalebox>b{display:block;margin:2px 0 9px;font-size:15px;color:var(--ink)}
.scalebox div{margin-top:7px}
.pairs{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:13px;margin:16px 0}
.pairblock{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 16px}
.pairtitle{font-size:15px;font-weight:600;margin-bottom:6px}
.pairrow{display:flex;align-items:center;gap:10px;margin:8px 0}
.pairlbl{width:32px;font-size:12px;color:var(--mut);text-align:right;flex:none}
.pairmeta{font-size:12px;color:var(--mut);white-space:nowrap}
.badge{display:inline-block;border-radius:5px;padding:1px 8px;font-size:12px;font-weight:600}
.badge.b{background:#e9f1ec;color:var(--good)}.badge.r{background:#f8ebe7;color:var(--bad)}.badge.g{background:#edf1f7;color:var(--acc)}
svg text{font-family:inherit}
.scatter .dot{cursor:pointer}
.scatter g.pt .tip{pointer-events:none;display:none}
footer{margin-top:56px;color:var(--mut);font-size:13.5px;border-top:1px solid var(--line);padding-top:18px;line-height:1.6}
@media print{.toc{display:none}header{border-radius:0}h2{page-break-after:avoid}}
"""


def pl_runs(n: int) -> str:
    return f"{n} " + ("прогон" if n % 10 == 1 and n % 100 != 11 else
                      "прогона" if n % 10 in (2, 3, 4) and n % 100 not in (12, 13, 14) else "прогонов")


def fmt_dur(v):
    # U+00A0 между числом и единицей — не переносится в узких колонках
    if v is None:
        return "—"
    if v < 1:
        return f"{round(v * 60)}\u00a0с"
    return f"{round(v):g}\u00a0мин" if v >= 10 else f"{round(v * 10) / 10:g}\u00a0мин"


def results_agg(data: list[dict]) -> list[dict]:
    """Свёртка прогонов в строки «модель в одном режиме»: средние —
    только по живым прогонам; прерванные (сбой/лимит/баланс), повисшие
    и пустые остаются в счётчике n_dead."""
    order = {FULL: 0, PART: 1, FAIL: 2, NA: 3}
    groups: dict[tuple, list[dict]] = {}
    for r in data:
        # экспериментальные теги (суффикс -tuned) — отдельные строки,
        # чтобы эксперимент не смешивался с основной серией модели
        tuned = "-tuned" in r["tag"]
        groups.setdefault((r["folder"], r["note"], r["ctx"], tuned, r["retag"]), []).append(r)

    def mean(rs, field):
        vals = [x[field] for x in rs if x[field] is not None]
        return sum(vals) / len(vals) if vals else None

    agg = []
    for (folder, note, ctx, tuned, retag), allrs in groups.items():
        n_dead = sum(1 for x in allrs if x["dead"])
        # разбивка «вне метрик» по типам — для разных значков в таблице
        deadkinds = {"cut": 0, "time": 0, "hang": 0, "blank": 0}
        for x in allrs:
            if x["dead"]:
                deadkinds[x["deadkind"]] += 1
        n_total = len(allrs)
        # средние — только по живым прогонам; вне метрик (✂/⏱/∅) — в счётчиках
        rs = [x for x in allrs if not x["dead"]]
        n = len(rs)
        kinds = [x["kind"] for x in rs]
        a1, a2 = mean(rs, "a1"), mean(rs, "a2")
        viol = sum(v for v in (mean(rs, f) for f in ("phpcs", "psalm", "phpstan"))
                   if v is not None)
        # доля кэша — по суммарным токенам группы, не среднее долей;
        # в знаменателе весь промпт: свежий ввод + чтение из кэша + запись в кэш
        cin_sum = sum(x["cin"] for x in rs)
        prompt_sum = sum(x["uin"] + x["cin"] + x["cinw"] for x in rs)
        agg.append({
            "short": allrs[0]["short"] + (" ·tuned" if tuned else "")
                    + (" · MTP" if retag else ""),
            # ключ группы — для обратной связи точек карт с прогонами
            "folder": folder, "ctx": ctx, "tuned": tuned, "retag": retag,
            "n_total": n_total, "n_dead": n_dead, "deadkinds": deadkinds,
            "prov": allrs[0]["prov"],
            "note": note, "n": n, "kinds": kinds,
            "worst": min(kinds, key=lambda k: order[k]) if kinds else NA,
            "n_full": kinds.count(FULL),
            "mand": mean(rs, "mand"), "bonus": mean(rs, "bonus"),
            "total": (mean(rs, "mand") or 0) + (mean(rs, "bonus") or 0),
            "a1": a1, "b1": mean(rs, "b1"), "a2": a2,
            "viol": viol, "cov": mean(rs, "cov"),
            "phpcs": mean(rs, "phpcs"), "psalm": mean(rs, "psalm"),
            "phpstan": mean(rs, "phpstan"), "phpmd": mean(rs, "phpmd"),
            "tests": mean(rs, "tests"), "loc": mean(rs, "loc"),
            "blank_pct": mean(rs, "blank_pct"), "comm_pct": mean(rs, "comm_pct"),
            "dur": mean(rs, "dur"), "tok": mean(rs, "tok"), "cost": mean(rs, "cost"),
            "out": mean(rs, "out"),
            "cache_pct": (100 * cin_sum / prompt_sum) if prompt_sum else None,
        })
    return agg


def results_table(data: list[dict]):
    """Строка — модель в одном режиме (уровень рассуждений + контекст):
    прогоны одинаковых условий сворачиваются в одну строку, все числа —
    средние на прогон. Детальные данные по каждому прогону — раздел 7."""
    agg = results_agg(data)

    def fmt_mn(v):
        """Среднее: целое — без дробной части, иначе один знак."""
        return "—" if v is None else (f"{v:g}" if abs(v - round(v)) < 0.05 else f"{v:.1f}")

    def sort_key(r):
        """Лучшие наверху: чекер → ревью (ось A) → нарушения → покрытие."""
        return (-r["total"], -(r["a1"] or 0), r["viol"], -(r["cov"] or 0))

    # значки колонки «Прогоны»: ✓ — живой (в метриках); вне метрик — по типу:
    # ✂ обрыв сбоем, ⏱ лимит времени, 💤 повисшая сессия, ∅ решение не написано
    DEAD_ICON = {
        "cut": ("✂", "обрыв сбоем: исчерпан баланс OpenRouter (402), лимит ключа "
                      "OrcaRouter (403) или падение pi; обрывок решения в средних "
                      "не участвует"),
        "time": ("⏱", "остановлен по лимиту времени 90 мин: агент работал (или висел) "
                      "до самого стопа, решение не завершено"),
        "hang": ("💤", "повисшая сессия: агент застрял в ожидании ответа bash-команды "
                      "и ждал её до лимита 90 минут; скелет нетронут"),
        "blank": ("∅", "решение не написано (дифф 0 строк): чекер оценивал "
                       "нетронутый скелет, мерить нечего"),
    }

    def run_badges(r) -> str:
        out = [f'<span class="okmark" data-tip="Живые прогоны: посчитаны в этой строке '
               f'и во всех метриках раздела">✓{r["n"]}</span>'] if r["n"] else []
        for kind, (glyph, tip) in DEAD_ICON.items():
            k = r["deadkinds"].get(kind, 0)
            if k:
                out.append(f'<span class="deadmark {kind}" data-tip="Прогон вне метрик: '
                           f'{tip}. Причина каждого — по пометке ‡ в разделе 7">'
                           f'{glyph}{k}</span>')
        return "<br>".join(out)

    trs = []
    for r in sorted(agg, key=sort_key):
        n, kind = r["n"], r["worst"]
        trcls = (' class="top"' if n and r["n_full"] == n
                 else ' class="neg"' if FAIL in r["kinds"] else "")
        w_m = max(2, min(100, (r["mand"] or 0) / 11 * 100))
        w_b = max(0, min(100, (r["bonus"] or 0) / 2 * 100))
        bar = ("—"
               if r["mand"] is None else
               f'<div class="barwrap s">'
               f'<div class="bar" style="width:{w_m:.1f}%;background:{KIND_HUE[kind]}"></div>'
               f'<span>{fmt_mn(r["mand"])}</span></div>'
               + (f'<div class="barwrap s">'
                  f'<div class="bar" style="width:{w_b:.1f}%;background:{KIND_HUE[kind]}"></div>'
                  f'<span><span class=star>★</span>{fmt_mn(r["bonus"])}</span></div>'
                  if w_b > 0 else ""))
        cell = lambda v: "—" if v is None else f"{v:.1f}"  # noqa: E731
        tests = "—" if r["cov"] is None else f"{r['cov']:.0f}%"
        toks = "—" if r["tok"] is None else f'{r["tok"]:.2f}M'
        if r["cache_pct"] is not None:
            # int() вместо округления: 99.9% → «99%», честные 100% не бывают
            toks += (f'<div class=sub style="line-height:1.3;white-space:nowrap">'
                     f'кэш {int(r["cache_pct"])}%</div>')
        # $ и ₽ (курс фиксирован — раздел 8, id=econ); формат как в отчёте интервью
        cost = ("₽0" if r["cost"] == 0 else
                f'${r["cost"]:.3f}<div class=sub style="line-height:1.3;white-space:nowrap">'
                f'₽{r["cost"] * RUB_USD:.1f}</div>' if r["cost"] is not None else "—")
        price = ("₽0" if r["cost"] == 0 and r["loc"] else
                 f'${r["cost"] / r["loc"] * 1000:.3f}'
                 f'<div class=sub style="line-height:1.3;white-space:nowrap">'
                 f'₽{r["cost"] / r["loc"] * 1000 * RUB_USD:.1f}</div>'
                 if r["cost"] is not None and r["loc"] else "—")
        # бейдж провайдера как в §3 и в отчёте интервью: облако — синий, локально — зелёный
        prov_badge = ('<span class="badge g">облако</span>' if r["prov"] != "ollama"
                      else '<span class="badge b">локально</span>')
        loc_cell = "—"
        if r["loc"] is not None:
            loc_cell = fmt_mn(round(r["loc"]))
            if r["blank_pct"] is not None:
                loc_cell += (f'<div class=sub style="line-height:1.3;white-space:nowrap">пуст. {r["blank_pct"]:.0f}%<br>'
                             f'комм. {r["comm_pct"]:.0f}%</div>')
        trs.append(
            f"<tr{trcls}><td><b>{r['short']}</b><br>{prov_badge}</td>"
            f'<td class="narrow">{r["note"]}</td>'
            f'<td class="narrow">{run_badges(r)}</td>'
            f'<td class="narrow">{bar}</td>'
            f'<td class="narrow">{cell(r["a1"])} / {cell(r["b1"])}</td>'
            f'<td class="narrow">{cell(r["a2"])}</td>'
            f'<td class="narrow">{fmt_mn(r["phpcs"])}</td><td class="narrow">{fmt_mn(r["psalm"])}</td>'
            f'<td class="narrow">{fmt_mn(r["phpstan"])}</td><td class="narrow">{fmt_mn(r["phpmd"])}</td>'
            f'<td class="narrow">{tests}</td>'
            f'<td class="narrow">{loc_cell}</td>'
            f'<td class="narrow">{toks}</td>'
            f'<td class="narrow">{fmt_dur(r["dur"])}</td>'
            f'<td class="narrow">{cost}</td><td class="narrow">{price}</td></tr>')
    return ('<div style="overflow-x:auto"><table><thead><tr>'
            "<th rowspan=2>Модель<sup>1</sup></th><th rowspan=2 class=narrow>Мышл.</th>"
            "<th rowspan=2 class=narrow>Про&shy;гоны<sup>1</sup></th>"
            "<th rowspan=2 class=narrow>Чекер<sup>2</sup></th>"
            "<th rowspan=2 class=narrow>Ревью<br>A/B<sup>3</sup></th>"
            "<th rowspan=2 class=narrow>Слепое<br>ревью<sup>3</sup></th>"
            '<th colspan=4>Качество кода<sup>4</sup></th>'
            "<th rowspan=2 class=narrow>Покр.<br>тест.<sup>5</sup></th>"
            "<th rowspan=2 class=narrow>LOC<sup>6</sup></th>"
            "<th rowspan=2 class=narrow>Токены<sup>7</sup></th>"
            "<th rowspan=2 class=narrow>Время<sup>8</sup></th>"
            "<th rowspan=2 class=narrow>Стои&shy;мость<sup>9</sup></th>"
            "<th rowspan=2 class=narrow>Цена<br>1000 строк<sup>10</sup></th></tr>"
            "<tr><th class=\"narrow sub\">phpcs</th><th class=\"narrow sub\">psalm</th>"
            "<th class=\"narrow sub\">phpstan</th><th class=\"narrow sub\">phpmd</th></tr></thead><tbody>"
            + "".join(trs) + "</tbody></table></div>"
            '<div class=note><sup>1</sup> Прогоны одинаковых условий свёрнуты в одну строку, '
            "числовые колонки такой строки — <b>среднее по прогонам</b>. Сортировка — "
            "от лучших к худшим: средняя сумма сценариев чекера → балл ревью (ось A) → "
            "суммарные нарушения phpcs+psalm+phpstan → покрытие тестами. Детальные данные "
            "Значки колонки «Прогоны»: ✓ — живой прогон (посчитан в строке и метриках); "
            "✂ — обрыв сбоем (исчерпанный баланс/ключ, падение pi); ⏱ — остановлен "
            "по лимиту времени 90 мин; 💤 — повисшая сессия (застрял в ожидании ответа "
            "инструмента); ∅ — решение не написано (дифф 0 строк — чекер оценивал "
            "нетронутый скелет, мерить нечего). Прогоны вне метрик не учитываются в общих "
            "результатах, каждая помечена ‡ в разделе 7, у ‡ — точная причина. "
            "Строки «orca · MTP» — прогоны тех же весов orca "
            "с включённым MTP draft (тег qwen3.8-unc — локальный ретаг тех же блобов, сверка "
            "2026-10-21), качество в пределах шума серий. Время прогона у MTP-серии втрое меньше: "
            "×1.8 — скорость генерации MTP draft (63 → 115 ток/с), ещё ×1.7 — меньший объём "
            "выхода, обычный разброс серий при temp=1 (родная IQ4-серия без MTP даёт 49–95K "
            "выходных токенов на прогон — тот же разброс).<br>"
            "<sup>2</sup> Чекер — детерминированный скрипт без LLM, как устроен — "
            '<a href="#checker">раздел 2</a>. Верхняя полоса — обязательные сценарии '
            "(шкала 11), нижняя ★ — бонусные (шкала 2). Цвет полос — по худшему прогону "
            "группы; разброс прогонов худший–лучший — в разделе 5.<br>"
            "<sup>3</sup> Ревью — ярус 3, формат «A / B»: A «код и архитектура», B «процесс и "
            "коммуникация» (0–4, RUBRIC.md). Слепое ревью — ярус 4: другой изолированный запуск "
            "того же GLM-5.3-Flash, видит только TASK.md и дифф — без чекера, метрик и сессии — "
            "и оценивает только ось A. Расхождения ярусов между собой — раздел 6.<br>"
            "<sup>4</sup> Качество кода — нарушения, найденные утилитами, штук, меньше "
            "лучше: phpcs PSR12 — стиль; "
            "psalm 6 (errorLevel 1) и phpstan (level 8) — типы и логика; их сумма — тай-брейк "
            "сортировки. phpmd — превышения порогов сложности и чистоты, наблюдение: в сравнение "
            "моделей не входит.<br>"
            "<sup>5</sup> Покр. тест. — покрытие строк src/ тестами кандидата (pcov); "
            "«—» — тестов нет.<br>"
            "<sup>6</sup> LOC: строки кода src/ по scc — комментарии и пустые строки не "
            "в счёте; под числом — доля пустых строк и строк-комментариев от всех строк.<br>"
            "<sup>7</sup> Токены — суммарные токены сессии pi.<br>"
            "<sup>8</sup> Время — длительность сессии wall-clock (запуск pi → финиш, до минут).<br>"
            "<sup>9</sup> Стоимость — в долларах, под ними рубли по фиксированному курсу ₽85.15/$ (2026-10-09). "
            "Облачные — токены по тарифу провайдера (OpenRouter — по журналу трат, DeepSeek — "
            "расчёт по usage и пик-тарифу родного API, GigaChat — freemium физлица, ₽0); "
            "локальные — электричество и амортизация: методика расчёта и курс — "
            '<a href="#econ">раздел 8</a>.<br>'
            "<sup>10</sup> Цена 1000 строк — стоимость прогона (сноска 9), делённая на LOC: "
            "во что обходится тысяча строк решения. Наследует особенности стоимости: "
            "у GigaChat — ₽0 (freemium), у локальных — электричество и амортизация.</div>")


def _place_labels(pts, obstacles=()):
    """Авторазмещение подписей точек: перебор кандидатных позиций вокруг
    точки; штраф — пересечение с чужими подписями, точками и obstacles
    (по мотивам place_labels из interview/build_report.py). key → (x, anchor, y)."""
    cands = []
    for dx, dy in ((10, 4), (-10, 4), (10, -6), (-10, -6)):
        cands.append((dx, dy, "start" if dx > 0 else "end"))
    for dist in (8, 14, 22, 32, 44, 58, 72, 86, 100):
        cands.append((0, -dist, "middle"))
        cands.append((0, dist, "middle"))
    for dist in (22, 34, 46, 58, 70, 82, 94, 106):
        cands.append((dist, 4, "start"))
        cands.append((-dist, 4, "end"))
        cands.append((dist, -6, "start"))
        cands.append((-dist, -6, "end"))
        cands.append((dist, 16, "start"))
        cands.append((-dist, 16, "end"))
    placed = list(obstacles)
    dots = [(p["x"] - p["r"], p["y"] - p["r"], p["x"] + p["r"], p["y"] + p["r"])
            for p in pts]

    def label_box(p, dx, dy, anchor):
        if anchor == "start":
            bx0, bx1 = p["x"] + dx, p["x"] + dx + p["w"]
        elif anchor == "end":
            bx0, bx1 = p["x"] + dx - p["w"], p["x"] + dx
        else:  # middle: подпись центрирована по точке
            bx0, bx1 = p["x"] + dx - p["w"] / 2, p["x"] + dx + p["w"] / 2
        return bx0, p["y"] + dy - 10, bx1, p["y"] + dy + 3, False

    out = {}
    for i, p in enumerate(pts):
        best = None
        for ci, (dx, dy, anchor) in enumerate(cands):
            bx0, by0, bx1, by1, _ = label_box(p, dx, dy, anchor)
            if bx0 < p["xmin"] or bx1 > p["xmax"] or by0 < p["ymin"] or by1 > p["ymax"]:
                continue
            area = 0.0
            for L in placed:
                ox = min(bx1, L[2] + 2) - max(bx0, L[0] - 2)
                oy = min(by1, L[3] + 2) - max(bx0, L[1] - 2)
                if ox > 0 and oy > 0:
                    area += ox * oy * 4   # любое пересечение подписей дорого
            for j, D in enumerate(dots):
                if j == i:
                    continue
                ox = min(bx1, D[2]) - max(bx0, D[0])
                oy = min(by1, D[3]) - max(by0, D[1])
                if ox > 0 and oy > 0:
                    area += 3.0 * ox * oy
            score = (round(area, 1), ci)
            if best is None or score < best[0]:
                best = (score, dx, dy, anchor)
        if best is None:   # все кандидаты вне поля — примыкающая подпись
            best = ((0.0, -1), -10, 4, "end")
        _, dx, dy, anchor = best
        placed.append(label_box(p, dx, dy, anchor))  # (5-кортеж: с флагом подписи)
        out[p["key"]] = (p["x"] + dx, anchor, p["y"] + dy)
    return out


MAP_SHORT = {"DeepSeek V4.1 Flash-1017": "DeepSeek"}


def _map_names(rows: list[dict]) -> None:
    """Подписи и ключи точек карт качества: у двух прогонов могут совпасть
    и модель, и режим (opus max: run-02 через OpenRouter, run-04 через
    OrcaRouter) — повторные подписи различаем провайдером вызова (OR/orca,
    без тега — порядковым номером), ключ делаем уникальным, иначе раскладка
    подписей схлопывает строки в одну. Пишет r["_name"] и r["_key"]."""
    from collections import Counter
    cnt = Counter(r["short"] for r in rows)
    prov_tag = {"openrouter": "OR", "orcarouter": "orca"}
    seen: dict[tuple, int] = {}
    for r in rows:
        k = (r["short"], r["note"])
        seen[k] = seen.get(k, 0) + 1
        name = MAP_SHORT.get(r["short"], r["short"])
        if cnt[r["short"]] > 1:
            name += f" ·{r['note']}"
        if seen[k] > 1:
            tag = prov_tag.get(r.get("prov"), "")
            name += f"·{tag}" if tag else f"·{seen[k]}"
        r["_name"], r["_key"] = name, (k[0], k[1], seen[k])


def quality_price_map(agg: list[dict]) -> str:
    """Карта моделей: результат чекера (из 13) против цены прогона,
    лог-шкала — как «Карта моделей» в отчёте интервью."""
    import math
    pts_src = [r for r in agg if r["cost"] is not None and r["a1"] is not None]
    if not pts_src:
        return ""
    W, H = 920, 540
    ML, MR, MT, MB = 64, 24, 28, 58
    PW, PH = W - ML - MR, H - MT - MB
    XMIN, XMAX = 0.005, 10           # $ за прогон, лог-шкала; GigaChat ₽0 — левый край,
                                     # правый — дорогие Anthropic-флагманы ($9.18 opus max)
    YMIN, YMAX = 0.0, 4.25           # балл ревью, ось A (код и архитектура)
    QX, QY = 0.15, REVIEW_GOOD       # пороги квадрантов: $0.15 и ревью оси A
    GROUPS = [("#315f9e", "облачные"), ("#5b9bd5", "российские облака"),
              ("#2f7d54", "локальные")]

    def hue_of(prov):
        return {"gigachat": "#5b9bd5", "ollama": "#2f7d54"}.get(prov, "#315f9e")

    def px(cost):
        c = max(cost, XMIN)
        return ML + (math.log10(c) - math.log10(XMIN)) / (math.log10(XMAX) - math.log10(XMIN)) * PW

    def py(v):
        return MT + (YMAX - v) / (YMAX - YMIN) * PH

    def rad_of(viol):
        # диаметр — суммарные замечания phpcs+psalm+phpstan: больше — грязнее
        return 4.5 if viol is None else 4.5 + min(viol / 250, 1) * 9

    # подписи на карте: краткие; режим (low/max) — только если у модели
    # несколько строк в таблице (иначе «Sol 6.1 ·low» и «Sol 6.1 ·max» неотличимы);
    # полные дубликаты (модель+режим) различаются провайдером — _map_names
    _map_names(pts_src)
    pts = []
    from collections import Counter
    spot_cnt = Counter((round(px(r["cost"])), round(py(r["a1"]))) for r in pts_src)
    same_spot: dict[tuple, int] = {}
    forced = {}
    for r in pts_src:
        lbl = r["_name"]
        x = px(r["cost"])
        y = py(r["a1"])
        # совпавшие точки (обе гигачат-модели ₽0 прижаты к левому краю):
        # подписи принудительным стеком с шагом 14, иначе накладываются
        spot = (round(x), round(y))
        n = same_spot.get(spot, 0)
        same_spot[spot] = n + 1
        if spot_cnt[spot] > 1:
            # стек вверх: внизу может лежать капшен квадранта
            forced[r["_key"]] = (x + 10, "start", y - 16 - 14 * n)
        pts.append({"key": r["_key"], "x": x, "y": y,
                    "r": rad_of(r["viol"]), "w": len(lbl) * 5.2 + 6,
                    "xmin": ML - 30, "xmax": ML + PW + 8,
                    "ymin": MT - 6, "ymax": MT + PH + 6})
    # верхние капшены — над полем (внутри поля они сталкиваются с точками
    # полного зачёта), нижние — внутри и считаются препятствиями раскладки
    caps = [(ML+10, MT-8, "start", "хорошо и дёшево"),
            (ML+PW-10, MT-8, "end", "хорошо и дорого"),
            (ML+10, MT+PH-12, "start", "плохо и дёшево"),
            (ML+PW-10, MT+PH-12, "end", "плохо и дорого")]
    obstacles = []
    for cx, cy, anc, txt in caps[2:]:
        w = len(txt) * 6.3 + 10
        bx0, bx1 = (cx, cx + w) if anc == "start" else (cx - w, cx)
        obstacles.append((bx0, cy - 12, bx1, cy + 3, True))
    label_geo = _place_labels(pts, obstacles)
    label_geo.update(forced)
    for r in pts_src:
        if r["a1"] == 0:   # точка на нижней кромке — подпись над точкой,
            label_geo[r["_key"]] = (px(r["cost"]) + 10, "start",
                                    py(r["a1"]) - 30)   # выше капшена

    svg = []
    for gv in (1, 2, 3, 4):
        y = py(gv)
        svg.append(f'<line x1={ML} y1={y:.0f} x2={ML+PW} y2={y:.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y+4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for xv in (0.01, 0.02, 0.05, 0.1, 0.2, 0.5, 1, 2, 5, 10):
        x = px(xv)
        svg.append(f'<line x1="{x:.0f}" y1="{MT}" x2="{x:.0f}" y2="{MT+PH}" stroke="#e4e7ec"/>'
                   f'<text x="{x:.0f}" y="{MT+PH+20}" text-anchor="middle" font-size="12" '
                   f'fill="#66707f">${xv:g}</text>')
    # квадранты
    xq, yq = px(QX), py(QY)
    svg.append(f'<line x1={xq:.0f} y1={MT} x2={xq:.0f} y2={MT+PH} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    svg.append(f'<line x1={ML} y1={yq:.0f} x2={ML+PW} y2={yq:.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    for cx, cy, anc, txt in caps:
        svg.append(f'<text x={cx} y={cy} text-anchor={anc} font-size=12.5 font-style=italic fill=#9aa3ad>{txt}</text>')
    # крупные точки рисуются первыми: мелкие остаются доступны для наведения
    for r in sorted(pts_src, key=lambda r: -rad_of(r["viol"])):
        x, y = px(r["cost"]), py(r["a1"])
        rad, hue = rad_of(r["viol"]), hue_of(r["prov"])
        fx = (x - ML) / PW
        chk = (f"чекер {r['mand']:g}" + (f"★{r['bonus']:g}" if r["bonus"] else "")
               + f" из 13 · прогонов {r['n']}"
               + (f" (+{r['n_dead']} вне метрик)" if r["n_dead"] else ""))
        viol = (f"нарушения {r['viol']:.0f}" if r["viol"] is not None
                else "нарушения —")
        cost_rub = "₽0" if r["cost"] == 0 else f"≈₽{r['cost'] * RUB_USD:.1f}"
        tip_lines = [f'{r["short"]} ({r["note"]})', chk,
                     f"ревью A {r['a1']:.1f} · {viol}",
                     f"цена ${r['cost']:.3f} {cost_rub}"]
        tip_w = max(len(t) for t in tip_lines) * 6.4 + 16
        tip_left = fx > 0.72   # у правого края карточка раскрывается влево
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x="{tx:.0f}" y="{y - 56:.0f}" width="{tip_w:.0f}" '
               f'height="{20 + 14*len(tip_lines):.0f}" rx="7" fill="#1d2433" fill-opacity="0.95"/>']
        for i, t in enumerate(tip_lines):
            bold = ' font-weight="bold"' if i == 0 else ""
            fill = "#ffffff" if i == 0 else "#c8d4e4"
            tip.append(f'<text x="{tx + 8:.0f}" y="{y - 38 + i*15:.0f}" font-size="11.5" '
                       f'fill="{fill}"{bold} text-anchor="start">{t}</text>')
        tx, anchor, ty = label_geo[r["_key"]]
        # линия-выноска: подпись не примыкает к точке — связываем визуально
        lx = tx + 3 if anchor == "end" else tx - 3
        leader = (f'<line x1="{x:.0f}" y1="{y:.0f}" x2="{lx:.0f}" y2="{ty - 3:.0f}" '
                  f'stroke="#98a2b3" stroke-width="1"/>'
                  if abs(tx - x) > rad + 14 or abs(ty - y) > rad + 12 else "")
        svg.append(f'<g class="pt">'
                   f'<circle class="dot" cx="{x:.0f}" cy="{y:.0f}" r="{rad:.1f}" fill="{hue}" '
                   f'fill-opacity="0.8" stroke="#fff" stroke-width="1.5"/>'
                   f'<text x="{tx:.0f}" y="{ty:.0f}" text-anchor="{anchor}" font-size="11" '
                   f'fill="{hue}">{r["_name"]}</text>'
                   + leader
                   + f'<g class="tip">' + "".join(tip) + "</g></g>")
    svg.append(f'<text x="{ML+PW/2:.0f}" y="{H-16}" text-anchor="middle" font-size="12.5" fill="#66707f">'
               f'цена прогона, $ — лог-шкала; GigaChat ₽0 (freemium) прижат к левому краю</text>')
    svg.append(f'<text x=16 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT+PH/2:.0f})">'
               f'ревью, ось A — код и архитектура (0–4)</text>')
    return ('<h3>Карта моделей: качество и цена</h3>'
            '<p>Каждая точка — строка таблицы выше: по вертикали балл ревью оси A '
            '(«код и архитектура», 0–4), по горизонтали средняя цена прогона в лог-шкале. '
            'Пунктир делит поле на зоны: порог качества — 3 балла ревью, порог цены — '
            '$0.15 за прогон. GigaChat физлицу стоит ₽0 (freemium) и вынесен на левый '
            'край; повисшие прогоны в средние точек не входят.</p>'
            + f'<svg class="scatter" width="{W}" height="{H}" viewBox="0 0 {W} {H}" style="max-width:100%">'
            + "".join(svg) + '<g class="tip-layer"></g></svg>'
            + '<p class=sub>' + " · ".join(f'<span style="color:{h}">●</span> {n}' for h, n in GROUPS)
            + '. Диаметр точки — суммарные замечания phpcs+psalm+phpstan '
              '(больше — грязнее). Тултип — при наведении на точку.</p>')


def quality_speed_map(agg: list[dict]) -> str:
    """Карта моделей: качество (ревью, ось A) против скорости (минуты прогона),
    диаметр точки — выходные токены сессии; как в отчёте интервью."""
    pts_src = [r for r in agg if r["a1"] is not None]
    if not pts_src:
        return ""
    _map_names(pts_src)

    W, H = 920, 540
    ML, MR, MT, MB = 64, 24, 28, 58
    PW, PH = W - ML - MR, H - MT - MB
    XMIN, XMAX = 0, 45               # минуты прогона
    YMIN, YMAX = 0.0, 4.25           # балл ревью, ось A
    QX, QY = 15, REVIEW_GOOD         # пороги квадрантов: 15 минут и ревью оси A
    GROUPS = [("#315f9e", "облачные"), ("#5b9bd5", "российские облака"),
              ("#2f7d54", "локальные")]

    def hue_of(prov):
        return {"gigachat": "#5b9bd5", "ollama": "#2f7d54"}.get(prov, "#315f9e")

    def px(v):
        return ML + (max(v, XMIN) - XMIN) / (XMAX - XMIN) * PW

    def py(v):
        return MT + (YMAX - v) / (YMAX - YMIN) * PH

    def rad_of(out):
        # корневая шкала: диаметр — выходные токены сессии
        return 5.5 + min(max((out - 40000) / 100000, 0), 1) ** 0.5 * 8

    pts_src = sorted(pts_src, key=lambda r: r["short"])
    pts = []
    for r in pts_src:
        pts.append({"key": r["_key"], "x": px(r["dur"]), "y": py(r["a1"]),
                    "r": rad_of(r["out"]), "w": len(r["_name"]) * 5.2 + 6,
                    "xmin": ML - 30, "xmax": ML + PW + 8,
                    "ymin": MT - 6, "ymax": MT + PH + 6})
    label_geo = _place_labels(pts)
    # Sol 6.1 ·low: автораскладка наезжает на подпись Luna 6 ·max — уводим под точку
    sols = [r for r in pts_src if r["short"] == "Sol 6.1" and r["note"] == "low"]
    if sols:
        rl = sols[0]
        label_geo[rl["_key"]] = (px(rl["dur"]), "middle", py(rl["a1"]) + 22)
    for r in pts_src:
        if r["a1"] == 0:   # точка на нижней кромке — подпись над точкой,
            label_geo[(r["short"], r["note"])] = (px(r["dur"]) + 10, "start",
                                                  py(r["a1"]) - 30)   # выше капшена

    svg = []
    for gv in (1, 2, 3, 4):
        y = py(gv)
        svg.append(f'<line x1={ML} y1={y:.0f} x2={ML+PW} y2={y:.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML-10} y={y+4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    for xv in (10, 20, 30, 40):
        x = px(xv)
        svg.append(f'<line x1="{x:.0f}" y1="{MT}" x2="{x:.0f}" y2="{MT+PH}" stroke="#e4e7ec"/>'
                   f'<text x="{x:.0f}" y="{MT+PH+20}" text-anchor="middle" font-size="12" '
                   f'fill="#66707f">{xv}</text>')
    # квадранты
    xq, yq = px(QX), py(QY)
    svg.append(f'<line x1={xq:.0f} y1={MT} x2={xq:.0f} y2={MT+PH} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    svg.append(f'<line x1={ML} y1={yq:.0f} x2={ML+PW} y2={yq:.0f} stroke="#aab3bf" stroke-dasharray="6,4"/>')
    caps = [(ML+10, MT-8, "start", "хорошо и быстро"),
            (ML+PW-10, MT-8, "end", "хорошо и медленно"),
            (ML+10, MT+PH-12, "start", "плохо и быстро"),
            (ML+PW-10, MT+PH-12, "end", "плохо и медленно")]
    obstacles = []
    for cx, cy, anc, txt in caps[2:]:
        w = len(txt) * 6.3 + 10
        bx0, bx1 = (cx, cx + w) if anc == "start" else (cx - w, cx)
        obstacles.append((bx0, cy - 12, bx1, cy + 3, True))
    for cx, cy, anc, txt in caps:
        svg.append(f'<text x={cx} y={cy} text-anchor={anc} font-size=12.5 font-style=italic fill=#9aa3ad>{txt}</text>')
    # большие (токengu-тяжёлые) точки рисуются первыми
    for r in sorted(pts_src, key=lambda r: -rad_of(r["out"])):
        x, y = px(r["dur"]), py(r["a1"])
        rad, hue = rad_of(r["out"]), hue_of(r["prov"])
        chk = (f"чекер {r['mand']:g}" + (f"★{r['bonus']:g}" if r["bonus"] else "")
               + f" из 13 · прогонов {r['n']}"
               + (f" (+{r['n_dead']} вне метрик)" if r["n_dead"] else ""))
        tip_lines = [f'{r["short"]} ({r["note"]})',
                     f"ревью A {r['a1']:.1f} · {chk}",
                     f"{r['dur']:.0f} мин · выход {r['out'] / 1000:.0f} тыс. токенов",
                     f"цена ${r['cost']:.3f}"]
        tip_w = max(len(t) for t in tip_lines) * 6.4 + 16
        tip_left = (x - ML) / PW > 0.72
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x="{tx:.0f}" y="{y - 56:.0f}" width="{tip_w:.0f}" '
               f'height="{20 + 14*len(tip_lines):.0f}" rx="7" fill="#1d2433" fill-opacity="0.95"/>']
        for i, t in enumerate(tip_lines):
            bold = ' font-weight="bold"' if i == 0 else ""
            fill = "#ffffff" if i == 0 else "#c8d4e4"
            tip.append(f'<text x="{tx + 8:.0f}" y="{y - 38 + i*15:.0f}" font-size="11.5" '
                       f'fill="{fill}"{bold} text-anchor="start">{t}</text>')
        tx, anchor, ty = label_geo[r["_key"]]
        svg.append(f'<g class="pt">'
                   f'<circle class="dot" cx="{x:.0f}" cy="{y:.0f}" r="{rad:.1f}" fill="{hue}" '
                   f'fill-opacity="0.8" stroke="#fff" stroke-width="1.5"/>'
                   f'<text x="{tx:.0f}" y="{ty:.0f}" text-anchor="{anchor}" font-size="11" '
                   f'fill="{hue}">{r["_name"]}</text>'
                   f'<g class="tip">' + "".join(tip) + "</g></g>")
    svg.append(f'<text x="{ML+PW/2:.0f}" y="{H-16}" text-anchor="middle" font-size="12.5" fill="#66707f">'
               f'длительность прогона, минуты; диаметр точки — выходные токены сессии</text>')
    svg.append(f'<text x=16 y={MT+PH/2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT+PH/2:.0f})">'
               f'ревью, ось A — код и архитектура (0–4)</text>')
    return ('<h3>Карта моделей: качество и скорость</h3>'
            '<p>Каждая точка — строка таблицы выше: по вертикали балл ревью оси A '
            '(«код и архитектура», 0–4), по горизонтали длительность прогона. Пунктир — '
            'пороги зон: 3 балла ревью и 15 минут. Диаметр точки — выходные токены '
            'сессии; повисшие прогоны в средние не входят.</p>'
            + f'<svg class="scatter" width="{W}" height="{H}" viewBox="0 0 {W} {H}" style="max-width:100%">'
            + "".join(svg) + '<g class="tip-layer"></g></svg>'
            + '<p class=sub>' + " · ".join(f'<span style="color:{h}">●</span> {n}' for h, n in GROUPS)
            + ". Подписи при наведении на точку.</p>")


def quality_triangle_map(agg: list[dict]) -> str:
    """3D-карта моделей: цена × качество × скорость (three.js с CDN, орбита
    и зум); радиус сферы — выходные токены сессии, золотой каркасный октандр
    у вершины качества — «идеальный угол» (дёшево + быстро + качественно).
    Порт quality_triangle_3d из interview/build_report.py."""
    import json as _json
    from collections import Counter
    pts_src = [r for r in agg if r["a1"] is not None]
    HUES = {"gigachat": "#5b9bd5", "ollama": "#2f7d54"}
    _map_names(pts_src)
    data = []
    for r in pts_src:
        price = (f"${r['cost']:.3f}"
                 + (" · ₽0" if r["cost"] == 0 else f" · ≈₽{r['cost'] * RUB_USD:.0f}"))
        data.append({"name": r["_name"], "score": r["a1"],
                     "cost": round(max(r["cost"], 0.01), 4),
                     "dur": round(r["dur"], 1), "tok": int(r["out"] or 0),
                     "chk": (f"чекер {r['mand']:g}" + (f"★{r['bonus']:g}" if r["bonus"] else "")
                             + " из 13"),
                     "price": price, "color": HUES.get(r["prov"], "#315f9e")})
    payload = _json.dumps(data, ensure_ascii=False)

    html = """<h3>Треугольник: быстро, качественно, дёшево</h3>
<p>«Железное» правило — быстро, качественно, дёшево: выбери любые два. Три оси —
цена прогона (лог-сжатие, чтобы дешёвые модели не слипались), балл ревью оси A
и длительность прогона в минутах. Каждый шарик — строка таблицы результатов
(модель в одном режиме мышления), радиус шарика — выходные токены сессии.
Сцену можно вращать мышью и приближать колесом; золотой каркасный октандр у
вершины качества — маркер недостижимого идеала (максимально дёшево + быстро +
качественно), вокруг него пусто.</p>
<div id="tri3d" style="position:relative;height:700px;border:1px solid var(--line);border-radius:12px;background:#fff">
  <div id="tri3d-fallback" style="padding:24px;color:#66707f;font-size:14px">
    3D-сцена не загрузилась: для неё нужен интернет (three.js с CDN).
  </div>
</div>
<p class=sub>Цвета повторяют группы карт выше: синие облака, голубые российские,
зелёные локальные. Наведите курсор на шарик — карточка с ревью, чекером, ценой,
минутами и выходными токенами. Модели без ревью (bonsai-2) на сцене не показаны.</p>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script>
(function () {
  if (typeof THREE === 'undefined') return;   // CDN недоступен — текст-заглушка остаётся
  var host = document.getElementById('tri3d');
  host.innerHTML = '';
  var DATA = __DATA__;
  var W = host.clientWidth || 920, H = 700;
  var scene = new THREE.Scene();
  scene.background = new THREE.Color(0xffffff);
  var FR = 11;
  var aspect = W / H;
  var camera = new THREE.OrthographicCamera(-FR * aspect, FR * aspect, FR, -FR, 0.1, 200);
  camera.position.set(14, 15, 18);
  var renderer = new THREE.WebGLRenderer({antialias: true});
  renderer.setSize(W, H);
  renderer.setPixelRatio(window.devicePixelRatio || 1);
  host.appendChild(renderer.domElement);
  var controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.target.set(4.5, 5.5, 3.5);
  controls.enableDamping = true;
  // сцена: куб 10×10×10, x = цена (лог, дёшево ближе), y = ревью ось A, z = минуты
  var S = 10;
  function vx(cost) { return Math.max(0, Math.min(S, (Math.log10(cost) + 2.3) / 2.6 * S)); }
  function vy(score) { return Math.max(0, Math.min(S, score / 4.25 * S)); }
  function vz(min) { return Math.max(0, Math.min(S, min / 45 * S)); }
  var grid = new THREE.GridHelper(S, 10, 0xdde3ea, 0xe9edf2);
  grid.position.set(S / 2, 0, S / 2); scene.add(grid);
  function axis(a, b) {
    var g = new THREE.BufferGeometry().setFromPoints([a, b]);
    scene.add(new THREE.Line(g, new THREE.LineBasicMaterial({color: 0x8b95a3})));
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
  label('ревью A', '#2f7d54', 1.4, S + 1.3, 0, 0.6);
  label('время', '#b3543f', 2.0, 0.6, S + 2.8, 0.6);
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
    var r = 0.16 + Math.sqrt(Math.max(0, Math.min(1, (m.tok - 40000) / 100000))) * 0.34;
    var mesh = new THREE.Mesh(
        new THREE.SphereGeometry(r, 24, 18),
        new THREE.MeshLambertMaterial({color: new THREE.Color(m.color), transparent: true, opacity: 0.92}));
    mesh.position.set(vx(m.cost), vy(m.score), vz(m.dur));
    mesh.userData = m;
    scene.add(mesh);
    spheres.push(mesh);
    label(m.name, m.color, mesh.position.x, mesh.position.y + r + 0.3, mesh.position.z, 0.42);
  });
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
      tip.innerHTML = '<b>' + m.name + '</b><br>ревью A ' + m.score.toFixed(2)
          + ' · ' + m.chk + '<br>' + m.price + ' · ' + Math.round(m.dur * 10) / 10 + ' мин'
          + '<br>~' + Math.round(m.tok / 1000) + ' тыс. выходных токенов';
      tip.style.display = 'block';
      tip.style.left = Math.min(e.clientX - b.left + 16, b.width - 230) + 'px';
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


def cost_scatter(data: list[dict]):
    """Время × токены; диаметр — пик контекста; цвет — итог чекера."""
    pts = [r for r in data if r["tok"]]
    if not pts:
        return '<div class=note>нет данных</div>'
    W, H = 920, 480
    ML, MR, MT, MB = 56, 24, 24, 52
    PW, PH = W - ML - MR, H - MT - MB
    XMAX = max(44, max(r["dur"] for r in pts) * 1.1)
    YMAX = max(4, max(r["tok"] for r in pts) * 1.15)

    def px(v):
        return ML + v / XMAX * PW

    def py(v):
        return MT + (YMAX - v) / YMAX * PH

    def dot_r(peak):
        return 7 + min(peak / 90, 1) * 8

    svg = []
    for k in range(5):
        gv = YMAX * (4 - k) / 4
        svg.append(f'<line x1={ML} y1={py(gv):.0f} x2={ML + PW} y2={py(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML - 10} y={py(gv) + 4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv:.0f}</text>')
    for k in range(5):
        xv = XMAX * k / 4
        svg.append(f'<line x1={px(xv):.0f} y1={MT} x2={px(xv):.0f} y2={MT + PH} stroke="#e4e7ec"/>'
                   f'<text x={px(xv):.0f} y={MT + PH + 20} text-anchor=middle font-size=12 fill=#66707f>{xv:.0f}</text>')
    # подписи: раскладка по кругу вокруг точки, повторные прогоны модели сдвигаются
    labeled: set[str] = set()
    seen: dict[str, int] = {}
    for r in pts:
        n = seen.get(r["short"], 0)
        seen[r["short"]] = n + 1
        show_label = r["short"] not in labeled
        labeled.add(r["short"])
        hue = KIND_HUE[r["kind"]]
        x, y = px(r["dur"]), py(r["tok"])
        dx, dy = [(10, -6), (-10, 12), (12, 6), (-12, -8)][n % 4]
        chk = f"{r['mand']:g}" + (f"★{r['bonus']:g}" if r['bonus'] else "")
        tip_lines = [f"{r['short']} · {r['run']}", f"чекер {chk} ({r['note']})",
                     f"{fmt_dur(r['dur'])} · {r['tok']:.2f}M токенов · пик {r['peak']:.1f}K · ⇄{r['comp']}"]
        tip_w = max(len(t) for t in tip_lines) * 6.6 + 18
        tip_left = x + 16 + tip_w > ML + PW
        tx = x - 14 - tip_w if tip_left else x + 14
        tip = [f'<rect x={tx:.0f} y={y - 52:.0f} width={tip_w:.0f} height=68 rx=7 fill="#1d2433" fill-opacity="0.95"/>']
        for i, t in enumerate(tip_lines):
            tip.append(f'<text x={tx + 8:.0f} y={y - 34 + i * 15:.0f} font-size=11.5 '
                       f'fill="{"#ffffff" if i == 0 else "#c8d4e4"}"{" font-weight=bold" if i == 0 else ""}>{t}</text>')
        label = (f'<text x={x + dx:.0f} y={y + dy:.0f} text-anchor="{"end" if dx < 0 else "start"}" '
                 f'font-size=11 fill={hue}>{r["short"]} ×{seen[r["short"]]}</text>') if show_label else ""
        svg.append(f'<g class="pt"><circle class="dot" cx={x:.0f} cy={y:.0f} r={dot_r(r["peak"]):.1f} '
                   f'fill={hue} fill-opacity="0.8" stroke="#fff" stroke-width="1.5"/>'
                   + label
                   + f'<g class="tip">' + "".join(tip) + "</g></g>")
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 14} text-anchor=middle font-size=12.5 fill=#66707f>'
               "минуты прогона (лимит 90 мин)</text>")
    svg.append(f'<text x=16 y={MT + PH / 2:.0f} font-size=12.5 fill=#66707f transform="rotate(-90 16 {MT + PH / 2:.0f})">'
               "млн токенов</text>")
    return ('<svg class="scatter" width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s'
            '<g class="tip-layer"></g></svg>'
            '<p class=sub>%s. Диаметр точки — пик контекста.</p>'
            % (W, H, W, H, "".join(svg),
               " · ".join(f'<span style="color:{KIND_HUE[h]}">●</span> {n}' for h, n in KIND_NAME.items() if h != NA)))


def ctx_chart(data: list[dict]):
    """Пик контекста по прогонам (лимит 102.4K), подпись — компакты."""
    pts = [r for r in data if r["peak"]]
    if not pts:
        return '<div class=note>нет данных</div>'
    W, H = 620, 310
    ML, MR, MT, MB = 46, 14, 22, 56
    PW, PH = W - ML - MR, H - MT - MB
    YMAX = 112

    def py(v):
        return MT + (YMAX - v) / YMAX * PH

    svg = []
    for gv in (0, 25, 50, 75, 100):
        svg.append(f'<line x1={ML} y1={py(gv):.0f} x2={ML + PW} y2={py(gv):.0f} stroke="#e4e7ec"/>'
                   f'<text x={ML - 10} y={py(gv) + 4:.0f} text-anchor=end font-size=12 fill=#66707f>{gv}</text>')
    n = len(pts)
    bw = PW / n * 0.52
    for i, r in enumerate(pts):
        cx = ML + PW / n * (i + 0.5)
        svg.append(f'<rect x={cx - bw / 2:.0f} y={py(r["peak"]):.0f} width={bw:.0f} height={MT + PH - py(r["peak"]):.0f} '
                   f'rx=4 fill={KIND_HUE[r["kind"]]} fill-opacity="0.82"/>'
                   f'<text x={cx:.0f} y={py(r["peak"]) - 24:.0f} text-anchor=middle font-size=12.5 font-weight=600 fill=#1d2433>{r["peak"]:.1f}K</text>'
                   f'<text x={cx:.0f} y={py(r["peak"]) - 9:.0f} text-anchor=middle font-size=11.5 fill=#66707f>⇄{r["comp"]}</text>'
                   f'<text x={cx:.0f} y={MT + PH + 18:.0f} text-anchor=middle font-size=11.5 fill=#66707f>{r["run"].removeprefix("run-")}</text>')
    # модель — один раз под группой её прогонов
    i = 0
    while i < n:
        j = i
        while j + 1 < n and pts[j + 1]["short"] == pts[i]["short"]:
            j += 1
        gx = ML + PW / n * (i + j + 1) / 2
        svg.append(f'<text x={gx:.0f} y={MT + PH + 34:.0f} text-anchor=middle font-size=12 fill=#1d2433>{pts[i]["short"]}</text>')
        if j + 1 < n:
            xsep = ML + PW / n * (j + 1)
            svg.append(f'<line x1={xsep:.0f} y1={MT} x2={xsep:.0f} y2={MT + PH + 6:.0f} stroke="#aab3bf" stroke-dasharray="3,3" opacity="0.6"/>')
        i = j + 1
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


def judges_dumbbell(data: list[dict]):
    rows = [(f"{r['short']} · {r['run'].removeprefix('run-')}", r["a2"], r["a1"],
             "слепое ревью, ярус 4 (GLM-флеш)", "ревью, ярус 3 (GLM-флеш)")
            for r in data
            if not r["dead"] and r["a1"] is not None and r["a2"] is not None]
    if not rows:
        return ""
    return dumbbell(rows, 4, (0, 1, 2, 3, 4), "ось A: что сделано (0–4)", fmt="{:g}")


def think_dumbbell(data: list[dict]):
    """Устойчивость: разброс суммы чекера по прогонам модели (худший → лучший)."""
    by_model: dict[str, list[int]] = {}
    for r in data:
        # только живые прогоны: обрывок решения (✂) — не «худший прогон» модели
        if not r["dead"] and r["mand"] is not None:
            by_model.setdefault(r["short"], []).append(r["mand"] + r["bonus"])
    rows = [(m, min(v), max(v), "худший прогон", "лучший прогон")
            for m, v in sorted(by_model.items())]
    if not rows:
        return ""
    return dumbbell(rows, 13.6, (0, 5, 11, 13), "сценарии чекера (из 13)",
                    ref_lines=((11, "обязательные 11"), (13, "полный зачёт 13")))


def structure_scan(data: list[dict]) -> list[dict]:
    """Слепки структуры src/ и tests/ по живым прогонам с решением.

    Каркас даёт только src/Kernel.php и пустой tests/, а TASK.md структуру
    не задаёт («Архитектура, разбиение на слои/модули, именование — на ваше
    усмотрение»), значит каждый каталог решения — выбор модели. Прогоны
    вне метрик, включая не написавшие решение, — вне анализа, как во всех
    метриках разделов 1–6."""
    out = []
    for r in data:
        if r["dead"]:
            continue
        sol = ROOT / "models" / r["folder"] / "backend" / "test-assignment" / r["run"] / "solution"
        src, tests = sol / "src", sol / "tests"
        if not src.is_dir():
            continue
        flist = sorted(p.relative_to(src).as_posix() for p in src.rglob("*.php"))
        if len(flist) <= 1:   # скелет нетронут: Kernel.php
            continue
        top = sorted((p.name for p in src.iterdir() if p.is_dir()), key=str.lower)
        dirs = {p.relative_to(src).as_posix() for p in src.rglob("*") if p.is_dir()}
        # каркасный Kernel.php — не выбор модели, в дерево файлов не попадает
        files = [f for f in flist if f != "Kernel.php"]
        tdirs: set[str] = set()
        tfiles: list[str] = []
        ntf = 0
        if tests.is_dir():
            tdirs = {p.relative_to(tests).as_posix() for p in tests.rglob("*") if p.is_dir()}
            tfiles = sorted(p.relative_to(tests).as_posix() for p in tests.rglob("*.php"))
            ntf = len(tfiles)
        out.append({**r, "top": top, "dirs": dirs, "files": files,
                    "tdirs": tdirs, "tfiles": tfiles, "ntf": ntf})
    return out


def _jaccard(a, b) -> float:
    """Пересечение наборов по объединению, 0–1."""
    A, B = set(a), set(b)
    return len(A & B) / len(A | B) if A | B else 1.0


def structure_section(data: list[dict]) -> str:
    """Анализ структуры хранения кода src/ и tests/: общий знаменатель
    (частоты каталогов), слепки верхнего уровня по прогонам и постоянство
    структуры внутри модели против типичности между моделями (аналог
    постоянства ответов в отчёте интервью). Выборка — успешные решения:
    прогоны режимов из верхних квадрантов карт §4 (ревью оси A ≥ REVIEW_GOOD)."""
    import statistics as st
    from collections import Counter
    from itertools import combinations

    agg_rows = results_agg(data)
    gkeys = {(a["folder"], a["note"], a["ctx"], a["tuned"])
             for a in agg_rows if a["n"] and (a["a1"] or 0) >= REVIEW_GOOD}
    excluded = [a for a in agg_rows if a["n"] and (a["a1"] or 0) < REVIEW_GOOD]

    def gkey(r):  # noqa: E731
        # серия MTP слита с родной: те же веса, скорость генерации на структуру
        # не влияет; -tuned — другие веса, остаётся отдельной группой
        return (r["folder"], r["note"], r["ctx"], "-tuned" in r["tag"])

    runs = [r for r in structure_scan(data) if gkey(r) in gkeys]
    if not runs:
        return ""
    n = len(runs)
    low = lambda names: [x.lower() for x in names]  # noqa: E731  (Ui/UI, Util/util)

    # группы — как в основной таблице §4: модель в одном режиме (уровень
    # размышлений + контекст + серия); уровень рассуждений может влиять на
    # раскладку, поэтому постоянство смотрится внутри режима; MTP-серия
    # слита с родной (те же веса)
    by_g: dict[tuple, list[dict]] = {}
    for r in runs:
        by_g.setdefault(gkey(r), []).append(r)

    def pair_jac(rs):
        return [(_jaccard(low(a["top"]), low(b["top"])), _jaccard(a["dirs"], b["dirs"]))
                for a, b in combinations(rs, 2)]

    within = [pj for rs in by_g.values() for pj in pair_jac(rs)]
    # типичность — между моделями (разные папки); пары одного файла в разных
    # режимах не участвуют ни там, ни там: это не «чужая модель», но и не
    # повтор режима
    between = [( _jaccard(low(a["top"]), low(b["top"])), _jaccard(a["dirs"], b["dirs"]))
               for a, b in combinations(runs, 2) if a["folder"] != b["folder"]]
    w_top = st.mean(p[0] for p in within) if within else 0.0
    w_all = st.mean(p[1] for p in within) if within else 0.0
    b_top = st.mean(p[0] for p in between) if between else 0.0
    b_all = st.mean(p[1] for p in between) if between else 0.0
    # тот же расчёт по всем живым прогонам с решением — для сравнения
    all_runs = structure_scan(data)
    if len(all_runs) > len(runs):
        by_ga: dict[tuple, list[dict]] = {}
        for r in all_runs:
            by_ga.setdefault(gkey(r), []).append(r)
        wt_a = st.mean(p[0] for rs in by_ga.values() for p in pair_jac(rs))
        bt_a = st.mean(_jaccard(low(a["top"]), low(b["top"]))
                       for a, b in combinations(all_runs, 2) if a["folder"] != b["folder"])
    else:
        wt_a = bt_a = None

    # сводное дерево каталогов: все пути, встреченные в src/ и tests/ у живых
    # прогонов, слитые в одну структуру. Процент — доля прогонов, где путь
    # существует (знаменатель — все прогоны анализа); яркость имени — та же
    # доля: реже — бледнее. Ветки, встреченные ровно в одном прогоне, свёрнуты
    # в строку «…»: поддерево одиночки состоит из одиночек (предок существует
    # в каждом прогоне потомка), так что ничего, кроме хвоста из 1 прогона,
    # свёртка не прячет
    src_dirs: Counter = Counter()
    src_files: Counter = Counter()
    t_dirs: Counter = Counter()
    t_files: Counter = Counter()
    for r in runs:
        src_dirs.update(r["dirs"])
        src_files.update(r["files"])
        t_dirs.update(r["tdirs"])
        t_files.update(r["tfiles"])

    def _ink(pct: int) -> str:
        """Яркость имени ступенями по проценту прогонов: чем реже, тем бледнее."""
        if pct >= 50:
            return "#10151f"
        if pct >= 25:
            return "#454e5d"
        if pct >= 10:
            return "#6e7683"
        return "#98a1ac"

    def _tree(dirs_c: Counter, files_c: Counter, root: str) -> str:
        tree: dict = {}
        for p, c in dirs_c.items():
            node = tree
            for part in p.split("/"):
                node = node.setdefault(part, {})
            node["#"] = c
        for p, c in files_c.items():
            node = tree
            for part in p.split("/"):
                node = node.setdefault(part, {})
            node["$"] = node.get("$", 0) + c

        def kids_of(node):
            # сначала подкаталоги, затем файлы; каждый список — по убыванию частоты
            dirs = sorted(((k, v) for k, v in node.items()
                           if k not in "#$" and "#" in v),
                          key=lambda kv: (-kv[1]["#"], kv[0].lower()))
            files = sorted(((k, v) for k, v in node.items()
                            if k not in "#$" and "$" in v and "#" not in v),
                           key=lambda kv: (-kv[1]["$"], kv[0].lower()))
            return dirs, files

        def line_html(prefix, conn, name, cnt, is_dir, expandable):
            pct = round(100 * cnt / n)
            box = ('<span class=tbox></span>' if expandable
                   else '<span class="tbox none"></span>')
            label = f'<b>{name}/</b>' if is_dir else name
            return (f'<span class=tpfx>{prefix}{conn}</span>{box}'
                    f'<span style="color:{_ink(pct)}">{label} {pct}%</span>')

        def walk(node, prefix) -> str:
            out = []
            dirs, files = kids_of(node)
            items = [(k, v, True) for k, v in dirs] + [(k, v, False) for k, v in files]
            for i, (k, v, is_dir) in enumerate(items):
                last = i == len(items) - 1
                conn = "└── " if last else "├── "
                child_prefix = prefix + ("    " if last else "│   ")
                if is_dir:
                    sub_dirs, sub_files = kids_of(v)
                    has_kids = bool(sub_dirs or sub_files)
                    if has_kids:
                        out.append(f'<details class=tnode><summary class=tline>'
                                   f'{line_html(prefix, conn, k, v["#"], True, True)}</summary>'
                                   + walk(v, child_prefix) + "</details>")
                    else:
                        out.append(f'<div class=tline>'
                                   f'{line_html(prefix, conn, k, v["#"], True, False)}</div>')
                else:
                    out.append(f'<div class=tline>'
                               f'{line_html(prefix, conn, k, v["$"], False, False)}</div>')
            return "".join(out)

        # корень — часть каркаса, процента не даём
        return (f'<div class=tline><span class="tbox none"></span>'
                f'<span class="troot">{root}</span></div>'
                + walk(tree, ""))

    n_tests = sum(1 for r in runs if r["ntf"])
    n_models = len({r["folder"] for r in runs})
    tests_txt = ("tests/ есть во всех прогонах" if n_tests == n
                 else f"tests/ пишут в {n_tests} из {n} прогонов")
    tree_html = (_tree(src_dirs, src_files, "src/")
                 + '<div class=tline style="height:7px"></div>'
                 + (_tree(t_dirs, t_files, "tests/") if t_dirs or t_files else ""))

    # распределение частоты имён: скольким прогонам встречается имя (путь
    # каталога или имя файла-класса). Форма отвечает на вопрос, где консенсус,
    # а где длинный хвост вариативности, и отделяет имя класса от его
    # расположения: путь файла переменчивее самого имени
    def _basename_counter(path_counter: Counter) -> Counter:
        b = Counter()
        for path, cnt in path_counter.items():
            b[path.rsplit("/", 1)[-1]] += cnt
        return b

    def _hist_bins(values):
        edges = (("1", 1, 1), ("2", 2, 2), ("3–4", 3, 4), ("5–9", 5, 9),
                 ("10–14", 10, 14), ("15–24", 15, 24), ("25", 25, 10 ** 9))
        return [(label, sum(1 for v in values if lo <= v <= hi))
                for label, lo, hi in edges]

    dir_freq = list((src_dirs + t_dirs).values())
    cls_base = _basename_counter(src_files + t_files)
    cls_freq = list(cls_base.values())
    fp_freq = list((src_files + t_files).values())

    def _hist_svg(panels: list[tuple[str, list[tuple[str, int]], str]],
                  xlabel: str, ylabel: str) -> str:
        """Панельная гистограмма: (заголовок, [(подпись бина, значение)], цвет)."""
        W, H, MT, MB = 880, 280, 44, 44
        svg = []
        for pi, (title, bins, hue) in enumerate(panels):
            x0 = pi * (W / 2)
            vmax = max(c for _, c in bins)
            step = 20 if vmax > 100 else 10 if vmax > 40 else 5
            top = max(((vmax + step - 1) // step) * step, step)
            plot_h = H - MT - MB
            svg.append(f'<text x="{x0 + 22}" y="20" font-size="13" font-weight="600" '
                       f'fill="#1d2433">{title}</text>')
            cy = MT + plot_h / 2
            svg.append(f'<text x="{x0 + 8}" y="{cy:.0f}" text-anchor="middle" '
                       f'transform="rotate(-90 {x0 + 8} {cy:.0f})" '
                       f'font-size="11.5" fill="#66707f">{ylabel}</text>')
            for t in range(0, top + 1, step):
                y = MT + (top - t) / top * plot_h
                svg.append(f'<line x1="{x0 + 40}" y1="{y:.0f}" x2="{x0 + W / 2 - 16}" y2="{y:.0f}" '
                           f'stroke="#e4e7ec"/>'
                           f'<text x="{x0 + 34}" y="{y + 4:.0f}" text-anchor="end" '
                           f'font-size="11" fill="#66707f">{t}</text>')
            bw = (W / 2 - 66) / len(bins)
            for bi, (label, c) in enumerate(bins):
                bx = x0 + 44 + bi * bw
                bh = plot_h * c / top
                svg.append(f'<rect x="{bx:.0f}" y="{MT + plot_h - bh:.0f}" width="{bw * 0.58:.0f}" '
                           f'height="{max(bh, 1):.0f}" rx="3" fill="{hue}" fill-opacity="0.82"/>'
                           f'<text x="{bx + bw * 0.29:.0f}" y="{MT + plot_h - bh - 6:.0f}" '
                           f'text-anchor="middle" font-size="11.5" font-weight="600" '
                           f'fill="#1d2433">{c}</text>'
                           f'<text x="{bx + bw * 0.29:.0f}" y="{H - 22}" text-anchor="middle" '
                           f'font-size="11.5" fill="#66707f">{label}</text>')
        svg.append(f'<text x="{W / 2}" y="{H - 3}" text-anchor="middle" font-size="12" '
                   f'fill="#66707f">{xlabel}</text>')
        return (f'<svg width="{W}" height="{H}" viewBox="0 0 {W} {H}" '
                f'style="max-width:100%">{"".join(svg)}</svg>')

    def _dist_svg() -> str:
        return _hist_svg(
            [(f"Каталоги — пути · {len(dir_freq)} имён",
              _hist_bins(dir_freq), "#315f9e"),
             (f"Файлы-классы — имена · {len(cls_freq)} имён",
              _hist_bins(cls_freq), "#2f7d54")],
            "прогонов, в которых встречено имя", "число имён")

    # размер решения: файлов и строк в прогоне — одновершинное распределение
    # вокруг типового объёма (в отличие от L-формы частоты имён)
    def _range_bins(values: list[int], width: int) -> list[tuple[str, int]]:
        out, start = [], min(values) // width * width
        while start <= max(values):
            end = start + width - 1
            out.append((f"{start}–{end}", sum(1 for v in values if start <= v <= end)))
            start = end + 1
        return out

    file_sizes = [len(r["files"]) for r in runs]
    loc_sizes = [r["loc"] for r in runs if r["loc"] is not None]
    total_files = sum(file_sizes)

    def _size_svg() -> str:
        return _hist_svg(
            [(f"Файлов (классов) в прогоне · медиана {st.median(file_sizes):.0f}",
              _range_bins(file_sizes, 5), "#315f9e"),
             (f"Строк (LOC src/) в прогоне · медиана {st.median(loc_sizes):.0f}",
              _range_bins(loc_sizes, 300), "#2f7d54")],
            "размер решения (диапазон)", "число прогонов")

    one_dirs = 100 * dir_freq.count(1) / len(dir_freq)
    one_cls = 100 * cls_freq.count(1) / len(cls_freq)
    one_fp = 100 * fp_freq.count(1) / len(fp_freq)
    b1_dirs, b1_cls = dir_freq.count(1), cls_freq.count(1)
    top_txt = ", ".join(
        f"{nm} — {'во всех прогонах' if cnt == n else f'в {cnt}'}"
        for nm, cnt in cls_base.most_common(3))
    rmin = min((r for r in runs if r["loc"]), key=lambda r: r["loc"])
    rmax = max((r for r in runs if r["loc"]), key=lambda r: r["loc"])

    # строки таблицы — в порядке качества, как основная таблица §4:
    # сумма чекера → ревью оси A
    def sort_key(item):
        _, rs = item
        total = st.mean([(r["mand"] or 0) + (r["bonus"] or 0) for r in rs])
        a1 = st.mean([r["a1"] for r in rs if r["a1"] is not None] or [0.0])
        return (-total, -a1)

    trs = []
    for gk, rs in sorted(by_g.items(), key=sort_key):
        rs = sorted(rs, key=lambda r: r["run"])
        n_r = len(rs)
        pj = pair_jac(rs)
        j_top = st.mean(p[0] for p in pj) if pj else None
        j_all = st.mean(p[1] for p in pj) if pj else None
        # каталоги режима: при одном прогоне — простое перечисление; при
        # нескольких — таблица «прогоны × каталоги», причём колонки только для
        # каталогов, встреченных больше одного раза (повторяемость видна
        # по столбцам), остальные — строкой «в одном прогоне»
        gforms: dict[str, Counter] = {}
        for r in rs:
            for t in r["top"]:
                gforms.setdefault(t.lower(), Counter())[t] += 1
        gdirs = sorted(gforms, key=lambda k: (-sum(gforms[k].values()), k))
        gname = {k: gforms[k].most_common(1)[0][0] for k in gdirs}
        if n_r == 1:
            runs_html = ", ".join(gname[k] for k in gdirs)
        else:
            multi = [k for k in gdirs if sum(gforms[k].values()) > 1]
            once = [k for k in gdirs if sum(gforms[k].values()) == 1]
            mini = [f'<tr><th class=rno></th>'
                    + "".join(f'<th>{gname[k]}</th>' for k in multi) + "</tr>"]
            for r in rs:
                has = {t.lower() for t in r["top"]}
                mini.append(f'<tr><th class=rno>{r["run"].removeprefix("run-")}</th>'
                            + "".join(f'<td><input type=checkbox class=ckbox disabled '
                                      f'{"checked" if k in has else ""}></td>' for k in multi)
                            + "</tr>")
            runs_html = f'<table class=ckmini>{"".join(mini)}</table>'
            if once:
                runs_html += (f'<div>в одном прогоне: '
                              f'{", ".join(gname[k] for k in once)}</div>')
        if j_top is None:
            const = "—"   # один прогон: постоянства нет
        else:
            hue = "#2f7d54" if j_top >= 0.7 else "#315f9e" if j_top >= 0.4 else "#b3543f"
            const = (f'<span title="Средний Jaccard наборов каталогов верхнего уровня '
                     f'между парами прогонов режима (0–1). По полным путям: {j_all:.2f}." '
                     f'style="cursor:help"><div class="barwrap s">'
                     f'<div class=bar style="width:{max(3, j_top * 100):.0f}%;background:{hue}"></div>'
                     f'<span>{j_top:.2f}</span></div></span>')
        with_tests = [r for r in rs if r["ntf"]]
        if not with_tests:
            tcell = '<span class=sub>нет</span>'
        else:
            mir = sum(1 for r in with_tests if r["tdirs"] & r["dirs"])
            if mir == len(with_tests):
                detail = "зеркалит src/"
            elif mir:
                detail = f"зеркало src/ в {mir} из {len(with_tests)}"
            else:
                tops = sorted({x.split("/")[0] for r in with_tests for x in r["tdirs"]},
                              key=str.lower)
                detail = " · ".join(t + "/" for t in tops) if tops else "в корне tests/"
            tcell = (f"{len(with_tests)} из {n_r}"
                     f'<div class=sub style="white-space:nowrap">{detail}</div>')
        label = rs[0]["short"] + (" ·tuned" if "-tuned" in rs[0]["tag"] else "")
        trs.append(f"<tr><td><b>{label}</b></td>"
                   f'<td class=narrow>{rs[0]["note"]}</td><td>{runs_html}</td>'
                   f'<td class=narrow>{const}</td><td class=narrow>{tcell}</td></tr>')

    # полюса постоянства — по режимам с 2+ прогонами
    rated = [(rs, st.mean(p[0] for p in pair_jac(rs)))
             for rs in by_g.values() if len(rs) > 1]
    best = max(rated, key=lambda x: x[1])
    worst = min(rated, key=lambda x: x[1])
    glabel = lambda rs: f"{rs[0]['short']} ·{rs[0]['note']}"  # noqa: E731
    n_mir = sum(1 for r in runs if r["ntf"] and r["tdirs"] & r["dirs"])
    excl_txt = " · ".join(f"{a['short']} ·{a['note']}" for a in excluded)
    n_modes = sum(1 for rs in by_g.values() if len(rs) > 1)

    return (f'<h3>Структура кода: src/ и tests/</h3>'
            f'<p class=lead>Задание не подсказывает устройство кода: скелет — это '
            f'src/Kernel.php и пустой tests/. Про архитектуру в TASK.md одна строка: '
            f'«Архитектура, разбиение на слои/модули, именование — на ваше '
            f'усмотрение (это предмет оценки)». Раскладка классов в src/ — датчик '
            f'врождённых предпочтений модели и оценка постоянства этих предпочтений '
            f'между прогонами. '
            f'Смотрим на успешные решения — {n} прогонов {n_models} моделей, чьи '
            f'режимы набрали в среднем не меньше {REVIEW_GOOD:g} баллов ревью по оси A '
            f'(верхние квадранты карт выше).</p>'
            f'<p class=excap>Сводная структура: все каталоги и файлы классов, которые '
            f'модели создали в src/ и tests/, слитые в одно дерево. Типовая раскладка — '
            f'гибрид Symfony-конвенций и DDD: Command (команды), Domain (домен) и '
            f'Entity (сущности) есть у большинства, но у моделей в целом нет общего '
            f'правила раскладки файлов классов (детали ниже).</p>'
            f'<div class=tree>{tree_html}</div>'
            f'<p class=sub>Процент — доля прогонов, где каталог или файл '
            f'существует; яркость имени — ступени по тому же проценту: ≥50% — чернильный, '
            f'25–49% — тёмно-серый, 10–24% — серый, 4% (один прогон) — бледный. '
            f'Все каталоги свёрнуты по умолчанию, клик по [+]/[−] раскрывает и '
            f'сворачивает; внутри каталогов — файлы классов: PSR-4 ставит имя файла '
            f'в соответствие имени класса, так что это срез того, как модели '
            f'называют классы. Каркасный src/Kernel.php не показан; src/ и tests/ — '
            f'серые корни каркаса.</p>'
            f'<p class="excap sb">Распределение имён: общие и уникальные</p>'
            f'{_dist_svg()}'
            f'<p class=sub>По оси X — скольким прогонам встречено имя (путь каталога '
            f'или имя файла-класса), по оси Y — сколько таких имён; панель каталогов '
            f'включает src/ и tests/. Например, левый столбик: {b1_dirs} путь каталога '
            f'и {b1_cls} имя класса существуют ровно в одном прогоне. Медиана у обеих '
            f'панелей — тот же один прогон: {one_dirs:.0f}% путей каталогов и {one_cls:.0f}% имён '
            f'классов — уникальные имена. Но есть и общие: '
            f'{top_txt}. Полные пути '
            f'файлов переменчивее самих имён: {one_fp:.0f}% путей — одиночные против '
            f'{one_cls:.0f}% у имён — класс переиспользуется чаще, чем его расположение. '
            f'Выборка — успешные прогоны, как и во всей подсекции.</p>'
            f'<p class="excap sb">Размер решения: классы и строки</p>'
            f'{_size_svg()}'
            f'<p class=sub>По оси X — размер решения (диапазон файлов или строк), '
            f'по оси Y — число успешных прогонов с размером из диапазона. Это другой '
            f'счёт, чем на гистограмме имён выше: там класс учтён один раз — как имя '
            f'({len(cls_base)} разных имён), здесь — каждый файл каждого прогона '
            f'({total_files} файлов src в сумме); классы переиспользуются между '
            f'прогонами, поэтому экземпляров больше, чем имён. '
            f'Типовой объём успешного решения — {st.median(file_sizes):.0f} файлов и '
            f'{st.median(loc_sizes):.0f} строк (разброс {min(file_sizes)}–{max(file_sizes)} '
            f'файлов, {min(loc_sizes)}–{max(loc_sizes)} строк); в отличие от частоты имён, '
            f'распределение одновершинное. Полюса: {rmin["short"]} ·{rmin["run"].removeprefix("run-")} '
            f'уместил задание в {rmin["loc"]} строк при {len(rmin["files"])} файлах — почти вдвое '
            f'компактнее следующего ({sorted(loc_sizes)[1]}), {rmax["short"]} '
            f'·{rmax["run"].removeprefix("run-")} растянул до {rmax["loc"]} строк при '
            f'{len(rmax["files"])} файлах — при одинаковом полном зачёте обоих.</p>'
            f'<p class="excap sb">Слепки по режимам и постоянство структуры</p>'
            f'<div style="overflow-x:auto"><table><thead>'
            f'<tr><th>Модель</th><th class=narrow>Мышл.</th>'
            f'<th>Каталоги верхнего уровня src/ по прогонам</th>'
            f'<th class=narrow>Постоя&shy;нство</th><th class=narrow>tests/</th>'
            f'</tr></thead><tbody>{"".join(trs)}</tbody></table></div>'
            f'<div class=note><b>Постоянство против типичности.</b> В ячейке слепков — '
            f'таблица «прогоны × каталоги» режима: строка — прогон, столбец — каталог '
            f'верхнего уровня (только встреченные в его прогонах), чекбокс — каталог '
            f'есть в этом прогоне. Полностью отмеченный столбец — каталог каждого '
            f'прогона режима; столбец с пропусками — каталог появился лишь в части '
            f'прогонов. '
            f'Мера — коэффициент Жаккара '
            f'наборов каталогов (0 = не пересекаются, 1 = совпадают), усреднённый по парам '
            f'прогонов. Постоянство считается внутри одного режима (модель + уровень '
            f'размышлений; режимов с 2+ прогонами — {n_modes}): структура повторяется '
            f'в среднем на {w_top:.2f} '
            f'(по полным путям {w_all:.2f}), между разными моделями — на {b_top:.2f} '
            f'({b_all:.2f} по путям): раскладка успешных решений — «почерк» модели. '
            + (f'Тот же расчёт по всем живым прогонам с решением ({len(all_runs)} шт.) '
               f'даёт {wt_a:.2f} против {bt_a:.2f} — зазор шире: качество заставляет '
               f'раскладки сближаться, но «почерк» остаётся. ' if wt_a is not None else "")
            + f'Полюса: {glabel(best[0])} повторяет раскладку точнее всех '
            f'({best[1]:.2f}), {glabel(worst[0])} каждый прогон строит заново ({worst[1]:.2f}). '
            f'Верхний уровень устойчивее глубины: по полным путям средние ниже — модели верны '
            f'слою, но не деталям раскладки. {tests_txt}: {n_mir} зеркалят раскладку src/, '
            f'остальные раскладывают '
            f'по-своему (Unit/ и т.п.) или держат файлы в корне tests/. '
            f'Вне анализа — режимы ниже порога ревью 3 ({excl_txt}) '
            f'и прогоны вне метрик, как во всех метриках разделов 1–6.</div>')


def quality_chart(data: list[dict]):
    """Нарушения phpcs/psalm/phpstan у полных зачётов."""
    src = [r for r in data
           if not r["dead"] and r["kind"] == FULL and r["phpcs"] is not None]
    src.sort(key=lambda r: (-(r["mand"] or 0) - (r["bonus"] or 0), -(r["a1"] or 0)))
    if not src:
        return ""
    rows_q = [(f"{r['short']} · {r['run'].removeprefix('run-')}", r["phpcs"], r["psalm"], r["phpstan"])
              for r in src]
    W = 620
    row_h, ML, MR, MT = 40, 118, 52, 8
    H = len(rows_q) * row_h + 54
    PW = W - ML - MR
    XMAX = max(max(d[1:]) for d in rows_q) * 1.12

    def px(v):
        return ML + v / XMAX * PW

    svg = []
    for t in range(25, int(XMAX) + 1, 25):
        svg.append(f'<line x1={px(t):.0f} y1={MT} x2={px(t):.0f} y2={MT + len(rows_q) * row_h:.0f} stroke="#e4e7ec"/>'
                   f'<text x={px(t):.0f} y={MT + len(rows_q) * row_h + 16:.0f} text-anchor=middle font-size=12 fill=#66707f>{t}</text>')
    hues = ("#315f9e", "#5b9bd5", "#98a4b3")
    names = ("phpcs PSR12", "psalm 6", "phpstan L8")
    for i, (label, *vals) in enumerate(rows_q):
        cy = MT + i * row_h + row_h / 2
        svg.append(f'<text x={ML - 10:.0f} y={cy + 4:.0f} text-anchor=end font-size=12 fill=#1d2433>{label}</text>')
        for j, v in enumerate(vals):
            off = (j - 1) * 8
            svg.append(f'<rect x={ML:.0f} y={cy - 10 + off:.0f} width={max(1, px(v) - ML):.0f} height=6 rx=3 '
                       f'fill={hues[j]} fill-opacity="0.9"/>')
        svg.append(f'<text x={px(vals[0]) + 8:.0f} y={cy + 4:.0f} font-size=12 font-weight=600 fill=#1d2433>{vals[0]}</text>')
    svg.append(f'<text x={ML + PW / 2:.0f} y={H - 8:.0f} text-anchor=middle font-size=12.5 fill=#66707f>'
               "нарушений, меньше — лучше (подпись справа — phpcs)</text>")
    return ('<svg width="%d" height="%d" viewBox="0 0 %d %d" style="max-width:100%%">%s</svg>'
            '<p class=sub>%s — прогоны с полным зачётом, сортировка по чекеру и ревью</p>'
            % (W, H, W, H, "".join(svg),
               " · ".join(f'<span style="color:{h}">■</span> {n}' for h, n in zip(hues, names))))


def cloud_pairs(data: list[dict]):
    """Облачные модели: low → max по чекеру, ревью и цене (div-бары)."""
    import html as _h
    blocks = []
    for folder in ("openai-gpt-6-luna", "openai-gpt-6.1-sol", "deepseek-flash"):
        rs = sorted([r for r in data if r["folder"] == folder and r["cost"] is not None],
                    key=lambda r: 0 if r["note"] == "low" else 1)
        if len(rs) < 2:
            continue
        rows = []
        for r in rs:
            total = (r["mand"] or 0) + (r["bonus"] or 0)
            w = max(3, total / 13 * 100)
            chk = f'{r["mand"]:g}' + (f'<span class=star>★</span>{r["bonus"]:g}' if r["bonus"] else "")
            rows.append(
                f'<div class=pairrow><span class=pairlbl>{_h.escape(r["note"])}</span>'
                f'<div class=barwrap style="flex:0 1 420px"><div class=bar style="width:{w:.0f}%;'
                f'background:{KIND_HUE[r["kind"]]}"></div><span>{chk}</span></div>'
                f'<span class=pairmeta>ревью {r["a1"]:g} · тесты '
                f'{r["tests"] if r["tests"] is not None else "—"} · ${r["cost"]:.3f}</span></div>')
        blocks.append(f'<div class=pairblock><div class=pairtitle>{_h.escape(rs[0]["short"])}</div>'
                      + "".join(rows) + "</div>")
    if not blocks:
        return ""
    return (f'<div class=pairs>{"".join(blocks)}</div>'
            '<p class=sub>Полоса — сумма сценариев чекера из 13 (★ — бонусные); справа — балл ревью (ось A), '
            "тестов написано и стоимость API. low/max — уровень размышлений провайдера.</p>")


PROVIDER = {  # провайдер инференса по папке
    "openai-gpt-6-luna": "OpenRouter",
    "openai-gpt-6.1-sol": "OpenRouter",
    "deepseek-flash": "DeepSeek API",
}


def models_table_rows() -> str:
    rows, seen = [], {}
    for r in DATA_ALL:   # состав окружения — по всем прогонам, включая вне метрик
        m = seen.setdefault(r["folder"], {"tag": r["tag"], "notes": [], "n": 0})
        m["notes"].append(f'{r["run"].removeprefix("run-")}: {r["note"]}')
        m["n"] += 1
    trs = []
    for folder, m in sorted(seen.items()):
        par, quant = PARAMS.get(folder, ("—", "—"))
        prov = PROVIDER.get(folder, "ollama")
        trs.append(f"<tr><td>{folder}</td><td>{m['tag']}</td><td class=narrow>{prov}</td>"
                   f"<td class=narrow>{par}</td><td class=narrow>{quant}</td>"
                   f"<td>{' → '.join(m['notes'])}</td>"
                   f"<td class=narrow>{m['n']}</td></tr>")
    return "".join(trs)


def tier_costs() -> str:
    """Стоимость конвейера по ярусам: медианы из CSV сетки."""
    import csv
    import statistics as st
    from collections import defaultdict

    csv_path = ROOT / "reports" / "test-assignment-tier-timing.csv"
    if not csv_path.is_file():
        return ""
    by = defaultdict(list)
    for r in csv.DictReader(csv_path.open()):
        by[r["tier"]].append(r)
    names = {"agent": "агент (решение)", "check": "чекер (ярус 1)",
             "metrics": "метрики (ярус 2)", "tier3": "ревью (ярус 3)",
             "tier4": "судья (ярус 4)"}
    trs = []
    for tier in ("agent", "check", "metrics", "tier3", "tier4"):
        rs = by.get(tier) or []
        if not rs:
            continue
        durs = [float(r["duration_s"]) for r in rs]
        toks = [int(r["tokens_total"]) for r in rs if r["tokens_total"]]
        cost = [float(r["cost_usd"]) for r in rs if r["cost_usd"]]
        med = lambda v: f"{st.median(v):,.0f}" if v else "—"  # noqa: E731
        trs.append(f"<tr><td>{names[tier]}</td><td class=narrow>{len(rs)}</td>"
                   f'<td class="narrow">{fmt_dur(st.median(durs) / 60)}</td>'
                   f'<td class="narrow">{med(toks)}</td>'
                   f'<td class="narrow">{("$%.4f" % st.median(cost)) if cost else "—"}</td></tr>')
    return ('<h3>Стоимость конвейера по ярусам</h3>'
            '<table style="width:auto;max-width:640px"><thead><tr><th>Ярус</th>'
            '<th class=narrow>запусков</th><th class=narrow>медиана времени</th>'
            '<th class=narrow>медиана токенов</th><th class=narrow>$ / запуск</th></tr></thead>'
            + f"<tbody>{''.join(trs)}</tbody></table>"
            '<p class=sub>Из reports/test-assignment-tier-timing.csv (медианы; время — на прогон, '
            "токены/цена — на запуск судьи). Медианы устойчивы к дублям строк при перегонах.</p>")


def api_spend() -> str:
    """Реальные траты API за облачные прогоны."""
    rows = [r for r in DATA if r["prov"] in ("openrouter", "orcarouter", "deepseek", "gigachat")]
    if not rows:
        return ""
    total = sum(r["cost"] for r in rows)
    src = {k: v for k, v in API_TARIFFS.items()}
    def tariff_note(model, prov):
        if prov == "gigachat":
            return ("freemium физлица (Ultra 50M / Max 25M токенов на 12 мес); "
                    "бизнес-тариф Max — ₽650/1M")
        pin, pout, pc = src[model]
        return f"${pin * 1e6:g}/$1M вход · ${pout * 1e6:g}/$1M выход · кэш ${pc * 1e6:g}/$1M" \
            if model != "deepseek-flash" else \
            f"${pin * 1e6:g}/$1M вход · ${pout * 1e6:g}/$1M выход · кэш-хит ${pc * 1e6:.3g}/$1M (пик)"
    trs = "".join(
        f"<tr><td><b>{r['short']}</b> · {r['run'].removeprefix('run-')} <span class=sub>{r['note']}</span></td>"
        f'<td class=narrow>{fmt_dur(r["dur"])}</td>'
        f'<td class=narrow>{r["tok"]:.2f}M</td>'
        f'<td class=narrow><b>${r["cost"]:.3f}</b></td>'
        f'<td>{tariff_note(r["tag"], r["prov"])}</td></tr>'
        for r in sorted(rows, key=lambda r: r["cost"]))
    return ('<h3>Реальные траты API: облачные прогоны</h3>'
            '<table style="width:auto;max-width:820px"><thead><tr><th>Прогон</th>'
            '<th class=narrow>время</th><th class=narrow>токены</th><th class=narrow>$</th>'
            '<th>тариф</th></tr></thead><tbody>' + trs + "</tbody></table>"
            f'<p class=sub>Вся облачная часть этапа — <b>${total:.2f}</b>. OpenRouter: сверено с журналом '
            "reports/openrouter-spend.csv; DeepSeek: расчёт по usage сессии и пик-тарифу родного API; "
            "GigaChat — freemium для физлиц (квота на 12 мес, списаний нет), через мост "
            "gigachat_proxy. Судейские ярусы (3–4) идут по подписке Z.AI вне этого счётчика; "
            "локальные прогоны — собственный ollama-сервер.</p>")


PROV_NAME = {"openrouter": "OpenRouter", "orcarouter": "OrcaRouter",
             "deepseek": "родной API DeepSeek",
             "gigachat": "родной API Сбер · мост gigachat_proxy"}
PROV_TAG = {"openrouter": "OR", "orcarouter": "orca"}   # суперскрипт у имени прогона


def runs_inventory() -> str:
    """Раздел «Прогоны и данные»: скан models/*/<роль>/test-assignment/run-XX.

    Как §9 отчёта интервью: прогоны сгруппированы по моделям (.mgroup —
    заголовок модели + своя таблица), модели — по группам RUN_GROUPS:
    сначала облако, затем локальные по семействам, с подзаголовками.
    """
    groups, total, fam_i = [], 0, None
    for d in sorted((ROOT / "models").iterdir(),
                    key=lambda p: (*run_group_of(p.name), p.name)):
        base = d / "backend" / "test-assignment"
        if not base.is_dir():
            continue
        # провайдеры группы до цикла строк: в смешанных группах (модель гонялась
        # через разные шлюзы, как opus: OpenRouter + OrcaRouter) помечаем
        # провайдер каждой строки
        gprovs: list[str] = []
        for rr in sorted(base.glob("run-*")):
            rj = rr / "run.json"
            if rj.is_file():
                p = json.loads(rj.read_text(encoding="utf-8")).get("provider", "ollama")
                if p not in gprovs:
                    gprovs.append(p)
        mixed = len(gprovs) > 1
        runs, foot = [], []
        retag_noted = False
        for r in sorted(base.glob("run-*")):
            rj = r / "run.json"
            if not rj.is_file():
                continue
            total += 1
            run = json.loads(rj.read_text(encoding="utf-8"))
            cj = json.loads((r / "check.json").read_text()) if (r / "check.json").is_file() else {}
            mj = json.loads((r / "metrics.json").read_text()) if (r / "metrics.json").is_file() else {}
            t3 = json.loads((r / "tier3.json").read_text()) if (r / "tier3.json").is_file() else {}
            t4 = json.loads((r / "tier4.json").read_text()) if (r / "tier4.json").is_file() else {}
            # токены сессии — сумма totalTokens по assistant-ходам (как в разделе 4)
            tok = u_in = u_cache = u_cw = 0
            nsess = 0
            last_stop = None
            for f in sorted((r / "pi-session").glob("*.jsonl")) if (r / "pi-session").is_dir() else []:
                nsess += 1
                for line in f.read_text(encoding="utf-8").splitlines():
                    try:
                        e = json.loads(line)
                    except json.JSONDecodeError:
                        continue
                    m = e.get("message") or {}
                    if m.get("role") == "assistant" and isinstance(m.get("usage"), dict):
                        u = m["usage"]
                        tok += int(u.get("totalTokens") or 0)
                        u_in += int(u.get("input") or 0)
                        u_cache += int(u.get("cacheRead") or 0)
                        u_cw += int(u.get("cacheWrite") or 0)
                    if m.get("role") == "assistant":
                        last_stop = m.get("stopReason")

            def rel(name, title):
                f = r / name
                if f.exists():
                    return f'<a href="../{f.relative_to(ROOT)}">{title}</a>'
                return f'<span class=sub>{title}</span>'

            foreign = run.get("ollama_foreign_models") or []
            if foreign:   # сноска под таблицей группы вместо плашки в ячейке
                foot.append(f'<sup>*</sup> {r.name} — при параллельной модели в ollama '
                            f'({", ".join(foreign)}): прогон вне метрик разделов 1–6.')
            retag = str(run.get("model", "")).startswith("qwen3.8-unc")
            if retag and not retag_noted:
                foot.append('<sup>†</sup> MTP-серия: те же веса, '
                            'что у orca — прогоны под тегом qwen3.8-unc (сверка блобов 2026-10-21); '
                            'генерация ×1.8 (63 → 115 ток/с), качество в пределах шума).')
                retag_noted = True
            loc27 = (mj.get("scc") or {}).get("loc")
            frozen = (run.get("duration_s") or 0) > 5000 and loc27 == 27
            blank_run = not run.get("diff_lines")
            ic = interrupt_cause(r, run)
            int_kind, int_cause = ic if ic else (None, None)
            dead = frozen or blank_run or bool(ic)
            dwhy = ""
            if int_cause:
                dwhy = int_cause + (
                    "; решение не написано (дифф 0 строк)" if blank_run
                    else f"; решение оборвано (дифф {run.get('diff_lines') or 0} строк), "
                         "оценки обрывка в средних не участвуют")
                if nsess > 1:
                    dwhy = f'попыток в каталоге: {nsess}; ' + dwhy
            elif frozen:   # замерший стрим: скелет нетронут, это не результат модели
                dwhy = ('агент застрял на bash-команде (команда не возвращалась, '
                        'таймаута на инструменты у pi нет): сессия дожила до лимита '
                        '90 минут, скелет нетронут; в средних не учитывается')
            elif blank_run:
                dwhy = ('решение не написано: дифф 0 строк, чекер оценивал нетронутый '
                        'скелет; в средних не учитывается')
                if last_stop == "length":
                    dwhy = ('вывод оборван потолком выходных токенов посреди размышлений '
                            '(финальный вызов модели — stop=length: ни текста, ни действий); '
                            'решение не написано, дифф 0 строк — чекер оценивал нетронутый '
                            'скелет; в средних не учитывается')
                if nsess > 1:
                    dwhy = f'попыток в каталоге: {nsess}; ' + dwhy
            dkind = dglyph = ""
            if dead:
                dkind = int_kind or ("hang" if frozen else "blank")
                dglyph = {"cut": "✂", "time": "⏱", "hang": "💤", "blank": "∅"}[dkind]
                foot.append(f'<sup>{dglyph}</sup> {r.name} — {dwhy}.')
            # контекст у всех прогонов одинаковый (102K) — вынесен в префикс раздела
            cond = run.get("thinking_effective") or run.get("thinking") or "?"
            if run.get("provider") == "gigachat":
                cond = "нет"  # управления размышлениями у GigaChat нет
            if run.get("model", "").endswith("-tuned"):
                cond += " · tuned"
            sc = lambda v: "—" if v is None else f"{v:g}"  # noqa: E731
            pu = mj.get("phpunit") or {}
            cov = pu.get("coverage_lines_pct")
            tests = f"{cov:.0f}%" if cov is not None else "—"
            cp, ct, bp = (cj.get("mandatory_passed"), cj.get("mandatory_total"),
                          cj.get("bonus_passed"))
            bt = cj.get("bonus_total")
            chk = ("—" if cp is None else
                   f"{cp} из {ct if ct is not None else '—'}"
                   # бонусные — всегда второй строкой, включая ★0 из 2
                   + (f'<br><span class=star>★</span>{bp} из {bt}' if bt is not None else ""))
            dur_s = run.get("duration_s")
            dur = "—" if not dur_s else (f"{dur_s / 60:.1f}" if dur_s < 600 else f"{dur_s / 60:.0f}")
            toks = f"{tok / 1e6:.2f}M" if tok else "—"
            prompt = u_in + u_cache + u_cw
            if toks != "—" and prompt:
                # int() вместо округления: 99.9% → «99%», честные 100% не бывают
                toks += (f'<div class=sub style="line-height:1.3;white-space:nowrap">'
                         f'кэш {int(100 * u_cache / prompt)}%</div>')
            # в строке — цветной значок у имени (детали — сноской под таблицей
            # группы); в смешанных группах провайдер — чипом под именем прогона
            provmark = (f'<br><span class="provchip" title="провайдер вызова: '
                        f'{PROV_NAME.get(run.get("provider"), "ollama")}">'
                        f'{PROV_TAG.get(run.get("provider"), "")}</span>' if mixed else "")
            rowmark = (f'<sup class="dsym {dkind}" title="{dwhy}">{dglyph}</sup>'
                       if dead else "")
            runs.append(
                f"<tr{' class=neg' if dead else ''}><td class=narrow>{r.name}"
                f"{'<sup>*</sup>' if foreign else ''}{'<sup>†</sup>' if retag else ''}"
                f"{rowmark}{provmark}</td>"
                f'<td class="narrow">{cond}</td>'
                f'<td class="narrow">{chk}</td>'
                f'<td class="narrow">{sc(t3.get("axis_a"))} / {sc(t3.get("axis_b"))}</td>'
                f'<td class="narrow">{sc(t4.get("axis_a"))}</td>'
                f'<td class="narrow">{sc((mj.get("phpcs") or {}).get("violations"))}</td>'
                f'<td class="narrow">{sc((mj.get("psalm") or {}).get("issues"))}</td>'
                f'<td class="narrow">{sc((mj.get("phpstan") or {}).get("errors"))}</td>'
                f'<td class="narrow">{sc((mj.get("phpmd") or {}).get("violations"))}</td>'
                f'<td class="narrow">{tests}</td>'
                f'<td class="narrow">{sc((mj.get("scc") or {}).get("loc"))}</td>'
                f'<td class="narrow">{toks}</td>'
                f'<td class="narrow">{dur}</td>'
                f'<td>{rel("report.html", "отчёт")} · {rel("solution", "код")} · '
                f'{rel("scorecard.md", "scorecard")} · {rel("review-zcode.md", "ревью яруса 3")} · '
                f'{rel("judge-glm.md", "судья яруса 4")} · {rel("audit-verdict.md", "аудит")}</td></tr>')
        if not runs:
            continue
        mt = sorted({json.loads(pp.read_text(encoding="utf-8")).get("max_tokens")
                     for pp in base.glob("run-*/run.json")} - {None})
        first_run = json.loads((sorted(base.glob("run-*"))[0] / "run.json").read_text(encoding="utf-8"))
        tag = first_run.get("model", "")
        n = len(runs)
        if len(gprovs) > 1:   # шлюзы группы — теми же чипами, что у строк
            prov = " + ".join(
                f'<span class="provchip" title="провайдер вызова: '
                f'{PROV_NAME.get(p, "ollama")}">{PROV_TAG.get(p, p)}</span>'
                for p in gprovs)
        else:
            prov = PROV_NAME.get(gprovs[0], "ollama") if gprovs else "ollama"
        gi = run_group_of(d.name)[0]
        head = ""
        if gi != fam_i:
            fam_i, head = gi, f'<div class=famhead>{RUN_GROUPS[gi][0]}</div>'
        sub = (f'<p class=sub>{tag} · {prov} · прогонов: {n}'
               + (f' · бюджет выхода: {" / ".join(str(v) for v in mt)} ток.</p>' if mt
                  else '</p>'))
        groups.append(
            head
            + f'<div class=mgroup><h3>{SHORT.get(d.name, d.name)}</h3>'
            + sub
            + '<table><thead><tr><th class=narrow rowspan=2>Прогон</th><th class=narrow rowspan=2>Мышл.</th>'
            '<th class=narrow rowspan=2>Чекер</th><th class=narrow rowspan=2>Ревью<br>A/B</th>'
            '<th class=narrow rowspan=2>Слепое<br>ревью</th>'
            '<th colspan=4>Качество кода</th>'
            '<th class=narrow rowspan=2>Покр.<br>тест. (%)</th>'
            '<th class=narrow rowspan=2>LOC</th>'
            '<th class=narrow rowspan=2>Токены</th>'
            '<th class=narrow rowspan=2>Время<br>(мин)</th>'
            '<th rowspan=2>Артефакты</th></tr>'
            "<tr><th class=\"narrow sub\">phpcs</th><th class=\"narrow sub\">psalm</th>"
            '<th class="narrow sub">phpstan</th><th class="narrow sub">phpmd</th></tr></thead><tbody>'
            + "".join(runs) + "</tbody></table>"
            + (f'<p class=sub>{"<br>".join(foot)}</p>' if foot else "")
            + "</div>")
    if not groups:
        return ""
    return ('<h2 id=runs><span class=no>7.</span>Прогоны и данные</h2>'
            f'<p class=lead>Все прогоны этапа — {pl_runs(total)} на момент сборки отчёта: '
            "сначала облачные модели (SOTA, DeepSeek, российские облака), "
            "затем локальные по семействам. Контекст у всех прогонов сетки — 102K; "
            "перезапуски облачных моделей после 2026-10-22 пойдут с нативными "
            "параметрами каталога pi (см. раздел 2). "
            "Здесь — детальные показатели каждого прогона; "
            "свёрнутые средние по режимам работы — в разделе 4.</p>"
            '<p class=sub>Пометки у имени прогона: '
            '<span class="dsym" style="color:#2f7d54">✓</span> — в метриках · '
            '<span class="dsym cut">✂</span> — обрыв сбоем (баланс/ключ, падение pi) · '
            '<span class="dsym time">⏱</span> — остановлен по лимиту 90 мин · '
            '<span class="dsym hang">💤</span> — повис в ожидании инструмента · '
            '<span class="dsym blank">∅</span> — решение не написано · '
            '<sup>*</sup> — чужая модель в ollama во время прогона · '
            '<sup>†</sup> — MTP-ретаг тех же весов · '
            '<span class=provchip>OR</span> — OpenRouter, '
            '<span class=provchip>orca</span> — OrcaRouter '
            "(чип провайдера — в группах с несколькими провайдерами). "
            "Наведение на значок — короткая причина, детали исключений — в сносках "
            "под таблицей группы.</p>"
            + "".join(groups)
            + '<p class=sub>Артефакты: визуальный отчёт (дашборд показателей — сценарии чекера, метрики, '
            "сессия, вердикты), код решения целиком, scorecard, ревью яруса 3, вердикт судьи "
            "яруса 4, аудит безопасности. Всё закоммичено в ветке "
            "<a href=\"https://github.com/prikotov/task-llm-hiring/tree/feat/billing-test-assignment\">feat/billing-test-assignment</a> — "
            "ссылки работают локально и на GitHub. Сводка судей текстом: "
            "<code>reports/TEST-ASSIGNMENT-judges.md</code>.</p>")


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

DATA = scan_runs()
# прогоны с параллельной моделью в ollama — вне метрик (разделы 1–6);
# факт их существования показывается в разделе 7
DATA_ALL = DATA
DATA = [r for r in DATA if not r["contention"]]
_dates = sorted(r.get("started_at", "")[:10] for r in
                (json.loads((ROOT / "models" / r["folder"] / "backend" / "test-assignment"
                             / r["run"] / "run.json").read_text()) for r in DATA)) if DATA else []
RANGE_DATES = (_dates[0] + " — " + _dates[-1]) if _dates else "—"

# выжимки для нарратива
# согласие судей — только по живым прогонам: вердикты обрывка решения
# (прерванный прогон) согласием ярусов не являются
_pairs = [r for r in DATA if not r["dead"] and r["a1"] is not None and r["a2"] is not None]
N_AGREE = sum(1 for r in _pairs if abs(r["a1"] - r["a2"]) <= 1)
N_PAIRS = len(_pairs)
AUDIT_OK = sum(1 for r in DATA if r["audit"] == "чисто")
AUDIT_WARN = sum(1 for r in DATA if r["audit"] == "пограничное")
_folders = {r["folder"]: r["prov"] for r in DATA}
N_LOCAL = sum(1 for p in _folders.values() if p == "ollama")
N_CLOUD = len(_folders) - N_LOCAL
_contam = [r for r in DATA_ALL if r["contention"]]


def _contam_note_global() -> str:
    """Предупреждение под §1, если есть прогоны вне метрик."""
    if not _contam:
        return ""
    lst = ", ".join(f"{r['short']} · {r['run'].removeprefix('run-')}" for r in _contam)
    return (f'<div class=note><b>{pl_runs(len(_contam))} — вне метрик:</b> во время '
            f'{"них" if len(_contam) > 1 else "него"} на сервере '
            f'ollama была загружена чужая модель ({lst}). Факт зафиксирован наблюдателем раннера; '
            f'в таблицы и графики разделов 1–6 такие прогоны не входят, детали — в разделе 7.</div>')


def _contam_note_results() -> str:
    """Сноска под таблицей §4 при наличии прогонов вне метрик."""
    if not _contam:
        return ""
    return (f'<p class=sub>{pl_runs(len(_contam)).capitalize()} вне метрик (параллельная модель в '
            f'ollama) — {"они не входят" if len(_contam) > 1 else "не входит"} в эту таблицу и графики; '
            f'факт и детали — в разделе 7.</p>')

HTML = f"""<!DOCTYPE html>
<html lang="ru"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Этап 2: тестовое задание «биллинг LLM-токенов»</title>
<style>{CSS}</style></head><body><div class=wrap>
<h1 class=pagetitle>Этап 2: тестовое задание «биллинг LLM-токенов»</h1>
<p class=sub style="margin:-6px 4px 14px">{pl_runs(len(DATA_ALL))} · {len({r["folder"] for r in DATA_ALL})} моделей ({N_LOCAL} локальных + {N_CLOUD} облачных) · {RANGE_DATES} · отчёт собран в ZCode при содействии GLM-5.3. Ветка feat/billing-test-assignment (PR #14).</p>

<nav class=toc>
<div class=toc-title>Содержание</div>
<ul>
<li><a href="#tldr">1. Главные выводы</a></li>
<li><a href="#method">2. Методика этапа</a></li>
<li><a href="#models">3. Модели</a></li>
<li><a href="#results">4. Результаты моделей</a></li>
<li><a href="#charts">5. Что показывают метрики</a></li>
<li><a href="#judges">6. Судьи: согласие ярусов</a></li>
<li><a href="#runs">7. Прогоны и данные</a></li>
<li><a href="#next">8. Стоимость и дальше</a></li>
</ul>
</nav>

<h2 id=tldr><span class=no>1.</span>Главные выводы</h2>
<p class=lead>Этап 2 разделил кандидатов очень контрастно. Лучший результат у облачного
DeepSeek V4.1 Flash: полный зачёт в обоих режимах размышлений и самый чистый код среди всех,
кто выполнил задание; следом Sol 6.1. Из девяти локальных моделей лучшей стала orca:
три полного зачёта, в общей таблице она обошла Luna 6 и вплотную подобралась к Sol, а стоит
втрое дешевле. huihui тоже выполнила
задание во всех прогонах, но наращивала объём кода, а базовый qwen3.8 нестабилен: от полного
зачёта до результата «почти». Худшие результаты выразительны: gemma4:31b не получила ни
одного зачёта; muse прошла быстро и дёшево, но не написала ни одного теста; ornith не
выполнила задание и потратила до 39 миллионов токенов за прогон; bonsai-2 не написала ни
одной строки кода, хотя её анонс обещал 98.2% бенчмарков базовой модели; GigaChat застрял
в цикле из одинаковых запросов. Судьи оценивали прогоны согласованно, аудит безопасности
нарушений не нашёл.</p>
<div class=cards>
<div class=card><b>DeepSeek V4.1 Flash — лучший результат этапа</b>Полный зачёт в обоих режимах размышлений:
low (3.6 мин, $0.06) и max (10.3 мин, $0.20). На max — самый чистый код среди выполнивших задание:
phpcs 0, psalm 24, phpstan 18. Такой же полный зачёт Sol 6.1 стоит $0.63.</div>
<div class=card><b>orca — лучший локальный по стабильности</b>Полное имя модели —
orcarouter/Qwen3.8-27B-Uncensored (orca — короткое имя для таблиц): uncensored-файнтюн
qwen3.8 27B, та же база со снятыми фильтрами безопасности. Родная серия: 3/3 полного
зачёта, среднее 13 из 13 — единственная локальная без потерь в среднем, стабильнее
базового qwen3.8 (13 → 11 → 9); в run-03 — phpcs 0 и покрытие 85%; $0.22 за прогон —
втрое дешевле Sol. MTP-серия (те же веса, включён MTP draft): качество
в пределах шума, генерация ×1.8 быстрее — 63 → 115 ток/с.</div>
<div class=card><b>Thinking max поднимает до полного зачёта</b>Luna 6: low 10 → max 13/13;
Sol 6.1: low 11 → max 13/13 — и это единственный прогон, где оба судьи дали ось A 4.0.
DeepSeek V4.1 Flash-1017 полон даже на low (3.6 мин, $0.06),
но max пишет код чище: phpcs 0 против 60, psalm 24 против 36.</div>
<div class=card><b>Локальный разброс: от стабильности до развала</b>huihui — 3/3 полного зачёта ценой
объёма (1.7–1.9K LOC, до 96 phpcs); muse — быстрая и дешёвая (5–6 мин, $0.04/прогон), но без единого теста и бонуса; gemma4:31b — 0/3 зачётов; ornith-1.5 — провал во всех
трёх (1–3 из 13) с бешеным разбросом темпа: 0.06M / 10M / 39M токенов за прогон.</div>
<div class=card><b>bonsai-2: «98.2% бенчмарков» ≠ агентная работа</b>Тернарный дистиллят Prism на базе
Qwen3.8 (в анонсе — 98.2% бенчмарков оригинала, включая агентность) за два прогона не написал ни
одной строки, оба — 2/11. run-01 (xhigh): 13 ходов только на чтение — модель ушла в исследование
маппинга jsonb в Doctrine и завершилась с нетронутым скелетом. run-02 (medium): мышление съело лимит
вывода 32K, сессия оборвана. Сходится с отзывами в сети о развале на длинных многошаговых задачах —
при том что база qwen3.8 в той же сетке берёт 9–13. Разбор — в model.md.</div>
<div class=card><b>Российские облака: агентная работа не даётся</b>GigaChat 3 Ultra (702B MoE на
архитектуре DeepSeek V3) начала строить решение, но застряла в вырожденном цикле — один и тот же
DQL-запрос повторён 448 раз (длиннейшая серия — 370 подряд), 44 минуты и 7.75M токенов ради 2/11; первая попытка кончилась планом текстом
за 5 секунд. GigaChat 2 Max — тот же самовыход за 7 секунд. Это известный дефект семейства: в
официальном <a href="https://github.com/ai-forever/langchain-gigachat/pull/69">langchain-gigachat открыт
PR #69</a> с тем же паттерном («infinite loop calling the same tool on every turn») — сообщество гасит
цикл guard'ом в харнесе, модель не менялась. Подробный вывод и источники — в model.md Ultra.</div>
<div class=card><b>Аудит безопасности: нарушений нет</b>Сеть не использовал ни один агент.
{AUDIT_OK} прогонов чисто, {AUDIT_WARN} — пограничные: только временные файлы в /tmp (фикстуры,
логи), каждый сигнал классифицирован судьёй-аудитором. Детектор ловит и ложные срабатывания —
(cd /tmp/billing-pi-*/repo — это сам cwd).</div>
</div>

<h2 id=method><span class=no>2.</span>Методика этапа</h2>
<p class=lead>Что получает кандидат, в каком окружении работает модель и как устроены пять контуров оценки: два детерминированных, два модельных судьи и аудит безопасности.</p>

<h3>Скелет задания</h3>
<p>Модель получает <a href="https://github.com/prikotov/task-llm-hiring/blob/main/test-assignment/backend/billing/skeleton/">каркас Symfony-проекта</a>
(composer.json: symfony/framework-bundle 8.0, console/yaml, doctrine-bundle 3.2,
doctrine/migrations 3.3, PHP ≥ 8.4; docker-compose с PostgreSQL 17; заготовка src/ и
мigrations/; в require-dev — phpunit, psalm 6 и phpcs PSR12 — инструменты
самопроверки кандидата) и два документа:</p>
<ul>
<li><a href="https://github.com/prikotov/task-llm-hiring/blob/main/test-assignment/backend/billing/skeleton/TASK.md"><b>TASK.md</b></a> — само тестовое
задание: требования к «биллингу LLM-токенов», по которым работает кандидат —
импорт тарифов и событий, тарификация с effective dating, оф-пик-окно
16:30–00:30 UTC через полночь ×0.5, идемпотентность по
(provider, provider_request_id), unrated-события, деньги на bcmath без float
с одним округлением HALF_UP 6 знаков на событие, эксклюзивный --to,
бонус — ретро-пересчёт после коррекции цен;</li>
<li><a href="https://github.com/prikotov/task-llm-hiring/blob/main/test-assignment/backend/billing/skeleton/AGENTS.md"><b>AGENTS.md</b></a> — базовые агентские
инструкции проекта тестового задания: код в src/, самопроверка приёмки,
артефакты COMMIT_MESSAGE.md/PR_DESCRIPTION.md с обоснованием отступлений,
русский язык. Харнес pi подхватывает его сам — как любому агенту в этом
репозитории. Практики (миграции или schema:update, офлайн или онлайн,
менять ли файлы каркаса) модель выбирает сама — чекер наблюдает выбор.</li>
</ul>

<h3>Как запускается</h3>
<p>Задание передаётся одним сырым сообщением без system prompt и подсказок.
Единственный промпт прогона — дословно, константа <code>TASK_PROMPT</code>
в <code>runner_pi.py</code>:</p>
<pre class=req># runner_pi.py, константа TASK_PROMPT — единственный промпт прогона
Прочитай TASK.md в корне репозитория и выполни задание.</pre>
<p>Раннер разворачивает свежую копию скелета (без var/, <code>.env</code> — из
<code>.env.example</code>) во временный каталог и запускает pi одной командой:</p>
<pre class=req>pi -p --session-dir &lt;run&gt;/pi-session --model &lt;тег&gt; --thinking &lt;уровень&gt; &quot;&lt;промпт&gt;&quot;</pre>
<p>Реальный вызов из сетки (run-02 qwen3.8, после перевода thinking на честный
xhigh):</p>
<pre class=req>python3 test-assignment/backend/billing/runner_pi.py \\
  --model qwen3.8:27b-pi-100k \\
  --results models/qwen3.8-27b-pi-100k/backend/test-assignment/run-02 \\
  --thinking max</pre>
<p>Облачная модель — тот же раннер с провайдером (ключи и прокси — из env, в git не
хранятся; thinking — уровень шкалы провайдера):</p>
<pre class=req>python3 test-assignment/backend/billing/runner_pi.py \\
  --model openai/gpt-6.1-sol --provider openrouter \\
  --results models/openai-gpt-6.1-sol/backend/test-assignment/run-02 \\
  --thinking max</pre>
<p class=sub>cwd — копия скелета; окружение: PI_CODING_AGENT_DIR — одноразовый
каталог (никаких глобальных конфигов и сессий pi), PI_OFFLINE=1; провайдер —
ollama /v1 (openai-completions) внутреннего сервера, models.json раннера
задаёт contextWindow=102400, maxTokens (8192; для xhigh/max — 32768, иначе
thinking выжигает лимит вывода в пустой ответ) и thinkingLevelMap из фактической
шкалы модели (/api/show). Бюджет — 90 минут на прогон, каждый с нуля.
Фактический бюджет выхода каждой группы прогонов — в шапке её таблицы в
разделе 7: 8192 у большинства облачных, 16000 у DeepSeek (родной API режет
короче), 32768 у локальных на xhigh/max и Anthropic-моделей (высокие уровни
размышлений съедают бюджет выхода; первый запуск Haiku с 8192 обрывался
по лимиту — см. журнал прогонов). Облачные модели OpenRouter с 2026-10-22
идут нативным провайдером pi (anthropic-messages к openrouter.ai/api,
нативные окно и бюджет выхода из каталога, шкала мышления с xhigh/max,
ключ через OPENROUTER_API_KEY): кастомная запись openai-completions с
переопределениями, которой они шли раньше, на Haiku 5.5 max дала два
прогона подряд stop=length посреди размышлений — в этом протоколе reasoning
и ответ делят один бюджет выхода (оба прогона вне метрик). Opus 5.5
дополнительно заведён через OrcaRouter (orcarouter.ai): тот же нативный
anthropic-messages (/v1/messages, Bearer-ключ), прайс Anthropic без наценки,
из домашней сети ходит без прокси; Haiku 5.5 в каталоге OrcaRouter нет
(2026-10-22). Мышление через OrcaRouter — классическими бюджетами pi
(low 2048 / medium 8192 / high и max 16384 reasoning-токенов на вызов):
adaptive-режим, включавшийся compat-флагами из каталога pi, не имеет потолка —
первый «low»-прогон в нём потратил 70.8K reasoning и $4.38 (прерван лимитом
ключа, вне метрик; разбор в model.md).
Полный след сессии (сообщения, tool-вызовы, размышления, токены) остаётся
в &lt;run&gt;/pi-session/*.jsonl.</p>

<h3>Конвейер оценки: пять контуров</h3>
<div class=flow>
<div class=step><b>1 · Чекер</b>детерминированный: golden-эталоны, чистая БД в docker</div>
<div class=arrow>→</div>
<div class=step><b>2 · Метрики</b>phpcs / psalm / phpstan + phpunit + AST; phpmd — наблюдение</div>
<div class=arrow>→</div>
<div class=step><b>3 · Ревью</b>GLM-5.3-Flash, pi-сессия: оси A/B, видит чекер и метрики</div>
<div class=arrow>→</div>
<div class=step><b>4 · Судья</b>GLM-5.3-Flash, слепое ревью диффа, ось A</div>
<div class=arrow>→</div>
<div class="step hot"><b>5 · Аудит</b>сеть и выходы за cwd; сигналы классифицирует судья-аудитор</div>
</div>
<p class=sub>Контуры 1–2 — детерминированные, без LLM. 3–4 — два независимых модельных судьи
(изолированные pi-сессии, GLM-5.3-Flash): ревью знает факты ярусов 1–2, судья — нет. Пятый — гибрид:
сигналы собирает детектор кодом, классифицирует их судья-аудитор.</p>

<div class=scalebox id=checker><b>Контур 1 — чекер: детерминированный, без LLM.</b>
<div><pre class=req>python3 test-assignment/backend/billing/checker/check.py --repo &lt;репозиторий кандидата&gt;</pre></div>
<div>Чекер поднимает чистую базу PostgreSQL в docker
на свободном порту, фиксирует способ разворота схемы (миграции или
schema:update), гоняет команды кандидата и сравнивает вывод с golden-эталонами.
Эталоны посчитаны дважды независимо — Python Decimal и PHP-референс судьи,
совпадение байт-в-байт.</div>
<div>11 обязательных сценариев: создание БД, разворот схемы, импорт цен,
импорт событий, тарификация (RATED/UNRATED), unrated в stderr, базовый отчёт,
повторный импорт дублей (идемпотентность), тарификация после дублей, отчёт
после дублей, вывод ретро-пересчёта, отчёт после ретро-коррекции; плюс
информационный skeleton_changes (что изменено в каркасе). Сценарии построены
на граничных нюансах: valid_from включительно, границы оф-пик-окна
16:29:59/16:30:00 и 00:29:59/00:30:00, unified-тариф, cache-цены,
коллизия id между провайдерами, округление HALF_UP на уровне события.</div>
<div>2 бонусных (retro-rebill): ретро-пересчёт после коррекции цены задним
числом — REPRICED_EVENTS и REPRICE_DELTA_USD против эталона.</div></div>

<div class=scalebox><b>Контур 2 — метрики: детерминированные, сравнительные.</b>
<div><pre class=req>python3 test-assignment/backend/billing/checker/metrics.py --repo &lt;репозиторий кандидата&gt;</pre></div>
<div>Инструменты — phar-кэш, без сети в окружении судьи, в порядке приоритета:
phpcs PSR12 — стиль; psalm 6 (errorLevel 1, src/) — типы и логика; phpstan level 8 —
строгий статанализ; phpunit 11 — тесты кандидата: количество, ассерты, LOC тестов и
покрытие строк (pcov); phpmd — нарушения порогов сложности/чистоты — <b>наблюдение</b>,
в сравнение моделей не входит; scc — размер (files, LOC); AST-сборщик coding-standard —
WMC, cyclomatic complexity, LCOM4, afferent/efferent coupling и превышения порогов
(method cc &gt; 10, method loc &gt; 50, class loc &gt; 300, wmc &gt; 50). Сырые выводы инструментов —
полные отчёты phpcs/psalm/phpstan/phpunit, scc.json и collector.json — сохраняются рядом,
в <code>&lt;run&gt;/metrics-raw/</code>, имена файлов продублированы в metrics.json в полях raw:
агрегаты проверяемы по сырью, за деталями есть куда сходить.</div>
<div>Метрики сравнивают модели между собой, проходного порога нет: у
эталонного решения судьи phpstan ≠ 0.</div></div>

<div class=scalebox><b>Контур 3 — ревью: изолированная pi-сессия.</b>
<div><pre class=req>python3 test-assignment/backend/billing/checker/judge_tier3.py \\
  --run models/&lt;модель&gt;/backend/test-assignment/run-XX --thinking low</pre></div>
<div>Отдельный запуск <code>pi -p --no-session --model zai/glm-5.3-flash</code>, thinking low (паттерн judge_glm.py,
подписка Z.AI; сабагенты Agent tool и OpenRouter запрещены правилами
репозитория). Ревьюер знает результат чекера и метрик, оценивает две оси
рубрики <a href="https://github.com/prikotov/task-llm-hiring/blob/main/test-assignment/backend/billing/RUBRIC.md">RUBRIC.md</a>:
A «код и архитектура» и B «процесс и коммуникация», 0–4.</div>
<div>Системный промпт ревьюера — дословно (SYSTEM в
<code>checker/judge_tier3.py</code>; на вход подаются TASK.md, дифф
решения и факты ярусов 1–2):</div>
<pre class=req>TIER3_SYSTEM_PLACEHOLDER</pre>
<div>Что подаётся ревьюеру, кроме TASK.md и диффа (обрезается до 2600 строк —
первые строки несут сущности и домен): блок <b>ФАКТЫ</b> — выжимка ярусов 1–2.
Метрики идут агрегатами (phpstan/phpmd/phppcs/LOC + AST-сводка и превышения
порогов), сырые выводы инструментов — нет; упавшие сценарии чекера —
с деталями. Вот ФАКТЫ дословно для первого прогона сетки
(строит build_facts() из judge_tier3.py — тот же код, что в бою):</div>
<pre class=req>TIER3_FACTS_PLACEHOLDER</pre>
<div>Время, токены и стоимость каждого вердикта пишутся рядом с прогоном
(<code>tier3.json</code>), свод по сетке — в разделе 8.</div></div>

<div class=scalebox><b>Контур 4 — судья: слепое второе мнение.</b>
<div><b>Зачем.</b> Ревьюер яруса 3 знает результат чекера и волей-неволей
опирается на него — может пропустить дефект, который не виден в
функциональных проверках, но виден в коде. Судья яруса 4 смотрит на
решение «с улицы»: только TASK.md и дифф, без чекера и метрик. Это решает
две задачи: (а) независимый контроль ревью — согласие ярусов 3/4 в сетке
{N_AGREE} из {N_PAIRS} прогонов в пределах ±1 балла, систематического сдвига нет,
значит ревью не подгонялось под чекер; (б) второй проход по коду. Ограничение:
на аккуратном, но частично нерабочем коде судья завышает (qwen run-03:
4 против 2.5 у ревью, зная чекер) — поэтому итоговый вердикт о работе
решения выносит только чекер, а судья — источник дефектов и второе
мнение, не истина.</div>
<div><pre class=req>python3 test-assignment/backend/billing/checker/judge_tier2.py \\
  --run models/&lt;модель&gt;/backend/test-assignment/run-XX --thinking low</pre></div>
<div>Pi-сессия с GLM-5.3-Flash (thinking low, изолированный запуск).
Системный промпт судьи — дословно (SYSTEM в
<code>checker/judge_tier2.py</code>):</div>
<pre class=req>TIER4_SYSTEM_PLACEHOLDER</pre></div>

<div class=scalebox><b>Контур 5 — аудит безопасности: детектор кодом, классификация судьёй.</b>
<div><b>Политика этапа — наблюдать, не блокировать</b>: жёстких запретов нет, каждое действие
агента за рамками — сигнал, а не нарушение. Детектор в <code>analyze_session.py</code> проходит
след сессии и помечает два рода сигналов: обращение к сети из любого инструмента и
чтение/запись за пределами cwd (до 60 сигналов на прогон) — итог пишется в
<code>&lt;run&gt;/security.json</code> рядом с прогоном.</div>
<div><pre class=req>python3 test-assignment/backend/billing/checker/judge_audit.py \\
  --run models/&lt;модель&gt;/backend/test-assignment/run-XX</pre></div>
<div>Сигналов нет — вердикт «чисто» без вызова LLM (0 токенов). Сигналы есть — судья-аудитор
(GLM-5.3-Flash, thinking low, изолированная сессия) классифицирует каждый — нарушение /
пограничное / безобидное — и выносит вердикт прогона: <code>audit-verdict.md</code> + <code>audit.json</code>.
По этапу: сеть не использовал ни один агент; {AUDIT_OK} прогонов чисто, {AUDIT_WARN} — пограничных
(временные файлы в /tmp: тестовые фикстуры и логи), нарушений нет. Детектор небезупречен
(catch: cd /tmp/billing-pi-*/repo — это сам cwd) — классификация судьёй как раз и отделяет
ложные срабатывания от реальных выходов за рамки.</div></div>

<h2 id=models><span class=no>3.</span>Модели</h2>
<p class=lead>Кто участвовал и почему: семь облачных моделей — потолок качества, вилка
цен и российские облака; девять локальных — кандидаты основного набора, его вариации и квант-эксперимент
(у двух облачных — по одному прогону). Состав отвечает на вопросы этапа:
дотягиваются ли локальные модели до полного зачёта и что даёт платный уровень
размышлений.</p>
<table><thead><tr><th>Модель</th><th>Провайдер вызова</th><th>Почему в тесте</th>
<th>Детали</th></tr></thead><tbody>
<tr><td><b><a href="https://openrouter.ai/openai/gpt-6.1-sol">Sol 6.1</a></b> <span class="badge g">облако</span></td><td>OpenAI · OpenRouter</td>
<td>главный SOTA-бенчмарк: как выглядит потолок чистых способностей в агентной работе</td>
<td>effort low и max; лучший прогон этапа — 13/13, обе судейские оси A 4.0, 54 теста, чистый аудит</td></tr>
<tr><td><b><a href="https://openrouter.ai/openai/gpt-6-luna">Luna 6</a></b> <span class="badge g">облако</span></td><td>OpenAI · OpenRouter</td>
<td>дешёвая модель топ-вендора: ценовой пол «умных»</td>
<td>effort low и max; полный зачёт на max за $0.06 — самый дешёвый полный зачёт сетки</td></tr>
<tr><td><b><a href="https://openrouter.ai/anthropic/claude-haiku-5.5">Haiku 5.5</a></b> <span class="badge g">облако</span></td>
<td>Anthropic · OpenRouter</td>
<td>дешёвая быстрая модель Anthropic — пара к Luna от другого вендора; нативно: контекст 1M,
бюджет выхода 128K, шкала thinking off…max, $0.10/$0.50 за 1M</td>
<td>effort low и max (прогоны 2026-10-22 — кастомная запись с прижатием max→high;
после их разбора раннер переведён на нативный каталог pi, level max нативный) ·
прогон: ctx 102400 вместо нативного 1M, бюджет выхода 32768 · 2 прогона</td></tr>
<tr><td><b><a href="https://openrouter.ai/anthropic/claude-opus-5.5">Opus 5.5</a></b> <span class="badge g">облако</span></td>
<td>Anthropic · OpenRouter + OrcaRouter</td>
<td>флагман Anthropic, самая дорогая модель интервью ($5.82 за собес); нативно: контекст 1M,
бюджет выхода 128K, шкала thinking off…max, $4/$20 за 1M</td>
<td>4 прогона: low и max через OpenRouter (low — полный зачёт $1.24; max оборван 402 —
баланс исчерпан) + low и max через OrcaRouter (нативный /v1/messages; low оборван 403 —
лимит ключа; max — полный зачёт в бюджетном режиме мышления, $9.34, судьи 3.5/3.5)</td></tr>
<tr><td><b><a href="https://api-docs.deepseek.com/">DeepSeek V4.1 Flash-1017</a></b> <span class="badge g">облако</span></td>
<td>родной API DeepSeek</td>
<td>рекорд цены/качества открытых китайских весов, без накладных расходов агрегатора</td>
<td>effort low и max; единственная модель сетки с нулём нарушений phpcs (на max);
снапшот -1017 — интервью этапа 1 гонялось на базовом V4.1 Flash</td></tr>
<tr><td><b><a href="https://developers.sber.ru/docs/ru/gigachat/models/gigachat-3-ultra">GigaChat 3 Ultra</a></b> <span class="badge g">облако</span></td>
<td>родной API Сбер · мост gigachat_proxy</td>
<td>флагман российских облаков: 702B MoE (36B активных) на архитектуре DeepSeek V3,
freemium физлиц (50M токенов)</td>
<td>управления размышлениями нет · провал (2/11): начала писать сущности, но застряла в
вырожденном цикле — один и тот же DQL-запрос повторён 448 раз (серия 370 подряд),
остановлена на 44-й минуте (7.75M токенов);
в первой попытке — самовыход планом текстом за 5 секунд. API говорит только легаси-форматом
функций — подключена через локальный мост tools↔functions с OAuth-рефрешем. Цикл воспроизведён
контрольным реплеем (вся история 86.5K токенов доставлена, модель повторила) и совпадает с
известным дефектом: <a href="https://github.com/ai-forever/langchain-gigachat/pull/69">PR #69
langchain-gigachat</a>, вывод и источники — model.md</td></tr>
<tr><td><b><a href="https://developers.sber.ru/docs/ru/gigachat/models/gigachat-2-max">GigaChat 2 Max</a></b> <span class="badge g">облако</span></td>
<td>родной API Сбер · мост gigachat_proxy</td>
<td>рабочая модель Сбера из интервью этапа 1 (clef 2.60): как та же модель ведёт себя
в агентном харнесе</td>
<td>управления размышлениями нет · провал: самовыход за 7 секунд (6.5K токенов) —
прочла TASK.md, выдала план текстом и завершилась, скелет нетронут</td></tr>
<tr><td><b>huihui</b> (<a href="https://ollama.com/huihui_ai/Qwen3.8-abliterated">huihui_ai/Qwen3.8-abliterated</a> 27B) <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td>
<td>локальный кандидат семейства qwen35</td>
<td>thinking max · 3 прогона; стабильный полный зачёт (3/3), средний чекер 12.7 — платит
объёмом и нарушениями стиля</td></tr>
<tr><td><b><a href="https://ollama.com/library/qwen3.8">qwen3.8</a></b> 27B <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td>
<td>главный локальный кандидат набора</td>
<td>thinking max · 3 прогона; нестабильность по прогонам: 13 → 11 → 9</td></tr>
<tr><td><b>orca</b> (<a href="https://ollama.com/orcarouter/Qwen3.8-27B-Uncensored">orcarouter/Qwen3.8-27B-Uncensored</a> 27B) <span class="badge b">локально</span></td>
<td>локально · ollama · одни веса в двух квантованиях: Q4_K_M и IQ4_XS</td>
<td>uncensored-файнтюн qwen3.8: что даёт снятие фильтров безопасности в агентной работе;
попутно — квант-эксперимент и включённый/выключенный MTP draft (спекулятивное
ускорение вывода) на тех же весах</td>
<td>thinking on (шкала сборки без градаций) · 12 прогонов. Q4_K_M: 3 под родным тегом —
все полные зачёты, лучший локальный код (phpcs 0 в run-03); 3 в режиме MTP — качество
в пределах шума, генерация ×1.8 (63 → 115 ток/с). IQ4_XS: 3 родных — 2/3 зачётов
(ср. 12.3); 3 в режиме MTP — на живых 12–13 из 13, run-06 завис на bash-команде
(помечен в §7). Серии MTP — прогоны под ретаг-тегами qwen3.8-unc*, веса идентичны
родным (сверка блобов 2026-10-21)</td></tr>
<tr><td><b><a href="https://ollama.com/library/ornith-1.5">ornith-1.5</a></b> 35B <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M, MoE)</td>
<td>флагман открытых весов в MoE-формате, локальный потолок интервью</td>
<td>thinking max · 3 прогона; провал во всех трёх (1–3 из 13) — длинная агентная работа
не даётся, разброс темпа 0.1M/10M/39M токенов. Публичные бенчмарки хвалят её на эталонном
vLLM-стеке в bf16, независимые проверки скептичны — наш результат на ollama/llama.cpp
согласуется со скепсисом (детали в model.md); проверен и отвергнут вариант с карточным сэмплингом</td></tr>
<tr><td><b><a href="https://ollama.com/library/muse-glimmer">muse-glimmer</a></b> 28B <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td>
<td>малоизвестное семейство агентов-дистиллятов; лучшая локальная по интервью</td>
<td>thinking max · 3 прогона; быстрая (5–6 мин) и дешёвая, но без тестов и бонуса — 0/3 зачётов</td></tr>
<tr><td><b><a href="https://ollama.com/library/gemma4">gemma4:31b</a></b> QAT <span class="badge b">локально</span></td><td>локально · ollama (Q4_0)</td>
<td>старшая gemma4 в QAT-квантовании — вторая точка среди локальных</td>
<td>thinking max · 3 прогона; единственный провал обязательных сценариев сетки (run-02: 8 из 11)</td></tr>
<tr><td><b><a href="https://ollama.com/library/gemma4">gemma4:26b</a></b> <span class="badge b">локально</span></td><td>локально · ollama (Q4_K_M)</td>
<td>младшая gemma4, проба</td>
<td>thinking on · 3 прогона; бимодальна: два самовыхода за ~1 мин (провал 2/11)
и одна полноценная работа до 10/11; разбор раннего exit — в model.md</td></tr>
<tr><td><b><a href="https://ollama.com/library/bonsai-2">bonsai-2</a></b> 27B <span class="badge b">локально</span></td>
<td>локально · ollama (Q2_0, тернарные веса)</td>
<td>новинка Prism ML: тернарный дистиллят Qwen3.8, анонс обещает 98.2% бенчмарков базы</td>
<td>thinking xhigh и medium · 2 прогона; оба 2/11, ни одной строки кода — 13 ходов
только чтение (xhigh) и обрыв по лимиту мышления (medium); разбор — model.md</td></tr>
</tbody></table>

<h3>Окружение и условия</h3>
<p>Харнес — <b>pi</b> (локально, изолированный PI_CODING_AGENT_DIR: никаких глобальных
конфигов и сессий), один прогон с нуля, лимит времени 90 минут. Локальные модели — с
внутреннего ollama-сервера (адрес — env <code>OLLAMA_BASE_URL</code>, в git не
хранится), thinking — максимум шкалы модели по <code>/api/show</code>. Облачные —
Luna 6 и Sol 6.1 через OpenRouter (ключ/прокси из env, доступ только через прокси),
DeepSeek V4.1 Flash — через родной API; reasoning effort задаётся явно (прогоны есть
на минимуме — low и на максимуме шкалы провайдера). Тарифы облачных — в
<code>reports/openrouter-spend.csv</code> и карточке «стоимость API» в отчётах прогонов.
Алиас <code>deepseek-flash</code> всегда указывает на актуальный снапшот: прогоны этапа
(2026-10-18) застали V4.1-Flash-1017 — обновление вышло 2026-10-17, за день до сетки;
интервью этапа 1 (2026-10-09) гонялось на базовом V4.1 Flash, так что при сопоставлении
этапов DeepSeek — фактически две разные версии.</p>
<p><b>Контекст 102.4K у всех — искусственное ограничение харнеса, а не лимит
модели.</b> pi получает contextWindow=102400 и ужимает историю (автокомпакты на
графиках) по этому порогу; реальные окна облачных моделей больше, до них прогоны
не доходят. Единый порог — условие равенства: все модели живут при одинаковом
прессе контекста.</p>
<table><thead><tr><th>Модель (папка)</th><th>Тег</th><th class=narrow>Провайдер</th>
<th class=narrow>Параметры</th><th class=narrow>Квант</th><th>Thinking по прогонам</th>
<th class=narrow>Прогонов</th></tr></thead><tbody>
{models_table_rows()}
</tbody></table>
<p class=sub>Условия каждого прогона — thinking и контекст 102400; детали и артефакты — в разделе 7.</p>

<h2 id=results><span class=no>4.</span>Результаты моделей</h2>
{results_table(DATA)}{quality_price_map(results_agg(DATA))}{quality_speed_map(results_agg(DATA))}{quality_triangle_map(results_agg(DATA))}
{structure_section(DATA)}
{_contam_note_results()}

<h2 id=charts><span class=no>5.</span>Что показывают метрики</h2>
<h3>Время и токены</h3>
<p class=excap>Каждая точка — прогон: по горизонтали время, по вертикали суммарные токены,
цвет — итог чекера. Облачные решают в 2–5 раз быстрее и на порядок экономнее: 0.3–6.1M токенов
против 8–20M у локальных, которые проговаривают рассуждения вслух под пиком 86K.</p>
{cost_scatter(DATA)}

<h3>Уровень размышлений: low → max</h3>
<p class=excap>У всех трёх облачных моделей есть пара прогонов low/max на прочих равных.
max поднимает до полного зачёта Luna и Sol; DeepSeek V4.1 Flash-1017 полон на обоих уровнях, но max пишет
заметно более чистый код. Цена max — от ×3 (Luna) до ×10 (Sol) по API.</p>
{cloud_pairs(DATA)}

<h3>Контекст: лимит один для всех</h3>
<p class=excap>Лимит 102.4K — искусственный порог харнеса, одинаковый для всех прогонов
(нативные окна облачных больше — до них сессии не доходят). Локальные модели живут у 86K
и жмут историю 3–5 раз за прогон; Sol 6.1 (max) уложился в 67K без единого компакта.</p>
{ctx_chart(DATA)}

<h3>Разброс прогонов</h3>
<p class=excap>Полоса — от худшего к лучшему прогону модели, каждый с нуля в одинаковых условиях.
У локальных это устойчивость: qwen гуляет 9–13, huihui держит 12–13. У облачных полоса —
тот же эффект уровня размышлений, что и на графике выше.</p>
{think_dumbbell(DATA)}

<h3>Качество кода полных зачётов</h3>
<p class=excap>Нарушения phpcs/psalm/phpstan у прогонов с полным зачётом. Две стратегии:
DeepSeek V4.1 Flash-1017 — минимум замечаний анализаторов при крупном коде (2K LOC, psalm 24);
Sol — компактное решение (862 LOC) с умеренными замечаниями и максимумом покрытия.</p>
{quality_chart(DATA)}

<h2 id=judges><span class=no>6.</span>Ревью против судьи: согласие ярусов 3 и 4</h2>
<p class=excap>Оба судьи — GLM-5.3-Flash в изолированных pi-сессиях, но ревью яруса 3 знает
результат чекера и метрик, слепой судья яруса 4 видит только TASK.md и дифф. {N_AGREE} из {N_PAIRS}
прогонов — в пределах ±1 балла оси A, систематического сдвига нет.</p>
{judges_dumbbell(DATA)}
<div class="quote bad"><div class=who>Слепое пятно судьи (ярус 4)</div>
Судья видит только дифф и не знает результата чекера. Выходы за ±1 (по живым
прогонам): ornith r3 — решение неработоспособно (3/11), но код выглядит опрятно,
слепой дал 2.5 против 1.0 у ревью; qwen r3 — обязательная часть на грани (9/11),
код аккуратный — 4.0 против 2.5 у ревью. Вывод этапа: истина о работоспособности —
только за чекером; судья — второй проход по коду и поиск дефектов, не итоговый
балл нерабочих решений.</div>

{runs_inventory()}<h2 id=next><span class=no>8.</span>Стоимость конвейера и что дальше</h2>
<div class=scalebox id=econ><b>Цены и курс.</b> Все цены отчёта — в рублях по фиксированному курсу <b>₽85.15/$</b> (задан владельцем 2026-10-09 и не пересчитывается). Облачные прогоны — токены по тарифам провайдеров (таблица реальных трат ниже). Локальные — <b>электричество и амортизация</b> железа: 550 Вт в момент генерации при ₽7.07/кВт·ч (тариф с 01.10.2026) плюс амортизация (₽600 000 минус остаточные 30% за 13 140 часов работы) = <b>₽35.85/ч чистой генерации</b>. Методика перенесена из §8 «Экономика собеса» отчёта интервью (interview/build_report.py).</div>
{tier_costs()}
{api_spend()}
<div class=note><b>Оптимизации конвейера.</b> Судейские ярусы копеечные
(медианы: ревью 18 с / $0.0065, судья 16 с / $0.0063, суммарно ~$0.013
за прогон по подписке) — перевод яруса 4 на локальную decision-модель
clef (паттерн интервью) возможен, но экономит ~$0.006 за вердикт:
имеет смысл только при упоре в квоту подписки. Реальный резерв — чекер:
медиана 92 с, но первый прогон решения с рассинхроном composer.json/lock
платит composer update (~380 с) — лечится кэшированием собранного vendor
по хэшу composer.json. Ассистентные токены (8–9M за прогон) — предмет
измерения, не оптимизации.</div>
<p>Дальше: (1) расширяем сетку на остальные генеративные модели набора —
конвейер типовой, прогон одной командой (<code>run_grid.py</code>), 3 прогона на локальную
модель; (2) аудит безопасности — наблюдаем до первых реальных нарушений, тогда и вводим
жёсткие пороги; (3) сводный отчёт этапа 2 — цена/качество по полной сетке. Все артефакты
прогонов — в папках моделей <code>models/&lt;модель&gt;/backend/test-assignment/run-XX/</code>
и в git (ветка feat/billing-test-assignment).</p>

<footer>
Все числа — из артефактов прогонов: run.json, check.json, metrics.json, security.json/audit.json,
pi-session/*.jsonl. Генератор: <code>test-assignment/backend/billing/build_report.py</code> —
правки данных только через него. Судьи: <code>checker/judge_tier3.py</code> (ревью),
<code>checker/judge_tier2.py</code> (слепой судья), <code>checker/judge_audit.py</code> (аудит безопасности).
Журналы: <code>reports/openrouter-spend.csv</code> (траты API),
<code>reports/test-assignment-tier-timing.csv</code> (время и токены ярусов),
<code>reports/TEST-ASSIGNMENT-judges.md</code> (сводка судей текстом).
</footer>
<script>{TOOLTIP_JS}</script>
</div></body></html>
"""


def main():
    import ast as _ast
    import html as _html
    src = (BILLING / "checker" / "judge_tier3.py").read_text(encoding="utf-8")
    tree = _ast.parse(src)
    system = next(n.value.value for n in tree.body
                  if isinstance(n, _ast.Assign) and getattr(n.targets[0], "id", "") == "SYSTEM")
    page = HTML.replace("TIER3_SYSTEM_PLACEHOLDER", _html.escape(system))
    src2 = (BILLING / "checker" / "judge_tier2.py").read_text(encoding="utf-8")
    tree2 = _ast.parse(src2)
    system2 = next(n.value.value for n in tree2.body
                   if isinstance(n, _ast.Assign) and getattr(n.targets[0], "id", "") == "SYSTEM")
    page = page.replace("TIER4_SYSTEM_PLACEHOLDER", _html.escape(system2))
    sys.path.insert(0, str(BILLING / "checker"))
    from judge_tier3 import build_facts  # noqa: E402
    sample = next((ROOT / "models" / r["folder"] / "backend" / "test-assignment" / r["run"]
                   for r in DATA if (ROOT / "models" / r["folder"] / "backend"
                                     / "test-assignment" / r["run"] / "check.json").is_file()), None)
    if sample is not None and (sample / "check.json").is_file():
        page = page.replace("TIER3_FACTS_PLACEHOLDER", _html.escape("\n".join(build_facts(sample))))
    else:
        page = page.replace("TIER3_FACTS_PLACEHOLDER",
                            "(артефакты прогона недоступны при сборке отчёта)")
    OUT.write_text(page, encoding="utf-8")
    print(f"готово: {OUT} ({OUT.stat().st_size} байт)")


if __name__ == "__main__":
    main()

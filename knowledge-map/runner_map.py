#!/usr/bin/env python3
"""Раннер карты знаний PHP (да/нет) для LLM через ollama.

Карта — по мотивам теста Карпаты «Land or Water?» (см. blueprint.md):
одна клетка = один независимый сырой запрос — утверждение и «Верно или
неверно?». Каждый запрос содержит единственное сообщение: клетки не делят
контекст между собой, изоляция полная. keep_alive держит веса загруженными
внутри прогона, выгрузка до и после — изоляция прогонов.

Примеры:
    python3 runner_map.py --model huihui-ai:pi-100k --runs 1
    python3 runner_map.py --model gemma4:26b --runs 1 --limit 8  # smoke
"""

import argparse
import csv
import json
import os
import re
import subprocess
import sys
import time
import urllib.error
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPT_DIR.parent / "interview"))
import env_local  # noqa: F401 — грузит .env.local до чтения OLLAMA_BASE_URL ниже

DEFAULT_BASE_URL = os.environ.get("OLLAMA_BASE_URL", "http://localhost:11434")
# system-промпт: фиксированная рамка теста, одинаковая для всех моделей
# и прогонов (решение владельца 2026-11-06 — перенос протокола из хвоста
# запроса в system: каноничное место инструкций + кэш общего префикса)
SYSTEM_PROMPT = (
    "Ты проходишь тест на знание PHP. Каждое сообщение — один вопрос. "
    'Отвечай строго в JSON: {"answer": ..., "пояснение": "..."}. '
    "Для вопроса «Верно или неверно?» answer — \"да\" или \"нет\"; "
    "для вопроса о результате answer — число, строка или да/нет. "
    "Пояснение — одно короткое предложение. Никакого текста вне JSON.")
QUESTION_SUFFIX = "\n\nВерно или неверно?"
ANSWER_SUFFIX = "\n\nОтветь результатом (число, строка или да/нет)."
SYSTEM_PROMPT_EN = (
    "You are taking a PHP knowledge test. Each message is one question. "
    'Answer strictly in JSON: {"answer": ..., "explanation": "..."}. '
    'For a "True or false?" question, answer is "yes" or "no"; '
    "for a result question, answer is a number, a string, or yes/no. "
    "The explanation is one short sentence. No text outside JSON.")
QUESTION_SUFFIX_EN = "\n\nTrue or false?"
ANSWER_SUFFIX_EN = "\n\nAnswer with the result (a number, a string, or yes/no)."
SYSTEM_PROMPT_ZH = (
    "你正在参加一项 PHP 知识测试。每条消息是一个问题。"
    '请严格以 JSON 格式回答：{"answer": ..., "解释": "..."}。'
    '对于「对还是错？」类问题，answer 为 "是" 或 "否"；'
    "对于求结果的问题，answer 为数字、字符串或 是/否。"
    "解释只写一个短句。JSON 之外不要有任何文字。")
QUESTION_SUFFIX_ZH = "\n\n对还是错？"
ANSWER_SUFFIX_ZH = "\n\n请回答结果（数字、字符串或 是/否）。"


def prompt_parts(lang: str) -> tuple[str, str, str]:
    """(system, суффикс verify, суффикс recall) для языка прогона."""
    if lang == "en":
        return SYSTEM_PROMPT_EN, QUESTION_SUFFIX_EN, ANSWER_SUFFIX_EN
    if lang == "zh":
        return SYSTEM_PROMPT_ZH, QUESTION_SUFFIX_ZH, ANSWER_SUFFIX_ZH
    return SYSTEM_PROMPT, QUESTION_SUFFIX, ANSWER_SUFFIX
THINK_RE = re.compile(r"<think>.*?</think>\s*", re.DOTALL)
# decision-протокол (/v1/systemone, как в interview/score.py): без chat —
# вероятностный ответ «утверждение верно» (noul), порог да/нет — 0.5
DECISION_INSTRUCTIONS = "Верно ли утверждение о PHP?"
DECISION_CRITERIA = {"false": "Утверждение неверно",
                     "true": "Утверждение верно"}


def extract_json_answer(raw: str) -> dict | None:
    """Достать JSON-объект из ответа модели: ```-заборы срезаются, берётся
    первый сбалансированный {...}. None — если валидного JSON нет."""
    text = re.sub(r"```(?:json)?", "", raw)
    start = text.find("{")
    if start == -1:
        return None
    depth = 0
    for i in range(start, len(text)):
        if text[i] == "{":
            depth += 1
        elif text[i] == "}":
            depth -= 1
            if depth == 0:
                frag = text[start:i + 1]
                try:
                    obj = json.loads(frag)
                except json.JSONDecodeError:
                    # мягкий ремонт: одиночные бэкслеши в путях (App\Model)
                    # ломают JSON-эскейпы — удваиваем невалидные
                    frag = re.sub(r'\\(?!["\\/bfnrtu])', r"\\\\", frag)
                    try:
                        obj = json.loads(frag)
                    except json.JSONDecodeError:
                        return None
                return obj if isinstance(obj, dict) else None
    return None


YES_WORDS = {"да", "true", "yes", "1", "是", "对", "正确"}
NO_WORDS = {"нет", "false", "no", "0", "否", "不是", "错", "错误"}


def extract_explanation(raw: str, qtype: str, expected: str | None) -> str | None:
    """Пояснение модели: текст после вердиктного слова (да/нет) или после
    эталонного значения. Пояснение не влияет на вердикт — это сигнал
    двусмысленности клетки и материал для вычитки вопросника."""
    if not raw:
        return None
    low = raw.lower()
    if qtype == "yesno":
        for w in ("да", "нет", "yes", "no", "是", "否", "对", "错"):
            pos = low.find(w)
            if pos != -1:
                rest = raw[pos + len(w):].strip(" :—-.\n")
                return rest or None
        return raw.strip() or None
    if expected:
        pos = low.find(expected.lower())
        if pos != -1:
            rest = raw[pos + len(expected):].strip(" :—-.\n")
            return rest or None
    return raw.strip() or None


def normalize(s: str) -> str:
    """Нормализация ответа recall-клетки: нижний регистр, без кавычек,
    сжатые пробелы. Сравнение — «эталон входит в ответ»."""
    s = re.sub(r"\s+", " ", s.strip().lower())
    return s.strip("«»\"'` ")


def squeeze(s: str) -> str:
    """Вариант для сопоставления без пробелов вовсе: '[20, 30]' == '[20,30]'."""
    return re.sub(r"\s+", "", s.lower())


def expected_value(grader: dict, cache: dict) -> str:
    """Эталон recall-клетки: из php-сниппета (объективный грейдер — интерпретатор
    8.5 на машине теста) или из accept-списка (первый вариант — канонический)."""
    if "php" in grader:
        key = grader["php"]
        if key not in cache:
            r = subprocess.run(["php", "-r", key], capture_output=True,
                               text=True, timeout=30)
            cache[key] = r.stdout.strip()
        return cache[key]
    return grader["accept"][0]


def build_decision_state(assertion: str) -> str:
    """state для decision-модели — по образцу build_state из score.py."""
    return (f"Ты проверяешь утверждение о PHP на верность.\n\n"
            f"УТВЕРЖДЕНИЕ:\n{assertion}")


def http_json(url: str, payload: dict, timeout: int, retries: int = 3,
              headers: dict | None = None) -> dict:
    """POST с JSON и повторами ТОЛЬКО на сетевые сбои (контент не ретраим)."""
    data = json.dumps(payload).encode("utf-8")
    last_err = None
    for attempt in range(1, retries + 1):
        try:
            req = urllib.request.Request(
                url, data=data,
                headers={"Content-Type": "application/json", **(headers or {})})
            with urllib.request.urlopen(req, timeout=timeout) as resp:
                return json.loads(resp.read().decode("utf-8"))
        except (urllib.error.URLError, ConnectionError, TimeoutError) as e:
            last_err = e
            if attempt < retries:
                wait = 5 * attempt
                print(f"    сеть недоступна ({e}); повтор через {wait}s",
                      file=sys.stderr)
                time.sleep(wait)
    raise RuntimeError(f"запрос к {url} не удался после {retries} попыток: {last_err}")


# родное API DeepSeek — калибровочные серии карты (сильная cloud-модель,
# рекомендация trust-mimo п.5). Ключи в workspace не копируем: DEEPSEEK_API_KEY
# читается из env-файла облаков (MODELS_RU_ENV), как в interview/runner_ru_cloud.py
DEEPSEEK_CHAT = "https://api.deepseek.com/chat/completions"
DEEPSEEK_EFFORTS = ("low", "high", "max")  # off/medium API не поддерживает


def cloud_env_value(name: str) -> str:
    path = os.environ.get("MODELS_RU_ENV")
    if not path:
        raise RuntimeError("задайте MODELS_RU_ENV — путь к env-файлу с ключами "
                           "облаков (какие бывают — README, «Быстрый старт»)")
    path = Path(path).expanduser()
    for line in path.read_text().splitlines():
        if line.startswith(name + "="):
            return line.split("=", 1)[1].strip()
    raise RuntimeError(f"{name} не найден в {path}")


def deepseek_chat(api_key: str, model: str, prompt: str, effort: str,
                  timeout: int, system_prompt: str | None = None) -> dict:
    """Один запрос к chat/completions: ответ нормализуется под ollama-структуру
    (message.content / thinking / done_reason / eval-поля usage) — отчёты
    и supplement работают с cloud-сессиями без изменений."""
    messages = ([{"role": "system", "content": system_prompt}]
                if system_prompt else [])
    messages.append({"role": "user", "content": prompt})
    resp = http_json(DEEPSEEK_CHAT,
                     {"model": model, "messages": messages,
                      "max_tokens": 16000, "reasoning_effort": effort},
                     timeout, headers={"Authorization": f"Bearer {api_key}"})
    ch = (resp.get("choices") or [{}])[0]
    msg = ch.get("message") or {}
    u = resp.get("usage") or {}
    # кэш: у DeepSeek нативное поле prompt_cache_hit_tokens, у OpenAI-стиля —
    # prompt_tokens_details.cached_tokens; на deepseek-flash наблюдается 0
    cached = u.get("prompt_cache_hit_tokens")
    if cached is None:
        cached = (u.get("prompt_tokens_details") or {}).get("cached_tokens")
    return {"message": {"content": (msg.get("content") or "").strip(),
                        "thinking": (msg.get("reasoning_content") or "").strip() or None},
            "done_reason": ch.get("finish_reason"),
            "eval_count": u.get("completion_tokens"),
            "prompt_eval_count": u.get("prompt_tokens"),
            "prompt_eval_cached_count": cached}


def cloud_safe(api_key: str, model: str, prompt: str, effort: str,
               timeout: int, system_prompt: str | None = None) -> dict | None:
    """deepseek_chat, но сбой возвращает None (клетка станет серой)."""
    try:
        return deepseek_chat(api_key, model, prompt, effort, timeout,
                             system_prompt)
    except Exception as e:
        print(f"      ⚠️ сбой генерации ({e}); пропускаю", file=sys.stderr)
        return None


def openrouter_chat(model: str, prompt: str, effort: str | None, key: str,
                    proxy: str, timeout: int,
                    system_prompt: str | None = None) -> dict:
    """Один запрос через OpenRouter (curl через прокси — WAF режет прямой
    доступ), usage.include=true даёт cost для журнала трат; ответ
    нормализуется под ollama-структуру."""
    from runner_openrouter import call_openrouter  # curl/прокси/заголовки
    messages = ([{"role": "system", "content": system_prompt}]
                if system_prompt else []) + [{"role": "user", "content": prompt}]
    payload = {"model": model, "messages": messages, "max_tokens": 16000,
               "usage": {"include": True}}
    if effort:
        payload["reasoning"] = {"effort": effort}
    last = ""
    for attempt in range(5):
        code, body = call_openrouter(payload, key, proxy, timeout)
        if code == 200 and body:
            resp = json.loads(body)
            ch = (resp.get("choices") or [{}])[0]
            msg = ch.get("message") or {}
            u = resp.get("usage") or {}
            reasoning = msg.get("reasoning") or ""
            if isinstance(reasoning, list):
                reasoning = " ".join(
                    (x.get("text") or x.get("summary") or "") if isinstance(x, dict)
                    else str(x) for x in reasoning)
            return {"message": {"content": (msg.get("content") or "").strip(),
                                "thinking": (str(reasoning)).strip() or None},
                    "done_reason": ch.get("finish_reason"),
                    "eval_count": u.get("completion_tokens"),
                    "prompt_eval_count": u.get("prompt_tokens"),
                    "prompt_eval_cached_count":
                        (u.get("prompt_tokens_details") or {}).get("cached_tokens"),
                    "cost": u.get("cost")}
        if code in (429, 500, 502, 503, 524, 529, 0) and attempt < 4:
            wait = min(90, 5 * (2 ** attempt))
            last = f"HTTP {code}"
            print(f"      ⚠️ {last}, повтор через {wait}s", file=sys.stderr)
            time.sleep(wait)
            continue
        raise RuntimeError(f"HTTP {code}: {body[:200] or 'empty'} ({last})")


def openrouter_safe(model: str, prompt: str, effort: str | None, key: str,
                    proxy: str, timeout: int,
                    system_prompt: str | None = None) -> dict | None:
    """openrouter_chat, но сбой возвращает None (клетка станет серой)."""
    try:
        return openrouter_chat(model, prompt, effort, key, proxy, timeout,
                               system_prompt)
    except Exception as e:
        print(f"      ⚠️ сбой генерации ({e}); пропускаю", file=sys.stderr)
        return None


def openrouter_creds() -> tuple[str, str]:
    """Ключ и прокси OpenRouter — из файлов по путям env (в git не живут)."""
    from runner_openrouter import read_secret
    key = read_secret("OPENROUTER_KEY_FILE")
    proxy = os.environ.get("OPENROUTER_PROXY") or read_secret("OPENROUTER_PROXY_FILE")
    return key, proxy


def openrouter_price(model_id: str) -> tuple[float, float]:
    """Живые цены модели с /api/v1/models ($/1M токенов); доступ через прокси."""
    from runner_openrouter import read_secret
    proxy = os.environ.get("OPENROUTER_PROXY") or read_secret("OPENROUTER_PROXY_FILE")
    cmd = ["curl", "-sS", "--max-time", "30", "--proxy", proxy,
           "https://openrouter.ai/api/v1/models"]
    out = subprocess.run(cmd, capture_output=True, text=True, timeout=60)
    models = json.loads(out.stdout).get("data", [])
    for m in models:
        if m.get("id") == model_id:
            p = m.get("pricing", {})
            return (float(p.get("prompt") or 0) * 1e6,
                    float(p.get("completion") or 0) * 1e6)
    raise RuntimeError(f"модель {model_id} не найдена на OpenRouter")


def unload_model(base_url: str, model: str, timeout: int) -> None:
    """Выгрузить модель из памяти (сброс KV-кэша ollama)."""
    http_json(f"{base_url}/api/generate",
              {"model": model, "keep_alive": 0}, timeout)


def slugify(tag: str) -> str:
    return tag.replace(":", "-").replace("/", "-")


def resolve_out_root(models_root: str, slug: str) -> Path:
    """Папка модели: приоритет уже существующей (как в interview/runner.py)."""
    root = Path(models_root)
    if (root / slug).is_dir():
        return root / slug
    if slug.endswith("-latest"):
        base = root / slug[:-len("-latest")]
        if base.is_dir():
            return base
    return root / slug


def chat_once(base_url: str, model: str, prompt: str, keep_alive, think,
              timeout: int, system_prompt: str | None = None) -> dict:
    messages = ([{"role": "system", "content": system_prompt}]
                if system_prompt else [])
    messages.append({"role": "user", "content": prompt})
    payload = {"model": model, "stream": False, "keep_alive": keep_alive,
               "messages": messages}
    if think in ("on", "off"):
        payload["think"] = (think == "on")
    elif think != "auto":
        payload["think"] = think  # уровни размышления: low / medium / xhigh
    return http_json(f"{base_url}/api/chat", payload, timeout)


def chat_safe(base_url: str, model: str, prompt: str, keep_alive, think,
              timeout: int, system_prompt: str | None = None) -> dict | None:
    """chat_once, но сетевой/таймаутный сбой возвращает None вместо исключения —
    серия не должна умирать из-за одной клетки (клетка станет серой)."""
    try:
        return chat_once(base_url, model, prompt, keep_alive, think, timeout,
                         system_prompt)
    except Exception as e:
        print(f"      ⚠️ сбой генерации ({e}); пропускаю", file=sys.stderr)
        return None


def decision_once(base_url: str, model: str, assertion: str, timeout: int) -> dict:
    """Один вызов /v1/systemone: вероятность верности утверждения (noul)."""
    payload = {"model": model, "state": build_decision_state(assertion),
               "questions": {"verdict": {"type": "noul",
                                         "instructions": DECISION_INSTRUCTIONS,
                                         "criteria": DECISION_CRITERIA}}}
    return http_json(f"{base_url}/v1/systemone", payload, timeout)


def decision_safe(base_url: str, model: str, assertion: str,
                  timeout: int) -> dict | None:
    """decision_once, но сбой возвращает None вместо исключения (как chat_safe)."""
    try:
        return decision_once(base_url, model, assertion, timeout)
    except Exception as e:
        print(f"      ⚠️ сбой decision-запроса ({e}); пропускаю", file=sys.stderr)
        return None


def parse_answer(raw: str) -> tuple[bool | None, str]:
    """Разобрать ответ модели. Возвращает (ответ, как разобрано).

    Строгий формат: первое слово «да» (yes) или «нет» (no). Любое другое
    первое слово — ищем первое подходящее по всему ответу (пометка late).
    Синонимы («верно», «истина»…) форматом не считаются — клетка станет
    серой: неумение следовать формату тоже измерение (см. blueprint).
    """
    text = THINK_RE.sub("", raw).strip().lower()
    # китайский фолбэк (zh-прогоны): вердиктные иероглифы в тексте; для
    # ru/en-ответов эти символы не встречаются — поведение не меняется
    for w in ("不是", "错误", "否", "错"):
        if w in text:
            return False, "late"
    for w in ("正确", "是", "对"):
        if w in text:
            return True, "late"
    tokens = re.findall(r"[а-яёa-z]+", text)
    if not tokens:
        return None, "none"
    if tokens[0] in ("да", "yes"):
        return True, "ok"
    if tokens[0] in ("нет", "no"):
        return False, "ok"
    for t in tokens[1:]:
        if t in ("да", "yes"):
            return True, "late"
        if t in ("нет", "no"):
            return False, "late"
    return None, "none"


def acc_pattern(v: str) -> str:
    """Regex эталона accept-клетки: дефисы/подчёркивания/пробелы/равно/
    двоеточия/запятые эталона соответствуют любому из них в ответе; зачёт
    только на
    границах «слов» — сосед-спереди не буква/цифра/_, сосед-сзади не
    буква/цифра/_/- (иначе container-xxl ловит «container», а
    --random-order — «random»; см. аудит trust-mimo)."""
    parts = [re.escape(x) for x in re.split(r"[-\s_:,=]+", v) if x]
    return r"[-\s_:,=]+".join(parts)


def resolve_questions_file(km_dir: Path, session: dict) -> Path | None:
    """Вопросник прогона по версии/языку из session.json — только строгие
    совпадения версий; без уверенного резолвинга None (прогон пропускается:
    переразметка чужими эталонами недопустима)."""
    qver = session.get("questionnaire")
    if not qver:
        return None
    lang = session.get("lang", "ru")
    repo = Path(__file__).parent
    qfile = session.get("questionnaire_file")

    def version_of(cand: Path):
        try:
            return json.loads(cand.read_text(encoding="utf-8")).get("version")
        except (OSError, json.JSONDecodeError):
            return None

    stems = [f"questions.v{qver}-{lang}", f"questions.v{qver}"] \
        if lang != "ru" else [f"questions.v{qver}"]
    for base in (repo, km_dir):
        for cand in ([base / qfile] if qfile else []) + \
                    [base / f"{stem}.json" for stem in stems]:
            if cand.exists() and version_of(cand) == qver:
                return cand
    return None


def supplement_deep(model_dir: Path, timeout: int,
                    limit: int | None = None) -> None:
    """Дополнение СУЩЕСТВУЮЩИХ прогонов клетками зоны «Глубина» (L9/L10).

    Это не новые прогоны и не новая версия вопросника: run-каталоги те же,
    основной вопросник остаётся v8 — deep-клетки живут в
    questions-deep{,-en,-zh}.json и дописываются в session.json (штамп
    «deep») и answers.csv. Условия запроса — из session.json прогона
    (model, base_url, think, lang, keep_alive); выгрузка модели до и после
    каждого прогона — изоляция как в основной карте. Дополнение идёт
    прогонно-прогрессивно: крах серии данные не теряет.
    """
    for s_path in sorted(model_dir.glob("run-*/session.json")):
        out_dir = s_path.parent
        session = json.loads(s_path.read_text(encoding="utf-8"))
        if session.get("protocol", "chat") != "chat":
            print(f"  {out_dir.name}: протокол {session.get('protocol')} — "
                  f"пропускаю")
            continue
        if session.get("questionnaire") != 8:
            print(f"  {out_dir.name}: вопросник v{session.get('questionnaire')} "
                  f"— deep-зона собрана для v8, пропускаю")
            continue
        lang = session.get("lang", "ru")
        stem = f"questions-deep{'' if lang == 'ru' else '-' + lang}.json"
        dpath = SCRIPT_DIR / stem
        ddata = json.loads(dpath.read_text(encoding="utf-8"))
        questions = ddata["questions"]
        check_questionnaire(questions)
        # резюме и новая версия deep-файла: уже отвеченные клетки не
        # перезапрашиваются — добавляются только недостающие; клетка,
        # чей эталон изменился с прошлого дополнения (correct_answer
        # расходится с текущим), переспрашивается — ответ был на другую
        # постановку вопроса; штамп обновляется до текущей версии файла
        have = {t["id"]: t for t in session["turns"]
                if t.get("level", 0) >= 9}
        expected_cache: dict = {}

        def _expected(q: dict) -> str:
            e = expected_value(q["grader"], expected_cache)
            return normalize(e) if e else e

        stale = [q["id"] for q in questions if q["id"] in have and
                 have[q["id"]].get("correct_answer") != _expected(q)]
        if stale:
            print(f"  {out_dir.name}: эталоны изменились у {len(stale)} "
                  f"клеток ({', '.join(stale)}) — переспрашиваю")
            gone = set(stale)
            session["turns"] = [t for t in session["turns"]
                                if t["id"] not in gone]
            have = {t["id"]: t for t in session["turns"]
                    if t.get("level", 0) >= 9}
        missing = [q for q in questions if q["id"] not in have]
        if not missing:
            if (session.get("deep") or {}).get("version") == ddata.get("version"):
                print(f"  {out_dir.name}: deep уже дополнен — пропускаю")
                continue
            session["deep"] = {"file": stem,
                               "version": ddata.get("version"),
                               "added_at": datetime.now(timezone.utc).isoformat()}
            s_path.write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")
            print(f"  {out_dir.name}: клетки все на месте — штамп "
                  f"обновлён до v{ddata.get('version')}")
            continue
        if have:
            print(f"  {out_dir.name}: deep есть {len(have)}/{len(questions)} "
                  f"— допрашиваю недостающие")
        session["deep"] = {"file": stem, "version": ddata.get("version"),
                           "added_at": datetime.now(timezone.utc).isoformat()}
        base_url = session.get("base_url") or DEFAULT_BASE_URL
        model = session.get("model")
        keep_alive = session.get("keep_alive") or "10m"
        think = session.get("think") or "off"
        provider = session.get("provider", "ollama")
        api_key = cloud_proxy = None
        if provider == "deepseek":
            api_key = cloud_env_value("DEEPSEEK_API_KEY")
        elif provider == "openrouter":
            api_key, cloud_proxy = openrouter_creds()
        effort = think if think in DEEPSEEK_EFFORTS else "low"
        sysp, _q_suf, a_suf = prompt_parts(lang)
        turns = session["turns"]
        supp_costs: list[float] = []
        supp_in: list[int] = []
        supp_out: list[int] = []
        print(f"[{out_dir.name}] {model} · deep v{ddata.get('version')} · "
              f"язык {lang} · think {think} · клеток {len(missing)}")
        if provider == "ollama":
            unload_model(base_url, model, timeout)
        for q in (missing[:limit] if limit else missing):
            t0 = time.monotonic()
            prompt = q["prompt"] + a_suf
            if provider == "deepseek":
                resp = cloud_safe(api_key, model, prompt, effort, timeout, sysp)
            elif provider == "openrouter":
                resp = openrouter_safe(model, prompt, effort, api_key,
                                       cloud_proxy, timeout, sysp)
            else:
                resp = chat_safe(base_url, model, prompt, keep_alive, think,
                                 timeout, sysp)
            duration = time.monotonic() - t0
            if resp and provider == "openrouter":
                supp_costs.append(float((resp.get("cost")) or 0))
                supp_in.append(resp.get("prompt_eval_count") or 0)
                supp_out.append(resp.get("eval_count") or 0)
            thinking = None
            if resp is None:
                answer_raw, json_ok = "", False
                eval_count = prompt_eval_count = cached_count = None
                done_reason = "error"
            else:
                msg = resp.get("message", {})
                answer_raw = (msg.get("content") or "").strip()
                thinking = (msg.get("thinking") or "").strip() or None
                done_reason = resp.get("done_reason")
                eval_count = resp.get("eval_count")
                prompt_eval_count = resp.get("prompt_eval_count")
                cached_count = resp.get("prompt_eval_cached_count")
            # разбор и грейдинг — как recall-клетки основной карты
            obj = extract_json_answer(answer_raw)
            json_ok = obj is not None and isinstance(
                obj.get("answer"), (str, int, float, bool))
            explanation = None
            if json_ok:
                explanation = (str(obj.get("пояснение") or obj.get("explanation")
                                   or obj.get("解释") or "").strip() or None)
            expected = expected_value(q["grader"], expected_cache)
            expected = normalize(expected) if expected else expected
            if json_ok:
                answer = normalize(str(obj["answer"]))
                parsed = "json"
            else:
                answer = (normalize(answer_raw.split("\n", 1)[0])
                          if answer_raw else None)
                parsed = "late" if answer else "none"
            if "regex" in q["grader"]:
                is_correct = answer is not None and re.fullmatch(
                    q["grader"]["regex"], answer) is not None
                strict = is_correct
            else:
                variants = [normalize(v)
                            for v in q["grader"].get("accept", [expected])]
                is_correct = answer is not None and any(
                    re.search(rf"(?<![\w_]){acc_pattern(v)}(?![\w-])", answer)
                    for v in variants)
                strict = answer is not None and answer == normalize(expected)
            if explanation is None:
                explanation = extract_explanation(answer_raw, "answer", expected)
            turns.append({
                "id": q["id"], "topic": q["topic"], "level": q["level"],
                "type": "answer", "prompt_text": q["prompt"],
                "explanation": explanation, "json_ok": json_ok,
                "correct_answer": expected, "prompt": prompt,
                "answer_raw": answer_raw, "answer": answer, "parsed": parsed,
                "expected": expected, "is_correct": is_correct,
                "strict": strict, "p": None, "thinking": thinking,
                "duration_s": round(duration, 2),
                "eval_count": eval_count,
                "prompt_eval_count": prompt_eval_count,
                "prompt_eval_cached_count": cached_count,
                "done_reason": done_reason,
            })
            session["question_count"] = len(turns)
            session["totals"] = {
                "duration_s": round(sum(t["duration_s"] for t in turns), 1),
                "prompt_tokens": sum(t.get("prompt_eval_count") or 0
                                     for t in turns),
                "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
                "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0
                                     for t in turns),
            }
            s_path.write_text(
                json.dumps(session, ensure_ascii=False, indent=2),
                encoding="utf-8")
            mark = "✅" if is_correct else ("⬜" if answer is None else "❌")
            warn = (f" ⚠️ ОБРЫВ: {done_reason}"
                    if done_reason not in (None, "stop") else "")
            print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark} "
                  f"'{answer or '—'}' (эталон: '{expected}'){warn}")
        write_answers_csv(out_dir, turns)
        if provider == "openrouter" and supp_costs:
            from runner_openrouter import append_ledger
            append_ledger(model, f"knowledge-map deep v{ddata.get('version')} "
                                 f"{out_dir.name}",
                          {"requests": len(supp_costs),
                           "input_tokens": sum(supp_in),
                           "output_tokens": sum(supp_out),
                           "cost_usd": sum(supp_costs)})
        if provider == "ollama":
            try:
                unload_model(base_url, model, timeout)
                print(f"  {out_dir.name}: готово ({len(turns)} клеток), "
                      f"модель выгружена")
            except Exception as e:
                print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
        else:
            print(f"  {out_dir.name}: готово ({len(turns)} клеток)")


def regrade(model_dir: Path, timeout: int) -> None:
    """Переоценка is_correct/strict по сохранённым ответам текущими
    грейдерами (перепрогон не нужен; аудит trust-mimo)."""
    cache: dict = {}
    fixed_cells = 0
    for s_path in sorted(model_dir.glob("run-*/session.json")):
        out_dir = s_path.parent
        session = json.loads(s_path.read_text(encoding="utf-8"))
        qpath = resolve_questions_file(model_dir, session)
        if qpath is None:
            print(f"  {out_dir.name}: пропускаю — вопросник не резолвится "
                  f"уверенно (questionnaire={session.get('questionnaire')})")
            continue
        questions = {q["id"]: q for q in json.loads(
            qpath.read_text(encoding="utf-8"))["questions"]}
        # зона «Глубина»: штамп deep в сессии → добавляем deep-грейдеры
        deep = session.get("deep")
        if deep:
            dpath = Path(__file__).parent / (deep.get("file") or "")
            try:
                ddata = json.loads(dpath.read_text(encoding="utf-8")) \
                    if deep.get("file") else None
            except (OSError, json.JSONDecodeError):
                ddata = None
            if ddata and (deep.get("version") is None
                          or ddata.get("version") == deep.get("version")):
                questions.update({q["id"]: q for q in ddata["questions"]})
            else:
                print(f"  {out_dir.name}: deep-вопросник не резолвится — "
                      f"deep-клетки не переоцениваются")
        changed = []
        for t in session.get("turns", []):
            q = questions.get(t["id"])
            if q is None:
                continue
            answer = t.get("answer")
            if t.get("type") == "answer":
                expected = expected_value(q["grader"], cache)
                expected = normalize(expected) if expected else expected
                if "regex" in q["grader"]:
                    ok = answer is not None and re.fullmatch(
                        q["grader"]["regex"], answer) is not None
                else:
                    variants = [normalize(v)
                                for v in q["grader"].get("accept", [expected])]
                    ok = answer is not None and any(
                        re.search(rf"(?<![\w_]){acc_pattern(v)}(?![\w-])",
                                  answer) for v in variants)
                strict = answer is not None and answer == normalize(expected)
            else:
                if "answer" not in q:
                    continue  # старый формат вопроса без эталона — не трогаем
                ok = answer is not None and answer == q["answer"]
                strict = ok
            if ok != t.get("is_correct") or strict != t.get("strict"):
                changed.append((t["id"], t.get("is_correct"), ok))
                t["is_correct"], t["strict"] = ok, strict
        if changed:
            s_path.write_text(json.dumps(session, ensure_ascii=False, indent=2),
                              encoding="utf-8")
            write_answers_csv(out_dir, session["turns"])
            fixed_cells += len(changed)
            print(f"  {out_dir.name}: переоценено {len(changed)} клеток: "
                  + ", ".join(f"{cid} {_old}→{new}" for cid, _old, new
                              in changed))
    print(f"regrade готов: изменённых клеток {fixed_cells}")


def check_questionnaire(questions: list) -> None:
    """Санитария вопросника: уникальные id, полные поля по типам клеток."""
    ids = [q["id"] for q in questions]
    dupes = {i for i in ids if ids.count(i) > 1}
    if dupes:
        raise SystemExit(f"дубликаты id в вопроснике: {', '.join(sorted(dupes))}")
    n_recall = 0
    for q in questions:
        qtype = q.get("type", "yesno")
        base = ("id", "topic", "level", "note")
        if qtype == "answer":
            required = base + ("prompt", "grader")
            n_recall += 1
            if "php" not in q.get("grader", {}) and "accept" not in q.get("grader", {}):
                raise SystemExit(f"у recall-клетки {q['id']} нет grader.php/accept")
        else:
            required = base + ("text", "answer")
        for field in required:
            if field not in q:
                raise SystemExit(f"у вопроса {q.get('id', '?')} нет поля {field}")
    if n_recall:
        # эталоны php-клеток вычисляем сразу — битый сниппет не должен
        # всплывать посреди прогона; regex-грейдеры компилируем сразу
        cache: dict = {}
        for q in questions:
            if q.get("type") == "answer" and "regex" in q.get("grader", {}):
                re.compile(q["grader"]["regex"])
        for q in questions:
            if q.get("type") == "answer" and "php" in q["grader"]:
                val = expected_value(q["grader"], cache)
                if not val:
                    raise SystemExit(f"php-эталон {q['id']} вернул пустой вывод")
        print(f"вопросник: {len(questions)} клеток, recall: {n_recall}, "
              f"да/нет: {len(questions) - n_recall}; php-эталоны проверены")
    else:
        true_n = sum(1 for q in questions if q["answer"])
        false_n = len(questions) - true_n
        print(f"вопросник: {len(questions)} утверждений, "
              f"верных {true_n} / ложных {false_n} "
              f"(базлайн константного ответа: {100 * min(true_n, false_n) / len(questions):.1f}%)")


def run_map(base_url: str, model: str, questions: list, q_version, run_no: int,
            out_dir: Path, keep_alive, think: str, timeout: int,
            limit: int | None, protocol: str = "chat",
            questions_file: str | None = None, lang: str = "ru",
            provider: str = "ollama", api_key: str | None = None,
            cloud_proxy: str | None = None) -> None:
    if protocol == "chat" and provider == "ollama":
        print(f"  выгружаю модель перед прогоном...")
        unload_model(base_url, model, timeout)

    turns: list = []
    session = {
        "model": model,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": ("openrouter.ai (via proxy)" if provider == "openrouter"
                     else DEEPSEEK_CHAT if provider == "deepseek" else base_url),
        "protocol": protocol,
        "provider": provider,
        "keep_alive": (keep_alive if protocol == "chat" and provider == "ollama"
                       else None),
        "kv_cache": ("cloud via OpenRouter: прогоны изолированы "
                     "независимостью запросов" if provider == "openrouter" else
                     ("cloud: прогоны изолированы независимостью запросов "
                      "(серверный кэш не наблюдается — cached_tokens=0)"
                      if provider == "deepseek" else
                      ("weights-only (клетки независимы, префиксы не шарятся)"
                       if protocol == "chat" else None))),
        "think": ("— (decision-протокол: вероятностный ответ noul)"
                  if protocol == "decision" else think),
        "question_count": 0,
        "questionnaire": q_version,
        "questionnaire_file": questions_file,
        "lang": lang,
        "system_prompt": (prompt_parts(lang)[0] if protocol == "chat" else None),
        "prompt_suffix": (prompt_parts(lang)[1].strip()
                          if protocol == "chat" else None),
        "answer_suffix": (prompt_parts(lang)[2].strip()
                          if protocol == "chat" else None),
        "decision_question": ({"instructions": DECISION_INSTRUCTIONS,
                               "criteria": DECISION_CRITERIA}
                              if protocol == "decision" else None),
        "totals": {},
        "turns": turns,
    }

    def flush():
        """Прогрессивное сохранение: крах/перезагрузка серию не теряют."""
        session["question_count"] = len(turns)
        session["totals"] = {
            "duration_s": round(sum(t["duration_s"] for t in turns), 1),
            "prompt_tokens": sum(t.get("prompt_eval_count") or 0 for t in turns),
            "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
            "cached_tokens": sum(t.get("prompt_eval_cached_count") or 0 for t in turns),
        }
        out_dir.mkdir(parents=True, exist_ok=True)
        (out_dir / "session.json").write_text(
            json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")

    to_ask = questions[:limit] if limit else questions
    expected_cache: dict = {}
    run_costs: list[float] = []
    for q in to_ask:
        qtype = q.get("type", "yesno")
        if protocol == "decision" and qtype == "answer":
            print(f"    [{q['id']}] recall-клетка не применима в "
                  f"decision-протоколе — пропускаю")
            turns.append({
                "id": q["id"], "topic": q["topic"], "level": q["level"],
                "type": qtype, "prompt_text": q["prompt"], "correct_answer": None,
                "prompt": "", "answer_raw": "", "answer": None, "parsed": "none",
                "expected": None, "is_correct": False, "strict": False,
                "p": None, "thinking": None, "duration_s": 0.0,
                "eval_count": None, "prompt_eval_count": None,
                "prompt_eval_cached_count": None, "done_reason": "unsupported",
            })
            flush()
            continue
        t0 = time.monotonic()
        if protocol == "decision":
            resp = decision_safe(base_url, model, q["text"], timeout)
        sysp, q_suf, a_suf = prompt_parts(lang)
        prompt = (q["prompt"] + a_suf) if qtype == "answer" else (q["text"] + q_suf)
        if provider == "deepseek":
            resp = cloud_safe(api_key, model, prompt, think, timeout, sysp)
        elif provider == "openrouter":
            resp = openrouter_safe(model, prompt, think, api_key, cloud_proxy,
                                   timeout, sysp)
        else:
            resp = chat_safe(base_url, model, prompt, keep_alive, think,
                             timeout, sysp)
        duration = time.monotonic() - t0
        if resp and provider == "openrouter":
            run_costs.append(float((resp.get("cost")) or 0))
        p = None
        thinking = None
        expected = None
        if resp is None:
            answer_raw, answer, parsed = "", None, "none"
            json_ok = False
            explanation = None
            eval_count = prompt_eval_count = cached_count = None
            done_reason = "error"
        elif protocol == "decision":
            a = (resp.get("answers") or {}).get("verdict") or {}
            p = a.get("noul")
            if p is None:
                answer_raw, answer, parsed = "", None, "none"
            else:
                p = round(float(p), 3)
                answer, parsed = p >= 0.5, "ok"
                answer_raw = f"noul={p}"
            u = resp.get("usage") or {}
            eval_count = u.get("output_tokens")
            prompt_eval_count = u.get("input_tokens")
            cached_count = None
            done_reason = "stop"
        else:
            msg = resp.get("message", {})
            answer_raw = (msg.get("content") or "").strip()
            thinking = (msg.get("thinking") or "").strip() or None
            # основной разбор — JSON (протокол требует строго JSON),
            # текстовый разбор — фолбэк с пометкой late
            obj = extract_json_answer(answer_raw)
            json_ok = obj is not None and isinstance(obj.get("answer"), (str, int, float, bool))
            explanation = None
            if json_ok:
                explanation = (str(obj.get("пояснение") or obj.get("explanation")
                                   or obj.get("解释") or "")
                               .strip() or None)
            if qtype == "answer":
                # recall-клетка: ответ = только результат; эталон — php или accept
                expected = expected_value(q["grader"], expected_cache)
                expected = normalize(expected) if expected else expected
                if json_ok:
                    answer = normalize(str(obj["answer"]))
                    parsed = "json"
                else:
                    # фолбэк: только первая строка сырого ответа — эталон в
                    # пояснении зачёту не подлежит (аудит trust-mimo)
                    answer = (normalize(answer_raw.split("\n", 1)[0])
                              if answer_raw else None)
                    parsed = "late" if answer else "none"
                done_reason = resp.get("done_reason")
            else:
                if json_ok:
                    a = normalize(str(obj["answer"]))
                    answer = True if a in YES_WORDS else \
                        (False if a in NO_WORDS else None)
                else:
                    answer = None
                if answer is None:
                    # фолбэк: текстовый разбор первого слова
                    answer, parsed = parse_answer(answer_raw)
                else:
                    parsed = "json"
                expected = "да" if q["answer"] else "нет"
                done_reason = resp.get("done_reason")
            if explanation is None:
                explanation = extract_explanation(answer_raw, qtype, expected)
            eval_count = resp.get("eval_count")
            prompt_eval_count = resp.get("prompt_eval_count")
            cached_count = resp.get("prompt_eval_cached_count")

        if qtype == "answer":
            if "regex" in q["grader"]:
                # строгое совпадение (например, номер версии): подстрока
                # «не 8.5» или «8.4 и 8.5» не должна засчитываться
                is_correct = answer is not None and re.fullmatch(
                    q["grader"]["regex"], answer) is not None
                strict = is_correct
            else:
                # эталоны: php-вывод или все варианты accept-списка; expected
                # может быть None при серой клетке (сбой запроса)
                variants = [normalize(v) for v in
                            q["grader"].get("accept",
                                            [expected] if expected else [])
                            if v is not None]
                # зачёт — эталон на границах «слов»: соседний символ не буква/
                # цифра/подчёркивание/дефис (иначе container-xxl ловит «container»,
                # --random-order ловит «random» — см. аудит trust-mimo)
                is_correct = answer is not None and any(
                    re.search(rf"(?<![\w_]){acc_pattern(v)}(?![\w-])", answer)
                    for v in variants)
                strict = answer is not None and answer == normalize(expected)
        else:
            is_correct = answer is not None and answer == q["answer"]
            strict = is_correct
        turns.append({
            "id": q["id"], "topic": q["topic"], "level": q["level"],
            "type": qtype,
            "prompt_text": q["text"] if qtype == "yesno" else q["prompt"],
            "explanation": explanation, "json_ok": json_ok,
            "correct_answer": expected if qtype == "answer" else q["answer"],
            "prompt": (prompt if protocol == "chat"
                       else build_decision_state(q["text"])),
            "answer_raw": answer_raw, "answer": answer, "parsed": parsed,
            "expected": expected, "is_correct": is_correct, "strict": strict,
            "p": p, "thinking": thinking,
            "duration_s": round(duration, 2),
            "eval_count": eval_count,
            "prompt_eval_count": prompt_eval_count,
            "prompt_eval_cached_count": cached_count,
            "done_reason": done_reason,
        })
        p_s = f", p={p}" if p is not None else ""
        truncated = done_reason not in (None, "stop")
        warn = " ⚠️ ОБРЫВ: " + str(done_reason) if truncated else ""
        if qtype == "answer":
            mark = "✅" if is_correct else ("⬜" if answer is None else "❌")
            given = answer if answer else "—"
            print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark} "
                  f"'{given}' (эталон: '{expected or '—'}'){warn}")
        else:
            mark = {True: "да ✅", False: "нет ❌", None: "— ⬜"}[answer]
            print(f"    [{q['id']}] {q['topic']}: {duration:.0f}s → {mark}{p_s}"
                  f" (верно: {'да' if q['answer'] else 'нет'}){warn}")
        flush()

    flush()
    write_answers_csv(out_dir, turns)
    total = sum(t["duration_s"] for t in turns)
    correct = sum(1 for t in turns if t.get("is_correct"))
    print(f"  готово: {out_dir} ({len(turns)} клеток, верных {correct}, {total:.0f}s)")
    if provider == "openrouter" and run_costs:
        from runner_openrouter import append_ledger
        append_ledger(model, f"knowledge-map {out_dir.name}",
                      {"requests": len(run_costs),
                       "input_tokens": sum(t.get("prompt_eval_count") or 0 for t in turns),
                       "output_tokens": sum(t.get("eval_count") or 0 for t in turns),
                       "cost_usd": sum(run_costs)})
        print(f"  траты: ${sum(run_costs):.4f} → reports/openrouter-spend.csv")


def write_answers_csv(out_dir: Path, turns: list) -> None:
    """Разбор одной строкой на клетку — для анализа кодом (как judges CSV).
    Для recall-клеток answer — нормализованный ответ строкой, expected —
    эталон; для да/нет — да/нет как раньше."""
    with (out_dir / "answers.csv").open("w", newline="", encoding="utf-8") as f:
        w = csv.writer(f, lineterminator="\n")
        w.writerow(["id", "topic", "level", "type", "answer", "expected",
                    "parsed", "correct_answer", "is_correct", "strict",
                    "explanation", "duration_s", "prompt_tokens",
                    "output_tokens", "cached_tokens"])
        for t in turns:
            qtype = t.get("type", "yesno")
            if qtype == "answer":
                answer = t.get("answer") or ""
                expected = t.get("expected") or ""
                correct_answer = t.get("correct_answer") or ""
            else:
                answer = "" if t["answer"] is None else \
                    ("да" if t["answer"] else "нет")
                expected = ""
                correct_answer = "да" if t["correct_answer"] else "нет"
            w.writerow([t["id"], t["topic"], t["level"], qtype,
                        answer, expected, t["parsed"], correct_answer,
                        int(bool(t.get("is_correct"))), int(bool(t.get("strict"))),
                        (t.get("explanation") or "")[:300],
                        t["duration_s"], t.get("prompt_eval_count"),
                        t.get("eval_count"), t.get("prompt_eval_cached_count")])


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__,
                                formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--model", default=None,
                   help="ollama-тег модели на сервере ollama (не нужен для "
                        "--regrade)")
    p.add_argument("--runs", type=int, default=1, help="число прогонов (default 1)")
    p.add_argument("--base-url", default=DEFAULT_BASE_URL)
    p.add_argument("--questions", default=str(SCRIPT_DIR / "questions.json"))
    p.add_argument("--models-root", default=str(SCRIPT_DIR.parent / "models"))
    p.add_argument("--out", default=None,
                   help="папка модели; по умолчанию <models-root>/<slug модели>")
    p.add_argument("--run", type=int, default=None,
                   help="номер первого прогона; по умолчанию — первый свободный")
    p.add_argument("--limit", type=int, default=None,
                   help="только первые N клеток (для smoke-теста)")
    p.add_argument("--think", choices=["auto", "on", "off", "low", "medium", "xhigh"],
                   default="off",
                   help="режим thinking (default off — стандарт локальных собесов; "
                        "в decision-протоколе не применяется)")
    p.add_argument("--regrade", metavar="MODEL_DIR",
                   help="переоценить сохранённые ответы всех прогонов модели "
                        "текущими грейдерами (без перепрогона); вопросник "
                        "берётся по версии/языку из session.json")
    p.add_argument("--yes", action="store_true",
                   help="подтвердить платный запуск (--api deepseek); без неё "
                        "раннер печатает оценку стоимости и выходит")
    p.add_argument("--api", choices=["ollama", "deepseek", "openrouter"],
                   default="ollama",
                   help="бэкенд запросов: ollama (default) или родное API "
                        "DeepSeek (калибровка сильной cloud-моделью); "
                        "ключ — DEEPSEEK_API_KEY из MODELS_RU_ENV")
    p.add_argument("--supplement", metavar="MODEL_DIR",
                   help="дополнить существующие v8-прогоны модели клетками "
                        "зоны «Глубина» (questions-deep) — те же run-каталоги, "
                        "без новых прогонов; условия из session.json")
    p.add_argument("--lang", choices=["ru", "en", "zh"], default="ru",
                   help="язык прогона: system-промпт и вопросы (вопросник "
                        "--questions должен соответствовать языку)")
    p.add_argument("--protocol", choices=["chat", "decision"], default="chat",
                   help="протокол ответов: chat — ollama /api/chat (default); "
                        "decision — /v1/systemone с вероятностью noul, для "
                        "decision-моделей (clef, clef-flash), не умеющих chat")
    p.add_argument("--keep-alive", default="10m",
                   help="keep_alive внутри прогона (default 10m); изоляция прогонов "
                        "— выгрузкой до и после")
    p.add_argument("--timeout", type=int, default=600,
                   help="таймаут одного запроса, сек (default 600)")
    args = p.parse_args()

    if args.regrade:
        regrade(Path(args.regrade), args.timeout)
        return 0
    if args.supplement:
        supplement_deep(Path(args.supplement), args.timeout, args.limit)
        return 0
    if not args.model:
        p.error("укажите --model (или --regrade MODEL_DIR)")
    api_key = cloud_proxy = None
    if args.api == "deepseek":
        api_key = cloud_env_value("DEEPSEEK_API_KEY")
        if args.think not in DEEPSEEK_EFFORTS:
            p.error(f"--think для --api deepseek — один из "
                    f"{DEEPSEEK_EFFORTS} (у flashes нет off/medium; "
                    f"стандарт облачных серий — low)")
    elif args.api == "openrouter":
        api_key, cloud_proxy = openrouter_creds()
        if args.think not in ("low", "medium", "high"):
            p.error("--think для --api openrouter — low, medium или high "
                    "(стандарт облачных серий — low)")

    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata
    check_questionnaire(questions)

    # предохранитель платных серий: оценка стоимости ДО запросов.
    # Тариф deepseek-flash: $0.30/1M вх (кэш не учитываем — на flash
    # не срабатывает), $1.20/1M вых. Оценка грубая (±30%): ru-текст
    # токенизируется ~6 симв./токен, выход ~220 ток/клетку (замер серии
    # 2026-11-08: 169 вх / 203 вых на клетку).
    if args.api == "deepseek" and not args.yes:
        sysp_chars = len(prompt_parts(args.lang)[0])
        q_chars = sum(len(q.get("prompt") or q.get("text") or "")
                      for q in questions)
        n_req = len(questions) * args.runs
        in_est = (sysp_chars + q_chars / max(1, len(questions))) * n_req / 6.0
        out_est = 220 * n_req
        cost = in_est / 1e6 * 0.30 + out_est / 1e6 * 1.20
        print(f"оценка платной серии: {len(questions)} клеток × {args.runs} "
              f"прогонов ≈ {in_est / 1e6:.2f}M вх + {out_est / 1e6:.2f}M вых "
              f"токенов ≈ ${cost:.2f} (грубо, ±30%; кэш не учитан — на "
              f"deepseek-flash не срабатывает)")
        print("для запуска добавьте --yes")
        return 1
    if args.api == "openrouter" and not args.yes:
        price_in, price_out = openrouter_price(args.model)
        sysp_chars = len(prompt_parts(args.lang)[0])
        q_chars = sum(len(q.get("prompt") or q.get("text") or "")
                      for q in questions)
        n_req = len(questions) * args.runs
        in_est = (sysp_chars + q_chars / max(1, len(questions))) * n_req / 6.0
        out_est = 220 * n_req
        cost = in_est / 1e6 * price_in + out_est / 1e6 * price_out
        print(f"оценка платной серии: {len(questions)} клеток × {args.runs} "
              f"прогонов ≈ {in_est / 1e6:.2f}M вх + {out_est / 1e6:.2f}M вых "
              f"токенов ≈ ${cost:.2f} (живые цены OpenRouter: "
              f"${price_in:.2f}/1M вх, ${price_out:.2f}/1M вых; кэш не учитан)")
        print("для запуска добавьте --yes")
        return 1

    out_root = (Path(args.out) if args.out
                else resolve_out_root(args.models_root, slugify(args.model)))
    out_root = out_root / "knowledge-map"

    keep_alive = int(args.keep_alive) if str(args.keep_alive).lstrip("-").isdigit() \
        else args.keep_alive

    run_no = args.run
    for i in range(args.runs):
        if run_no is None:
            run_no = 1
            while (out_root / f"run-{run_no:02d}").exists():
                run_no += 1
        out_dir = out_root / f"run-{run_no:02d}"
        if out_dir.exists():
            print(f"папка {out_dir} уже существует — пропускаю "
                  f"(удалите или укажите --run)", file=sys.stderr)
            run_no += 1
            continue
        print(f"[run-{run_no:02d}] {args.model} · карта знаний PHP "
              f"· протокол: {args.protocol}")
        run_map(args.base_url, args.model, questions, q_version, run_no,
                out_dir, keep_alive, args.think, args.timeout, args.limit,
                args.protocol, Path(args.questions).name, args.lang,
                provider=args.api, api_key=api_key, cloud_proxy=cloud_proxy)
        run_no += 1
        if args.protocol == "decision" or args.api != "ollama":
            continue  # decision/облако не держат модель в ollama — выгружать нечего
        # выгрузка после КАЖДОГО прогона: изоляция прогонов
        try:
            unload_model(args.base_url, args.model, args.timeout)
            print("  модель выгружена (изоляция прогонов)")
        except Exception as e:
            print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

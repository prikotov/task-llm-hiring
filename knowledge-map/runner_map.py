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


def http_json(url: str, payload: dict, timeout: int, retries: int = 3) -> dict:
    """POST с JSON и повторами ТОЛЬКО на сетевые сбои (контент не ретраим)."""
    data = json.dumps(payload).encode("utf-8")
    last_err = None
    for attempt in range(1, retries + 1):
        try:
            req = urllib.request.Request(
                url, data=data,
                headers={"Content-Type": "application/json"})
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
            questions_file: str | None = None, lang: str = "ru") -> None:
    if protocol == "chat":
        print(f"  выгружаю модель перед прогоном...")
        unload_model(base_url, model, timeout)

    turns: list = []
    session = {
        "model": model,
        "run": run_no,
        "started_at": datetime.now(timezone.utc).isoformat(),
        "base_url": base_url,
        "protocol": protocol,
        "keep_alive": keep_alive if protocol == "chat" else None,
        "kv_cache": ("weights-only (клетки независимы, префиксы не шарятся)"
                     if protocol == "chat" else None),
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
        if qtype == "answer":
            prompt = q["prompt"] + a_suf
            resp = chat_safe(base_url, model, prompt, keep_alive, think,
                             timeout, sysp)
        else:
            prompt = q["text"] + q_suf
            resp = chat_safe(base_url, model, prompt, keep_alive, think,
                             timeout, sysp)
        duration = time.monotonic() - t0
        p = None
        thinking = None
        expected = None
        if resp is None:
            answer_raw, answer, parsed = "", None, "none"
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
                # эталоны: php-вывод или все варианты accept-списка
                variants = [normalize(v) for v in q["grader"].get("accept", [expected])]
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
                  f"'{given}' (эталон: '{expected}'){warn}")
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
    if not args.model:
        p.error("укажите --model (или --regrade MODEL_DIR)")

    _qdata = json.loads(Path(args.questions).read_text(encoding="utf-8"))
    q_version = _qdata.get("version") if isinstance(_qdata, dict) else None
    questions = _qdata["questions"] if isinstance(_qdata, dict) else _qdata
    check_questionnaire(questions)

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
                args.protocol, Path(args.questions).name, args.lang)
        run_no += 1
        if args.protocol == "decision":
            continue  # decision-сервис не держит модель в ollama — выгружать нечего
        # выгрузка после КАЖДОГО прогона: изоляция прогонов
        try:
            unload_model(args.base_url, args.model, args.timeout)
            print("  модель выгружена (изоляция прогонов)")
        except Exception as e:
            print(f"  не удалось выгрузить модель: {e}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())

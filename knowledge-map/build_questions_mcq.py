#!/usr/bin/env python3
"""MCQ-варианты recall-клеток для decision-моделей (clef).

noul меряет верность утверждения — «какое значение» напрямую не спросить.
Формат: вопрос + 4–5 вариантов ответа; для каждого варианта — отдельный
noul-запрос «правильный ответ — вариант N»; выбор модели = argmax.
Правильный вариант всегда равен машинному эталону клетки (php-вывод или
accept[0]); дистракторы — детерминированные правдоподобные неверные
значения по типу эталона (соседние версии, ошибки-исключения, перестановки
списков, трансформации строк), без случайности.

Вывод: questions-mcq{,-en,-zh}.json (65 recall базы + 56 deep = 121).
Запуск: python3 knowledge-map/build_questions_mcq.py
"""
import base64
import json
import re
import subprocess
import sys
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent

EXC_POOL = ["TypeError", "ValueError", "RuntimeException", "LogicException",
            "ParseError", "DivisionByZeroError", "DomainException", "Error"]
FN_POOL = ["array_keys", "array_values", "str_repeat", "substr_count",
           "json_encode", "password_verify", "str_contains", "intdiv"]
BOOL_POOL = {
    "ru": ["да", "нет", "ошибка", "null", "0"],
    "en": ["yes", "no", "error", "null", "0"],
    "zh": ["是", "否", "错误", "null", "0"],
}


def php_expected(grader: dict, cache: dict) -> str:
    if "php" in grader:
        key = grader["php"]
        if key not in cache:
            r = subprocess.run(["php", "-r", key], capture_output=True,
                               text=True, timeout=30)
            cache[key] = r.stdout.strip()
        return cache[key]
    return grader["accept"][0]


def transforms(s: str) -> list:
    """Строковые трансформации эталона — правдоподобные «почти ответы»."""
    out = []
    if len(s) > 1:
        out.append(s[::-1])                     # реверс
        out.append(s[:-1])                      # без последнего символа
    if s and s[-1].isdigit():
        out.append(s[:-1] + str((int(s[-1]) + 1) % 10))
    if s.isalpha():
        out.append(s.upper())
        out.append(s.capitalize())
    if "," in s:                                # список → перестановки/обрезки
        parts = s.split(",")
        out.append(",".join(reversed(parts)))
        out.append(parts[0])
        out.append(",".join(parts[:-1]))
        out.append(str(sum(int(p) for p in parts if p.lstrip("-").isdigit())))
    return [x for x in out if x and x != s]


def version_pool(v: str) -> list:
    nums = re.findall(r"\d+", v)
    if not nums:
        return []
    major, minor = int(nums[0]), int(nums[-1])
    cands = [f"{major}.{minor - 1}", f"{major}.{minor - 2}",
             f"{major}.{minor + 1}", f"{major - 1}.{minor}"]
    return [c for c in cands if c != v and int(c.split(".")[-1]) >= 0]


def base64_pool(v: str) -> list:
    seeds = ["a:", "ab", "a=b", "a b", "b:"]
    return [base64.b64encode(x.encode()).decode() for x in seeds
            if base64.b64encode(x.encode()).decode() != v]


def numeric_pool(v: str) -> list:
    try:
        n = int(v)
    except ValueError:
        return []
    cands = {n + 1, n - 1, n * 2, n // 2 if n else n + 10}
    return [str(c) for c in sorted(cands) if c != n]


def distractors(expected: str, lang: str, code: str = "") -> list:
    """4 дистрактора по типу эталона; детерминированно, без expected."""
    e = expected
    pool: list = []
    if e in BOOL_POOL[lang] or e in ("true", "false", "yes", "no"):
        pool += [x for x in BOOL_POOL[lang] if x != e]
    if re.fullmatch(r"-?\d+\.\d+|\d+", e):
        pool += numeric_pool(e)
        if "." in e:
            pool += numeric_pool(e.split(".")[0])
    if re.fullmatch(r"\d+\.\d+", e):
        pool += version_pool(e)
    if re.fullmatch(r"[A-Za-z0-9+/=]+", e) and len(e) >= 4 and e.endswith("="):
        pool += base64_pool(e)
    if any(x in e for x in ("Error", "Exception")):
        pool += [x for x in EXC_POOL if x != e]
    if re.fullmatch(r"[a-z_][a-z0-9_]*", e) and "_" in e:
        pool += [x for x in FN_POOL if x != e]
    # литералы из кода клетки — «ответы из сниппета»
    for lit in re.findall(r"'([^']{1,24})'|\"([^\"\\n]{1,24})\"", code):
        x = lit[0] or lit[1]
        if x and x != e:
            pool.append(x)
    pool += transforms(e)
    # фолбэк — нейтральные неверные
    pool += [x for x in BOOL_POOL[lang] if x != e]
    seen, out = {e}, []
    for x in pool:
        if x and x not in seen:
            seen.add(x)
            out.append(x)
        if len(out) == 4:
            break
    return out


def mcq_for_cell(q: dict, expected: str, lang: str, cache: dict) -> dict:
    code = ""
    m = re.search(r"```(?:php|sql|twig|bash)\n(.*?)\n```", q.get("prompt", ""),
                  re.S)
    if m:
        code = m.group(1)
    opts = [expected] + distractors(expected, lang, code)
    # детерминированный «перемешанный» порядок: сдвиг по длине id
    shift = sum(ord(c) for c in q["id"]) % len(opts)
    opts = opts[shift:] + opts[:shift]
    return {"id": q["id"] + "-mc", "topic": q["topic"], "level": q["level"],
            "kind": ("deep" if q.get("level", 0) >= 9 else None),
            "type": "answer",
            "prompt": q.get("prompt") or q.get("text", ""),
            "options": opts, "correct_index": opts.index(expected),
            "expected": expected}


FRAMES = {
    "ru": ("Вопрос с вариантами ответа.\n\n{q}\n\nВарианты:\n{opts}\n\n"
           "УТВЕРЖДЕНИЕ:\nПравильный ответ — вариант {n} ({val})."),
    "en": ("Multiple-choice question.\n\n{q}\n\nOptions:\n{opts}\n\n"
           "STATEMENT:\nThe correct answer is option {n} ({val})."),
    "zh": ("选择题。\n\n{q}\n\n选项：\n{opts}\n\n"
           "断言：\n正确答案是选项 {n}（{val}）。"),
}


def mcq_state(cell: dict, opt_idx: int, lang: str) -> str:
    opts = "\n".join(f"{i + 1}) {o}" for i, o in enumerate(cell["options"]))
    return FRAMES[lang].format(q=cell["question"], opts=opts,
                               n=opt_idx + 1, val=cell["options"][opt_idx])


def main() -> int:
    cache: dict = {}
    for lang, suffix in (("ru", ""), ("en", "-en"), ("zh", "-zh")):
        base = json.loads((SCRIPT_DIR / f"questions{suffix}.json")
                          .read_text(encoding="utf-8"))
        deep_p = SCRIPT_DIR / f"questions-deep{suffix}.json"
        deep = json.loads(deep_p.read_text(encoding="utf-8")) \
            if deep_p.exists() else {"questions": []}
        cells = []
        for q in base["questions"] + deep["questions"]:
            if q.get("type") != "answer":       # только recall
                continue
            expected = php_expected(q["grader"], cache)
            if not expected:
                raise SystemExit(f"{q['id']}: пустой эталон")
            cells.append(mcq_for_cell(q, expected, lang, cache))
        out = SCRIPT_DIR / f"questions-mcq{suffix}.json"
        out.write_text(json.dumps(
            {"version": 1, "mc_for": "v8", "lang": lang,
             "cells": cells}, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8")
        print(f"{out.name}: {len(cells)} клеток "
              f"(вариантов 5: {sum(1 for c in cells if len(c['options']) == 5)}, "
              f"4: {sum(1 for c in cells if len(c['options']) == 4)})")
    return 0


if __name__ == "__main__":
    sys.exit(main())

#!/usr/bin/env python3
"""Генератор вопросников зоны «Глубина»: deep_cells.py →
questions-deep{,-en,-zh}.json (по 52 клетки, 26 тем × L9/L10).

Имена тем берутся из основных вопросников (questions{,-en,-zh}.json) по
префиксу id — локализации всегда согласованы. Запуск:
python3 knowledge-map/build_deep_cells.py
"""
import json
import sys
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR))
from deep_cells import CELLS  # noqa: E402

FRAME = {
    "ru": "Что выведет этот код?",
    "en": "What does this code output?",
    "zh": "这段代码会输出什么？",
}
TWIG_FRAME = {"ru": "Что выведет этот шаблон Twig?",
              "en": "What does this Twig template output?",
              "zh": "这个 Twig 模板会输出什么？"}
SQL_FRAME = {"ru": "Что вернёт этот SQL-запрос (PostgreSQL 17)?",
             "en": "What does this SQL query return (PostgreSQL 17)?",
             "zh": "这个 SQL 查询返回什么（PostgreSQL 17）？"}
PSQL_FRAME = {"ru": "Что выведет эта последовательность команд psql?",
              "en": "What does this sequence of psql commands output?",
              "zh": "这组 psql 命令会输出什么？"}
CURL_FRAME = {"ru": "Какой HTTP-метод отправит эта команда?",
              "en": "Which HTTP method does this command send?",
              "zh": "这条命令发送哪个 HTTP 方法？"}
TESTS_FRAME = {"ru": "Сколько тестов выполнит эта команда?",
               "en": "How many tests will this command run?",
               "zh": "这条命令会执行多少个测试？"}
DOCTRINE_CTX = {
    "ru": ("Дано: Doctrine ORM 3, EntityManager на SQLite in-memory, "
           "entity User с автоинкрементным первичным ключом. Таблица "
           "users пуста. "),
    "en": ("Given: Doctrine ORM 3, an EntityManager on in-memory SQLite, "
           "entity User with an auto-increment primary key. The users "
           "table is empty. "),
    "zh": ("给定：Doctrine ORM 3，EntityManager 使用内存 SQLite，实体 User "
           "带自增主键。users 表为空。"),
}


def build_prompt(c: dict, lang: str) -> str:
    if "prompt" in c:
        return c["prompt"][lang].format(code=c.get("code", ""))
    if c.get("lang") == "twig":
        return f"{TWIG_FRAME[lang]}\n\n```twig\n{c['code']}\n```"
    if "sql" in c:
        frame = SQL_FRAME[lang] if c["id"] != "T17-L10" else PSQL_FRAME[lang]
        return f"{frame}\n\n```sql\n{c['sql']}\n```"
    if "cmd" in c and isinstance(c["cmd"], str):  # curl
        return f"{CURL_FRAME[lang]}\n\n```bash\n{c['cmd']}\n```"
    if "cmd" in c:  # phpunit: код + команда
        return (f"{TESTS_FRAME[lang]}\n\n```php\n{c['code']}\n```\n\n"
                f"{c['cmd'][lang]}")
    code_frame = FRAME[lang]
    ctx = DOCTRINE_CTX[lang] if c.get("ctx") == "doctrine" else ""
    return f"{ctx}{code_frame}\n\n```php\n{c['code']}\n```"


def main() -> int:
    topics: dict[str, dict[str, str]] = {}
    for lang, suffix in (("ru", ""), ("en", "-en"), ("zh", "-zh")):
        data = json.loads(
            (SCRIPT_DIR / f"questions{suffix}.json").read_text(encoding="utf-8"))
        topics[lang] = {}
        for q in data["questions"]:
            prefix = q["id"].split("-L")[0]
            topics[lang].setdefault(prefix, q["topic"])

    for lang, suffix in (("ru", ""), ("en", "-en"), ("zh", "-zh")):
        questions = []
        for c in CELLS:
            prefix = c["id"].split("-L")[0]
            if prefix not in topics[lang]:
                raise SystemExit(f"{c['id']}: нет темы в questions{suffix}.json")
            questions.append({
                "id": c["id"],
                "topic": topics[lang][prefix],
                "level": c["level"],
                "kind": "deep",
                "zone": "base",
                "type": "answer",
                "note": c["note"][lang],
                "prompt": build_prompt(c, lang),
                "grader": c.get("grader") or {"php": c["code"]},
            })
        out = SCRIPT_DIR / f"questions-deep{suffix}.json"
        out.write_text(json.dumps(
            {"version": 1, "deep_for": 8, "questions": questions},
            ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
        print(f"{out.name}: {len(questions)} клеток")
    return 0


if __name__ == "__main__":
    sys.exit(main())

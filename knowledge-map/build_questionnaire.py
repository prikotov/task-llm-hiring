#!/usr/bin/env python3
"""Страница-просмотр вопросника «карты знаний PHP»: темы × уровни,
все клетки с эталонами и карточками (note) — для ревью вопросника
владельцем. Три языка: ru (раскрыт), en, zh (свёрнуты). Запуск:
python3 knowledge-map/build_questionnaire.py [--out knowledge-map/questionnaire.html]"""
import argparse
import json
import sys
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent
LANGS = (("ru", "Русский", True), ("en", "English", False), ("zh", "中文", False))
TYPE_LABEL = {"yesno": "да/нет", "answer": "recall"}


def esc(s: str) -> str:
    return (str(s).replace("&", "&amp;").replace("<", "&lt;")
            .replace(">", "&gt;").replace('"', "&quot;"))


def expected_text(q: dict) -> str:
    g = q.get("grader")
    if g:
        if "php" in g:
            return f"php: <code>{esc(g['php'])}</code>"
        if "regex" in g:
            return (f"{esc(g['accept'][0])} "
                    f"<code class=\"re\">{esc(g['regex'])}</code>")
        return " / ".join(esc(a) for a in g["accept"])
    return "✓ (верно)" if q.get("answer") else "✗ (миф)"


def lang_section(lang: str, title: str, data: dict, open_: bool) -> str:
    topics: list[str] = []
    for q in data["questions"]:
        if q["topic"] not in topics:
            topics.append(q["topic"])
    rows = []
    for t in topics:
        cells = [q for q in data["questions"] if q["topic"] == t]
        zone = cells[0].get("zone", "base")
        zone_label = {"base": "", "current": " · зона: PHP 8.4–8.5",
                      "future": " · зона: будущее 8.6+"}[zone]
        n_yes = sum(1 for q in cells if q["type"] == "yesno" and q["answer"])
        n_no = sum(1 for q in cells if q["type"] == "yesno" and not q["answer"])
        n_rec = sum(1 for q in cells if q["type"] == "answer")
        rows.append(
            f'<h3>{esc(t)}<small>{zone_label} · {len(cells)} клеток: '
            f'{n_yes}✓ {n_no}✗ {n_rec}recall</small></h3><table class="data">'
            '<tr><th>Клетка</th><th>Тип</th><th>Вопрос / утверждение</th>'
            '<th>Эталон</th><th>Карточка (note)</th></tr>')
        for q in cells:
            text = q.get("text") or q.get("prompt")
            rows.append(
                f'<tr id="{lang}-{esc(q["id"])}"><td><code>{esc(q["id"])}</code></td>'
                f'<td>{TYPE_LABEL[q["type"]]}</td>'
                f'<td class="q">{esc(text)}</td>'
                f'<td class="exp">{expected_text(q)}</td>'
                f'<td class="note">{esc(q.get("note", ""))}</td></tr>')
        rows.append("</table>")
    v = data["version"]
    details = " open" if open_ else ""
    return (f'<details{details}><summary>{esc(title)} — вопросник v{v}, '
            f'{len(data["questions"])} клеток</summary>\n{"".join(rows)}\n</details>')


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--out", default=str(SCRIPT_DIR / "questionnaire.html"))
    args = p.parse_args()
    langs = {}
    for lang, _title, _open in LANGS:
        langs[lang] = json.loads(
            (SCRIPT_DIR / f"questions{'' if lang == 'ru' else '-' + lang}.json")
            .read_text(encoding="utf-8"))
    versions = {d["version"] for d in langs.values()}
    if len(versions) != 1:
        raise SystemExit(f"версии вопросников расходятся: {versions}")
    n = len(langs["ru"]["questions"])
    sections = "".join(
        lang_section(lang, title, langs[lang], open_)
        for lang, title, open_ in LANGS)
    html = f"""<!doctype html>
<html lang="ru">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вопросник v{next(iter(versions))} — темы и вопросы</title>
<style>
body {{ font: 15px/1.5 system-ui, sans-serif; color: #1c2130;
       background: #f6f7f9; margin: 0; padding: 28px 18px 60px; }}
main {{ max-width: 1200px; margin: 0 auto; }}
h1 {{ font-size: 24px; margin: 0 0 4px; }}
.proto {{ color: #6b7280; margin: 0 0 16px; }}
details {{ background: #fff; border: 1px solid #e3e6ea; border-radius: 12px;
           padding: 12px 16px 16px; margin: 12px 0; }}
summary {{ font-weight: 600; cursor: pointer; padding: 6px 0; }}
h3 {{ font-size: 16px; margin: 18px 0 6px; }}
h3 small {{ color: #6b7280; font-weight: 400; font-size: 12.5px;
            margin-left: 8px; }}
table.data {{ border-collapse: collapse; width: 100%; font-size: 14px; }}
table.data th, table.data td {{ border: 1px solid #e3e6ea;
                                 padding: 6px 8px; text-align: left;
                                 vertical-align: top; }}
table.data th {{ background: #eef1f5; font-size: 12.5px; }}
code {{ background: #f1f3f6; border-radius: 4px; padding: 1px 5px;
        font-size: 12.5px; }}
code.re {{ color: #8a2d2d; }}
td.q {{ width: 42%; }} td.exp {{ width: 18%; }} td.note {{ width: 30%;
        color: #4b5563; font-size: 13px; }}
tr:target td {{ background: #fffbe8; }}
footer {{ color: #6b7280; font-size: 12.5px; margin-top: 18px; }}
</style>
<main>
<h1>Вопросник v{next(iter(versions))} — темы и вопросы</h1>
<p class="proto">{n} клеток: 21 тема × 8 уровней (12 тем языка PHP + 9 тем
стека TasK) + 16 версионных (PHP 8.4–8.5 и будущее 8.6+). Типы: да/нет
(утверждение, эталон — правда/миф) и recall (ответ — только результат,
эталон — php-грейдер или accept/regex-список). Эталон и карточка (note)
приведены для ревью; языки ru/en/zh — параллельные версии одного набора,
эталоны эквивалентны.</p>
{sections}
<footer>построено knowledge-map/build_questionnaire.py · сгенерировано из
questions{{,-en,-zh}}.json; перегенерация после правок вопросника</footer>
</main>
</html>"""
    out = Path(args.out)
    out.write_text(html, encoding="utf-8")
    print(f"просмотр вопросника: {out}")


if __name__ == "__main__":
    sys.exit(main())

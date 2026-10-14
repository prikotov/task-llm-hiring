# Судейство ornith-1.5-9b (run-01, think on) — резюме

Сырые данные трёх судей: **[judges-run-01.csv](ornith-1.5-9b/backend/judgments/judges-run-01.csv)** (41 строк).

| Судья | Средний балл | Галлюцинаций помечено |
|---|---|---|
| clef-flash | 3.45 | p>0.5: 0 |
| clef | 3.17 | p>0.5: 12 |
| GLM-Flash (эталон) | 2.72 | 21 |

Колонки CSV: `question`; clef-flash: `cf_score`, `cf_conf` (confidence
score-вопроса), `cf_hall_p` (вероятность галлюцинации, noul-вопрос);
clef — те же три поля `clef_*`; GLM-Flash: `glm_score`,
`glm_hallucination` (true/false), `glm_note` (текстовое пояснение,
что именно не так / выдумано). Всё сырое, без обработки.

Полные JSON с служебными полями — в подпапках `clef-flash/`, `clef/`,
`glm-5.3-flash-iso/` (файл `run-01.json`).
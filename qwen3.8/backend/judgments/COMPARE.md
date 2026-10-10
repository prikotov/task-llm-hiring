# Судейство qwen3.8 (run-06, think off (валидная серия)) — резюме

Сырые данные трёх судей: **[judges-run-06.csv](qwen3.8/backend/judgments/judges-run-06.csv)** (38 строк).

| Судья | Средний балл | Галлюцинаций помечено |
|---|---|---|
| clef-flash | 3.43 | p>0.5: 0 |
| clef | 3.33 | p>0.5: 3 |
| GLM-Flash (эталон) | 3.26 | 12 |

Колонки CSV: `question`; clef-flash: `cf_score`, `cf_conf` (confidence
score-вопроса), `cf_hall_p` (вероятность галлюцинации, noul-вопрос);
clef — те же три поля `clef_*`; GLM-Flash: `glm_score`,
`glm_hallucination` (true/false), `glm_note` (текстовое пояснение,
что именно не так / выдумано). Всё сырое, без обработки.

Полные JSON с служебными полями — в подпапках `clef-flash/`, `clef/`,
`glm-5.3-flash-iso/` (файл `run-06.json`).
# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 24.4с, 40,464 ток.)

- **Ось A (судья яруса 4): 3.5**
  - цитата: `Порядок: (1) базовая стоимость из rates × tokens; (2) off-peak-множитель к базовой стоимости; (3) **единственное** округление — `roundHalfUp6``
  - цитата: `final class Money { public const SCALE = 16; ... float is never used.`
  - цитата: `The window is [start, end); it may cross midnight (e.g. 16:30–00:30).`
  - цитата: `INSERT ... ON CONFLICT (provider, provider_request_id) DO NOTHING`
  - цитата: `UNRATED %s/%s %s no_price`
- заметка: Сильное решение: чистая доменная логика (Money на bcmath scale 16, OffPeak с границами и переходом через полночь, EventRater с единственным HALF_UP-округлением) отделена от импорта и отчёта; идемпотентность на уникальных индексах и ON CONFLICT; edge-cases (границы окна, unrated, дубли, ZERO-токены) покрыты кодом и 32 тестами; бонус reprice сделан аккуратно с price_row_id. Смущают мелочи: агрегация отчёта гоняет все события через ORM в PHP вместо SQL-агрегата, SQL upsert продублирован в Repricer'е, decimal() парсера ограничивает 6 знаками после точки не по ТЗ (в ТЗ — 6 у rates, но здесь и multiplier) — всё это не ломает корректность, поэтому не 4.

<details><summary>сырой ответ</summary>

```
{"axis_a": 3.5, "quotes": ["Порядок: (1) базовая стоимость из rates × tokens; (2) off-peak-множитель к базовой стоимости; (3) **единственное** округление — `roundHalfUp6`", "final class Money { public const SCALE = 16; ... float is never used.", "The window is [start, end); it may cross midnight (e.g. 16:30–00:30).", "INSERT ... ON CONFLICT (provider, provider_request_id) DO NOTHING", "UNRATED %s/%s %s no_price"], "note": "Сильное решение: чистая доменная логика (Money на bcmath scale 16, OffPeak с границами и переходом через полночь, EventRater с единственным HALF_UP-округлением) отделена от импорта и отчёта; идемпотентность на уникальных индексах и ON CONFLICT; edge-cases (границы окна, unrated, дубли, ZERO-токены) покрыты кодом и 32 тестами; бонус reprice сделан аккуратно с price_row_id. Смущают мелочи: агрегация отчёта гоняет все события через ORM в PHP вместо SQL-агрегата, SQL upsert продублирован в Repricer'е, decimal() парсера ограничивает 6 знаками после точки не по ТЗ (в ТЗ — 6 у rates, но здесь и multiplier) — всё это не ломает корректность, поэтому не 4."}
```

</details>

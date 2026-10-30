# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 16.0с, 24,809 ток.)

- **Ось A (судья яруса 4): 3**
  - цитата: ``UNIQUE(provider_code, provider_request_id)` + `ON CONFLICT DO NOTHING`. Повторная доставка — no-op, без обновлений`
  - цитата: `// Crosses midnight: [start, 24:00) ∪ [00:00, end)`
  - цитата: `Хранение денег — как строки VARCHAR(30)... Все вычисления — в PHP через bcmath (масштаб 20)`
  - цитата: `SELECT ... WHERE provider_code = ? AND model_code = ? AND valid_from <= ?::timestamptz ORDER BY valid_from DESC LIMIT 1`
- заметка: Корректная тарификация: bcmath (без float), единственное округление HALF_UP до 6 знаков на событии, выбор цены valid_from <= ts, оф-пик-окно через полночь в UTC, дедупликация естественным ключом, unrated повторяются при следующих запусках; бонус reprice реализован с хранением price_valid_from. Слабости: домен тонкий (Money — статические утилиты, вся логика в SQL-сервисах), ORM из требований заменён на DBAL (обосновано в PR_DESCRIPTION, но формально отступление), computeBaseCost дублирован в BillingEngine и RepriceEngine, тестов нет, честный bcmath-HALF_UP написан вручную вместо готовых механизмов.

<details><summary>сырой ответ</summary>

```
{"axis_a": 3, "quotes": ["`UNIQUE(provider_code, provider_request_id)` + `ON CONFLICT DO NOTHING`. Повторная доставка — no-op, без обновлений", "// Crosses midnight: [start, 24:00) ∪ [00:00, end)", "Хранение денег — как строки VARCHAR(30)... Все вычисления — в PHP через bcmath (масштаб 20)", "SELECT ... WHERE provider_code = ? AND model_code = ? AND valid_from <= ?::timestamptz ORDER BY valid_from DESC LIMIT 1"], "note": "Корректная тарификация: bcmath (без float), единственное округление HALF_UP до 6 знаков на событии, выбор цены valid_from <= ts, оф-пик-окно через полночь в UTC, дедупликация естественным ключом, unrated повторяются при следующих запусках; бонус reprice реализован с хранением price_valid_from. Слабости: домен тонкий (Money — статические утилиты, вся логика в SQL-сервисах), ORM из требований заменён на DBAL (обосновано в PR_DESCRIPTION, но формально отступление), computeBaseCost дублирован в BillingEngine и RepriceEngine, тестов нет, честный bcmath-HALF_UP написан вручную вместо готовых механизмов."}
```

</details>

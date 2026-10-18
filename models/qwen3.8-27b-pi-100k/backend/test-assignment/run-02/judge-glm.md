# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 16.0с, 42,204 ток.)

- **Ось A (судья яруса 4): 4**
  - цитата: `Единственное округление — итоговой стоимости события до 6 знаков, HALF_UP. Окружение: сначала применяется множитель оф-пика, затем одно финальное округление`
  - цитата: `Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)`
  - цитата: `CREATE UNIQUE INDEX uq_usage_events_provider_request ON usage_events (provider_id, provider_request_id)`
  - цитата: `SELECT p FROM PriceRow p WHERE p.model = :model AND p.validFrom <= :ts ORDER BY p.validFrom DESC`
  - цитата: `public const SCALE = 30; // Запас точности для промежуточных вычислений (требование: ≥ 16)`
- заметка: Полноценная доменная модель: тарификация (PricingCalculator, OffPeakWindow) отделена от приёма (UsageImportService) и отчёта (ReportService), все деньги на bcmath (SCALE=30) без float, единственное округление HALF_UP на уровне события. Edge-cases покрыты явно: оф-пик через полночь, пустое окно при равных границах, включительный valid_from, дедупликация по (provider, provider_request_id) на уровне уникального индекса, unrated с повторными попытками — плюс 33 юнит-теста на границы. Реализован бонус reprice с журналом корректировок. Мелкие шероховатости: отчёт агрегирует все события в памяти PHP (не SQL-агрегация), дубли rate/multiplier при выборке цены и проверка ограничений вместо NOT NULL в сущности UsageEvent — на качество домена не влияют.

<details><summary>сырой ответ</summary>

```
{"axis_a": 4, "quotes": ["Единственное округление — итоговой стоимости события до 6 знаков, HALF_UP. Окружение: сначала применяется множитель оф-пика, затем одно финальное округление", "Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)", "CREATE UNIQUE INDEX uq_usage_events_provider_request ON usage_events (provider_id, provider_request_id)", "SELECT p FROM PriceRow p WHERE p.model = :model AND p.validFrom <= :ts ORDER BY p.validFrom DESC", "public const SCALE = 30; // Запас точности для промежуточных вычислений (требование: ≥ 16)"], "note": "Полноценная доменная модель: тарификация (PricingCalculator, OffPeakWindow) отделена от приёма (UsageImportService) и отчёта (ReportService), все деньги на bcmath (SCALE=30) без float, единственное округление HALF_UP на уровне события. Edge-cases покрыты явно: оф-пик через полночь, пустое окно при равных границах, включительный valid_from, дедупликация по (provider, provider_request_id) на уровне уникального индекса, unrated с повторными попытками — плюс 33 юнит-теста на границы. Реализован бонус reprice с журналом корректировок. Мелкие шероховатости: отчёт агрегирует все события в памяти PHP (не SQL-агрегация), дубли rate/multiplier при выборке цены и проверка ограничений вместо NOT NULL в сущности UsageEvent — на качество домена не влияют."}
```

</details>

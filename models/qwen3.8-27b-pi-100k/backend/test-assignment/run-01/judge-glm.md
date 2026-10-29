# Ярус 4: GLM-5.3-Flash (pi, thinking=low, 17.2с, 50,462 ток.)

- **Ось A (судья яруса 4): 3.5**
  - цитата: `Округление ровно один раз — итоговая стоимость события, HALF_UP до 6 знаков`
  - цитата: `final class Money { public const SCALE = 30; ... roundHalfUp`
  - цитата: `return $minuteOfDayUtc >= $this->startMinute || $minuteOfDayUtc < $this->endMinute;`
  - цитата: `CREATE UNIQUE INDEX uq_usage_events_provider_request ON usage_events (provider_code, provider_request_id)`
  - цитата: `return $this->createQueryBuilder('p') ... ->andWhere('p.validFrom <= :ts') ... ->orderBy('p.validFrom', 'DESC')->setMaxResults(1)`
- заметка: Сильное решение: чистый домен (Money на bcmath, CostCalculator, OffPeakWindow) отделён от приёма/отчёта, единое HALF_UP-округление события, суммы отчёта из уже округлённых стоимостей, UTC-строгость (IsoTime с суффиксом Z, минута суток UTC), окно через полночь и границы [start, end) покрыты тестами; дедуп по (provider, provider_request_id) и upsert цен по (provider, model, valid_from) через уникальные индексы; бонус reprice с аудитом и идемпотентным повтором. Слабости: RatingRunner/RepriceRunner в Domain завязаны на EntityManager и репозитории (граница слоя условна); в закоммиченном .phpunit.result.cache есть defects по MoneyTest (след неудавшегося прогона в диффе); roundHalfUp через floor-трюк работает, но только для неотрицательных — допущение задокументировано, тесты это подтверждают.

<details><summary>сырой ответ</summary>

```
{"axis_a": 3.5, "quotes": ["Округление ровно один раз — итоговая стоимость события, HALF_UP до 6 знаков", "final class Money { public const SCALE = 30; ... roundHalfUp", "return $minuteOfDayUtc >= $this->startMinute || $minuteOfDayUtc < $this->endMinute;", "CREATE UNIQUE INDEX uq_usage_events_provider_request ON usage_events (provider_code, provider_request_id)", "return $this->createQueryBuilder('p') ... ->andWhere('p.validFrom <= :ts') ... ->orderBy('p.validFrom', 'DESC')->setMaxResults(1)"], "note": "Сильное решение: чистый домен (Money на bcmath, CostCalculator, OffPeakWindow) отделён от приёма/отчёта, единое HALF_UP-округление события, суммы отчёта из уже округлённых стоимостей, UTC-строгость (IsoTime с суффиксом Z, минута суток UTC), окно через полночь и границы [start, end) покрыты тестами; дедуп по (provider, provider_request_id) и upsert цен по (provider, model, valid_from) через уникальные индексы; бонус reprice с аудитом и идемпотентным повтором. Слабости: RatingRunner/RepriceRunner в Domain завязаны на EntityManager и репозитории (граница слоя условна); в закоммиченном .phpunit.result.cache есть defects по MoneyTest (след неудавшегося прогона в диффе); roundHalfUp через floor-трюк работает, но только для неотрицательных — допущение задокументировано, тесты это подтверждают."}
```

</details>

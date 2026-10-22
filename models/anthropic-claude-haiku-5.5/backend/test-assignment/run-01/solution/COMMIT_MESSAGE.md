Учёт расходов на LLM-токены: импорт цен и событий, тарификация, отчёт, ретро-коррекция

- Доменная модель (src/Billing/Domain): провайдер с оф-пик окном, модель,
  строка цен (valid_from, схемы per_1m_tokens и per_1m_tokens_unified),
  событие использования с естественным ключом (provider, provider_request_id).
- Денежная арифметика только через bcmath (масштаб 30); единственное
  округление — стоимость события, HALF_UP до 6 знаков.
- Команды: app:prices:import, app:usage:import, app:billing:rate,
  app:billing:report, app:billing:reprice (бонус).
- Идемпотентный импорт: upsert справочника; дубли событий игнорируются.
  Изменение уже загруженной строки цен через импорт запрещено — только reprice.
- Ретро-коррекция: пересчёт затронутых событий с журналом CostAdjustment,
  без задвоения сумм в отчёте.
- Миграция схемы: migrations/Version20261022043507.php (doctrine:migrations:diff).
- Unit-тесты расчёта и оф-пик окна (tests/Billing/ChargeTest.php).

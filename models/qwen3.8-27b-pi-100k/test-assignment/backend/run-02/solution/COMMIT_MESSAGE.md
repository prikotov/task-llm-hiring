feat(billing): расчёт расходов на LLM-провайдеров по токенам

Реализован полный цикл биллинга: импорт справочника цен и событий
использования, тарификация с учётом временной истории тарифов,
схем per_1m_tokens / per_1m_tokens_unified, окна off_peak у каждого
провайдера и дубликатов событий; точные десятичные счисления (bcmath,
округление HALF_UP до 6 знаков, без float-представлений); CSV-отчёт
по периоду (user + provider/model, TOTAL, только тарифицированные
события) и корректировка цен с пересчётом затронутых событий
(app:billing:reprice) и дельтой в USD.

- Схема БД: providers, models, prices, usage_events
  (уникальные ключи: provider.code, (provider, model.code),
  (model, valid_from), (provider, provider_request_id)).
- Выбор цены: последняя строка с valid_from <= ts(события);
  valid_from включительно.
- off_peak: включённое начало / исключённый конец окна,
  пересечение полуночи поддерживается; мультипликатор на всю стоимость.
- События до появления цен остаются нетарифицированными и
  перетарифицируются повторным app:billing:rate.
- Команды: app:prices:import, app:usage:import, app:billing:rate,
  app:billing:report, app:billing:reprice.
- Юнит-тесты (PHPUnit 11): Money, Iso8601, OffPeakPolicy,
  PriceSelection, PricingCalculator, PricesFileParser.
- Приёмка с fixtures: отчёт совпадает байт-в-байт с примером из
  TASK.md (14.262499 / 8.400000 / 11.000000; TOTAL 33.662499);
  повторный импорт идемпотентен.

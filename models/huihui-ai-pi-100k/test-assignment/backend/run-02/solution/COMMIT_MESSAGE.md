Учёт расходов на LLM-токены: импорт цен и событий, тарификация, отчёт, ретро-коррекция

Консольное приложение (bin/console, Symfony Console + Doctrine ORM + PostgreSQL 17):

- app:prices:import — идемпотентная загрузка провайдеров/моделей/строк цен
  (upsert по code, (provider, code), (provider, model, valid_from));
- app:usage:import — приём JSONL-журнала событий, дубли по
  (provider, provider_request_id) игнорируются (уникальный индекс);
- app:billing:rate — тарификация нерассчитанных событий;
  цена — строка с наибольшим valid_from <= ts (valid_from включительно),
  схемы per_1m_tokens и per_1m_tokens_unified, оф-пиковая скидка по окну
  провайдера (UTC, [start; end), переход через полночь);
  деньги — только bcmath (масштаб 16), ровно одно округление HALF_UP до 6 знаков;
- app:billing:report --from --to — строгий CSV по (user, provider/model)
  с TOTAL; unrated-события не попадают в отчёт; stdout — только CSV;
- app:billing:reprice — ретро-коррекция строки цен: upsert по справочнику,
  пересчёт уже рассчитанных событий (стоимость перезаписывается, журнал
  reprice_log), stdout — ровно две строки REPRICED_EVENTS / REPRICE_DELTA_USD;
- unrated-события повторно тарифицируются при каждом запуске app:billing:rate;
- схема БД — Doctrine-миграции (идемпотентный DDL), разворачивается на чистой базе;
- тесты PHPUnit: домен (Money, OffPeakWindow, TokenUsage,
  PricingUnit, RateCalculator) и парсеры ввода — 55 тестов.

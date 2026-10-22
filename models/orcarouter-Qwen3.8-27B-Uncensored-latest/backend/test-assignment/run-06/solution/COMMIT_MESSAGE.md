billing: трекинг расходов на LLM-токены (цены, тарификация, отчёты, reprice)

Реализована консольная система учёта расходов на токены LLM на
PHP 8.4 / Symfony 7 / Doctrine ORM / PostgreSQL 17:

- 5 команд bin/console: app:prices:import, app:usage:import,
  app:billing:rate, app:billing:report, app:billing:reprice;
- цены: история ставок per_1m_tokens (input/output/cache_read/cache_write)
  и per_1m_tokens_unified (any), ставка действует с valid_from (включительно);
- оф-пик: окно провайдера (может переходить через полночь), множитель
  применяется к итоговой стоимости до окончательного округления;
- тарификация: выбор актуальной строки цены на момент ts события,
  unrated-события остаются в очереди и пробуются повторно;
- точная арифметика денег: bcmath, стоимость события — целые "микро-юниты"
  (1/10^6 USD), единственное округление HALF_UP до 6 знаков в конце;
- идемпотентность: upsert справочника по (provider),
  дубли usage по (provider, provider_request_id) игнорируются;
- отчёт: CSV строго в stdout, группы (user, provider/model), TOTAL,
  unrated-события никогда не входят в отчёт;
- retroactive reprice: обновление ставок из файла, пересчёт событий,
  выведенных по этой строке цены, с дельтой в USD;
- миграция схемы (provider, model, price_row, usage_events);
- unit-тесты домена (MoneyMath, OffPeakWindow, PricingEngine, IsoTime).

Примеры (fixtures/prices.json, fixtures/usage.jsonl) воспроизводятся
байт-в-байт; повторный импорт не меняет состояние.

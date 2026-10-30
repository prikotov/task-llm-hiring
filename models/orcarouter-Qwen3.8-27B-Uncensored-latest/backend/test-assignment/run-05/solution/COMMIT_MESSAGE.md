feat(billing): учёт стоимости LLM-событий по историческим ценам

- Доменная модель (DDD): Provider, Model, PriceRow, UsageEvent + CostCalculator;
  тарифный период (off-peak) — свойство провайдера, стоимость события — производная,
  хранящаяся в событии (cost_usd VARCHAR(32), строка точных десятичных).
- Деньги только через bcmath (App\Support\Money): все промежуточные суммы — без
  округления; событие округляется HALF-UP до 6 знаков (центы/миллион); отчёт
  суммирует именно окончательные значения событий (сверка с образцом — байт-в-байт).
- app:prices:import — JSON; провайдеры/модели создаются автоматически, ставки
  заменяются idempotently по (provider, model, unit, valid_from); валидационные
  ошибки → код выхода 1, сообщение в stderr, файл не применяется.
- app:usage:import — JSONL, идемпотентно по (provider, provider_request_id)
  (ON CONFLICT DO NOTHING); дубликаты молча пропускаются, exit 0;
  невалидная строка → exit 1 с номером строки, файл не применяется.
- app:billing:rate — пересчёт только UNRATED; выбор цены — строка с наибольшим
  valid_from ≤ ts события; ставка «любой единицы» (any) применяется к сумме всех
  токенов; off-peak — множитель, включение окна [start; end) с поддержкой переноса
  через полночь. UNRATED остаётся UNRATED со строкой вида
  `UNRATED <provider>/<provider_request_id> <ts> no_price` (stdout: RATED=<n> UNRATED=<m>).
- app:billing:report — строка на пару (user, model), отсортирована лексикографически,
  счётчики целые, cost_usd — 6 знаков, строка TOTAL, UTF-8, разделитель «,», перевод
  строки LF; фильтры --from / --to (включительно/исключительно).
- Бонус: app:billing:reprice — замена ставок по файлу цен с пересчётом событий,
  на которые они ссылаются (price_row_id), и выводом REPRICED_EVENTS /
  REPRICE_DELTA_USD.
- Схема БД — миграцией (make migrate, чистая БД); DBAL-тип timestamptz + сессия в
  UTC (конфиг doctrine/services расширены под требование timestamptz).
- Тесты: Money, окно off-peak, CostCalculator (16 тестов, vendor/bin/phpunit).
- Новых внешних зависимостей нет.

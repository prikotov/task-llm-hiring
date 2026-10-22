# Отчёт расходов на LLM-токены (учёт и тарификация)

Реализован весь основной функционал задания (TASK.md) плюс бонусный
командный интерфейс ретро-коррекции цен. PHP 8.5, Symfony Console,
Doctrine ORM/DBAL, PostgreSQL 17, точная десятичная арифметика на
bcmath (без float в деньгах).

## Что сделано

### Команды

| Команда | Что делает |
|---|---|
| `app:prices:import <файл.json>` | Идемпотентно загружает провайдеров, моделей и **историю** строк цен (по ключу `provider + model + valid_from` — upsert) из JSON. Строгая валидация: `providers`, `models`, `prices`; единицы `per_1m_tokens` (4 ставки) и `per_1m_tokens_unified` (ставка `any`); `off_peak` — окно `HH:MM` (UTC) × множитель (0, 1]. |
| `app:usage:import <файл.jsonl>` | Приём событий использования из JSONL. Дедупликация по `(provider, provider_request_id)` (уникальный индекс в БД + проверка до вставки): повторная доставка игнорируется, событие не перезаписывается (код 0 даже если все строки — дубли). Любой невалидный признак — ошибка всего файла, код 1 (транзакция откатывается). |
| `app:billing:rate` | Тарифицирует события в статусе `pending`: выбирает строку цен модели с **наибольшим** `valid_from <= ts` (`valid_from` включительно); нет строки — событие остаётся `pending` («unrated») и будет повторено в следующем запуске. Стоимость считается по жёсткой модели ставок (см. ниже); оф-пик провайдера применяется по минутам UTC-времени суток события (окно может пересекать полночь, граница `window_end` — исключается). Итог события — `HALF_UP`-округление **ровно один раз** до 6 знаков. Вывод: `RATED=<n> UNRATED=<m>`, детализация unrated — в stderr. |
| `app:billing:report --from … --to …` | CSV (UTF-8, `\n`) по парам `(user, model)` за период: `--from` включительно, `--to` исключительно. Столбцы: `user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd`. `cost_usd` — сумма стоимости уже округлённых событий (без повторных округлений), ровно 6 знаков. Строки отсортированы по `user`, затем по `model` (возрастание). Последняя строка — `TOTAL`. В stdout — только CSV; ошибки — в stderr, ненулевой код. |
| `app:billing:reprice <коррекция.json>` *(бонус)* | Ретро-коррекция: принимает тот же формат, что и импорт цен (секции `providers`/`models` **допустимы отсутствовать**, а `prices` — обязательна). Обновляет строки цен (upsert по `provider + model + valid_from`; провайдер и модель должны существовать в справочнике — секции `providers`/`models` в файле коррекции могут отсутствовать), затем **пересчитывает** все события, уже тарифицированные затронутыми строками (включая влияние оф-пика провайдера, если секция `providers` была изменена). Журналирует старую/новую стоимость по каждому событию в `price_corrections`. Отчёт сразу отразит новые суммы; событие учитывается один раз (без двойного начисления). В stdout — ровно две строки: `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`. |

### Точная арифметика денег

- Все операции — bcmath на строках: `Decimal::add/sub/mul/div`, общий масштаб 32 знака.
- Единственное округление стоимости события — `HALF_UP` до 6 знаков, один раз (`RateCalculator::eventCost`).
- Суммирование групп/`TOTAL` — точное `add` поверх уже округлённых значений событий; формат — ровно 6 знаков.
- Формат вывода нормализован: отрицательный ноль не выводится со знаком, «0» — как «0.000000».

### Схема БД (миграция `Version20261017082358.php`)

- `providers` (`code` — unique; опциональная оф-пик-политика: `off_peak_start_minutes`, `off_peak_end_minutes` — минуты UTC, `off_peak_multiplier`);
- `models` (`provider_id` + `code`, unique `uniq_models_provider_code`);
- `price_rows` — история цен: `valid_from timestamptz`, `currency`, `unit`, ставки `input_rate/output_rate/cache_read_rate/cache_write_rate/any_rate` (`numeric(20,10)`, NULL для неприменимых); unique `uniq_price_rows (provider_id, model_id, valid_from)` — идемпотентный upsert;
- `usage_events` — события: `provider_id/model_id`, `provider_request_id`, `user`, `ts timestamptz`, `tokens_*` (bigint), `status` (`pending`/`rated`), `cost numeric(20,6)` (NULL, пока unrated), `price_row_id` (строчка, по которой рассчитано), уникальность `uniq_usage_events_provider_request (provider_id, provider_request_id)` — дедупликация на уровне схемы;
- `price_corrections` — журнал ретро-коррекций: `usage_event_id`, `price_row_id`, `old_cost`, `new_cost`, `created_at`.

Столбец `usage_events.user` — резервируемое имя в PostgreSQL; добавленная `UserQuotedQuoteStrategy` цитирует его во всех генерируемых запросах.

### Домен (src/Domain)

- `Pricing` (Provider, Model, PriceRow, PriceUnit), `Usage` (UsageEvent, UsageStatus), `Billing` (TokenCounts, OffPeakPolicy, RateCalculator, PriceCorrection), `Shared` (Decimal, TimeOfDay).
- `RateCalculator::eventCost(tokens, row, policy, minuteOfDayUtc)` — чистая функция:
  - `per_1m_tokens`: `in*rateIn + out*rateOut + cacheRead*rateCacheRead + cacheWrite*rateCacheWrite` (каждая ставка обязательна, жёсткая модель ставок);
  - `per_1m_tokens_unified`: `(in+out+cacheRead+cacheWrite) * any`;
  - затем `× multiplier` (если оф-пик), затем `HALF_UP 6`.
- `PriceUnit` — enum, запрещает неизвестные единицы.

### Прикладной слой (src/Application)

- `Prices/PriceFileParser` — парсинг+валидация (секции, ставки, `valid_from` UTC ISO-8601, окно оф-пика).
- `Prices/PricesImportService` — транзакционный upsert справочника.
- `Usage/UsageFileParser`, `Usage/UsageImportService` — построчный JSONL, дедупликация через уникальный индекс (ошибка «duplicates ignored» — ожидаемый путь, не ошибка).
- `Billing/RatingService` — выбор строки цен, вызов калькулятора, `markRated`, повтор unrated.
- `Billing/ReportService` — агрегация по `(user, model)`, `TOTAL`, CSV.
- `Billing/RepriceService` — upsert строк, пересчёт затронутых событий, журналирование.
- `Shared/Decimal` — вся арифметика на бcmath, `normalize`, `format6`, `roundHalfUp`.

### Команды (src/Command)

`ImportPricesCommand`, `ImportUsageCommand`, `RateCommand`, `ReportCommand`, `RepriceCommand` — валидация аргументов, запуск в сервисе, вывод в stdout/stderr, коды возврата (0 — успех, 1 — предметная ошибка).

### Тесты (tests/)

- `DecimalTest` — `normalize`, `HALF_UP`, `format6`, точность операций (задача про «31249875…», 83333 × 3.75 / 1e6 → 0.312499).
- `OffPeakPolicyTest` — окно с пересечением полуночи, включённость границ, `HH:MM`-парсер (валидные/невалидные варианты).
- `RateCalculatorTest` — обе единицы, оф-пик в окне/вне/на границе, нулевые токены, HALF_UP.
- `PriceFileParserTest`, `UsageFileParserTest` — валидные и невалидные входы.

Итог: `76 tests, 106 assertions`, все зелёные.

## Воспроизведение приёмки

```bash
make db-up                     # PostgreSQL 17, база app
make migrate                   # schemaVersion → 20261017082358

php bin/console app:prices:import fixtures/prices.json
php bin/console app:usage:import fixtures/usage.jsonl
php bin/console app:billing:rate
php bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# → CSV сверяется байт-в-байт с эталонным (expected_report.csv); TOTAL=33.662499.
```

Повторный импорт цен/событий идемпотентен: `RATED=0 UNRATED=0`, отчёт идентичен.

## Отступления от каркаса / допущения

1. **Новые зависимости: нет.** Использованы только уже установленные (symfony/console, doctrine, phpunit).
2. **Изменён `config/packages/doctrine.yaml`** — добавлен `quote_strategy: App\Doctrine\UserQuotedQuoteStrategy` (новое значение в секции `orm.entity_managers.default`), чтобы резервируемое имя колонки `user` корректно квотировалось во всех сгенерированных SQL/DQL. Альтернатива — переименовать колонку или держать alias-обёртку; выбран вариант с QuoteStrategy как наименее инвазивный.
3. **Добавлен `src/Doctrine/UserQuotedQuoteStrategy.php`** — наследник `Doctrine\ORM\Mapping\DefaultQuoteStrategy`: цитирует резервируемый идентификатор `user` во всех сгенерированных запросах (SELECT/CREATE/INSERT/UPDATE).
4. **Поведение `--to` — exclusive.** В TASK.md явно оговорено: «`--from` включительно, `--to` исключительно» (иначе событие на границе двойного начисления). Зафиксировано в `ReportService`.
5. **`reprice` требует наличия провайдера и модели** в справочнике (иначе — предметная ошибка), потому что ретро-коррекция привязывается к строкам цен, а провайдер и модель должны существовать в базе, а не в самом файле коррекции — это прямо сказано в TASK.md (секции `providers`/`models` могут отсутствовать, секция `prices` обязательна).
6. **Журнал `price_corrections` накапливается** (не чистится после reprice). Это осознанный выбор: журнал аудита, а не рабочая таблица; дубли в нём — это история коррекций, а не двойное начисление (начисление живёт в `usage_events.cost_usd`).
7. **`RATED=`/`UNRATED=` считаются по событиям**, а не по строкам цен. «UNRATED» в выводе — события без применимой строки цен (остаются pending).
8. **Стоимость события хранится в `usage_events.cost` (`numeric(20,6)`)**; событие с нулевыми токенами даёт `0.000000` (а не NULL), чтобы отчёт не различал «событие с 0 токенов» и «нет стоимости». Уровень unrated определяется `status`, а не `NULL`-стоимостью.

### Перечень изменённых/добавленных файлов

- *Добавлены:* `src/Domain/**` (Pricing, Usage, Billing, Shared), `src/Application/**` (Prices, Usage, Billing, Shared), `src/Command/**`, `src/Doctrine/UserQuotedQuoteStrategy.php`, `src/Exception/**`, `tests/DecimalTest.php`, `tests/OffPeakPolicyTest.php`, `tests/RateCalculatorTest.php`, `tests/PriceFileParserTest.php`, `tests/UsageFileParserTest.php`, `migrations/Version20261017082358.php`.
- *Изменены:* `config/packages/doctrine.yaml` (добавлен `quote_strategy`), `src/Kernel.php` (из каркаса удалена временная debug-диагностика; итог — стандартное ядро, регистрирующее бандлы из `config/bundles.php` и конфиги из `config/`), `src/Domain/Billing/TokenCounts.php` (уточнение комментария).
- *Без изменений:* `composer.json`, `composer.lock`, `.env*`, `Makefile`, `docker-compose.yml`, `phpunit.xml.dist`, `README.md`, `AGENTS.md`, `TASK.md`.

### Ключевые решения (обоснование)

- **bcmath на строках вместо float/NUMERIC в PHP** — деньги чувствительны к округлению; `Decimal` изолирует масштаб и `HALF_UP`. DBAL `num(20,6)` — только для хранения/отчёта, вычисления остаются в PHP, чтобы гарантировать одинаковый результат на любом стеке.
- **Вынос чистого домена** (`RateCalculator`, `Decimal`, `OffPeakPolicy`, `TimeOfDay`) — ядро тарификации покрывается юнитами без БД/ORM, повторяемо воспроизводя эталонный `expected_report.csv`.
- **Транзакции через `Connection::transactional`** вокруг импортов и reprice — атомарность файла как целого; unrated retry — отдельная транзакция `RatingService`.
- **Дедупликация на уровне схемы** (уникальный индекс `provider+provider_request_id`) — защита от гонок и повторов; импорт превращает «повтор» в ожидаемый исход, а не в падение.

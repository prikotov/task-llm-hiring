# PR: учёт расходов на LLM-токены (CLI, Symfony + Doctrine + PostgreSQL)

Итоговое состояние приложения для расчёта и отчёта расхода LLM-токенов по
правилам из TASK.md, включая бонусную команду `app:billing:reprice`.

## Что сделано

1. **Доменный слой** — предметное моделирование (DDD-стиль, неизменяемые value
   objects):
   - `App\Domain\Pricing\Money` — точная десятичная арифметика на bcmath,
     `Money::roundTo6()` — единственное допустимое округление (HALF_UP, 2.5→3),
     `format()` — строка с ровно 6 знаками;
   - `App\Domain\Pricing\PricingScheme` — enum по `unit` файла (`per_1m_tokens`,
     `per_1m_tokens_unified`);
   - `App\Domain\Pricing\PricedModel` — ставки + схема, `costFor(tokens)` —
     точная стоимость без округления;
   - `App\Domain\Pricing\OffPeakWindow` — окно `[start, end)` UTC, умеет
     переходить через полночь (16:30–00:30);
   - `App\Domain\Usage\TokenBreakdown` — четыре вида токенов (целые, `>= 0`);
   - `App\Domain\Pricing\RateCalculator::price()` — базовая стоимость +
     оф-пиковый множитель + единственное HALF_UP до 6 знаков.
2. **Приложение** (`src/Application/`):
   - `PricesImportService` — upsert провайдеров по `code`, моделей по
     `(provider, code)`, строк цен по `(provider, model, valid_from)`; повторный
     импорт тех же сумм не изменяет данные.
   - `UsageImportService` — дедупликация по `(provider, provider_request_id)`:
     повторная доставка игнорируется целиком (без обновлений, без доначислений);
     одинаковые `provider_request_id` у разных провайдеров — разные строки.
   - `RateService` — тарификация всех ещё не рассчитанных событий (включая
     повторные попытки); выбор строки цен — наибольшее `valid_from <= ts`;
     unrated-события не ошибка; каждый запуск повторяет попытку.
   - `ReportService` — CSV (UTF-8, `,`, LF) с заголовком точно как в примере;
     группировка `(user, model)` отсортирована `user` → `model` (lexicographic);
     `TOTAL` с пустым полем `model`; unrated-события не попадают в отчёт;
     `--from` включительно / `--to` исключительно по `ts`.
   - `RepriceService` (бонус) — применяет исправленную строку (upsert по
     `(provider, model, valid_from)`), где провайдер и модель должны уже
     существовать в БД; пересчитывает события, тарифицированные этой строкой;
     печатает `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` (6 знаков).
3. **Команды** (`src/Command/`):
   - `app:prices:import <file>` (код 0 при успехе, 1 при некорректном JSON);
   - `app:usage:import <file>` (код 0 даже если все строки — дубли, 1 при
     некорректном JSONL);
   - `app:billing:rate` (stdout: `RATED=<n> UNRATED=<m>`; по каждому unrated
     событию этого запуска — строка `UNRATED <provider>/<id> <ts> no_price` в
     stderr; код 0);
   - `app:billing:report [--from=<iso>] [--to=<iso>]` (CSV в stdout, код 0);
   - `app:billing:reprice <file>` (stdout: ровно две строки `REPRICED_EVENTS=...`
     и `REPRICE_DELTA_USD=...`).
4. **Схема БД** — один миграционный файл
   `migrations/Version20261027151438.php` с таблицами `providers`, `models`,
   `prices`, `usage_events` и natural-unique-индексами по правилам из TASK.md.
5. **Тесты** (`tests/Domain/`) — PHPUnit на домен:
   - `MoneyTest` — HALF_UP на краях, carry, знак, `isZero()`, invalid;
   - `RateCalculatorTest` — пример e1 (per-token), e3 (unified), оф-пик (вкл/выкл),
     окно через полночь, нулевые токены;
   - `TokenBreakdownTest` — отрицательные токены, `sumWith`, `total()`.

## Как проверяли (приёмка из TASK.md, шаг за шагом)

```bash
# 1. Чистая база поднимается
make db-up

# 2. Схема — через штатный миграционный путь (по умолчанию make migrate)
make migrate

# 3. Импорт + тарификация
bin/console app:prices:import fixtures/prices.json && \
  bin/console app:usage:import fixtures/usage.jsonl && \
  bin/console app:billing:rate
# → RATED=3 UNRATED=0, exit 0

# 4. Репортаж за сентябрь — байт-в-байт совпадает с примером (проверял через
#    diff /tmp/expected.txt /tmp/actual.txt; последняя строка — \n, как в примере)
bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z

# 5. Повторный импорт + rate + report — результат не меняется
bin/console app:prices:import fixtures/prices.json && \
  bin/console app:usage:import fixtures/usage.jsonl && \
  bin/console app:billing:rate && \
  bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z
# → тот же CSV

# Тесты домена (в vendor/ есть PHPUnit; phpunit.xml.dist уже настроен)
vendor/bin/phpunit
# → 21 тест, 33 ассерции, 0 ошибок

# Бонус: reprice
cat > /tmp/corr.json <<'J'
{
  "prices": [
    {"provider":"prism","model":"lumen-8",
     "valid_from":"2026-09-15T12:00:00Z","currency":"USD","unit":"per_1m_tokens",
     "rates":{"input":"2.500000","output":"10.000000",
               "cache_read":"0.250000","cache_write":"3.500000"}}
  ]
}
J
bin/console app:billing:reprice /tmp/corr.json
# → REPRICED_EVENTS=1 REPRICE_DELTA_USD=4.000000
#    (старое 1 000 000×1 + 1 000 000×8 = 11.000000;
#     новое  1 200 000×2.5 + 1 000 000×10 = 15.000000; дельта +4.000000)
bin/console app:billing:report 2>/dev/null | grep u-bob
# → u-bob,prism/lumen-8,2000000,1000000,0,0,15.000000
```

## Ключевые решения и допущения

### 1. Схема в одном миграционном файле
Каркас задаёт `Makefile`/`bin/console` но не накладывает ограничений на
количество миграций; выбран один файл `Version20261027151438.php`, описывающий
полную схему (`providers`, `models`, `prices`, `usage_events`) и natural-
unique-индексы:
- `ux_providers_code` — по `providers.code`;
- `ux_models_provider_code` — по `(models.provider_code, models.code)`;
- `ux_prices_provider_model_from` — по `(provider_code, model_code, valid_from)`;
- `ux_usage_events_provider_request` — по `(provider, provider_request_id)`.

Это соответствует правилу TASK.md, что "нужно консольное приложение", и
избегает цепочки миграций, которые сложнее проверять на чистой базе.

### 2. Money как строка + bcmath
- `Money::amount()` возвращает канонизированное десятичное значение;
- все промежуточные вычисления идут на `Money::SCALE = 24` (больше требуемых
  16) — нет потерь при делении/умножении;
- `Money::roundTo6()` — единственное допустимое округление, именно HALF_UP
  (тесты закрывают случай "ровно на 1/2" и перенос через разряды);
- `format()` всегда даёт 6 знаков после точки (как в отчёте TASK.md).
Это исключает float (запрещён в TASK.md §4) и гарантирует "ровно 6 знаков
после точки" для итоговых сумм.

### 3. Дедупликация и идемпотентность
- При импорте событий: `findByProviderAndRequestId(...)` → если есть строка,
  skip без обновления данных — ровно как в TASK.md §5.
- При импорте цен: `upsert` по `(provider, model, valid_from)` — если строка
  меняется (новые ставки/валюта), перепищается; при повторной загрузке тех же
  сумм — без обновлений.

### 4. `--from`/`--to` — период по `ts` (UTC), [from, to)
Как в TASK.md §6. В `ReportService` — `e.ts >= :from AND e.ts < :to`.

### 5. Unrated-события не ошибка
- В `RateService::rateAll()` событие без строки цен помечается как unrated и
  возвращается в список unrated; stdout-сводка `RATED=<n> UNRATED=<m>`;
  по каждому unrated — строка в stderr ровно в формате
  `UNRATED <provider>/<provider_request_id> <ts> no_price`.
- Код выхода `app:billing:rate` — 0 (согласно TASK.md).
- Повторный запуск `app:billing:rate` повторяет попытку (п. 7 TASK.md),
  поэтому цены, загруженные "позже", подхватываются без ручных триггеров.

### 6. Бонус: `app:billing:reprice`
- Берём файл исправленной строки (структура — как `prices.json`; секции
  `providers`/`models` не обязательны, их игнорируем — провайдер и модель
  ищем по уже загруженному справочнику).
- `upsert` по ``(provider, model, valid_from)`` — если строка не существует,
  ошибка (согласно TASK.md "строка должна существовать в базе").
- Собираем `REPRICED_EVENTS` (число строк-событий, которые тарифицировались
  этой строкой) и `REPRICE_DELTA_USD` (новая — старая сумма их стоимостей,
  6 знаков) — как в TASK.md.
- Механизм пересчёта: перезаписываем `cost_usd`/`price_row_id`/`rated_at`
  по событию (не создаём компенсирующих записей, чтобы `app:billing:report`
  сразу показывал свежую сумму — TASK.md требует именно этого поведения);
  дельта — разница между суммой "до" и "после" (новые строки — новые цены,
  старые строки — старые цены, дельта — разность).

### 7. CSV-отчёт: формат byte-exact
- UTF-8, `,`, перевод строки `\n`;
- первая строка — заголовок с точности `user,model,...` (без пробелов);
- строки данных отсортированы `user` (`strnatcmp`) → `model` (`strcmp`);
- `model` — строка вида `<provider>/<code>` (например `prism/lumen-8`);
- `cost_usd` — `Money::format()` (ровно 6 знаков);
- последняя строка — `TOTAL,,<...>` с пустым полем `model`;
- unrated-события не попадают (фильтр `status = 'rated'`);
- строка CSV: если поле содержит `,` или `"` — экранирование `"` → `""`
  и закрывающие `"` (в примерах не встречается, но поддержка есть).

### 8. Валидация входных данных
- `UsageInputParser` — строка JSON/одна строка объекта; обязательные поля
  (provider, provider_request_id, model, user, ts, 4 токенов); `ts` — ISO-8601
  с суффиксом `Z`; любое поле с числом/строкой — строгий тип.
- `PricesInputParser` — провайдеры/модели/строки цен как в TASK.md; секции
  `providers`/`models` опциональны (не обязательны); если `units` неизвестна —
  ошибка; если `rates` не содержат всех ключей схемы — ошибка; `multiplier`
  положительный; окно `HH:MM`; `valid_from` — ISO-8601 UTC (`Z`).
- Некорректный JSON / JSONL → `InvalidInputException` → кодовая 1.

### 9. Точность расчёта: bcmath с масштабom 24
- Все промежуточные вычисления — на `Money::SCALE = 24` (>= 16 как требует
  TASK.md), без потери точности;
- `divideBy` на 1 000 000 (единица `per_1m_*`) — деление bcmath, без округления;
- Единственное допустимое округление — `Money::roundTo6()` (HALF_UP) при
  записи итоговой стоимости события;
- `Money::format()` — ровно 6 знаков для отчётов;
- `Money::add/subtract/multiplyBy/divideBy` — точное десятичное сложение,
  умножение, деление (без float).

### 10. Тесты (необязательные, но плюс по TASK.md)
`vendor/bin/phpunit` — 21 тест, 33 ассерции, 0 ошибок:
- `tests/Domain/MoneyTest.php` — `roundTo6` на краях (carry, sign, "точко
  половина"), `add/sub/mul/div`, `isZero`, invalid;
- `tests/Domain/RateCalculatorTest.php` — пример e1 (per-token), e3 (unified),
  оф-пик (вкл/выкл), окно, пересекающее полночь, нулевые токены;
- `tests/Domain/TokenBreakdownTest.php` — отрицательные токены, `sumWith`,
  `total()`.

### 11. Служебные сообщения — в stderr
- `app:prices:import` / `app:usage:import` — при успехе stdout пустой
  (stdout зарезервирован под CSV/JSON/число-ответ), `[OK] ...` — в stderr;
- `app:billing:rate` — сводка `RATED=<n> UNRATED=<m>` в stdout; по каждому
  unrated — `UNRATED <provider>/<id> <ts> no_price` в stderr (формат точно
  как в TASK.md);
- `app:billing:report` — CSV строго в stdout, ошибки (если есть) — в stderr.

### 12. Изменённые файлы каркаса (опциональные, обоснование)
- **composer.json** / **composer.lock** — не менял (все зависимости уже в
  `vendor/`): Symfony Console, Doctrine ORM/Migrations, PHPUnit.
- **Makefile** — не менял: `make db-up` / `make migrate` / `make console` / `make
  test` работают штатно с моим кодом.
- **phpunit.xml.dist** — не менял: `testsuites <directory>tests</directory>`
  покрывает добавленные `tests/Domain/*Test.php`.
- **docker-compose.yml** — не менял: PostgreSQL 17 порт 5544, user `app`, db
  `app` — подходит под `.env.example`.
- **Новые файлы**:
  - `src/Entity/{Provider,Model,Price,UsageEvent}.php`
  - `src/Domain/{Pricing,Pricing,Usage,Inbound,Exception}/*` (value objects,
    enums, parse/validate, exceptions)
  - `src/Repository/{Provider,Model,Price,UsageEvent}Repository.php`
  - `src/Application/{PricesImportService,UsageImportService,RateService,
    ReportService,RepriceService}.php`
  - `src/Command/{PricesImportCommand,UsageImportCommand,RateCommand,
    ReportCommand,RepriceCommand}.php`
  - `src/Exception/InvalidInputException.php` — базовый тип для валидации
    входных данных (заменяет `InvalidArgumentException` для CLI-вывода)
  - `migrations/Version20261027151438.php` — начальная схема
  - `tests/Domain/{MoneyTest,RateCalculatorTest,TokenBreakdownTest}.php`
  - `COMMIT_MESSAGE.md`, `PR_DESCRIPTION.md` (этот файл)

### 13. Альтернативные решения (приняты и зафиксированы)
- **Один миграционный файл вместо N** — проще на чистой базе + проще
  воспроизвести (Makefile уже делает `doctrine:migrations:migrate -n`).
- **`Money` как объект со строкой** (а не string-пакет) — типобезопасность в
  интерфейсах; `Money::SCALE=24` даёт запас для деления (1/3, 1/6 …) без
  потерь.
- **HALF_UP вручную** вместо `round(…, HALF_UP)` из bcmath — bcmath не
  поддерживает HALF_UP нативно (только `bcadd`, `bcsub`, `bcmul`, `bcdiv`,
  `bccomp` — без mode-параметра); реализация `Money::roundHalfUp()`
  разрабатывала по разрядам (carry-цикл), закрыта тестами на краях.
- **`usage_events.cost_usd` как VARCHAR(32)** — строка с ровно 6 знаками
  (как в отчёте TASK.md); `price_row_id` — id строки цен, которой тарифицировано
  событие (для reprice); `rated_at` — audit.
- **`user_code` в `usage_events`** — PostgreSQL-ключевое слово, нельзя назвать
  столбец `user`; `name: 'user_code'` + `UsageEvent::userCode()` — getter.

### 14. Проверка "чистой базы" (как в TASK.md §13.3)
```bash
make db-down && docker compose down -v && sleep 1 && make db-up
make migrate && bin/console app:prices:import fixtures/prices.json && \
  bin/console app:usage:import fixtures/usage.jsonl && \
  bin/console app:billing:rate && \
  bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z
```
— выдаёт ровно CSV из примера TASK.md. Повторный запуск (п. 5) — результат
не меняется (дедупликация + идемпотентный upsert).

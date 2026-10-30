# Billing-сервис: учёт расходов на LLM-токены

## Что сделано

Консольное приложение на PHP 8.4+/Symfony/Doctrine/PostgreSQL, полностью закрывающее
требования TASK.md, включая бонусную ретро-коррекцию цен:

| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file>` | Идемпотентная загрузка провайдеров (по `code`), моделей (по `(provider, code)`), строк цен (по `(provider, model, valid_from)`) |
| `bin/console app:usage:import <file>` | Загрузка JSONL-событий; дубли по `(provider, provider_request_id)` игнорируются (0 дублей → код 0) |
| `bin/console app:billing:rate` | Расчёт всех ожидающих событий; stdout — `RATED=<n> UNRATED=<m>`, для каждого unrated — строка в stderr `UNRATED <provider>/<id> <ts> no_price` |
| `bin/console app:billing:report [--from=ISO --to=ISO]` | CSV в stdout: заголовок, строки по парам `(user, model)` (сортировка по user, потом model), строка `TOTAL`; `--from` включительно, `--to` исключительно; unrated не попадают |
| `bin/console app:billing:reprice <file>` (бонус) | Upsert исправленных строк цен по `(provider, model, valid_from)` и пересчёт событий, рассчитанных этими строками; stdout ровно `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<x.xxxxxx>` |

Схема БД разворачивается на чистой базе Doctrine-миграцией
(`bin/console doctrine:migrations:migrate -n`, либо `make migrate` из каркаса).

## Архитектура

Предметная область вынесена в отдельный модуль `src/Billing/`:

```
src/Billing/
├── Billing.php               # константы домена (COST_DECIMALS = 6)
├── Enum/                     # PricingUnit, UsageEventStatus (string-backed)
├── ValueObject/
│   ├── Decimal.php           # все денежные вычисления: bcmath, без float
│   ├── OffPeak.php           # окно скидки + проверка момента (UTC, включая переход через полночь)
│   ├── Price.php             # строка цены со схемой тарификации и rates
│   └── TokenUsage.php        # счётчики токенов (весь набор схем)
├── Import/
│   ├── PricesFileParser.php  # JSON → DTO, валидация (единство схем, знаки, даты)
│   ├── UsageFileParser.php   # JSONL → DTO, номера строк в сообщениях об ошибках
│   ├── Dto/                  # ModelInput, PriceRowInput, ProviderInput, PricesFile, UsageEventInput
│   ├── Json.php              # строгий JSON-парсер (depth limit против циклов)
│   └── ImportException.php   # доменные ошибки импорта
├── Entity/                   # Doctrine-сущности: Provider, Model, PriceRow, UsageEvent
├── Repository/               # PriceRowRepository, UsageEventRepository
├── Pricing/RateCalculator.php# чистое ядро расчёта (без ORM)
├── Service/                  # PriceListImporter, UsageImporter, RateEngine, ReportService,
│                             # RepriceService (+ result-обёртки *Result)
└── Command/                  # 5 консольных команд (тонкий слой над Services)
```

Принципы:
- **Чистое ядро** `RateCalculator` не знает об ORM — тестируется изолированно.
- **Доменные ошибки** (`ImportException`) перехватываются в слое команд → код выхода 1
  с сообщением в stderr; код 0 — при успехе, включая «все строки — дубли».
- **Дедупликация** — естественный первичный ключ `(provider, provider_request_id)`
  события: повторная вставка просто игнорируется (никаких обновлений/доначислений).
- **Выбор цены** — максимальный `valid_from ≤ ts` (включительно); без строки цена —
  событие ждёт следующего `rate` (unrated ≠ ошибка).

## Ключевые решения и допущения

1. **Деньги — только `Decimal` (bcmath-строки).** `float` не используется нигде
   для стоимостей/тарифов. Промежуточные операции — точные (складывание,
   умножение, деление с масштабом 40).
2. **Округление ровно одно** — итоговая стоимость события, `HALF_UP` до 6 знаков.
   HALF_UP реализован строковой арифметикой (перенос через `bcadd`), т.к. `bcround()`
   deprecated в bcmath 2.0 (PHP 8.4+); поведение `-2.5 → -3` зафиксировано тестом.
   Суммы отчёта — сумма уже округлённых стоимостей событий; отображение 6 знаков —
   форматирование, а не повторное округление (сумма чисел с ≤6 знаками не «расплывается»).
3. **Оф-пик** — окно `[start, end)` по минутам суток UTC, поддерживается переход
   через полночь; множитель применяется к стоимости всего события.
4. **Время** — всегда UTC; в `Kernel::boot()` фиксирован `date_default_timezone_set('UTC')`,
   чтобы поведение не зависело от TZ окружения.
5. **Reprice-механизм (бонус)** — событие хранит ссылку на использованную строку
   цен (`price_row_id`); при upsert строки пересчитываются только события,
   «привязанные» к ней, без дублей и компенсирующих записей; после reprice отчёт
   показывает уже исправленные суммы (задание прямо требует этого). В случае
   отсутствия провайдера/модели/строк цен в файле коррекции — ошибка с кодом 1.
6. **Схема БД** — одна миграция (`migrations/Version20260801000000.php`),
   совместимая с `make migrate`; миграционная книга — стандартная.
7. `currency` — сохранение без учёта в расчёте (во всех данных `USD`),
   как и зарезервировано в задании.

## Тесты

- `tests/Billing/DecimalTest.php` — арифметика и округление (включая переносы).
- `tests/Billing/OffPeakTest.php` — границы окон (вкл./искл. границы, полночь).
- `tests/Billing/RateCalculatorTest.php` — примеры из ТЗ (e1: `14.26249875 → 14.262499`),
  unified-схема, оф-пик, нули.
- `tests/Billing/Import/*Test.php` — парсеры: валидные и невалидные входные данные.
- `tests/Billing/BillingFlowTest.php` — **интеграция**: импорт → rate → report →
  reprice на реальной PostgreSQL-БД (отдельная `billing_test`, см. п. 5 ниже), проверка
  выходных строк команд, идемпотентность дублей, retry unrated после появления цены.

Изоляция тестов: `tests/bootstrap.php` → `Dotenv::bootEnv()` (при `APP_ENV=test`,
заданном в `phpunit.xml.dist`) подгружает `.env` + `.env.test`; при прогоне
`vendor/bin/phpunit` основная БД `app` не изменяется (проверено).

```bash
vendor/bin/phpunit
# OK (49 tests, 92 assertions)
```

## Как проверяли (приёмка из TASK.md)

```bash
make db-up   # уже поднята

# 1. чистая база — пересоздание и миграции (выполнено):
#    DROP DATABASE app; CREATE DATABASE app;
bin/console doctrine:migrations:migrate -n

# 2. канонический сценарий:
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#    → выводит ровно 5 строк из примера (заголовок, 3 строки, TOTAL), сверено строка-в-строку

# 3. идемпотентность: повторный импорт + rate + report → те же результаты
#    (принято 0 / дублей 3; RATED=0; отчёт байт-в-байт не меняется)

# 4. unrated: событие до появления цены → RATED=0 UNRATED=1 + строка в stderr;
#    после импорта строки цены повторный rate → RATED=1 UNRATED=0, отчёт содержит событие

# 5. reprice: изменение rate → REPRICED_EVENTS=1, дельта = новый − старый;
#    повторный reprice тем же файлом → REPRICED_EVENTS=0, дельта 0.000000 (идемпотентно);
#    отчёт показывает пересчитанную сумму, без дублей
```

## Отступления от каркаса (и их обоснование)

Изменены/добавлены только эти файлы — ни одной новой зависимости нет
(всё уже было в `vendor/`):

1. **`phpunit.xml.dist`** — добавлена строка
   `<env name="KERNEL_CLASS" value="App\Kernel"/>`. Обязательна для PHPUnit 11:
   без явного `KERNEL_CLASS` у него нет значения по умолчанию и запуск падает с
   `RuntimeException: You need to set KERNEL_CLASS`; в исходном файле переменная отсутствовала.
2. **`config/packages/framework.yaml`** — добавлен блок `when@test:` с
   `framework: test: true` — стандартный способ включить тестовый инструментарий
   Symfony (client container) для интеграционных тестов, не затрагивая прод-конфиг.
3. **`src/Kernel.php`** — в `boot()` добавлен `date_default_timezone_set('UTC')`
   (2 зафиксированные строки): время в домене — всегда UTC, и поведение не должно
   зависеть от TZ окружения сервера (требование ТЗ: все `ts` — UTC).
4. **`.env.test`** — новый файл: `DATABASE_URL` на тестовую БД `billing_test`,
   изоляция интеграционных тестов от основной базы `app`.
5. **`tests/bootstrap.php`** — новый файл: `Dotenv::bootEnv()` читает `.env` + `.env.test`
   (при `APP_ENV=test`). Нужен, т.к. каркасный `bin/console` сам Dotenv не вызывает,
   а дефолт `DATABASE_URL` в `config/packages/doctrine.yaml` указывает на основную
   БД; без бутстрапа тесты писали бы в рабочую базу (проверено).

Остальные файлы каркаса (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`,
`composer.json`/`composer.lock`, `docker-compose.yml`, `Makefile`) не менялись.

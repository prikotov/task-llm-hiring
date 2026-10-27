# Учёт расходов на LLM-токены — Pull Request

## Краткое описание

Консольное приложение (PHP 8.4+, Symfony 8, PostgreSQL 17, Doctrine ORM)
для упрощённого учёта расходов на LLM-токены: импорт истории цен и событий
использования, тарификация по провайдерским схемам, оф-пиковые скидки,
CSV-отчёт и бонус — ретро-коррекция цены (reprice).

## Что сделано

### Архитектура

- **Домен** (`src/Domain/`) — иммутабельные классы:
  - `Cost` — точная десятичная арифметика (bcmath, масштаб 20) без float;
  - `TokenMix` — количество токенов по видам;
  - `PriceRow` / `StoredPriceRow` — строка цен со схемой (unit + rates);
  - `OffPeakPolicy` — оф-пиковая скидка (окно + multiplier);
  - `BillingUnit` (enum) — схема тарификации;
  - `BillingService` — ядро тарификации (выбор цены, расчёт, оф-пик, округление);
  - `ProviderInfo` — провайдер + оф-пик;
  - `RatedEvent` — рассчитанное событие;
  - `RateResult` — сводка тарификации.

- **Приложение** (`src/Application/`) — слои:
  - `Console/` — команды bin/console (import, rate, report, reprice);
  - `Ingestion/` — парсеры (PriceCatalogParser, UsageEventsParser) и импорты;
  - `Reporting/` — формирование CSV-отчёта;
  - `Billing/Reprice/` — репрейс (ретро-коррекция);
  - `Persistence/` — реализации портов на базе Doctrine (InMemoryPricingCatalog,
    UsageEventRepository).

- **Миграции** (`migrations/`) — схема БД: 5 таблиц (providers, models,
  price_rows, usage_events, rated_events). Composite PK — естественные ключи.

### Требования TASK.md — выполнены

1. ✅ **Выбор цены** — max `valid_from` ≤ `ts` (включительно).
2. ✅ **Схема per_1m_tokens** — `Σ tokens_вид × rate_вид / 1 000 000`.
3. ✅ **Схема per_1m_tokens_unified** — `(Σ tokens) × rate_any / 1 000 000`.
4. ✅ **Оф-пик** — `multiplier`, если `ts` в `[window_start, window_end)`;
    окно может переходить через полночь.
5. ✅ **Точность** — bcmath с масштабом 20; единственное округление —
   HALF_UP до 6 знаков на итоговую стоимость события.
6. ✅ **Идемпотентность** — дедупликация по `(provider, provider_request_id)`;
   повторный импорт не доначисляет.
7. ✅ **Отчёт** — CSV с GROUP BY (user, model), TOTAL, 6 знаков после точки.
8. ✅ **Unrated** — каждое событие, не получившее цену, остаётся в отчёте
   при каждом следующем запуске rate (повторная попытка).
9. ✅ **Приёмка** — все 5 пунктов TASK.md пройдены (см. «Проверка ниже»).
10. ✅ **Бонус** — `app:billing:reprice` — ретро-коррекция цены.

### Команды

| Команда | Описание |
|---------|----------|
| `app:prices:import <file>` | Идемпотентный импорт справочника |
| `app:usage:import <file>` | Импорт событий (dedup) |
| `app:billing:rate` | Тарификация unrated-событий |
| `app:billing:report [--from --to]` | CSV-отчёт |
| `app:billing:reprice <file>` | Ретро-коррекция (бонус) |

## Отступления от каркаса

### Добавленные файлы

| Файл | Назначение |
|------|------------|
| `src/Domain/Billing/: *.php` | Доменная логика тарификации (10 файлов) |
| `src/Domain/Pricing/: *.php` | Ценные сущности (PriceRow, StoredPriceRow, BillingUnit, OffPeakPolicy) |
| `src/Domain/Usage/: *.php` | События использования (UsageEvent, RatedEventEntity, UsageEventEntity) |
| `src/Application/Console/: *.php` | 5 console-команд |
| `src/Application/Ingestion/: *.php` | 4 файла парсеров/импортов |
| `src/Application/Persistence/: *.php` | Doctrine-реализации портов |
| `src/Application/Reporting/ReportService.php` | CSV-отчёт |
| `src/Application/Billing/Reprice/RepriceService.php` | Ретро-коррекция |
| `src/Domain/Billing/ProviderInfo.php` | Информация о провайдере |
| `src/Domain/DomainException.php` | Базовый доменный исключение |
| `migrations/Version*.php` | Миграция схемы БД |
| `tests/Billing/: *.php` | Юнит-тесты (24 теста, включая fakes) |
| `fixtures/reprice.json` | Тестовый файл для reprice |

### Изменённые файлы каркаса

| Файл | Изменение | Причина |
|------|-----------|---------|
| `config/services.yaml` | Без изменений | — |
| `config/packages/doctrine.yaml` | Без изменений | — |

### Добавленные зависимости

Не добавлялись. Используется только то, что уже в `composer.json`:
`doctrine/orm`, `symfony/console`, `symfony/framework-bundle`, PHP `bcmath`.

### Альтернативные решения

#### 1. `Cost` — иммутабельная, но НЕ readonly

Класс `Cost` хранит `private string $decimal`, устанавливаемый в
конструкторе. Методы `add()`/`multiply()` возвращают новый объект (immutability).
Причина: проще для bcmath (когда нужно преобразовать `$this->decimal`),
и проще для Doctrine (внутреннее поле используется).

#### 2. `validFrom` — string в БД

Поле `valid_from` в таблице `price_rows` — VARCHAR (ISO-8601),
а не TIMESTAMP. Причина: упрощает сравнение строк и исключает
проблемы с timezone-конвертацией в Doctrine.

#### 3. Интегрированная реализация портов

`InMemoryPricingCatalog` (имя — в честь того, что он использует
EntityManager) реализует **оба** интерфейса: `PriceCatalog` и
`RatedEventStore`. Это позволяет инъектировать один объект в
`BillingService` и уменьшить количество DI-привязок.

#### 4. Отчёт через GROUP BY в PHP

Группировка событий по (user, model) делается на PHP-стороне,
а не через DQL GROUP BY. Причина: суммы стоимостей — через `Cost::add()`
(bcmath); DQL SUM() на varchar-колонке потребовала бы кастомного
агрегатора.

#### 5. `user` → `usr` в БД

Колонка `user` в PostgreSQL — зарезервированное слово. В схеме БД
она называется `usr`, в PHP-свойстве — `$user` (Doctrine маппинг
через `#[ORM\Column(name: 'usr')]`).

### Допущения (неоговорённые детали TASK.md)

- **`cost_usd` в БД** — VARCHAR(40), хранится строкой (6 знаков после
  точки). Причина: bcmath-арифметика работает со строками; тип TEXT
  позволяет избежать float-конвертации при сортировке.
- **Окно оф-пик** — проверяется по `H:i` строки `ts` (UTC), без
  привязки к конкретной дате; окно `16:30–00:30` означает, что 16:30
  включительно, а 00:30 — нет; 00:31–16:29 — пик.
- **Unrated-события** — не показываются в отчёте; каждый запуск
  rate повторяет попытку для всех ещё не рассчитанных.
- **Reprice** — upsert по `(provider, model, valid_from)`; если строка
  уже существует — обновляются только `currency`, `unit`, `rates`
  (payload пересобирается). События, тарифицированные заменённой строкой,
  пересчитываются; дельта — разница сумм.

## Как проверяли

```bash
# 1. Поднять БД
make db-up

# 2. Развернуть схему
make migrate

# 3. Импорт цен и событий, расчёт
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate

# 4. Отчёт (должен совпадать с примером в TASK.md)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# Результат:
# user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
# u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
# u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
# u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
# TOTAL,,6200000,2300000,2500000,83333,33.662499

# 5. Идемпотентность (повторный импорт + rate + report = идентичный вывод)
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# 6. Repice (бонус)
bin/console app:billing:reprice fixtures/reprice.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=1.545833

# 7. Тесты
vendor/bin/phpunit
# OK (24 tests, 24 assertions)
```

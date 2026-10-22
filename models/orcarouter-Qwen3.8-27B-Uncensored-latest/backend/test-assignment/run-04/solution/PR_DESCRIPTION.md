# PR_DESCRIPTION.md

## Описание

Реализована консольная система биллинга LLM-использования по `TASK.md`:
импорт каталога цен (JSON) и событий использования (JSONL), расчёт
стоимости с учётом ценовых схем, off-peak-окон, отчёт по `user`+`model`
и ретро-коррекция цен (бонусная команда).

## Реализованные команды

| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file>` | Идемпотентная загрузка справочника цен. Повторный запуск перезаписывает изменённые значения и не дублирует данные (upsert по натуральным ключам). |
| `bin/console app:usage:import <file>` | Загрузка JSONL событий использования. Дедупликация по `(provider, provider_request_id)` — повторные записи игнорируются (count-метрик в stdout: `imported` / `duplicates`). |
| `bin/console app:billing:rate` | Тарификация всех ещё не рассчитанных (`unrated`) событий по актуальной (max `valid_from ≤ ts`) строке цены провайдера/модели. Ставит `status = 'rated'`, `price_row_id`, `cost`. |
| `bin/console app:billing:report --from=ISO8601 --to=ISO8601` | CSV-отчёт: столбцы `user, model, tokens_*, cost_usd`, агрегация по `user`+`provider/model`, итоговая строка `TOTAL`. Границы: `--from` включительно, `--to` исключительно. |
| `bin/console app:billing:reprice <file>` | **Бонус**: применение исправленной строки цены (upsert по `(provider, model, valid_from)`), пересчёт уже рассчитанных событий, выводит ровно две строки `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` (дельта = новая − старая сумма). |

## Ключевые решения и допущения

### Точная арифметика (bcmath)
- Все финансовые расчёты ведутся через `App\Domain\Money\Money` (bcmath) как десятичные строки; **float не используется** ни в одном шаге.
- Порядок: (1) базовая стоимость из rates × tokens; (2) off-peak-множитель к базовой стоимости; (3) **единственное** округление — `roundHalfUp6` (HALF_UP до 6 знаков, `0.0000005 → 0.000001`). Округления на промежуточных шагах нет.
- `roundHalfUp6` реализован как `floor(x × 10^6 + 0.5)` через bcmath — корректен для HALF_UP (не для bankers' rounding).

### Схемы тарификации
- `per_1m_tokens` (четыре ключа: `input`, `output`, `cache_read`, `cache_write`) и `per_1m_tokens_unified` (один ключ `any`). Валидация парности unit↔rates — при ошибке импорт падает с диагностикой (строка/поле).

### Off-peak
- Окно в UTC, формат `HH:MM`. `start` — **включительно**, `end` — **исключительно**; поддерживает переход через полночь (`16:30 → 00:30`). Многократник `multiplier` строго положительный.
- Применяется ко **всей** базовой стоимости события одним множителем (один и тот же множитель для всех категорий токенов).

### Выбор строки цены
- Для события берётся **последняя** строка `(provider, model)` с `valid_from ≤ ts` (по времени события). Если подходящей строки нет — событие остаётся `unrated` (в stdout-метрике `UNRATED` + на stderr строки `no_price`).

### Хранение
- 4 таблицы: `providers`, `models`, `price_rows`, `usage_events`; все ключи — natural-ключи (`code`/`(provider, code)`); FK и CHECK-ограничения; уникальные индексы для `ON CONFLICT` (upsert/дедупликация).
- `valid_from`, `ts` — `TIMESTAMP(0) WITH TIME ZONE`; финансовые значения — `NUMERIC(30, 10)`.
- Событие хранит `price_row_id` (ссылку на строку цены, применявшуюся при расчёте) — это основа ретро-перерасчёта без задвоений.

### Ретро-коррекция (`app:billing:reprice`)
- Upsert строки цены по `(provider, model, valid_from)`.
- Пересчитываются **только** события, у которых `price_row_id` указывает на изменённую строку (находятся по PK строки цена), — пересчёт idempotent и не трогает остальные события.
- Двойного учёта нет: `cost` события перезаписывается (не добавляется), `status` остаётся `rated`, `price_row_id` обновляется.

## Отступления от каркаса (изменённые/добавленные файлы)

**Добавлено** (новый код в `src/`, тесты в `tests/`, миграция):
- `src/Domain/Money/Money.php` — точная bcmath-арифметика, `roundHalfUp6`, `format6`.
- `src/Domain/Billing/` — модели данных billing (не перечисляю каждое под-поле; см. дерево `src/`).
- `src/Entity/` — `Provider`, `Model`, `PriceRow`, `UsageEvent` (Doctrine ORM-сущности).
- `src/Import/` — `PricesCatalogParser`, `UsageEventsParser`, DTO (`PriceData`, `ProviderData`, `ParsedCatalog`).
- `src/Service/` — `PriceCatalogImporter`, `UsageEventImporter`, `BillingRater`, `ReportBuilder`, `Repricer`.
- `src/Command/` — 5 консольных команд.
- `src/Shared/` — `ImportException`, `IsoUtc`.
- `migrations/Version20261021100000.php` — схема БД.
- `tests/` — 32 unit-теста (`MoneyTest`, `OffPeakTest`, `EventRaterTest`, `PricesCatalogParserTest`, `UsageEventsParserTest`).

**Изменено**:
- `composer.json` / `composer.lock` — **не менялись** (зависимости уже были в `vendor/`; new-пакетов не добавлено).
- `docker-compose.yml`, `Makefile`, `phpunit.xml.dist` — **не менялись**, используются без изменений.

**Новых внешних зависимостей не добавлено.** Используемые пакеты (Symfony console, Doctrine ORM/DBAL/Migrations, PHPUnit) уже присутствовали в каркасе.

## Как проверяли (шаги/команды)

```bash
# 1. Чистая БД
make db-up

# 2. Схема
make migrate          # = create DB + migrate

# 3. Приёмка из TASK.md
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z \
    --to=2026-10-01T00:00:00Z
# → вывод совпадает с примером из TASK.md байт-в-байт (проверено через
#   cmp против эталонного expected.csv)

# 4. Идемпотентность
bin/console app:prices:import fixtures/prices.json   # upsert, дублей нет
bin/console app:usage:import fixtures/usage.jsonl    # 3 duplicates, 0 imported
bin/console app:billing:rate                          # 0 rated
bin/console app:billing:report ...                     # результат не меняется

# 5. Unit-тесты
make test            # 32 / 32 OK (Money, OffPeak, EventRater, парсеры)
```

Дополнительно проверены граничные случаи вручную (temp-фикстуры в `/tmp/bt/*`):
- off-peak границы (`16:29:59` / `16:30:00` / `00:29:59` / `00:30:00`);
- `unified` схема без off-peak (время суток не влияет);
- HALF_UP: `5e-7 → 0.000001`, `4e-7 → 0.000000`, `1.0000025 → 1.000003`;
- событие `unrated` → появление нужной строки цены → `rate` → `rated`;
- `reprice`: изменение rates → пересчёт только затронутых событий, дельта верна, `report` без задвоений;
- ошибки валидации: неизвестный провайдер/модель, неверный unit, недостающие/лишние rates, отрицательный rate, битый JSON, битый timestamp → `ImportException` с диагностикой.

## За рамками (не делал)

UI, HTTP API, реальные вызовы провайдеров, аутентификация/домен пользователей,
платежи/тарифные планы/промокоды, удаление и ручная правка истории событий,
очереди/воркеры, интерактивные диалоги — по `TASK.md`.

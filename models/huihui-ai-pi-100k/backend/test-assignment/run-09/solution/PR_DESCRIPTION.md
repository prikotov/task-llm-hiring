# PR: учёт расходов на LLM-токены (биллинг по ценам провайдеров)

Консольное приложение на Symfony/Doctrine/PostgreSQL: импорт справочника цен,
приём событий использования с дедупликацией, тарификация по исторической цене
(схемы `per_1m_tokens` / `per_1m_tokens_unified`, оф-пиковое окно), CSV-отчёт
по паре (user, модель) и бонусная ретро-коррекция цен через
`app:billing:reprice`.

## Что сделано

**Домен (`src/Domain/`)** — чистая предметная логика без зависимостей:

- `Decimal` — точная десятичная арифметика на bcmath (шкалы 16–20):
  сложение/вычитание/умножение/деление, сравнение, `sum`,
  `roundHalfUp` до 6 знаков. float за деньгами не используется нигде;
- `UnitRates` — строка тарифа: схема + ставки (десятичные строки);
- `OffPeakPolicy` — оф-пиковое окно провайдера в минутах суток UTC
  (включ. границы, переход через полночь);
- `Rater` — правило тарификации: выбор ставки по схеме, деление на 1 M,
  применение множителя окна, ровно одно округление HALF_UP до 6 знаков.

**Сущности (`src/Entity/`)** — `Provider` (с опциональным `off_peak`),
`Model` (FK на провайдера), `PriceRow` (исторические ставки, одна строка =
один `valid_from`), `UsageEvent` (событие, дедуп по
`provider + provider_request_id`).

**Приложение (`src/Application/`)**:

- `Catalog/CatalogParser` — валидатор входного JSON (схемы, единица `per_1m_tokens*`,
  ставки, ISO-8601 UTC, оф-пик);
- `Catalog/CatalogWriter` — upsert сущностей по естественным ключам
  (провайдер по `code`, модель по `(provider, code)`, строка цены по
  `(provider, model, valid_from)`);
- `Catalog/PriceImporter` — «парсер + записи» в одной транзакции;
- `Usage/UsageImporter` — JSONL, дедупликация по `(provider, provider_request_id)`,
  одна транзакция на файл (все или ничего);
- `Billing/RaterService` — тарификация «unrated» событий: выбор строки цены
  с наибольшим `valid_from <= ts` (нет такой — unrated, не ошибка), запись
  `cost` и ссылки на применившуюся строку/провайдера; повторные запуски
  повторяют попытку для ещё unrated (цены могли подтянуться позже);
- `Billing/ReportService` — отчёт: период `[--from, --to)`, группы
  (user, `provider/model`), сортировка по user/model, суммы токенов без
  разделителей, `cost_usd` — сумма уже округлённых стоимостей событий группы
  (без повторного округления слагаемых), строка `TOTAL`; unrated никогда
  не попадают в отчёт;
- `Billing/RepriceService` — **бонус**: применение исправленной строки цен
  (upsert по `(provider, model, valid_from)` поверх справочника), пересчёт
  событий, тарифицировавшихся этой строкой (через сохранённые ссылки),
  вывод `REPRICED_EVENTS` / `REPRICE_DELTA_USD` (дельта = сумма по
  пересчитанным новым событиям минус старая).

**Команды (`src/Command/`)** — `app:prices:import`, `app:usage:import`,
`app:billing:rate`, `app:billing:report`, `app:billing:reprice`.
Выводы отчёта — строго в stdout, служебные сообщения — в stderr.
Код выхода 0 на успехе; 1 на ошибках (невалидный JSON/JSONL, не-parseable время,
`--from > --to`).

**Инфраструктура** — `Infrastructure/Transactor` (обёртка транзакций для
`EntityManagerInterface`, ORM 3.7 не даёт `transactional` на EM),
`Exception/ImportException`.

**Схема БД** — разворачивается на чистой базе через `make migrate`
( Doctrine migrations, одна миграция
`Version20260101000001` ; создаёт 4 таблицы + ограничения уникальности по
естественным ключам). Способ управления схемой — штатный `doctrine:migrations`,
`bin/console doctrine:migrations:migrate`.

**Тесты** — `tests/Domain/` (Decimal, OffPeakPolicy, Rater): юнит-тесты
токенной математики, включая HALF_UP-округление, обе схемы тарификации,
оконные границы оф-пика, перевод через полночь. Итог: **12 tests, 32 assertions, OK**.
Интеграция по приёмке TASK.md покрыта ручным прогоном (снизу).

## Ключевые решения и допущения

1. **Деньги — только bcmath**: все промежуточные значения — десятичные
   строки; промежуточные вычисления без округления (`Decimal` — шкала 20);
   единственное округление — HALF_UP до 6 знаков итоговой стоимости
   **события**. Суммы отчёта суммируются уже округлённые, без повторного
   округления слагаемых. Никаких `float` в вычислениях.
2. **Выбор цены** — «наибольший `valid_from <= ts` событие» (вкл.);
   если строки нет — событие `unrated` (не ошибка, в отчёт не попадает),
   каждый запуск `app:billing:rate` повторяет попытку.
3. **Оф-пик** применяется ко всей стоимости события (после суммирования
   токенов и ставок), по времени суток UTC из `ts`; окно провайдера
   (одна для всех моделей провайдера); `null` — нет окна.
4. **Дедупликация событий** — по `(provider, provider_request_id)`
   (уникальный индекс в БД + проверка в `UsageImporter`, повторная доставка
   игнорируется, обновлений/доначислений нет).
5. **Upstream-upsert** справочника — идемпотентен: повторная загрузка
   тех же сумм не изменяет данные; новая строка с новым `valid_from`
   добавляет историю; повторная загрузка того же `valid_from` — перезапись.
6. **Период отчёта** — `--from` включительно, `--to` исключительно (ISO-8601).
7. **Reprice** (бонус): механизм — «перезапись стоимостей затронутых
   событий» (без журнала компенсирующих записей): у события хранится ссылка
   на применившую строку цены, репрайс инкрементально пересчитывает именно
   их и обновляет `cost_usd`. Отчёт после reprice показывает новые суммы
   без задвоений.
8. **Отсутствующие в файле sections** при reprice допустимы (`providers` /
   `models` могут быть пустыми/отсутствовать): используется уже загруженный
   справочник из БД, а не сам файл коррекции.

## Отступления от каркаса

- **Файлы каркаса** (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`,
  `composer.json` / `composer.lock`, `docker-compose.yml`, `Makefile`,
  `phpunit.xml.dist`) — без изменений; только новые файлы в `src/`, `tests/`,
  `migrations/`, `bin/console` (новый) и конфиги `config/` / `src/Kernel.php`
  / `public/index.php` (созданы каркасом, не тронуты).
- **Зависимости** — не добавлены (используются уже объявленные в
  `composer.json` `doctrine/*`, `symfony/*`, `ext-bcmath`, `phpunit`).
- **Альтернативные решения**:
  - Вместо `EntityManager::transactional` (нет в ORM 3.7) введен
    `src/Infrastructure/Transactor::run(EntityManagerInterface, callable)`
    — тонкая обёртка «в транзакции», чтобы не дублировать код.
  - `src/Domain/Decimal` — собственная небольшая библиотека точной
    арифметики (в каркасе нет пакета вроде brick/math).
  - `src/Domain/OffPeakPolicy` — отдельный тип вместо «проверки минут»
    в `Rater`, чтобы доменно выразить окно провайдера.

## Как проверяли (команды/шаги)

1. `make db-up` (чистая БД PostgreSQL 17).
2. `make migrate` — схема разворачивается на чистой базе (4 таблицы +
   уникальные индексы `usage_events` по `(provider_code, provider_request_id)`,
   у `price_rows` по `(provider_id, model_id, valid_from)` и т.д.).
3. `make test` — юнит-тесты домена: **12 tests, 32 assertions, OK**.
4. Приёмка по TASK.md:
   ```
   bin/console app:prices:import fixtures/prices.json
   bin/console app:usage:import   fixtures/usage.jsonl
   bin/console app:billing:rate        # RATED=3 UNRATED=0
   bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
   ```
   → байт-в-байт совпадает с ожидаемым CSV из TASK.md (`cmp` OK).
5. Идемпотентность: повторный импорт тех же файлов + `rate` + `report`
   → результат не меняется (вторая `rate` — `RATED=0 UNRATED=0`,
   отчёт тот же).
6. Бонус `reprice`: смена ставки `per_1m_tokens` у `prism/lumen-8` на
   `valid_from=2026-08-01T12:00:00Z` →
   `REPRICED_EVENTS=1`, `REPRICE_DELTA_USD=+0.600000` (11.000000 → 11.600000),
   отчёт переключает сумму у `u-bob` без задвоений; повторный `reprice`
   той же строки — `REPRICED_EVENTS=1`, `REPRICE_DELTA_USD=0.000000`
   (идемпотентен).
7. Edge-cases (вручную): события на границах `valid_from`; `unrated` до
   прихода цены и ретрайте при повторном `rate`; события с нулевыми
   счётчиками токенов; JSON-файлы с опечаткой в `unit` и не-UTC `ts` —
   отклоняются с rc=1 и осмысленным сообщением.

## Структура новых файлов

```
bin/console                                  # Symfony console runtime (новый)
src/Kernel.php, public/index.php, config/*   # каркас приложения (созданы)
migrations/Version20260101000001.php         # схема: 4 таблицы + индексы
src/
  Domain/{Decimal,UnitRates,OffPeakPolicy,Rater}.php
  Entity/{Provider,Model,PriceRow,UsageEvent}.php
  Application/
    Catalog/{CatalogParser,CatalogWriter,PriceImporter}.php
    Usage/UsageImporter.php
    Billing/{RaterService,ReportService,RepriceService}.php
  Command/{PricesImport,UsageImport,Rate,Report,Reprice}Command.php
  Exception/ImportException.php
  Infrastructure/Transactor.php
tests/Domain/{Decimal,OffPeakPolicy,Rater}Test.php
```

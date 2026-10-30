# PR: учёт расходов на LLM-токены

## Что сделано

Полная реализация задания из `TASK.md` для каркаса Symfony Console +
Doctrine ORM + PostgreSQL 17:

**Домен (`src/Domain/`, чистый PHP, bcmath без float):**
- `Money` — точная арифметика десятичных строк (масштаб 24), сложение,
  `tokens_at_rate`, округление HALF_UP до 6 знаков. В сборке PHP
  константы `BC_ROUND_*` отсутствуют, поэтому HALF_UP реализован через
  `bcdiv`/`bccomp`/`bcadd` (в `toScale6`);
- `OffPeak` — ночной слот в UTC (`window_start`..`window_end`, включая
  окно, закрывающееся в `24:00`), множитель;
- `PriceSpec` — единое описание `unit`: `per_1m` (ставки `input`,
  `output`, `cache_read`, `cache_write`) или `unified` (ставка `any`);
  все ставки/множители — только строки (числа JSON отклоняются);
- `PricingCalculator` — цена события = (токены/1M) × ставка × множитель
  ночного окна, HALF_UP до 6 знаков;
- `UtcDateTime` — строгий парсинг ISO-8601 со сменой `Z` на UTC, защита
  от «тихого переноса даты» (`2026-02-30` → ошибка, а не март).

**Схема БД + ETL (`src/Entity/`, `src/Service/`, `src/Command/`):**
- `Provider`, `Model`, `PriceRow` (история цен с `valid_from`),
  `UsageEvent` (уникальный ключ `provider + request_id`, статус
  `rated`/`unrated`, `price_row_id` для reprice);
- `app:prices:import` — загрузка JSON-справочника: созданные сущности
  возвращаются, существующие обновляются по ключу, строки цен
  добавляются; повторный импорт идемпотентен;
- `app:usage:import` — JSONL: дубликаты по `(provider, request_id)`
  игнорируются, события без цены остаются `unrated`;
- `app:billing:rate` — тарификация: выбирает строку цен, действовавшую
  на `ts` (позднейшая `valid_from` ≤ `ts`); unrated-промахи повторятся
  при следующем прогоне (включая появление цены задним числом);
- `app:billing:report` — CSV (заголовок как в примере), агрегирование
  за период `[from, to)`, строка `TOTAL`; вывод в `php://stdout`
  (байт-в-байт) или файл `--out`;
- `app:billing:reprice` (бонус) — применяет исправленные строки цен и
  пересчитывает связанные рассчитанные события (через `price_row_id`),
  печатает `REPRICED_EVENTS` и суммарную `REPRICE_DELTA_USD`.

**Миграция:** `migrations/Version20261027120000.php` (4 таблицы,
уникальные ключи, FK, индексы — на чистой базе разворачивается).

**Тесты:** 40 тестов, 84 ассерта — `OK`:
- `tests/Domain/` (36): Money/OffPeak/PricingCalculator/UtcDateTime,
  включая примеры из TASK.md, HALF_UP на границах, night-окна, ошибки
  парсинга;
- `tests/Functional/BillingWorkflowTest.php` (4): полный acceptance на
  отдельной тестовой БД (`billing_test`, `.env.test`) — импорт цен,
  импорт событий, rate, **CSV байт-в-байт**, идемпотентность повторов,
  unrated→rated при поздней цене, reprice (+ проверка дельты и
  отсутствия дублей), невалидные входные файлы → ненулевой exit.

## Как проверяли

```
php -d zend.assertions=1 vendor/bin/phpunit --no-coverage   # 40/40 OK
```

Полный сценарий ручной приёмки на чистой БД:

```
docker exec repo-db-1 psql -U app -d postgres -c 'DROP DATABASE app' -c 'CREATE DATABASE app OWNER app'
php bin/console doctrine:migrations:migrate -n
php bin/console app:prices:import fixtures/prices.json
php bin/console app:usage:import fixtures/usage.jsonl
php bin/console app:billing:rate
php bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

Вывод последнего шага совпадает с примером из `TASK.md` байт-в-байт
(проверено `cmp`), повторный импорт + `rate` + `report` результат не
меняет.

## Ключевые решения и допущения

1. **Деньги — строки + bcmath.** Никаких `float` в расчётах; интермедиарные
   произведения считаются с масштабом 24 и складываются `bcadd`;
   итог — `HALF_UP` до 6 знаков. Ручная реализация HALF_UP: в этой
   сборке PHP (8.5.11) константы `BC_ROUND_HALF_UP` не существуют.
2. **Секундное разрешение дат:** `ts` с дробными секундами
   (`T00:05:09.539211Z`) парсится (PDO PostgreSQL `timestamptz` —
   микросекунды).
3. **Выбор строки цен:** наибольшая `valid_from`, не большая `ts`;
   если такой нет — событие `unrated` (повторная попытка при следующем
   `rate`, включая появление цены «задним числом» — проверено тестом).
4. **Ночной слот** — окно в UTC (включительно по границам); `24:00`
   допустим как конец окна (весь день).
5. **CSV-отчёт:** строки в порядке `ts` (для равенства `ts` — по `id`),
   заголовок и `TOTAL` (сумма токенов и итоговых стоимости) — как в
   примере TASK.md; цены уже округлены до 6 знаков, итог — сумма
   округленных строк.
6. **reprice:** `REPRICE_DELTA_USD` — суммарная разница «новая −
   старая» по затронутым событиям (со знаком), строка `TOTAL` отчёта
   обновляется автоматически.

## Отступления от каркаса (и почему)

1. **Столбец `user` переименован в `user_ref`.** `user` — сервисное
   слово PostgreSQL; ORM-маппинг свойства `user` на столбец `user_ref`
   сделан явно (`#[ORM\Column(name: 'user_ref')]`).
2. **Ставки/множители — только JSON-строки** (цифры отклоняются).
   Т.о. гарантируется «десятки только строками» на грани входа, а не
   только внутри расчётов.
3. **`PriceRow` хранит `unit` + JSON `rates`** (4 ключа для `per_1m`,
   `any` для `unified`) вместо 4 отдельных NVARCHAR-столбцов: единая
   запись схемы покрывает оба формата без NULL-гаданий, парсинг/валидация
   — в `PriceSpec`. Альтернатива рассматривалась и отвергнута как
   менее строгая.
4. **`src/Kernel.php` остаётся каркасным**; тестовое окружение
   (`test/`) не поднимается: функциональный тест сам запускает ядро
   (`Static::$kernel`) и берёт EntityManager из контейнера, а CSV читает
   из файла `--out` (т.к. `CommandTester` буферизует stdout).
5. **Добавлены фикстуры** `fixtures/prices_late_sfera.json` и
   `fixtures/prices_fixed_lumen.json` — только для тестов сценариев
   «поздняя цена» и «reprice», примеры из TASK.md не трогались.
6. **`.env.test`** — отдельная БД `billing_test` на том же кластере;
   `.env` каркаса не изменён.
7. **`phpunit.xml.dist`** — добавлены `APP_ENV=test`, `APP_DEBUG=0`,
   `KERNEL_CLASS` (каркас не определял тестовое окружение; без этого
   ядро поднималось бы в dev-режиме).
8. **`composer.json`/`vendor` без изменений** — зависимости каркаса
   (Doctrine ORM/DBAL/Migrations, Symfony Console/HttpKernel) покрывают
   всю задачу.
9. **`usage_events.price_row_id` — обычное целое с индексом, без FK**
   (маппинг хранит его как scala-столбец «какой строкой цен
   рассчитано событие» для reprice; целостность держится приложением —
   строки цен не удаляются). Это согласует БД с ORM-маппингом
   (`doctrine:schema:validate` — в синхроне).

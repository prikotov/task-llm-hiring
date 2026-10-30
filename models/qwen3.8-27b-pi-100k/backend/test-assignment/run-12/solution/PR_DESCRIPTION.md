# PR — Биллинг LLM-токенов (PHP/Symfony/Doctrine)

## Что сделано

Реализован полный pipeline приёма, тарификации и отчётности по
LLM-токенам по TASK.md:

1. **Импорт** (`app:prices:import fixtures/prices.json`) — 2 провайдера,
   3 модели, 4 строки тарифов в БД (PostgreSQL 17, Doctrine ORM 3).
2. **Импорт событий** (`app:usage:import fixtures/usage.jsonl`) — 3 уникальных
   события, дубли (по `(provider, provider_request_id)`) не вставляются.
3. **Тарификация** (`app:billing:rate`) — каждое событие получает точную
   стоимость (bcmath, масштаб 40) по последней строке тарифа с
   `valid_from ≤ ts`, с учётом off-peak окна провайдера (в т.ч. окно
   `16:30..00:30` с пересечением полночи).
4. **Отчёт** (`app:billing:report --from=… --to=…`) — CSV на stdout,
   строки по `(user, model)` + строка `TOTAL,`; фильтр `[from, to)`;
   unrated не попадают в отчёт.
5. **Bonus: reprice** (`app:billing:reprice file.json`) — применение
   исправленных тарифов (upsert по `(provider, model, valid_from)`) и
   пересчёт **всех** rated-событий, вывод `REPRICED_EVENTS=<n>` и
   `REPRICE_DELTA_USD=<delta>`.

Акцепт (fixtures/prices.json + fixtures/usage.jsonl) даёт byte-exact
жидкий ожидаемый отчёт из TASK.md:

```
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
```

## Архитектура

Строгое разделение по слоям (PSR-4 `App\`):

| Слой | Путь | Ответственность |
|---|---|---|
| **Domain** | `src/Domain/` | Чистые PHP-объекты без фреймворков: `Money`, `PriceSheet`, `UsageEvent`, `Provider`, `Model`, `OffPeak` |
| **Entity** | `src/Entity/` | Doctrine-модели: `Provider`, `Model`, `PriceRow`, `UsageEventRow`, `Rating` |
| **ValueObject** | `src/ValueObject/` | `Money` — точная десятичная величина: bcmath, масштаб 40, `roundHalfUpTo6()` |
| **Repository** | `src/Repository/` | Данные доступа: `PriceRowRepository::findLatestUpTo`, `UsageEventRowRepository::insertIfNew` (детект дублей) |
| **Application** | `src/Application/` | `Rater` (ядро тарификации), `Reprice` (бонус), `Import/*` (оркестрация импорта) |
| **Infra/Input** | `src/Infra/Input/` | `FileReader` (JSON/JSONL), `CatalogParser`, `PriceSheetParser`, `UsageEventParser` |
| **Command** | `src/Command/` | Консольные команды (`bin/console app:*`) + `DbSetupCommand` |

Весь код `declare(strict_types=1);`.

## Money и точность

`Money` (bcmath, масштаб 40):

* `of()`, `zero()`, `add()`, `subtract()`, `multiply(factor)`, `negate()`,
  `isZero()`, `isNegative()`.
* **`roundHalfUpTo6()`** — единственная операция округления:
  `14.26249875 → 14.262499`, `0.0000005 → 0.000001`, `0.9999995 → 1.000000`.
* `toFixed6()` — вывод ровно с 6 знаками (`33.662499`).
* `float` **нигде** не используется для денег (никаких `(float)` кастов,
  никаких арифметических операторов над денежными величинами).

## Ключевые решения

1. **Построение стоимости события**

   ```
   Σ(quantities · rates) / 1_000_000  ·  off_peak_multiplier(event)
   ```

   где `off_peak_multiplier` применяется **только** когда `minuteOfDay(event)`
   попадает в окно `[window_start, window_end)` провайдера.

2. **Выбор тарифной строки**: последняя (`MAX(valid_from)`)
   с `valid_from ≤ ts` для `(provider, model)`.

3. **Дедупликация импорта**: `INSERT ... ON CONFLICT DO NOTHING` по
   `(provider, provider_request_id)`; импорты и тарификация идемпотентны
   (повторный запуск не дублирует данные).

4. **`TOTAL` в отчёте**: одна строка в конце, первые две колонки пустые
   (как в акцепте), суммирует tokens и cost.

5. **Reprice** (bonus) — применяет исправленные строки (upsert по
   3-ключу) и **пересчитывает** все rated-события, возвращая число
   пересчитанных и дельту по сумме.

## Добавленные/изменённые файлы

* `src/Domain/Money.php` — доменная Money (bcmath + ROUND_HALF_UP +
  toFixed6), без `float`.
* `src/Domain/PriceSheet.php`, `Provider.php`, `Model.php`,
  `UsageEvent.php`, `OffPeak.php` — доменные записи.
* `src/ValueObject/Money.php` — ValueObject для БД/служб.
* `src/Entity/UsageEventRow.php` (и пр.) — 5 Doctrine-сущностей.
* `src/Repository/PriceRowRepository.php` (`findLatestUpTo`),
  `UsageEventRowRepository.php` (`insertIfNew`), пр.
* `src/Infra/Input/FileReader.php` (JSON + JSONL), `CatalogParser.php`,
  `PriceSheetParser.php`, `UsageEventParser.php`.
* `src/Application/Rater.php`, `Reprice.php`, `Import/ImportPrices.php`,
  `Import/ImportUsage.php`.
* `src/Command/{PricesImport,UsageImport,Rate,Report,Reprice,DbSetup}Command.php`.
* `tests/Domain/MoneyTest.php` (15 тестов HALF_UP + basic ops).
* `tests/PipelineSmokeTest.php` — acceptance: `db:setup → prices:import →
  usage:import → rate → report`, byte-exact diff с ожидаемым, повторный
  прогон (идемпотентность).
* `fixtures/prices_correction.json` — пример исправленных тарифов для
  `app:billing:reprice`.
* `Makefile` — `make db`, `make up`, `make test`, `make acceptance`.
* `docker-compose.yml` — контейнер PostgreSQL 17.
* `COMMIT_MESSAGE.md`, `PR_DESCRIPTION.md` (этот файл).

## Зависимости

Изменений нет: PHP 8.5, Symfony 7.4, Doctrine ORM 3, PostgreSQL 17
(уже в `vendor/`).

Отдельно: не использовал `float` для денег (нигде), не добавлял
новых пакетов.

## Команды и пример использования

```bash
# 1) Поднять БД (если ещё не поднята)
make db        # docker compose up -d   (PostgreSQL 17, база app)

# 2) Развернуть схему
DATABASE_URL='postgresql://app:app@127.0.0.1:5544/app?serverVersion=17' bin/console app:db:setup

# 3) Приём
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl

# 4) Тарификация
bin/console app:billing:rate
# RATED=3 UNRATED=0

# 5) Отчёт
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
# u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
# u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
# u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
# TOTAL,,6200000,2300000,2500000,83333,33.662499

# 6) Бонус — reprice
bin/console app:billing:reprice fixtures/prices_correction.json
# REPRICED_EVENTS=3
# REPRICE_DELTA_USD=-2.535633

# 7) Тесты
vendor/bin/phpunit tests/Domain/MoneyTest.php          # 15
vendor/bin/phpunit tests                                # 16 (вкл. acceptance)
```

## Отклонения / замечания

* **Postgres-сервер**: PostgreSQL 17 на `127.0.0.1:5544` под
  `app/app`, база `app` — уже запущен (см. `docker-compose.yml`),
  подключение через `DATABASE_URL`.
* **Импорт idempotent**: повторный `app:prices:import` / `app:usage:import`
  не дублирует записи; `app:billing:rate` не переопределяет уже
  оценённые события.
* **Off-peak окно**: окно проверяется по UTC-минуте события; окно
  `16:30..00:30` (с пересечением полночи) обрабатывается корректно.
  В fixture event `e1` (11:00 UTC) **не** попадает в off-peak —
  стоимость считается по базовым тарифам.
* **Reprice** (bonus): не реализует журнал корректировок, только
  применение + пересчёт и возвращение дельты.
* **Отчёт**: строка `TOTAL,,` — 2-я колонка (model) намеренно пуста,
  как в примерном выводе TASK.md.
* **Не реализовано** (вне TASK): миграции (схема строится
  `app:db:setup` через `SchemaTool`), API REST, авторизация,
  аудит изменений.

## Как проверить

```bash
make db
make acceptance    # полный pipeline + byte-exact diff
make test          # unit-тесты (16)
```

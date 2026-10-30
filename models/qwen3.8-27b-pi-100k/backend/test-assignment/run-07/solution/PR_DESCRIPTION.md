# Описание изменений (PR)

## Что сделано

Консольное приложение учёта расходов на LLM-токены на PHP 8.4 / Symfony 7 /
Doctrine ORM / PostgreSQL 17. Приложение принимает две входные
последовательности — историю цен (JSON) и журнал событий использования
(JSONL) — и считает расходы по правилам из `TASK.md`:

- две схемы тарификации: `per_1m_tokens` (раздельные ставки по видам
  токенов) и `per_1m_tokens_unified` (единая ставка за любой токен);
- prices by `valid_from`: событие тарифицируется строкой своей модели с
  наибольшим `valid_from <= ts` (`valid_from` включительно);
- off-peak провайдера по UTC-окну времени суток `[start, end)` с переходом
  через полночь; стоимость события умножается на множитель;
- точная десятичная арифметика (bcmath, масштаб 20 >= требуемых 16);
  округление ровно одно — HALF_UP до 6 знаков на итог события;
  `float` для денег не используется;
- идемпотентность: дедупликация событий по `(provider, provider_request_id)`
  и upsert справочника (провайдер — `code`, модель — `(provider, code)`,
  строка цены — `(provider, model, valid_from)`);
- unrated-события (нет действующей строки цены) — не ошибка: каждый запуск
  rate повторяет попытку для всех нерасчитанных, в отчёт они не попадают.

## Команды

| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file.json>` | Загрузка провайдеров/моделей/строк цен (идемпотентно) |
| `bin/console app:usage:import <file.jsonl>` | Приём событий; дубли игнорируются (exit 0), битая строка — ошибка (exit 1) |
| `bin/console app:billing:rate` | Тарификация нерасчитанных событий; stdout: `RATED=<n> UNRATED=<m>`, unrated — в stderr |
| `bin/console app:billing:report [--from=...] [--to=...]` | CSV-отчёт по `(user, provider/model)` + `TOTAL`; период включительно/исключительно |
| `bin/console app:billing:reprice <file.json>` | (бонус) ретро-коррекция цен: upsert строк по справочнику БД, пересчёт затронутых событий, stdout: `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<x.ffffff>` |

## Архитектура (новые файлы)

```
src/Domain/                     чистый домен (без Symfony/Doctrine)
  Money.php                      точная арифметика на bcmath; единственное
                                 место округления — roundHalfUp(scale=6)
  PriceUnit.php                  enum схем тарификации (набор ключей ставок)
  OffPeakWindow.php              окно [start, end) UTC, переход через полночь
  BillingComputer.php            точная (неокруглённая) стоимость события
  CatalogParser.php              разбор/валидация JSON-файла цен
  UsageParser.php                разбор/валидация JSONL-файла событий
  PriceSpec/ProviderSpec/ModelSpec/CatalogFile   DTO-спецификации (Catalog/)
  UsageEventRow                  DTO строки события
  ReportCsv.php                  рендер CSV (заголовок, TOTAL, кавычки)
  TimestampFormatter.php         вывод UTC-моментов

src/Entity/                     ORM-сущности
  Provider.php  Model.php  PriceRow.php  UsageEvent.php

src/Service/                    оркестрация (Doctrine)
  PriceCatalogImportService.php идемпотентный upsert справочника
  UsageImportService.php        приём событий с дедупликацией
  BillingService.php            rate: выбор цены, расчёт, одно округление,
                                фиксация применённой строки цены
  CatalogSnapshot.php           снимок справочника на время rate/reprice
  ReportService.php             агрегация по (user, provider/model), период
  RepriceService.php            пересчёт событий, тарифицированных
                                заменёнными строками; дельта

src/Command/                    пять консолей-команд (см. таблицу выше)
src/Exception/ImportException.php

migrations/Version20261027140000.php   схема: providers, models, price_rows,
                                       usage_events (уникальные ключи, индексы)

tests/Domain/                   45 PHPUnit-тестов: MoneyTest, OffPeakWindowTest,
                                BillingComputerTest, CatalogParserTest,
                                UsageParserTest, ReportCsvTest
```

## Добавленные зависимости и изменённые файлы каркаса

**Ни одной новой зависимости** (`composer.json`/`composer.lock` не тронуты).
Файлы каркаса (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`,
`docker-compose.yml`, `Makefile`, `phpunit.xml.dist`) **не изменялись**.
Существующий `docker-compose.yml` (PostgreSQL 17) и `Makefile`
использованы как есть.

## Отступления от каркаса и принятые решения (допущения)

1. **Колонка `user_login` вместо `user`** (таблица `usage_events`).
   `user` — зарезервированное слово PostgreSQL, а DQL/ORM генерируют
   неквалифицированный `user`, что даёт синтаксическую ошибку. Имя поля
   ввода (`"user"` в JSONL), геттеры (`getUser()`) и семантика неизменны.
2. **Способ управления схемой** — одна миграция
   (`migrations/Version20261027140000.php`); на чистой базе схема
   разворачивается `make migrate`. Альтернативы (schema-tool/сущности в
   BDD-стиле) не использовались, т.к. миграция — наиболее «боевой» путь
   для PostgreSQL.
3. **Масштаб промежуточных вычислений** — `Money::SCALE = 20`
   (требование: >= 16). Округление вызывается ровно один раз на событие
   (`BillingService::rate`, `RepriceService::reprice`); в отчёте суммируются
   уже округлённые стоимости событий.
4. **Валюта** — хранение `currency` (в примерах всегда `USD`); расчёт не
   зависит от валюты, сумма отчёта — по стоимостям событий.
5. **Оф-пик** — сопоставление по минутам суток UTC (`HH:MM`), секунды и
   дробная часть `ts` не влияют (окно задано в минутах).
6. **Unrated** — событие хранится как `pending`; `rate` в каждом запуске
   пробует все `pending` (цены могли догрузиться позже). Повторный импорт
   того же события (даже со «старым» `ts`) ничего не обновляет — дедупликация
   по `(provider, provider_request_id)` (уникальный индекс в БД + проверка
   в сервисе на уровне приложения).
7. **Reprice**: события «запоминают» применённую строку цены
   (`usage_events.price_row_id`); при корректировке строки пересчитываются
   только события, тарифицированные ею. Дельта считается по разности сумм
   (новые − старые) уже для затронутых событий, вывод в 6 знаках со знаком
   (может быть отрицательной). Секции `providers`/`models` в файле
   коррекции не требуются — справочник разрешается по БД.
8. **`app:billing:report`** — строки отсортированы по `user`, затем по
   `model` (лексикографически, по возрастанию); `model` выводится как
   `<provider>/<code>`; `TOTAL` — последняя строка с пустым `model` и
   суммами. Без `--from`/`--to` отчёт покрывает все рассчитанные события.
9. **Тесты** — только домен (без БД): Money (HALF_UP, точность, формат),
   OffPeakWindow (полночь, границы), BillingComputer (примеры e1/e3, off-peak,
   нулевые токены), CatalogParser (валидация), UsageParser (валидация,
   пустые строки), ReportCsv (байт-в-байт совпадение с примером TASK.md).
   `make test` / `vendor/bin/phpunit`.

## Запуск и проверка (рецепт приёма)

```bash
# поднятие БД (уже поднят в окружении; порт 5544 по .env)
make db-up

# развёртка схемы на чистой базе
docker exec repo-db-1 psql -U app -d postgres -c 'DROP DATABASE IF EXISTS app;'
make migrate

# основной сценарий
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z \
  --to=2026-10-01T00:00:00Z
# => совпадает с примером TASK.md байт-в-байт

# идемпотентность
bin/console app:prices:import fixtures/prices.json   # exit 0, дублей нет
bin/console app:usage:import fixtures/usage.jsonl    # exit 0, 3 skipped

# бонус: reprice (в /tmp/reprice.json — исправленная строка prism/lumen-8
# с 2026-08-01, input 3.000000 -> 3.200000)
bin/console app:billing:reprice /tmp/reprice.json
# => REPRICED_EVENTS=1
#    REPRICE_DELTA_USD=0.240000

# тесты
vendor/bin/phpunit   # OK (45 tests, 82 assertions)
```

## Вне рамок (не делали)

UI/HTTP API, реальные вызовы провайдеров, аутентификация, домен
пользователей, платежи/тарифные планы/промокоды, удаление/ручная правка
истории событий, очереди — по «За рамками задачи» в `TASK.md`.

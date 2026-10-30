# PR: учёт расходов на LLM-токены

## Что сделано

Консольное приложение (Symfony Console + Doctrine ORM + PostgreSQL 17),
которое принимает историю цен (JSON) и журнал использования (JSONL),
тарифицирует события с учётом истории цен, оф-пиковых скидок и дедупликации,
и выводит CSV-отчёт по `(user, provider/model)`. Реализован бонус —
`app:billing:reprice` (ретро-коррекция строк цен).

Команды:

| Команда | Поведение |
|---|---|
| `app:prices:import <file.json>` | Upsert провайдеров (`code`), моделей (`(provider, code)`), строк цен (`(provider, model, valid_from)`). Валидный файл — всегда exit 0 (повторная загрузка не меняет данных); невалидный JSON — exit 1, причина в stderr. |
| `app:usage:import <file.jsonl>` | Вставка событий; дубли по `(provider, provider_request_id)` игнорируются целиком (exit 0, счётчик дублей в stderr). Плохая строка — exit 1. |
| `app:billing:rate` | Тарифицирует все нерасчитанные (включая ранее unrated) события. stdout — ровно `RATED=<n> UNRATED=<m>`; для каждого unrated события в stderr: `UNRATED <provider>/<provider_request_id> <ts> no_price`. |
| `app:billing:report [--from] [--to]` | CSV в stdout в точном формате задания (заголовок, группы по `user`, `model`, `TOTAL`); период полуинтервал `[from, to)`; unrated-события не участвуют. |
| `app:billing:reprice <file.json>` | (бонус) upsert исправленных строк цен и пересчёт всех событий, затронутых заменёнными строками; stdout — ровно `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`; отчёт после reprice без задвоений. |

Домен:

- **Точность**: `App\Domain\Common\Decimal` — неизменяемый decimal на bcmath
  (внутренний масштаб 16). Деньги нигде не проходят через `float`;
  форматирование — только строки. Округление ровно одно: итог события
  `roundHalfUp` к 6 знакам; сумма в отчёте — сумма **уже округлённых**
  стоимостей событий, без повторного округления слагаемых.
- **Схемы**: `per_1m_tokens` (4 вида) и `per_1m_tokens_unified` (единая
  ставка `any`), см. `CostCalculator::eventCost()`.
- **Выбор цены**: наибольшее `valid_from <= ts` (включительно).
- **Оф-пик**: окно `[start, end)` по минутам UTC, поддерживен переход через
  полночь (`16:30–00:30`); множитель применяется к **всему** событию,
  один раз, до единого округления.
- **Время**: строгий ISO-8601 с явным смещением/`Z` → нормализация в UTC
  (`Iso8601Utc`); хранение — `timestamptz`.

Персистентность: сущности `Provider`, `Model`, `PriceRow`, `UsageEvent`
(`src/Persistence/Entity`); уникальные индексы обеспечивают контракт
идемпотентности на уровне БД. Схема — в миграции
`src/Persistence/Migration/Version20260203000001.php`
(каталог включён в автозагрузку — см. отклонения).

## Ключевые решения и допущения

1. **Статусы события**: `pending` (не считали), `unrated` (считали, цены
   ещё не было), `rated`. Каждый запуск `app:billing:rate` повторно
   пробует и `pending`, и `unrated` (п. 7 задания: цены могли подтянуться позже).
   `unrated` — нормальное повторяемое состояние, не ошибка.
2. **Механика reprice** — перезапись `billed_cost` на месте (без журнала и
   компенсирующих записей; задания не требуют журнала, зато требуют
   идемпотентность и отсутствие задвоений, что проще гарантировать
   перезаписью). Заменённые строки цен и все события, завязанные на них,
   пересчитываются от исходного количества токенов и `valid_from` события;
   `REPRICE_DELTA_USD = Σ new − Σ old` по тем событиям. Повторный reprice
   корректной строкой даёт дельту 0 и отчёт без изменений (события не
   дублируются).
3. **Повторная загрузка строки цены** (тот же `(provider, model,
   valid_from)`) молча заменяет значения — «повторная загрузка тех же
   сумм не изменяет данных» (upsert), разные суммы — обновление справочника.
   Уже тарифицированные события от этого не трогаются — их меняет только
   `app:billing:reprice`.
4. **Валидация входных файлов** — строгая: типы, непустые коды, ISO-8601,
   неотрицательные целые токены (дополнительно принимаем JSON-числа,
   которые PHP отдаёт как целые float, — например, большие значения;
   дробные — ошибка), ставки — неотрицательные decimal-строки,
   `multiplier > 0`. Ошибки — с точным контекстом (`prices[0]`,
   `строка 2`).
5. **stdout-контракт**: служебные сообщения всегда в stderr; stdout —
   только данные команды (CSV или строго заданные строки) — удобно для
   скриптов.
6. **Отчёт**: сортировка групп — `user`, затем `model`
   лексикографически; суммы токенов — целые; `cost_usd` — ровно 6 знаков;
   `TOTAL` с пустым полем `model`.
7. **Диапазон отчёта**: `--from` включительно, `--to` исключительно;
   без опций — все `rated`-события.

## Отступления от каркаса

Каркас не менял, кроме зафиксированного ниже.

1. **`config/services.yaml`** — добавлены:
   - `resource ../src/Command/` + теги `console.command` для пяти команд
     (без этого консольные классы не регистрируются;
     автоавайр-инжекция уже настроена и покрывает зависимости);
   - `_instanceof`-теги `doctrine.service` и
     `doctrine.repository_service` для `src/Application` и
     `src/Persistence/Repository` (чтобы репозитории и импортеры
     автоавайривались).
   Причина: стандартного способа объявления команд/репозиториев в каркасе
   не было.
2. **`config/packages/doctrine_migrations.yaml`** — namespace
   `App\Persistence\Migration`, directory `src/Persistence/Migration`.
   Причина: миграция должна лежать в автозагружаемом PSR-4 namespace
   (`App\`), а стандартный `DoctrineMigrations` в этом проекте не
   автозагружается (composer.json только `App\`). Существующий пустой
   `migrations/` в корне оставлен нетронутым (он не используется
   конфигурацией).
3. **`README.md`** — добавлен раздел «Команды» с примером прогона на
   fixtures. Причина: быстрый старт в README говорит про окружение, но не
   про сами операции; раздел закрывает разрыв и не меняет поведение.
4. **Новые каталоги**: `src/Application`, `src/Command`, `src/Domain`,
   `src/Persistence`, `tests/` — рабочая область по AGENTS.md
   («новый код — в `src/`, тесты — в `tests/`).

Зависимости не добавлял: использованы только уже настроенные `bcmath`,
`pdo_pgsql`, `symfony/console`, `doctrine/orm`, `doctrine/dbal`,
`doctrine/migrations`, `doctrine/doctrine-bundle`, PHPUnit.

## Как проверяли

Скрипт приёмки (весь прогон проходит на чистой БД, `make test` зелёный):

```bash
make db-up        # docker compose up -d + pg_isready — «PostgreSQL готов»
make migrate      # doctrine:database:create --if-not-exists + migrations:migrate -n
php bin/console doctrine:schema:validate
#   [OK] The mapping files are correct.
#   [OK] The database schema is in sync with the mapping files.
```

Приемка по TASK.md:

```bash
php bin/console app:prices:import fixtures/prices.json
php bin/console app:usage:import fixtures/usage.jsonl
php bin/console app:billing:rate
# stdout: RATED=3 UNRATED=0
php bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# (сравнение с примером из TASK.md — байт-в-байт)
# повторный прогон тех же трёх команд + report — вывод тот же:
#   импорт цен   — «Импорт завершён…» (exit 0)
#   импорт usage — «…дубликатов пропущено — 3.»
#   rate         — RATED=0 UNRATED=0
#   report       — тот же CSV
```

Дополнительная проверка:

```bash
# unrated-поворот (сцена 7 задания): добавить событие с ещё не существующей
# строкой цены (например, model "lumen-mini" от 2026-07-01) и выполнить
# php bin/console app:billing:rate
#   stdout: RATED=0 UNRATED=1
#   stderr: UNRATED prism/unr1 2026-07-01T09:00:00Z no_price
# после подтягивания цены (app:prices:import …) повторный
# app:billing:rate даёт RATED=1 UNRATED=0.

# reprice (бонус): исправленная строка lumen-8 с 2026-08-01
# (input 2.000000, output 12.000000, cache_read 0.300000, cache_write 3.750000)
php bin/console app:billing:reprice /tmp/correction.json
#   REPRICED_EVENTS=1
#   REPRICE_DELTA_USD=-1.200000
# второй запуск той же коррекции:
#   REPRICED_EVENTS=1
#   REPRICE_DELTA_USD=0.000000
#   (отчёт после reprice — пересчитанные суммы, дублей нет)

# формат-ошибки:
#   php bin/console app:prices:import {invalid}.json;  echo $?   # 1
#   php bin/console app:usage:import {invalid}.jsonl ;  echo $?  # 1
#   причины — в stderr (с контекстом строки/поля)
```

Тесты (45, зелёные):

```bash
make test         # vendor/bin/phpunit
```

Накрыто: `Decimal` (арифметика, HALF_UP, деление, форматирование),
`Iso8601Utc` (строгие валидации, смещение, reanchor), `OffPeakWindow`
(включая полночь и края), `CostCalculator` (per-token, unified,
оф-пик, округление), парсеры (валидные и все ошибочные ветки),
`ReportBuilder` (агрегация, формат, `TOTAL`, диапазон).

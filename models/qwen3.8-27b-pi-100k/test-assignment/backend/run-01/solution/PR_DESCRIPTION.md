# PR: учёт расходов на LLM-токены по истории цен (консольное приложение)

## Что сделано

Полная реализация задания из `TASK.md` на PHP 8.4 + Symfony Console +
Doctrine ORM/Migrations + PostgreSQL:

| Команда | Назначение |
| --- | --- |
| `bin/console app:prices:import <file.json>` | импорт каталога цен (провайдеры / модели / строки цен), идемпотентный |
| `bin/console app:usage:import <file.jsonl>` | импорт событий расходования, дубликаты `(provider, provider_request_id)` молча игнорируются |
| `bin/console app:billing:rate` | тарификация всех `pending`-событий; без цены — `unrated` + причина, повторные попытки на следующем запуске |
| `bin/console app:billing:report [--from=ISO8601Z --to=ISO8601Z]` | CSV-отчёт по `(user, model)` с итогом `TOTAL` (stdout) |
| `bin/console app:billing:reprice <file.json>` | бонус ТЗ: ретро-коррекция строки цены, пересчёт затронутых событий, `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<±x.xxxxxx>` |

Ключевые требования ТЗ обеспечены:

- **Точность денег** — все стоимостные значения — decimal-строки,
  арифметика `bcmath` (рабочий масштаб 20), `float` в деньгах не
  используется нигде; цена/модель/событие хранятся текстом;
- **Единственное округление** — один `HALF_UP` до 6 знаков на стоимость
  события; сумма в отчёте — точное сложение уже округлённых значений
  (масштабирование токенов `tokens/1 000 000*rate` выполняется в точной
  арифметике до округления);
- **Выбор цены** — наибольшее `valid_from <= ts` для пары
  `(provider, model)`; строки без подходящей версии цены не «откатываются»
  на соседние — событие ждёт (`no_price`), и следующий `rate` повторяет
  попытку;
- **Оф-пик** — окно провайдера в UTC (может пересекать полночь) с
  множителем; флаг «скидка применена» сохраняется в событии;
- **Идемпотентность** — повторный импорт не дублирует сущности
  (upsert по естественным ключам; при повторном `rate` уже рассчитанные
  события не пересчитываются);
- **Отчёт** — период `[from, to)`, без опций — все события; порядок
  групп: `user` ASC, затем `model` по алфавиту (как в примере ТЗ);
  формат — 6 знаков, `TOTAL` — сумма всех событий периода.

## Ключевые решения и допущения

1. **Хранение денег — точные decimal-строки**:
   ставки цен — JSONB из decimal-строк, итог события `cost_usd` — decimal-
   строка; суммирование в отчёте — точное `SUM` чисел, заранее округлённых
   до 6 знаков; в PHP — только bcmath (рабочий масштаб 20), `float` в
   деньгах нигде не используется (импорт → расчёт → отчёт → reprice).
2. **Уникальность события** — составной ключ
   `(provider, provider_request_id)`; повторная доставка того же
   `provider_request_id` молча игнорируется (сообщается количество).
   Логика «новое vs повторно доставленное» — `ON CONFLICT DO NOTHING`
   при вставке, без чтения «из прошлого» (дешёво и атомарно).
3. **Состояние события** — `status` ∈ {`pending`, `rated`, `unrated`};
   `rate` берёт только `pending`, поэтому повторный запуск ничего не
   меняет, а unrated-события добираются следующем запуском; причина
   `no_price` по ТЗ печатается в stderr.
4. **Схема БД** — одна миграция Doctrine
   (`migrations/Version20261016125000.php`), разворачивается на чистой
   базе: `make db-up && make migrate` (или
   `bin/console doctrine:database:create --if-not-exists && bin/console
   doctrine:migrations:migrate -n`).
5. **Reprice (бонус)** — механизм: после проверки «исправленный файл —
   тот же формат» происходит upsert строки цены, затем пересчитываются
   события, тарификационные которых выбрали именно эту строку (по записи
   `price_row_id` — ссылка на выбранную строку цены); стоимость события
   перезаписывается,
   дельта = новая − старая сумма (может быть отрицательной, знак
   сохраняется). Итоговый `report` показывает пересчитанные суммы без
   задвоений. Альтернатива «журнал корректировок + компенсирующие
   записи» в ТЗ допустима, но избыточна для консольного учётного
   журнала: выбран прямой пересчёт с атомарной транзакцией.
6. **Именование колонок** — `user` в PostgreSQL — зарезарвированное
   слово; колонка называется `user_ref`, а в CSV выводится именно как
   `user` (формат отчёта ТЗ сохранён байт-в-байт).
7. **Время** — все метки времени событий и цен хранятся/обрабатываются
   в UTC (ISO-8601, `Z`); оф-пик окно провайдера интерпретируется в UTC
   (явное допущение — в ТЗ окно указано как «оф-пик провайдера»,
   локаль провайдера не задана; в файлах фиксюр окно 00:30–16:00,
   переход через полночь поддерживается логикой).
8. **Отсутствие провайдера/модели в справочнике при импорте событий** —
   событие сохраняется как есть (задача не требует жёсткого FK-режима на
   справочник), но `rate` не сможет выбрать цену и оставит `unrated`.
9. **Валидация на входе** — парсеры отклоняют файл целиком с понятным
   сообщением (пополнение, строка JSONL); допустимые «чёрные дыры»
   (незнакомые ключи в JSON, `null` в `off_peak`) описаны в коде
   парсеров и покрыты тестами.

## Отступления от каркаса

- **Новые файлы** (все — в рамках каркаса; каркасные файлы
  `TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`, `composer.json`,
  `composer.lock`, `docker-compose.yml`, `Makefile`, `phpunit.xml.dist`
  не изменялись):
  - `src/Kernel.php`, `config/*` (Doctrine/Services/Migrations),
  - `src/Entity/*` — `Provider`, `Model`, `PriceRow`, `UsageEvent`;
  - `src/Import/*` — парсеры (`PriceFileParser`, `UsageFileParser`),
    спецификации (`*Spec`), импортеры (`PriceImporter`, `UsageImporter`),
    `ImportException`;
  - `src/Billing/*` — `CostCalculator` (два формата тарифа + оф-пик),
    `OffPeak`, `RateEngine`, `RepriceService`, `ReportService`;
  - `src/Command/*` — 5 команд;
  - `src/Support/Decimals.php` (bcmath-обёртка: арифметика, `HALF_UP`,
    форматирование), `src/Support/Iso8601.php`;
  - `migrations/Version20261016125000.php` — схема БД;
  - `tests/` — 7 файлов (unit + e2e).
- **Зависимости** — новые пакеты не добавлялись; использованы только
  уже объявленные в `composer.json` (Symfony Console/Doctrine, bcmath —
  системное расширение PHP).
- **`public/index.php`** отсутствует — приложение консольное, HTTP не
  требуется (в ТЗ HTTP API в «За рамками»).
- **Тестовая БД** — e2e-тест создаёт и разбирает `app_test`
  (отдельная база рядом с `app`) и поднимает/разбирает её сам,
  поэтому `vendor/bin/phpunit` работает без ручных шагов.

## Как проверяли

```bash
make db-up                     # PostgreSQL 17 (docker-compose, каркас)

# 1) чистая база + схема
rm -f /var/lib/pg/app          # (в sandbox: DROP/CREATE DATABASE app)
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate -n

# 2) импорты и тарификация
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# → RATED=3 UNRATED=0

# 3) отчёт — байт-в-байт пример из ТЗ
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# →
# user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
# u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
# u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
# u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
# TOTAL,,6200000,2300000,2500000,83333,33.662499

# 4) идемпотентность
bin/console app:usage:import fixtures/usage.jsonl   # повторно доставленных: 3 (0 новых)
bin/console app:prices:import fixtures/prices.json  # upsert, без дублей
bin/console app:billing:rate                        # → RATED=0 UNRATED=0
bin/console app:billing:report --from=... --to=...  # тот же байт-в-байт результат

# 5) бонус reprice (на сценарии «цена выросла»):
#    REPRICED_EVENTS=1, REPRICE_DELTA_USD=0.929166; повторный запуск —
#    дельта 0 (идемпотентность), report без задвоений.

# 6) автоматические тесты (48/48, включая e2e на чистой app_test)
vendor/bin/phpunit
```

Покрошенность проверок: арифметика и округление (`DecimalsTest`,
`CostCalculatorTest`, `OffPeakTest`), валидация и отклонение файлов
(`PriceFileParserTest`, `UsageFileParserTest`), полный сценарий ТЗ
включая unrated→retry, идемпотентность и reprice (`EndToEndTest`,
чистая `app_test` БД, stdout команд сверяется строка в строку).

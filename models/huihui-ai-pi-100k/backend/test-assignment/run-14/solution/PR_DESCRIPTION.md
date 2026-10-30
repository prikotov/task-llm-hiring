# PR: учёт расходов на LLM-токены

## Что сделано

Реализация полного сценария задания (TASK.md):

1. **Схема БД** — 4 сущности: `providers`, `models`, `prices`, `usage_events`
   (связи 1:N). Уникальные ключи: `(code)` у providers, `(provider, code)` у
   models, `(model, valid_from)` у prices. `usage_events` имеет
   `priced_by` (FK) и `replaces_event_id` (FK self) для механизма
   «ретро-коррекции» (бонус).

2. **Импорт цен** (`app:prices:import <file>`):
   - idempotent upsert по (provider, code), (provider, model code),
     (model, valid_from).
   - off_peak-окно сохраняется как `(start_minutes, end_minutes,
     multiplier, enabled)`; окно, переходящее через полночь, кодируется
     `start > end` (например 16:30 → 990, 00:30 → 30).

3. **Импорт событий** (`app:usage:import <file>`):
   - дедупликация по `(provider, provider_request_id)`;
   - повторная поставка того же события — игнорируется (counted дубли,
     но не вставлено).

4. **Тарификация** (`app:billing:rate`):
   - выбор цены: наибольшее `valid_from ≤ ts` для модели;
   - схема per-token: `Σ tokens_вид × rate_вид / 1 000 000`;
   - схема unified: `(Σ все tokens) × rate_any / 1 000 000`;
   - оф-пик: если `minutes(ts) ∈ [start, end)` провайдера события —
     стоимость события умножается на `multiplier`;
   - точная десятичная арифметика (bcmath, scale 16);
   - HALF_UP-округление до 6 знаков — ровно одно, на уровне события;
   - unrated: событие, у которого нет строки цены (по модели и ts) —
     статус `pending`, при повторном запуске повторяется попытка.
   - Вывод: `RATED=<n> UNRATED=<m>` в stdout,
     `UNRATED <provider>/<req> <ts> no_price` в stderr.

5. **Отчёт** (`app:billing:report [--from=..] [--to=..]`):
   - по паре (user, model), отсортировано по user, затем по model;
   - `model` выводится как `provider/code`;
   - токены — суммы по событиям группы;
   - `cost_usd` — сумма уже-округлённых стоимостей событий группы,
     ровно 6 знаков;
   - TOTAL-строка в конце, суммарные токены и стоимость;
   - `--from` включительно, `--to` исключительно.

6. **Ретро-коррекция цены** (бонус) `app:billing:reprice <file>`:
   - upsert строки цены по `(provider, model, valid_from)`;
   - пересчёт событий, закреплённых за этой строкой (через прайс-ссылку
     `usage_events.priced_by`);
   - вывод: `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`
     (6 знаков, новая минус старая сумма).

## Ключевые решения и допущения

- **Схема БД**: `user` — SQL-резервированное слово. В `usage_events`
  колонка называется `user_id` (через ORM `name: 'user_id'`), а
  в PHP-свойстве — `$user` (getter `getUser()`). Внешне это выглядит как
  поле «user»; в DDL — `user_id`.
- **Безопасность**: все расчёты — bcmath; масштаб 16; единственное
  округление — на уровне события (HALF_UP, 6 знаков).
- **Импорт**: строки `prices` idempotent по `(provider, model,
  valid_from)`; новые `valid_from` в будущем добавляются; повторная
  загрузка тех же строк без изменений — не меняет данных.
- **Reprice**: механизм — in-place пересчёт стоимости события
  (поле `costUsd`), без журналов коррекции (по заданию — «механизм
  пересчёта — на ваше усмотрение»). После reprice `app:billing:report`
  показывает пересчитанную стоимость.
- **Импорт событий**: `replaces_event_id` и `priced_by` заполняются
  автоматически (ссылка на price, записанную при расчёте). Для unrated
  `priced_by` = NULL.
- **Исключение**: если модель существует в `models`, но её провайдер
  (provider.code) в событии не совпадает с тем, что в справочнике —
  событие считается unrated (нет цены для модели с таким provider).

## Отступления от каркаса

### Изменённые файлы

Ничего из каркаса (TASK.md, AGENTS.md, README.md, fixtures/*,
composer.json / composer.lock, docker-compose.yml, Makefile,
phpunit.xml.dist) не менялся.

- `config/services.yaml` — добавлены `default_autoconfigure`,
  `default_autowire` (для DI).

### Добавленные файлы

- `migrations/Version20261027145401.php` — DDL на 4 таблицы (providers,
  models, prices, usage_events) + индексы.
- `src/Application/Billing/*` — Rater, EventPricing, ReportBuilder,
  Repricer (домен + арифметика + отчёт + reprice).
- `src/Application/Import/*` — PricesImporter, UsageImporter.
- `src/Command/*` — 5 консольных команд.
- `src/Domain/*` — PricingUnit, OffPeakWindow, UsageTokens, EventKey
  (доменные значения).
- `src/Entity/*` — Provider, Model, Price, UsageEvent (ORM-сущности).
- `src/Repository/*` — 4 репозитория.
- `tests/Application/EventPricingTest.php`,
  `tests/Domain/OffPeakWindowTest.php` — unit-тесты (8 тестов).
- `COMMIT_MESSAGE.md` — commit message.

### Альтернативные решения (и почему выбрано текущее)

- **Имя колонки `user` / `user_id`**: SQL `user` — рез. слово, поэтому
  `user_id` (в DDL). В PHP-коде — `user` — для API-совместимости с
  форматом ввода/вывода задания.
- **Механизм reprice**: in-place (без журнала коррекции). Чистый,
  простой, полностью покрывает требования «итоговый report —
  пересчитанные суммы без задвоений».
- **Точность**: bcmath (без float для денег) + один HALF_UP-округление.
  Это прямо из задания (п. 4): `bcmath с масштабом ≥ 16`,
  «округление ровно одно: итоговая стоимость события … до 6 знаков …
  HALF_UP».
- **Импорт цен — idempotent по (provider, model, valid_from)**:
  повторная загрузка тех же строк — не меняет данных. Новые `valid_from`
  добавляются. Старее-старых `valid_from` не удаляются.
- **Окно оф-пика, переходящее через полночь**: `start > end`;
  `contains(minute)` = `minute >= start || minute < end` (в этом
  случае).
- **Unrated-события**: хранятся в БД с `status='pending'`, в отчёте не
  участвуют, повторный `rate` — повторяет попытку (если цена
  подтянулась позже).

## Проверка (приёмка)

1. `make db-up` — PostgreSQL 17 поднят, `pg_isready` — OK.
2. `docker compose exec -T db psql -U app -d app -c "TRUNCATE ...;"` —
   чистая база (или `dropdb` + `make migrate`).
3. `make migrate` — схема развёрнута (1 миграция, 12 SQL).
4. `bin/console app:prices:import fixtures/prices.json` —
   `providers: 2, models: 3, prices: 4`.
5. `bin/console app:usage:import fixtures/usage.jsonl` — `строк: 3,
   вставлено: 3, дубли: 0`.
6. `bin/console app:billing:rate` — `RATED=3 UNRATED=0`.
7. `bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z` —
   ```
   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
   u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
   u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
   u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
   TOTAL,,6200000,2300000,2500000,83333,33.662499
   ```
   — совпадает с примером из TASK.md.
8. Повторный импорт тех же файлов + rate + report — результат не
   меняется (idempotency).
9. `vendor/bin/phpunit` — 8 unit-тестов, все OK.
10. Бонус: `app:billing:reprice` (с файлом коррекции) — работает,
    итоговый report — с пересчитанными суммами.

## Как проверять в своём окружении

```
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

Для повторного запуска с нуля:

```
docker compose exec -T db psql -U app -d app -c "TRUNCATE usage_events, models, providers, prices RESTART IDENTITY CASCADE;"
```

(или `dropdb app` перед `make migrate`).

# PR: учёт расходов на LLM-токены

## Что сделано

Реализовано консольное приложение на Symfony 8 / Doctrine ORM / PostgreSQL 17,
которое принимает историю цен и события использовании и считает расходы —
включая бонусную команду ретро-коррекции цен.

### Доменная модель (DDD-стиль)
- `App\Domain\Provider\Provider` — провайдер + оф-пиковое окно
  (`window_start`/`window_end`/`multiplier`), содержит методику проверки
  попадания времени в окно (с переходом через полночь).
- `App\Domain\Model\Model` — модель (уникальность по `(provider, code)`).
- `App\Domain\Model\PriceLine` — строка цены (уникальность по
  `(provider, model, valid_from)`), хранит `unit` и ставки в JSON-текстовом
  виде (decimal-строки).
- `App\Domain\Usage\UsageEvent` — событие (уникальный естественный ключ
  `(provider, provider_request_id)`), со статусом `rated`/`unrated`,
  расчётной стоимостью и ссылкой на использованную строку цены.

### Учётная логика
- `App\Billing\Money` — точная десятичная арифметика (bcmath, scale ≥ 16) и
  округление HALF_UP до 6 знаков.
- `App\Billing\BillingService` — выбор цены (максимальный `valid_from ≤ ts`),
  расчёт стоимости по двум схемам, оф-пиковый множитель, дедупликация,
  отчёт, ретро-пересчёт.

### Команды
- `app:prices:import <file>` — идемпотентный upsert справочника.
- `app:usage:import <file>` — импорт событий с дедупликацией по
  `(provider, provider_request_id)` (повтор — игнорируется целиком).
- `app:billing:rate` — тарификация всех ещё не рассчитанных (stdout:
  `RATED=<n> UNRATED=<m>`, stderr: `UNRATED <prov>/<id> <ts> no_price`).
- `app:billing:report [--from=] [--to=]` — CSV в stdout (байт-в-байт совпадает
  с примером задания).
- `app:billing:reprice <file>` — бонус: upsert исправленных строк,
  пересчёт затронутых событий, stdout: `REPRICED_EVENTS=<n>` и
  `REPRICE_DELTA_USD=<x.xxxxxx>`.

### Схема БД
Одна migration (`migrations/Version*.php`), создаётся автоматически.
Поднимается на чистой базе `make migrate` (default). Проверено:
`make db-up` + `make migrate`.

## Ключевые решения и допущения

1. **Деньги только decimal-строки (bcmath).** Нигде нет float. Промежуточные
   вычисления — со запасом точности 16 знаков; единственное округление —
   стоимость события до 6 знаков HALF_UP (добавление `0.5·10⁻⁶` и обрезка).
   Суммы отчёта складываются уже-округлённые стоимости без повторно
   округления (`bcadd`, scale 6).
2. **Статусы событий — два: `unrated`/`rated`.** `unrated` = «ещё не
   рассчитан». Каждый `app:billing:rate` пытается рассчитать все `unrated`,
   так что повторная подтяжка цен автоматически доприсваивает unrated.
3. **Событие хранит коды провайдера/модели строками** (а не FK) — это
   естественно для «журнала провайдера», упрощает дедупликацию по
   естественному ключу и независимость от домена справочника. Отчётная
   группировка идёт по этим строкам (`provider/model`).
4. **Ретро-пересчёт.** Определяются строки, уже существующие в БД и
   «замещаемые» файлом коррекции (по `(provider, model, valid_from)`), затем
   пересчитываются только `rated`-события, ссылку-щиеся на них. Секции
   `providers`/`models` в файле коррекции опциональны (проверяются по БД).
   Итог — простое перезаписание + корректная дельта, без задвоений в отчёте.

## Как проверяли (акцептация)
```bash
make db-up     # postgres 17 на 127.0.0.1:5544
make migrate   # чистая БД
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import  fixtures/usage.jsonl
bin/console app:billing:rate                    # RATED=3 UNRATED=0
bin/console app:billing:report \
    --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   выдал точно ожидаемый CSV (проверено diff'ом — byte-identical)
# повторный импорт + rate + report — результат не меняется (RATED=0 UNRATED=0)
vendor/bin/phpunit    # 27 тестов, 29 ассерций — OK
```
Доп. проверки: bad-JSON → exit 1; оф-пик (23:00 → discount; 00:30 → граница
исключается; 00:29 → discount); дубль `provider_request_id` у разных
провайдеров = разные события; partial `--from/--to` (in/exclusive).

## Отступления от каркаса

- Добавлен `migrations/Version*.php`. Автогенератор эмитит
  `CREATE SCHEMA public`, а на PostgreSQL схема `public` уже существует —
  заменено на `CREATE SCHEMA IF NOT EXISTS public`.
- Колонка события `user` (из-за зарезервированного слова в PostgreSQL)
  переименована на уровне БД в `user_code` (property/поле в DQL —
  `UsageEvent.user`).
- Все остальные файлы каркаса (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`,
  `composer.json`, `docker-compose.yml`, `Makefile`, `phpunit.xml.dist`,
  `config/packages/*`) не изменялись. Зависимости не добавлялись (всё уже
  в `vendor/`).

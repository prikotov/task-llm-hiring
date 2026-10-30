# Pull Request Description

## Что сделано
Реализована базовая доменная логика учёта расходов на LLM-токены:
- Сущности Doctrine: `Provider`, `Model`, `Price`, `UsageEvent`.
- Команды Symfony Console:
  * `app:prices:import <file>` — загрузка провайдеров, моделей и цен с идемпотентным upsert.
  * `app:usage:import <file>` — загрузка JSONL событий с дедупликацией по `(provider, provider_request_id)`.
  * `app:billing:rate` — тарификация нерассчитанных событий, выбор цены по `valid_from <= ts` max, учёт off-peak окна провайдера, расчёт стоимости с точностью bcmath, округление события HALF_UP до 6 знаков, вывод сводки и ошибок unrated в stderr.
  * `app:billing:report [--from] [--to]` — CSV отчёт с группировкой по user/model, суммами токенов и стоимостей, строка TOTAL.
- Миграции Doctrine для создания схемы.
- Точность денег через bcmath, `user` колонка переименована в `user_name` во избежание конфликта с зарезервированным словом PostgreSQL.

## Ключевые решения и допущения
- Цены хранятся отдельными колонками `rate_input/output/cache_read/cache_write/rate_any`.
- Off-peak окно проверяется по UTC времени `ts` с поддержкой перехода через полночь.
- Округление реализовано вручную через масштабирование и `bcadd(..., '0.5')`, HALF_UP.
- Отчёт суммирует уже округлённые стоимости событий, повторного округления нет.
- Идемпотентность импорта обеспечивается поиском по естественным ключам.
- `unrated`-события остаются с `cost_usd = NULL` и повторно обрабатываются при каждом `rate`.

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером из задания байт-в-байт. Повторный импорт не меняет данные.

## Отступления от каркаса
- Добавлены сущности и миграции в `src/Entity/` и `migrations/`.
- Добавлены команды в `src/Command/`.
- Колонка `user` переименована в `user_name` (PostgreSQL reserved word).
- Не добавлены новые зависимости.

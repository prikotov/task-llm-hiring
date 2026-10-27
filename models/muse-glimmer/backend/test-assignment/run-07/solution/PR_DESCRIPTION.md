# Реализация учёта расходов на LLM-токены

## Что сделано
- Схема БД (Doctrine ORM, PostgreSQL): `providers`, `models`, `prices`, `usage_events`
  - `providers` хранит off-peak окно и multiplier
  - `prices` с JSON rates, `valid_from` для тарификации по времени
  - `usage_events` с уникальным ключом `(provider_code, provider_request_id)`, поле `cost_usd` nullable
- Команды:
  - `app:prices:import <file>` — upsert провайдеров, моделей, цен
  - `app:usage:import <file>` — импорт JSONL с дедупликацией
  - `app:billing:rate` — тарификация нерассчитанных событий, выбор цены `max valid_from <= ts`, расчёт по схемам `per_1m_tokens` / `per_1m_tokens_unified`, оф-пик скидка, округление HALF_UP до 6 знаков, вывод `RATED/UNRATED`, ошибки unrated в stderr
  - `app:billing:report [--from] [--to]` — CSV отчёт с агрегацией по user/model, суммы токенов и стоимостей, итоговая строка TOTAL
- Арифметика: bcmath с масштабом ≥16, округление реализовано через умножение+0.5, float не используется

## Ключевые решения и допущения
- `user` переименован в колонку `user_name` из-за зарезервированного слова PostgreSQL
- Off-peak окно проверяется в минутах суток, корректно обрабатывается переход через полночь
- Повторный импорт не меняет данных, дубли событий игнорируются
- Unrated события остаются с `cost_usd NULL` и повторно пробуются при следующем запуске
- Отчёт группирует через DQL, сортировка по user затем provider/model

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером задания байт-в-байт, повторные запуски идемпотентны.

Отступления от каркаса: нет, добавлены только src/Entity, src/Command, миграции.

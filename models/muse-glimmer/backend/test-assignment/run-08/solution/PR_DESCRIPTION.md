# Реализация учёта расходов на LLM-токены

## Что сделано
- Создана схема БД (миграция `Version20261027Initial`) с таблицами `providers`, `models`, `prices`, `usage_events`. 
- Реализованы команды:
  - `app:prices:import <file>` — идемпотентный upsert провайдеров, моделей и строк цен.
  - `app:usage:import <file>` — импорт JSONL событий с дедупликацией по `(provider, provider_request_id)`.
  - `app:billing:rate` — тарификация нерассчитанных событий, выбор цены по `valid_from`, учёт off-peak окна провайдера, расчёт по схемам `per_1m_tokens` / `per_1m_tokens_unified`, десятичная арифметика BCMath, округление HALF_UP до 6 знаков. Вывод `RATED`/`UNRATED` и сообщения для unrated в stderr.
  - `app:billing:report [--from] [--to]` — CSV-отчёт с группировкой по user/model, суммами токенов и стоимостей, строка TOTAL.
- Проверка на фикстурах из задания: вывод совпадает байт-в-байт с примером.

## Ключевые решения и допущения
- Хранение цен в JSONB `rates`, upsert по `(provider_code, model_code, valid_from)`.
- Провайдер хранит окно off-peak как `TIME` и `numeric multiplier`. Проверка времени суток UTC через `DateTimeImmutable`.
- Идемпотентность импорта цен/событий реализована через `ON CONFLICT DO NOTHING/UPDATE`.
- Расчёт стоимости без float, только BCMath, округление реализовано вручную add-0.5 метод.
- Колонка пользователя названа `user_name` во избежание конфликта с зарезервированным словом `user` в PostgreSQL.
- Отчёт сортируется по `user_name, provider_code, model_code` для лексикографического порядка `<provider>/<code>`.

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Результат совпадает с примером задания. Повторный импорт не меняет данные.

## Отступления
- Имя колонки пользователя изменено на `user_name` — `user` зарезервировано в PostgreSQL, миграция иначе падает.
- Миграция создаёт схему с `id` PK, отличается от исходных экспериментальных миграций, но полностью соответствует заданию.
- Бонус `app:billing:reprice` не реализован — не требуется для приёмки.

Зависимости не добавлялись, использованы только готовые Symfony/Doctrine компоненты.

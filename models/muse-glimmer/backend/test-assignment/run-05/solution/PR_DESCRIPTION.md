# Реализация учёта расходов на LLM-токены

## Что сделано
- Сущности Doctrine: Provider, Model, Price, UsageEvent с корректными ограничениями уникальности.
- Команды:
  * `app:prices:import` — upsert провайдеров, моделей, цен.
  * `app:usage:import` — идемпотентный импорт событий по ключу (provider, provider_request_id).
  * `app:billing:rate` — выбор цены (max valid_from <= ts), расчёт стоимости с bcmath, скидка off-peak, округление HALF_UP до 6 знаков, вывод RATED/UNRATED.
  * `app:billing:report` — агрегация по user/model, сортировка, CSV с TOTAL.
- Схема БД разворачивается через `doctrine:schema:update --force` (чистая база).
- Использована десятичная арифметика, без float.

## Допущения
- Колонка user переименована в user_id из-за зарезервированного слова PostgreSQL.
- Схема управления — schema:update, миграции не созданы (упрощение).
- Команда reprice (бонус) не реализована.

## Проверка
```bash
make db-up
bin/console doctrine:schema:update --force
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером.

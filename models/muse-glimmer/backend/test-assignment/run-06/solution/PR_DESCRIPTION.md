# Реализация учёта расходов на LLM-токены

## Что сделано
- Созданы Doctrine-сущности: Provider, Model, Price, UsageEvent.
- Миграции разворачивают схему на чистой БД.
- Команды:
  - `app:prices:import` — идемпотентный upsert провайдеров/моделей/цен.
  - `app:usage:import` — импорт JSONL с дедупликацией по (provider, provider_request_id).
  - `app:billing:rate` — тарификация нерассчитанных событий, выбор цены по max valid_from ≤ ts, расчёт по схемам per_1m_tokens / per_1m_tokens_unified, оф-пик множитель, округление HALF_UP до 6 знаков, вывод RATED/UNRATED.
  - `app:billing:report` — CSV-отчёт с агрегацией по user+model, сортировка, TOTAL.
- Использована десятичная арифметика bcmath, float запрещён.
- Провайдер off_peak хранится как строки "HH:ii", окно может переходить через полночь.

## Допущения
- valid_from хранится как строка ISO-8601 для корректного сравнения без проблем с DateTimeImmutable в идентификаторах Doctrine.
- Колонка user переименована в user_name из-за зарезервированного слова PostgreSQL.
- Цены сравниваются лексикографически по ISO-строке.

## Проверка
```
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером задания. Повторный импорт не меняет результат.

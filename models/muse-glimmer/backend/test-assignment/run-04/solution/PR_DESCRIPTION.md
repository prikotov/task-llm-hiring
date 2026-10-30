# Реализация учёта расходов на LLM-токены

## Что сделано
- Схема БД: `providers`, `models`, `prices`, `usage_events` с уникальными ограничениями.
- Команды:
  - `app:prices:import <file>` — upsert провайдеров, моделей, цен
  - `app:usage:import <file>` — JSONL импорт с дедупликацией по (provider, provider_request_id)
  - `app:billing:rate` — тарификация нерассчитанных событий, выбор цены по max valid_from ≤ ts, оф-пик, округление HALF_UP до 6 знаков, вывод сводки
  - `app:billing:report [--from] [--to]` — CSV отчёт с группировкой по user/model, суммы токенов и cost_usd
- Вычисления через bcmath, без float.
- Обработка окна оф-пик через полночь.

## Ключевые решения и допущения
- Поле `user` в БД переименовано в колонку `user_name` из-за зарезервированного слова PostgreSQL.
- `cost_usd` хранится как decimal, `isRated` флаг.
- Unrated события выводятся в stderr как требуется.
- Для ретро-коррекции цены (бонус) реализация не добавлена.

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером задания. Повторный импорт не меняет результат.

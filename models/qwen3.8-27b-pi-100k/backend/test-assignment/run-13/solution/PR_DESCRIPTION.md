# PR DESCRIPTION

## Что сделано

Реализовано консольное приложение для учёта расходов на LLM-токены:

1. **Импорт цен** (`app:prices:import`) — парсит JSON, идемпотентно
   создаёт/обновляет таблицы `billing_provider`, `billing_model`,
   `billing_price_row`.

2. **Импорт событий** (`app:usage:import`) — парсит JSONL, дедуплицирует
   по (provider, provider_request_id), сохраняет в `billing_usage_event`.

3. **Тарификация** (`app:billing:rate`) — ищет непроработанные события,
   находит применимую строку цены (max valid_from ≤ ts), считает
   стоимость, применяет оф-пик, округляет (HALF_UP до 6 знаков),
   записывает CostRecord. Идемпотентен.

4. **Отчёт** (`app:billing:report`) — CSV в stdout, группировка по
   (user, provider/model), суммы токенов и стоимости, сортировка, TOTAL.
   Поддерживает фильтры `--from` / `--to`.

5. **Ретро-коррекция** (`app:billing:reprice`) — upsert строки цен,
   пересчёт связанных CostRecord, вывод delta.

## Ключевые решения

### Денежная арифметика
Используется `bcmath` (bcmul, bcdiv, bcadd, bcround):
- Базовый масштаб: 16 знаков (безопасный запас точности)
- Округление: `bcround` (ROUND_HALF_UP)
- В intermediate-расчётах — нет округления; единственное — итоговая
  стоимость события до 6 знаков
- Float для денег не используется

### Выбор цены
- `validFromEpoch` (integer timestamp) в таблице, индекс
  (provider, model, valid_from_epoch)
- Поиск: `valid_from_epoch <= ts_epoch ORDER BY valid_from_epoch DESC LIMIT 1`

### Равноправные единицы (token kinds)
Все четыре вида токенов имеют отдельные ставки при `per_1m_tokens`;
при `per_1m_tokens_unified` — одна ставка `any` для суммы всех токенов.

### Идемпотентность
- Провайдеры/модели/строки — upsert по естественным ключам
- События — `UNIQUE (provider, request_id)`, повторная вставка игнорируется
- Rate — проверяет существующий CostRecord для eventId
- Reprice — обновляет existing CostRecord, не создаёт новые

### Схема БД
Управление — `doctrine:schema:create` (без миграций, как допустимое от
характера задачи). Схема:

| таблица | ключи |
|---------|-------|
| billing_provider | id PK, code UK |
| billing_model | id PK, (provider_code, code) UK |
| billing_price_row | id PK, (provider, model, valid_from_epoch) UK |
| billing_usage_event | id PK, (provider, request_id) UK |
| billing_cost_record | id PK, event_id UK |

## Допущения и отступления

1. **`user` → `user_id`**: PostgreSQL reserved word, переименована в
   `user_id` для корректной работы без кавычек в DML.
2. **`valid_from_epoch` (int)** вместо `valid_from` (timestamp): проще
   сравнение, меньше проблем с часовыми поясами.
3. **Doctrine schema:create** вместо миграций: для тестового задания
   достаточно, и схема создаётся детерминированно.

## Проверка

```bash
# Чистая база
docker exec repo-db-1 psql -U app -d app \
  -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
bin/console doctrine:schema:create

# Импорт
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl

# Тарификация
bin/console app:billing:rate
# → RATED=3 UNRATED=0

# Отчёт
bin/console app:billing:report \
  --from='2026-09-01T00:00:00Z' \
  --to='2026-10-01T00:00:00Z'
# → byte-в-байт совпадает с примером в TASK.md

# Повторный импорт (идемпотентность)
bin/console app:prices:import fixtures/prices.json  # не изменяет
bin/console app:usage:import fixtures/usage.jsonl   # Вставки: 0, дубли: 3
bin/console app:billing:rate                        # RATED=0 UNRATED=0

# Ретро-коррекция
bin/console app:billing:reprice /tmp/reprice_fix.json
# → REPRICED_EVENTS=1, REPRICE_DELTA_USD=2.100000

# Тесты
vendor/bin/phpunit
# → OK (19 tests, 33 assertions)
```

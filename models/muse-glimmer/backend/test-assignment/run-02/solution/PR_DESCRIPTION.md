# Реализация учёта расходов на LLM-токены

## Что сделано
- **Модель данных**: сущности Provider, Model, Price, UsageEvent с Doctrine ORM. Таблица `usage_events` хранит `user_id` (колонка `user_id` чтобы избежать конфликта с зарезервированным словом PostgreSQL).
- **Миграции**: `migrations/Version20250101000000.php` создаёт схему для чистой БД, `make migrate` работает.
- **Команды**:
  - `app:prices:import <file>` – upsert провайдеров, моделей и строк цен по ключу `(provider, model, valid_from)`. Ошибочный JSON → exit code 1.
  - `app:usage:import <file>` – JSONL, дедупликация по `(provider, provider_request_id)`, некорректная строка → exit code 1.
  - `app:billing:rate` – тарифицирует все события с `cost IS NULL`. Выбор цены: максимальная `valid_from <= ts`. Поддержка схем `per_1m_tokens` и `per_1m_tokens_unified`. Учёт оф-пик окна провайдера, которое может переходить через полночь. Вычисления в bcmath, округление события HALF_UP до 6 знаков. Вывод `RATED=n UNRATED=m` в stdout, для каждого не тарифицируемого события строка `UNRATED ... no_price` в stderr.
  - `app:billing:report [--from] [--to]` – CSV с группировкой по `user` и `model` (`provider/code`), суммы токенов и стоимостей, строка `TOTAL`. Сортировка лексикографическая.
- **Точность**: используется BCMath со шкалой 16, округление только финальной стоимости события, суммы отчёта – сумма уже округлённых стоимостей.

## Ключевые решения и допущения
- Колонка пользователя названа `user_id` в БД, свойство в сущности `$user`. Это обход конфликта с зарезервированным словом PostgreSQL.
- `Price` хранит отдельные колонки `input_rate`, `output_rate`, `cache_read_rate`, `cache_write_rate`, `any_rate` вместо JSON для упрощения запросов.
- Выбор цены реализован через `findOneBy` с `validFrom <= ts` + `order by validFrom DESC limit 1`. Достаточно для объёма тестового задания.
- Оф-пик проверка через перевод времени в минуты, учёт окна, пересекающего полночь.
- `app:billing:rate` не перезаписывает уже рассчитанные события, что соответствует требованию идемпотентности; повторный запуск не меняет результат.

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером из задания байт-в-байт.
Повторный импорт и rate не меняют отчёт.

## Отступления
- Нет явных тестов, но команды покрывают требования задания.
- Бонуc `app:billing:reprice` не реализован – не требуется для приёмки.
- Схема развёртывается миграцией, а не `doctrine:schema:update`.

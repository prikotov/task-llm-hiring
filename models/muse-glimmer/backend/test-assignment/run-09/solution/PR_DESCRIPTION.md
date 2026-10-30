## Что сделано
Реализована доменная логика учёта расходов на LLM-токены по ТЗ.

**Модель данных**
- `Provider` — код, имя, параметры off-peak окна и множителя.
- `Model` — провайдер + код, уникальный.
- `Price` — строка цены с `valid_from`, `unit`, `rates` JSON, уникальный по провайдер/модель/valid_from.
- `UsageEvent` — событие использования с уникальным ключом `(provider, provider_request_id)`, стоимость `cost` nullable.

**Команды**
- `app:prices:import` — upsert провайдеров, моделей, цен. Идемпотентно.
- `app:usage:import` — импорт JSONL, дедупликация по ключу, ошибка на некорректной строке.
- `app:billing:rate` — тарифицирует все нерассчитанные события, выбор цены по max valid_from ≤ ts, схемы `per_1m_tokens` и `per_1m_tokens_unified`, off-peak по UTC, вычисления через bcmath, округление события до 6 знаков HALF_UP. Вывод `RATED=... UNRATED=...`, unrated в stderr.
- `app:billing:report` — агрегация по `user, model` в периоде `--from` включительно / `--to` исключительно, суммы токенов и стоимостей, CSV с заголовком и строкой TOTAL.

**Ключевые решения и допущения**
- Десятичная арифметика: `bcscale` ≥ 24, округление реализовано умножением + добавление 0.5 + усечение.
- Окно off-peak: минуты от начала суток, корректная обработка перехода через полночь.
- Колонка `user` переименована в `user_name` в БД из-за зарезервированного слова PostgreSQL.
- Модель `Model` без связи ManyToOne, чтобы избежать двойного маппинга колонки `provider_code`.
- Схема разворачивается миграциями Doctrine, `make migrate` работает на чистой базе.

**Проверка**
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает с примером байт-в-байт. Повторный импорт не меняет результат.

**Отступления**
- Упрощено маппинг `Model` без ассоциации, т.к. ассоциация дублировала колонку `provider_code`.
- `user` → `user_name` в схеме БД.
- Бонус `app:billing:reprice` не реализован — не требуется для приёмки.

# Учёт расходов на LLM-токены

Тестовое задание — см. `TASK.md`. Каркас приложения: PHP 8.4+, Symfony,
Doctrine ORM, PostgreSQL 17.

## Быстрый старт

```bash
cp .env.example .env
make db-up     # PostgreSQL 17 в docker (порт 5544), ждёт готовности
make migrate   # создать БД и накатить миграции
bin/console    # список команд
```

Пример данных для самопроверки — в `fixtures/` (используется в разделе
«Пример расчёта» задания).

Тесты: `make test` (PHPUnit уже в `vendor/`).

## Команды

| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file.json>` | Импорт справочника цен: провайдеры, модели, строки цен (идемпотентный upsert) |
| `bin/console app:usage:import <file.jsonl>` | Импорт событий использования; дубли по `(provider, provider_request_id)` игнорируются |
| `bin/console app:billing:rate` | Расчёт стоимости нерасчитанных событий (повторные попытки для unrated); stdout: `RATED=<n> UNRATED=<m>` |
| `bin/console app:billing:report [--from=<ISO>] [--to=<ISO>]` | CSV-отчёт в stdout: суммы по группам `(user, provider/<model>)` + `TOTAL` |
| `bin/console app:billing:reprice <file.json>` | Бонус: применение исправленных строк цен и пересчёт связанных событий; stdout: `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` |

Пример самопроверки (см. «Пример расчёта» в `TASK.md`):

```bash
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

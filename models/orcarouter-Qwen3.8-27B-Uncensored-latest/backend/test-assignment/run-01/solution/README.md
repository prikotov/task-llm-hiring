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

Тесты: `make test` (PHPUnit уже в `vendor/`); контекстный тест требует
запущенной БД (`make db-up`), иначе пропускается.

## Команды

```bash
bin/console app:prices:import <file.json>                  # каталог/история цен (идемпотентно)
bin/console app:usage:import <file.jsonl>                  # события (идемпотентно, без цена — pending)
bin/console app:billing:rate                               # тарификация pending → rated
bin/console app:billing:report --from <ts> --to <ts>       # CSV в stdout
bin/console app:billing:reprice <file.json>                # замена строки цен + пересчёт
```


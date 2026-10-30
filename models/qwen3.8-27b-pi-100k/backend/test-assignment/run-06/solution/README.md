# Учёт расходов на LLM-токены

Тестовое задание — см. `TASK.md`. Каркас: PHP 8.4+/8.5, Symfony 7 (console),
Doctrine ORM, PostgreSQL 17, bcmath для всех денежных операций (float не
используется).

## Быстрый старт

```bash
cp .env.example .env
make db-up     # PostgreSQL 17 в docker (порт из .env, по умолчанию 5544)
make migrate   # создать БД (если нет) и накатить миграции
bin/console    # список команд
```

## Команды

| Команда | Назначение |
| --- | --- |
| `app:prices:import <file.json>` | Импорт/обновление справочника: провайдеры (с окном off-peak — обновляются по code), модели, строки цен (upsert по `(provider, model, valid_from)`). Любая ошибка — откат всего файла. |
| `app:usage:import <file.jsonl>` | Импорт событий по JSONL. Дубликаты по `(provider, provider_request_id)` пропускаются (`inserted=N duplicates_skipped=M`). Невалидная строка — откат всего файла. |
| `app:billing:rate` | Тарификация нерассчитанных событий по последней цене `valid_from <= ts`. Событие без подходящей цены становится `unrated` (повторно проверяется при следующем проходе). Вывод: `RATED=N UNRATED=M`. |
| `app:billing:report [--from TS] [--to TS]` | CSV в stdout, группировка по `(user, model)` + строка `TOTAL`. Интервал по `ts`: `[from, to)`. Только рассчитанные события. |
| `app:billing:reprice <file.json>` | Бонус: ретро-коррекция цены (upsert тех же строк) + пересчёт событий, тарифицированных изменёнными строками. Вывод: `REPRICED_EVENTS=N`, `REPRICE_DELTA_USD=Δ`. |
| `doctrine:database:create`, `doctrine:migrations:migrate` | Развёртывание схемы в чистой БД (таблицами в БД управляем только миграциями). |

## Расчёт стоимости

- Промежуточные вычисления — bcmath со шкалой 24;
- **одно** округление HALF_UP до 6 знаков в хвосте — на событие;
- off-peak: окно по часам UTC `[start, end)`, может пересекать полночь;
  множитель применяется к итоговой стоимости;
- схемы `per_1m_tokens` (раздельные тарифы) и `per_1m_tokens_unified`
  (единый тариф на сумму всех категорий).

## Данные

`fixtures/prices.json` и `fixtures/usage.jsonl` — пример из раздела
«Пример расчёта» задания.

## Тесты

```bash
make test        # = vendor/bin/phpunit
```

- `tests/Domain/` — чистые доменные тесты (PricingCalculator, OffPeakWindow, UtcInstant);
- `tests/Integration/` — сквозные сценарии через консоль на реальной БД
  (приёмочный пример из TASK.md, идемпотентность, окна off-peak,
  unrated → rated, ретро-коррекция, дедупликация, откат битого файла,
  фильтры отчёта). Каждая серия тестов стартует с чистых таблиц.

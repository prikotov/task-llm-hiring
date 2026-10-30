# Описание PR: учёт расходов на LLM-токены

## Что сделано

Полная реализация задания из TASK.md:

| Команда | Назначение |
|---------|-----------|
| `app:prices:import <file>` | Импорт провайдеров, моделей и цен (идемпотентный upsert) |
| `app:usage:import <file>` | Импорт событий из JSONL (дедупликация по естественному ключу) |
| `app:billing:rate` | Тарификация нерасчётных событий |
| `app:billing:report` | CSV-отчёт с фильтрацией по периоду |
| `app:billing:reprice <file>` | **Бонус**: ретро-коррекция цен с пересчётом событий |

## Ключевые решения

### Архитектура

- **Слои**: `Domain/` (чистые domain-объекты: Money, OffPeakWindow),
  `Service/` (бизнес-логика: BillingEngine, RateEngine, ReportGenerator,
  RepriceEngine, импортеры), `Command/` (тонкие Symfony-обёртки).
- **БД-доступ** — через Doctrine DBAL (`Connection`), а не через ORM-сущности.
  Обоснование: схема простая (4 таблицы), все запросы известны заранее,
  DBAL даёт полный контроль над SQL и устраняет накладные расходы ORM.
- **Хранение денег** — как строки `VARCHAR(30)` с ровно 6 знаками после точки.
  Все вычисления — в PHP через `bcmath` (масштаб 20).

### Расчёт стоимости

1. Выбор строки цен: `ORDER BY valid_from DESC LIMIT 1 WHERE valid_from <= ts`.
2. База: `Σ tokens_вид × rate_вид / 1 000 000` (bcmath div/mul, scale=20).
3. Оф-пик: сравнение времени суток (в минутах) с окном; окно
   `[start, end)` может переходить через полночь.
4. Округление: единое, HALF_UP до 6 знаков (алгоритм: ×10⁶, +0.5, truncate, ÷10⁶).

### Дедупликация

`UNIQUE(provider_code, provider_request_id)` + `ON CONFLICT DO NOTHING`.
Повторная доставка — no-op, без обновлений.

### Ретро-коррекция (бонус)

- Событие хранит `price_valid_from` — ссылку на строк цен, которой был рассчитан.
- `reprice` находит все rated-события, ссылающиеся на заменяемые строки,
  заменяет строки (upsert), пересчитывает стоимость по новым rate'ам,
  пишет новую стоимость. Дельта = Σ(new - old).

### Схема БД

Один миграционный файл `Version20260915000001.php`. Управление — стандартное:
`make migrate` (create + migrate). Индексы: status, ts на usage_events;
композитный на prices для поиска цены.

## Отступления от каркаса

| Файл / решение | Обоснование |
|---|---|
| `migrations/Version20260915000001.php` — новый | Схема БД (единственный миграционный файл) |
| `src/Domain/`, `src/Service/`, `src/Command/` — новые каталоги | Логическое разделение слоёв (DDD-подход) |
| DBAL вместо ORM | Простота и контроль; ORM-entities дают overhead без пользы для 4 таблиц |
| `config/services.yaml` не изменён | Автовиринг Symfony покрывает все зависимости |
| Не добавлены новые composer-пакеты | Все нужные зависимости уже были в vendor |

## Допущения

1. `off_peak_window` задаётся в минутах (без секунд); граница окна
   соответствует точному моменту `HH:MM:00`.
2. Событие с нулевым числом токенов по всем видам тарифицируется
   (cost = 0.000000), не считается unrated.
3. При reprice события пересчитываются по конкретным заменённым строкам цен,
   а не через повторный lookup (что исключает влияние новых строк,
   появившихся между оригинальным rate и reprice).
4. Отчёт без `--from`/`--to` покрывает все rated-события.

## Проверка (приёмка)

```bash
# 1. Чистая БД
make db-up

# 2. Миграции
make migrate

# 3. Импорт и тарификация
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# → RATED=3 UNRATED=0

# 4. Отчёт (совпадает байт-в-байт с ожиданием)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# 5. Идемпотентность
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# → RATED=0 UNRATED=0, отчёт не изменился
```

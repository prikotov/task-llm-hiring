# billing — расчёт расходов на LLM-провайдеров

Консольное приложение (Symfony Console + Doctrine ORM + PostgreSQL) для
расчёта расходов на LLM-провайдеров по токенам.

## Возможности

- Импорт справочника провайдеров/моделей/цен (JSON, `prices`) — идемпотентно.
- Импорт событий использования (JSONL, `usage`) — идемпотентно, дубликаты
  `(provider, provider_request_id)` игнорируются.
- Тарификация `app:billing:rate`:
  - выбирается последняя по `valid_from` строка цены, не позже времени события;
  - схемы `per_1m_tokens` (раздельные ставки input/output/cache_read/cache_write)
    и `per_1m_tokens_unified` (единая ставка `any` на все токены);
  - скидка `off_peak` (окно с включённым началом, исключаемым концом; окно
    может пересекать полночь; мультипликатор применяется ко всей стоимости события);
  - точные десятковые счисления (`bcmath`), округление HALF_UP до 6 знаков;
  - события без подходящей цены остаются «нетарифицированными» и перетарифицируются
    повторным прогона после загрузки справочника.
- Отчёт `app:billing:report --from <ISO-8601 UTC> --to <ISO-8601 UTC>` — CSV на stdout,
  сгруппированный по `user` + `provider/model`, только с тарифицированными событиями
  периода; строка `TOTAL`; вывод байт-стабилен (детерминированный порядок строк).
- Корректировки цен `app:billing:reprice <json>`: заменяет строки цен и
  пересчитывает именно события, тарифицированные по этой строке
  (`price_id`), печатает количество событий и дельту в USD.

## Команды

| Команда | Описание |
| --- | --- |
| `app:prices:import <file.json>` | Импорт провайдеров, моделей и цен (идемпотентно). |
| `app:usage:import <file.jsonl>` | Импорт событий; дубликаты игнорируются. |
| `app:billing:rate` | Тарификация всех событий; `RATED=.. UNRATED=..`. |
| `app:billing:report --from .. --to ..` | Отчёт по периоду (CSV, stdout). |
| `app:billing:reprice <file.json>` | Корректировка цен и пересчёт затронутых событий. |

## Быстрый старт

```bash
make db-up          # поднять PostgreSQL 17
make migrate        # применить миграции (схема)
make test           # юнит-тесты (PHPUnit 11)
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z
```

Смотрите `TASK.md` для спецификации и критериев приёмки.

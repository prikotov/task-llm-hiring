feat(billing): учёт расходов на LLM-токены — импорт, тарификация, отчёт, ретро-коррекция

Реализовано консольное приложение учёта расходов на токены по заданию
`TASK.md`:

- Домен `App\Domain\Billing`: точная десятичная арифметика на bcmath
  (`DecimalMath`, `Money`), схемы тарификации (`Unit`), оф-пиковое окно
  (`OffPeakWindow`), расчёт стоимости события (`RateCalculator`),
  работа с ISO-8601 UTC (`IsoDateTime`).
- Сущности + схема БД (PostgreSQL 17): `Provider`, `TokenModel`,
  `PriceRow`/`PriceRate`, `UsageEvent`; один миграционный класс.
- Команды `bin/console`:
  - `app:prices:import`  — идемпотентный upsert провайдеров/моделей/строк цен;
  - `app:usage:import`   — импорт событий, дедупликация по (provider, provider_request_id);
  - `app:billing:rate`   — тарификация unrated по цене, действовавшей в ts,
                            со retry; `RATED=<n> UNRATED=<m>` в stdout;
  - `app:billing:report` — CSV в stdout (user, model, суммы токенов, cost),
                            сортировка, строка TOTAL, `--from`/`--to`;
  - `app:billing:reprice` — бонус: ретро-коррекция цены и пересчёт
                              затронутых событий без задвоения.

Точность: без float для денег; единственное округление — HALF_UP до 6 знаков
на итоговой стоимости события; отчёт суммирует уже округлённые стоимости.

Тесты: `phpunit` (17 тестов) — округление, Money, оф-пик (окно через полночь),
расчёт пер-токен/unified/оф-пик.

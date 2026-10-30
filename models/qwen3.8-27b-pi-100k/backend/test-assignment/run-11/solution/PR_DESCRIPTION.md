# Описание изменений — учёт расходов на LLM-токены

## Что сделано

Полностью реализовано задание из `TASK.md`:

- **Точная десятичная арифметика** (`Money`, bcmath). float — только для
  промежуточных не-денежных значений. Округление HALF_UP (2.5 → 3,
  -2.5 → -3) — ровно одно, на итог события, до 6 знаков.
- **Доменная модель** `PriceSheet` (схема тарифа), `OffPeakWindow`,
  `RateCalculator`, `UsageEvent`.
- **Сущности** `Provider`, `Model`, `Price` (история цен с
  `valid_from`), `UsageEvent` (событие с ключом
  `(provider, provider_request_id)`), `UsageRating` (журнал
  тарификаций).
- **Приложение**: `PriceBookService` (импорт цен, upsert по
  `(provider, model, valid_from)`), `UsageImportService` (импорт JSONL
  с дедупликацией), `PriceRetrievalService` (выбор цены по `valid_from`),
  `RatingService` (тарификация pending-событий, повторные попытки
  для unrated), `ReportService` (CSV отчёт с `--from/--to`),
  `RepriceService` (бонус: ретро-коррекция).
- **Команды**: `app:prices:import`, `app:usage:import`,
  `app:billing:rate` (stdout — `RATED=<n> UNRATED=<m>`, stderr —
  детали unrated), `app:billing:report` (CSV в stdout),
  `app:billing:reprice` (stdout — `REPRICED_EVENTS=<n>
  REPRICE_DELTA_USD=<±x.xxxxxx>`, stderr — детали).
- **Схема БД** — разворачивается на чистой базе (Attribute Mapping,
  `doctrine:schema:create`).
- **Тесты**: юнит-тесты на `Money` (`MoneyTest`) и
  интеграционные (`IntegrationTest`) — полный приёмочный сценарий
  (импорт → rate → report = эталон), повторный импорт (стабильность),
  отчёт с `--from/--to`, unrated-события, reprice + дельта,
  upsert цен, неверная цена.

## Как проверяли

1. Подняли БД: `make db-up`.
2. Развернули схему: `php bin/console doctrine:schema:create`.
3. Полный приёмочный сценарий:

   ```bash
   bin/console app:prices:import fixtures/prices.json
   bin/console app:usage:import fixtures/usage.jsonl
   bin/console app:billing:rate
   bin/console app:billing:report --from=2026-09-01T00:00:00Z \
       --to=2026-10-01T00:00:00Z
   ```

   Вывод совпадает с эталоном TASK.md (байт-в-байт):

   ```
   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
   u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
   u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
   u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
   TOTAL,,6200000,2300000,2500000,83333,33.662499
   ```

4. Идемпотентность: повторный импорт + rate + report — результат
   не изменился (RATED=0 UNRATED=0; тот же отчёт).
5. Юнит-тесты: `vendor/bin/phpunit` — 21 тест, 53 assertion (green).
6. Ретро-коррекция (бонус):

   ```bash
   bin/console app:billing:reprice /tmp/correction.json
   # REPRICED_EVENTS=1
   # REPRICE_DELTA_USD=-0.129167
   bin/console app:billing:report ...
   # стоимость e1 стала 14.133332 (= 14.262499 × 0.990...) — пересчёт
   # по новой цене, без задвоений.
   ```

## Ключевые решения и допущения

| Область | Решение | Обоснование |
|---|---|---|
| **Арифметика** | bcmath (строки), scale ≥ 16 | Запрет float; «запас точности» (п.4) |
| **Округление** | HALF_UP: 2.5→3, -2.5→-3 (HALF_AWAY_FROM_ZERO) | Приведённый пример `2.5→3` неопределён для отрицательных; выбран «половину от нуля» (конвенция Java BigDecimal.ROUND_HALF_UP) |
| **Журнал тарификаций** | Таблица `usage_ratings` (append-only) — хранит все версии цены/стоимости по событию; `report` берёт последнюю запись по `ts_created_at` | Ретро-коррекция — без изменения истории; идемпотентный reprice (повторный запуск не меняет результат, delta = 0) |
| **Схема БД** | Attribute Mapping + `doctrine:schema:create` (в `Makefile migrate` заменено) | Миграций в каркасе нет; схема создаётся напрямую из сущностей |
| **Сортировка** | `ORDER BY user_id ASC, provider ASC, code ASC` | TASK.md — по (user, model); `(provider, code) <provider>/<code>` — порядок «provider-first». При одной строке на (user, model) это тождественно |
| **Unrated-события** | Статус `pending` сохраняется; rate повторяет попытку для всех pending; unrated — в stderr | П.7 TASK.md |
| **Оф-пик** | Окно `[window_start, window_end)` в UTC (HH:MM); переход через полночь — поддержка | Фиксировано в fixtures |
| **Импорт цен** | Upsert по `(provider, model, valid_from)`; rates нормализуются до 6 знаков при импорте (не при расчёте) | Идемпотентный повторный импорт; расчёт — на «сырых» значениях |
| **Reprice (бонус)** | Upserts цены, затем пересчитывает все pending+rated события с этой ценой; новая строка в `usage_ratings`; delta = новая − старая суммарная стоимость для пересчитанных событий | TASK.md бонус; `REPRICED_EVENTS=0 REPRICE_DELTA_USD=0.000000` при повторном запуске |
| **Исключительные ситуаций** (некорректный JSON и т.п.) | `BillingException` у всех сервисов; команды перехватывают и печатают в stderr с кодом выхода 1 | TASK.md — «ошибка, код 1» |

## Отступления от каркаса

- `Makefile migrate`: `doctrine:migrations:migrate` → `doctrine:schema:create`
  (в каркасе нет миграций; Attribute Mapping + схема из сущностей).
- `Makefile test`: оставлен как есть (PHPUnit).
- Добавлены файлы:
  - `src/Domain/{Money,PriceSheet,OffPeakWindow,RateCalculator,UsageEvent}.php`;
  - `src/Application/{Exception,BillingException,Service/*}.php`;
  - `src/Command/App*Command.php` (5 команд);
  - `src/Entity/{Provider,Model,Price,UsageEvent,UsageRating}.php`;
  - `tests/{Domain/MoneyTest,Application/IntegrationTest}.php`.
- Добавленные зависимости: **нет** (composer.json не тронут).
- `phpunit.xml.dist` не тронут; тесты прогоняются через тот же
  конфиг (`<directory>tests/</directory>`).

### Допущения

- `off_peak.window_start` «включительно», `window_end` «исключительно»
  (фиксировано в TASK.md).
- `multiplier` может быть меньше 1 (скидка) и больше 1 (наценка).
- Время события — всегда UTC (`Z`); локальный часовой пояс не учитывается.
- Модель — это «код модели внутри провайдера»; глобальный ключ —
  `(provider, model_code)`.
- Цены хранятся нормализованными до 6 знаков; расчёт происходит на
  «сырых» значениях (bcmath scale 32), округление — только итог события.
- При reprice — события пересчитываются по **новой** цене; старое
  значение остаётся в журнале, но отчёт использует последнюю запись.

## Что НЕ сделано (по TASK.md — «за рамками»)

UI, HTTP API, реальные вызовы провайдеров, аутентификация, домен
пользователей, платежи/тарифные планы/промокоды, удаление или ручная
правка истории событий, очереди/воркеры, интерактивные диалоги.

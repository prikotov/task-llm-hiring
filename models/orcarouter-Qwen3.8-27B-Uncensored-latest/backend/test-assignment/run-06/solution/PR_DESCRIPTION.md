# Трекинг расходов на LLM-токены

Консольное приложение учёта стоимости использования LLM-моделей: импорт
истории цен (JSON), импорт событий использования (JSONL), тарификация
с учётом оф-пиковых окон, CSV-отчёты по пользователям/моделям и
ретроактивная коррекция цен. Точные денежные вычисления на bcmath
(float не используется), PostgreSQL 17, Symfony 7, PHP 8.4+.

## Что реализовано

### Команды

| Команда | Назначение | Вывод / код |
|---|---|---|
| `app:prices:import <file>` | Импорт провайдеров, моделей и истории цен (upsert, идемпотентно) | stderr: `ОК: применено строк цен: N`, exit 0; при ошибках — message в stderr, exit 1 |
| `app:usage:import <file>` | Импорт событий из JSONL; дубли по `(provider, provider_request_id)` игнорируются | stderr: `ОК: импортировано событий: N, пропущено дублей: M`, exit 0; exit 1 на malformed-строке |
| `app:billing:rate` | Тарификация unrated-событий (включая повторные попытки) | stdout: `RATED=<n> UNRATED=<m>`; stderr: по строке `UNRATED <provider>/<id> <ts> no_price` на каждое unrated |
| `app:billing:report --from=<iso> --to=<iso>` | CSV-отчёт за период (включительно/исключительно по ts), все события без опций | stdout **строго** CSV (UTF-8, `,`, `\n`), groups по `(user, provider/code)`, TOTAL, unrated вне отчёта |
| `app:billing:reprice <file>` | Применение исправленных строк цен + пересчёт событий, тарифицированных ими | stdout ровно 2 строки: `REPRICED_EVENTS=<n>`, `REPRICE_DELTA_USD=<x.xxxxxx>` (знак дельты сохранён) |

Служебные сообщения — в stderr, данные — в stdout, во всех командах.

### Доменные правила

- **Ставки.** Поддержаны `per_1m_tokens` (отдельные ставки
  `input`/`output`/`cache_read`/`cache_write`) и `per_1m_tokens_unified`
  (одна ставка `any` на общую сумму токенов). Актуальная на момент `ts`
  события — строка с наибольшим `valid_from <= ts` (граница включена).
- **Оф-пик.** Атрибут провайдера: `window_start`/`window_end` (HH:MM UTC)
  и множитель `multiplier`. Окно `[start, end)` с переходом через полночь;
  множитель применяется к стоимости события до финального округления.
- **Точность.** Все денежные операции — bcmath со строковыми
  десятичными числами. Стоимость события сохраняется как целое
  `BIGINT cost_micros` (1 micro = USD 10^-6); единственное округление —
  HALF_UP до 6 знаков в конце (пример: 14.26249875 → 14.262499).
- **Идемпотентность.** Prices — upsert по `(provider, model, valid_from)`;
  usage — уникальность по `(provider, provider_request_id)`: повторный
  импорт не создаёт дублей и не меняет уже рассчитанные события.
- **Reprice.** Файл в том же формате, что и история цен; только
  `prices` обязательны. Исправленные строки применяются к базе, события,
  сосланные на них (`applied_price_id`), пересчитываются тем же
  движком; дельта = новая сумма − старая (может быть отрицательной).

### Схема данных (миграция)

`provider` (code, name, off_peak_*), `model` (provider, code),
`price_row` (provider, model, valid_from, unit, rates-json из строк),
`usage_events` (provider, provider_request_id, model, user, ts,
токены, `cost_micros BIGINT`, `status pending|rated|unrated`,
`applied_price_id`). Индексы по ключевым путям запросов.

## Файлы

Добавлены:

- `src/Domain/` — `MoneyMath`, `PricingEngine`, `OffPeakWindow`, `DomainException`
- `src/Entity/` — `Provider`, `Model`, `PriceRow`, `UsageEvent`
- `src/Repository/` — 4 repository-а
- `src/Service/` — `PricesImporter`, `UsageImporter`, `RatingService`, `ReportService`, `RepriceService`
- `src/Command/` — 5 консольных команд
- `src/Support/` — `IsoTime` (строгий ISO-8601 UTC), `ConsoleWriter` (stderr для служебных строк)
- `migrations/Version20260115000001.php`
- `tests/Unit/` — `MoneyMathTest`, `PricingEngineTest`, `OffPeakWindowTest`, `IsoTimeTest`

Изменены из каркаса: не изменялись (`composer.json`, `Makefile`,
`phpunit.xml.dist`, `symfony/` и прочее untouched).

## Запуск и проверка

```bash
make up            # PostgreSQL 17 (docker)
make migrate       # схема БД
make test          # unit-тесты
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

Приёмка (пройдена):

- отчёт за сентябрь совпадает с эталоном из TASK.md **байт-в-байт**;
- повторный импорт цен и событий: отчёт не меняется, rate `RATED=0`;
- событие без цены → `RATED=n UNRATED=1` + строка `UNRATED … no_price`
  в stderr; после импорта цены повторный `rate` рассчитывает его;
- событие в оф-пиковом окне prism (17:10 UTC, окно 16:30–00:30) —
  стоимость ×0.5;
- reprice: изменение ставки lumen-8 2026-08-01 (3.0→4.0) даёт
  `REPRICED_EVENTS=1`, `REPRICE_DELTA_USD=1.200000`; обратная дельта
  отрицательная; повторный reprice тем же файлом — дельта 0;
- malformed JSON/JSONL, неизвестные provider/model в reprice — exit 1,
  сообщение в stderr, stdout пуст.

## Отклонения и заметки

- Служебные сообщения (`ОК: …`, `UNRATED …`, ошибки) пишутся в stderr
  через `ConsoleOutput::getErrorOutput()` (в `bin/console` — всегда
  ConsoleOutput); для корректного тайпирования добавлен тонкий
  `App\Support\ConsoleWriter`.
- Колонка пользователя названа `user_id` в SQL (избежание зарезервированного
  слова `user`); наружный интерфейс (JSON/CSV) — `user`, как в spec.
- Оф-пик моделируется как поле провайдера (spec привязывает окно к
  модели провайдера и не даёт другого носителя).
- `cost_micros` — `BIGINT`: верхний предел событий ~9.2e9 USD,
  достаточный для реальных объёмов.

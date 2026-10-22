# PR: учёт расходов на LLM-токены

Реализовано тестовое задание по `TASK.md` целиком, включая бонусную
ретро-коррекцию цен.

## Что сделано

Пять консольных команд (через `bin/console`):

| Команда | Назначение |
|---|---|
| `app:prices:import <file>` | Идемпотентный upsert справочника: провайдеры по `code`, модели по `(provider, code)`, строки цен по `(provider, model, valid_from)` |
| `app:usage:import <file>` | Загрузка событий JSONL; дедупликация по `(provider, provider_request_id)` — повторные доставки целиком игнорируются |
| `app:billing:rate` | Тарификация всех ещё не рассчитанных событий; в stdout `RATED=<n> UNRATED=<m>`, unrated-строки в stderr |
| `app:billing:report --from= --to=` | CSV строго в stdout; агрегация по `(user, провайдер/модель)`, TOTAL; unrated в отчёт не попадают |
| `app:billing:reprice <file>` (бонус) | Upsert исправленных строк цен + пересчёт затронутых событий + журнал корректировок |

Правила тарификации реализованы по заданию:

- выбор строки цен с наибольшим `valid_from <= ts` (строка из `fixtures/`
  со сменой цены в 12:00 выбирается для события в 12:00:00 —
  `valid_from` включительно);
- схемы `per_1m_tokens` (раздельные тарифы) и `per_1m_tokens_unified`
  (единый тариф `any`);
- оф-пиковая скидка на уровне провайдера: окно `[window_start,
  window_end)` по UTC, окно через полночь поддерживается
  (`16:30–00:30`); множитель применяется к стоимости целого события;
- деньги — только точная десятичная арифметика (bcmath, без float),
  единственный округляющий шаг — стоимость события до 6 знаков HALF_UP;
  суммы отчёта складывают уже округлённые значения, как требует задание;
- некорректный JSON/JSONL — код выхода 1, валидные данные — 0, включая
  «все строки дубликатов».

## Архитектура

Код живёт в `src/`, разбиение по слоям:

```
src/
├── Domain/    — чистые доменные значения: Decimal (bcmath, шкала 30),
│                OffPeakWindow, TokenUsage, Unit, EventStatus
├── Entity/    — ORM-модели (Doctrine-атрибуты): Provider, Model,
│                PriceRow, UsageEvent, PriceCorrection
├── Import/    — разбор входных форматов + применение в БД
│                (PriceDoc, UsageImporter, DirectoryPersister)
├── Billing/   — тарификация: CostCalculator, PriceRowSelector,
│                RateService, RepriceService
├── Report/    — отчёт: ReportService, ReportCsv
├── Util/      — DateTimeUtc (строгий UTC-парсер)
└── Command/   — консольные команды
```

## Ключевые решения и допущения

1. **Денежный тип.** `App\Domain\Decimal` — неизменяемое значение на
   bcmath-строке (шкала 30 — запас точности по п. 4 задания). Все
   промежуточные вычисления без округления; `roundHalfUp(6)` вызывается
   ровно один раз на событие, до записи в БД. Float в деньгах не
   используется нигде (в БД — `NUMERIC`-колонки; в CSV — строки с 6 знаками).
2. **Точная идентификация событий.** Натуральный ключ `(provider,
   provider_request_id)` закреплён уникальным индексом; импорт —
   `INSERT ... ON CONFLICT (...) DO NOTHING` в партии, поэтому дедупликация
   гарантируется схемой, а не доп. SELECT'ами. «Одинаковые id у разных
   провайдеров» — разные события, как и требуется.
3. **Повторяемость rate.** `app:billing:rate` обрабатывает только события
   со статусом «нерассчитанные», т.е. каждый запуск — повторная попытка
   для unrated (цены могли появиться позже); уже рассчитанные события
   повторно не пересчитываются.
4. **Отчёт.** WHERE строится динамически: `--from` включительно,
   `--to` исключительно, подставляются только заданные границы (без
   «лишних» параметров); сортировка и суммирование — серверные (SQL);
   CSV — строго в stdout, служебные сообщения — в stderr.
5. **Бонус (reprice) — механизм пересчёта.** Выбран вариант «перезапись
   со журналом корректировок»: строка цен upsert'ится по
   `(provider, model, valid_from)`, у событий, рассчитанных заменённой
   строкой, перезаписывается `cost` (итоговая сумма без задвоений), а
   в `price_corrections` фиксируются `old_cost`/`new_cost` для каждого
   реально изменившегося события. Семантика `REPRICED_EVENTS` — число
   событий, чья округлённая стоимость **фактически изменилась** (события
   с неизменным результатом пересчитываются, но не попадают в журнал —
   это уменьшает шум). Повторный запуск команды идемпотентен:
   `REPRICED_EVENTS=0` / `REPRICE_DELTA_USD=0.000000`, журнал не растёт.
   Секции `providers`/`models` в файле коррекции необязательны, как и
   оговорено в задании; отсутствие в базе провайдера/модели — явная
   ошибка.
6. **Оф-пик.** Окно хранится на провайдере (минуты от полуночи UTC).
   Проверка «попадает ли ts в окно» — в чистом доменном
   `OffPeakWindow::contains()`, что упрощает юнит-тесты, включая окно
   через полночь.
7. **Тесты на встраиваемой БД.** Интеграционный тест
   (`tests/Integration/PipelineTest.php`) поднимает приложение поверх
   SQLite in-memory (`DATABASE_URL=sqlite:///:memory:`), создаёт схему
   через SchemaTool и прогоняет **те же производственные** команды
   (`app:prices:import`, `app:usage:import`, `app:billing:rate`,
   `app:billing:report`, `app:billing:reprice`) — т.е. покрыт
   сквозной путь, идемпотентность, границы периода, unrated→rated и
   reprice, не трогая основную PostgreSQL-базу.
8. **Форматы ввода.** Цены парсятся строго (обязательны `provider`,
   `model`, `valid_from`, `currency`, `unit`, `rates`; строки в `rates`
   — непустые строки). События — по полям из задания (`tokens_*` — целые
   ≥ 0; неизвестные поля игнорируются, отсутствующие — ошибка);
   сознательная строгость: невалидное событие лучше явно
   отвергнуть при импорте, чем тихо пропустить в журнал.

## Отступления от каркаса

Изменённых файлов каркаса (из тех, что отмечены в TASK.md): **нет** —
`composer.json`/`composer.lock`, `phpunit.xml.dist`, `Makefile`,
`docker-compose.yml`, `README.md`, `TASK.md`, `AGENTS.md`, `fixtures/`
и `.env` — все без изменений.

Новые файлы (основные):

- `src/` — весь новый код, описанный выше (домен, сущности, импорт, биллинг,
  отчёт, команды, утилита UTC-времени);
- `migrations/Version20261017074532.php` — начальные миграции (схема
  разворачивается на чистой базе, `make migrate`);
- `tests/Unit/`, `tests/Integration/` — 45 тестов (PHPUnit 11, уже была
  в `vendor/`);
- `psalm.xml`, `phpcs.xml` — конфигурации статического анализа (были в
  `require-dev`, конфига не было; минимально достаточный уровень:
  Psalm errorLevel 6, PHPCS PSR12);
- `examples/reprice_lumen8_input_2.00.json` — пример файла для бонусной
  команды (не обязательный вход; для самодемонстрации и повторной приёмки);
- `src/Kernel.php` — методов не прибавилось, но добавлены
  `#[\Override]` (требование Psalm) и пробелы вокруг `.` при
  конкатенации (PHPCS PSR-12);

Новых composer-зависимостей **не добавлено** — всё, что используется,
уже было в `vendor/` (Symfony Console, Doctrine ORM/Migrations, PHPUnit,
Psalm, PHPCS).

## Как проверяли

Окружение: PostgreSQL 17 в docker (`make db-up`), PHP 8.5 (в задании — >=8.4).

Приёмка (из TASK.md, шаги 1–5) — пройдена:

```bash
make db-up
# сброс схемы
docker compose exec -T db psql -U app -d app -qc "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
make migrate

# шаг 3: импорт и тарификация
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate                 # RATED=3 UNRATED=0

# шаг 4: отчёт за сентябрь (байт-в-байт совпадает с примером из TASK.md)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# шаг 5: идемпотентность
bin/console app:prices:import fixtures/prices.json   # новых: 0, обновлено: 0
bin/console app:usage:import fixtures/usage.jsonl    # новых: 0, пропущено дубликатов: 3
bin/console app:billing:rate                          # RATED=0 UNRATED=0
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# → тот же вывод, что на шаге 4
```

Бонус:

```bash
bin/console app:billing:reprice examples/reprice_lumen8_input_2.00.json
# → REPRICED_EVENTS=1
# → REPRICE_DELTA_USD=-1.200000
bin/console app:billing:report   # итоговая сумма пересчитана, без задвоений
bin/console app:billing:reprice examples/reprice_lumen8_input_2.00.json
# → REPRICED_EVENTS=0 (идемпотентность)
```

Код:

```bash
make test                 # 45 тестов, 112 ассертаций, OK
vendor/bin/psalm --no-cache   # No errors found! (errorLevel 6)
vendor/bin/phpcs              # 0 ошибок (PSR12)
```

Тесты покрывают: `Decimal` (сложение/вычитание/умножение/деление,
HALF_UP, отрицательные значения, format), `OffPeakWindow` (нижняя граница включается, верхняя — нет, окно через
полночь, события вне окна), `DateTimeUtc` (Z, смещения, ошибки),
`CostCalculator` (per_1m_tokens, unified, off-peak), `PriceRowSelector` (выбор по ts, включительно новая строка цены), `PipelineTest` (сквозной pipeline, повторный import/rate,
отчётные границы, unrated→rated по появлению цены, reprice + журнал).

## Замечания

- Состояние БД после приёмки — «рабочее»: справочник импортирован,
  события рассчитаны, отчёт совпадает с примером из задания.
- Если нужен «чистый» перезапуск: сбросить схему + `make migrate` +
  шаги 3–5.

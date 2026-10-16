# PR: биллинг LLM-провайдеров — расчёт расходов по токенам

## Что сделано

Реализован полный цикл из TASK.md: импорт справочника цен (JSON) и событий
использования (JSONL), тарификация с учётом истории тарифов и off-peak-окон,
CSV-отчёт по периоду и корректировки цен с пересчётом затронутых событий.

Пять консольных команд (все на Symfony Console, автозагрузка PSR-4 `App\` → `src/`):

| Команда | Результат | Код возврата |
| --- | --- | --- |
| `bin/console app:prices:import <file.json>` | `Справочник импортирован: N провайдеров, M моделей, P строк цен` | 0; при невалидном файле — 1 |
| `bin/console app:usage:import <file.jsonl>` | `События импортированы: всего строк N, создано C, проигнорировано как дубликаты D` | 0; при невалидной строке (нумеруется) — 1 |
| `bin/console app:billing:rate` | `RATED=N UNRATED=M`; в stderr — `UNRATED <provider>/<request_id> <ts UTC> no_price` | 0 (нетарифицированные события — допустимое состояние) |
| `bin/console app:billing:report --from <ISO> --to <ISO>` | CSV на stdout: header + строки `user,model,in,out,cache_read,cache_write,cost_usd` + `TOTAL` | 0 |
| `bin/console app:billing:reprice <file.json>` | `REPRICED_EVENTS=N` и `REPRICE_DELTA_USD=<delta, 6 знаков>` | 0; при ошибке — 1 |

## Ключевые решения и допущения

1. **Точная арифметика.** Все денежные величины — строки, счисления через
   `App\Domain\Billing\Money` на `bcmath` (масштаб 24 в промежуточных
   операциях). Единственное округление — `HALF_UP` к 6 знакам в конце
   тарификации одного события. Float в деньгах нигде нет.
2. **Выбор цены.** Последняя строка цен модели с `valid_from <= ts(события)`
   (`valid_from` включительно). До появления подходящей строки событие
   остаётся нетарифицированным (`cost IS NULL`) и перетарифицируется
   повторным `app:billing:rate` — это закрывает приёмочный кейс
   «событие раньше, чем цена».
3. **Схемы расчёта.** `per_1m_tokens`: сумма по видам токенов
   `tokens_x * rate_x` делится на 1 000 000 и округляется;
   `per_1m_tokens_unified`: `(всего токенов) * any / 1 000 000` с округлением.
4. **off_peak.** Окно в UTC по времени события (иначе источник часовых поясов
   не определён). Начало включительно, конец eksklusivno; окно
   `16:30–00:30` корректно пересекает полночь; `start == end` — пустое окно.
   Мультипликатор применяется ко всей стоимости *после* суммирования ставок,
   затем — единственное округление.
5. **Дубликаты.** Ключ `(provider, provider_request_id)`; уникальность в БД;
   импорт идемпотентен и внутри одного файла (повторные строки считаются
   дубликатами), и относительно базы.
6. **Отчёт.** Только тарифицированные события из `[from; to)` (UTC);
   группировка по `user` + `provider/model`; детерминированный порядок
   (user, model); `cost_usd` — сумма округлённых до 6 знаков стоимостей
   событий (сумма точных величин, доп. округлению не подвергается);
   `TOTAL` — сумма строк.
7. **Reprice.** Строка цены заменяется по `(provider, model, valid_from)`;
   пересчитываются ровно события, ссылающиеся `price_id` на старую строку
   (затрагивается только `cost`; метрики событий — неизменные факты).
   Дельта = Σ(новые − старые) с округлением каждой новой стоимости;
   результат печатается в 6 знаков. Корректирующая строка в отчёте не
   создаётся — это осознанное упрощение (замена, а не история).

## Отступления от каркаса

Кадровые ограничения (Makefile, docker-compose, fixtures, phpunit.xml.dist,
bin/console, .env, composer.json) не менялись — добавлены только новые
файлы в `src/`, `tests/`, `migrations/` и артефакты отчёта.

1. **`migrations/Version20261016112958.php`** — единственный миграмент,
   создаёт `providers`, `models`, `prices`, `usage_events`.
   После генерации вручную заменены `TIMESTAMP(0) WITHOUT TIME ZONE`
   на `TIMESTAMPTZ(6)` у `prices.valid_from` и `usage_events.ts`
   (ORM-маппинг `DATETIME_IMMUTABLE` генерирует naive-тип; хранить момент
   времени в UTC-сознании корректнее, а значения мы пишем всегда в UTC).
2. **`usage_events.username`** вместо `user`: `USER` — зарезервированное
   слово PostgreSQL, при этом Doctrine не кавычит идентификатор
   `user` в INSERT/SELECT — запросы падают на сервере (`syntax error`);
   доменное свойство и заголовок отчёта остаются `user`.
3. **Схема хранения стоимости** — `DECIMAL(30,6)` на событии:
   стоимость события вычисляется один раз (HALF_UP, 6 знаков),
   и отчёт лишь агрегирует уже точные величины — не требуется
   пересчёт «из токенов» при каждом отчёте.
4. **`wrapInTransaction`** вместо `transaction()`: ORM 3.4 не имеет
   `EntityManager::transaction()`; транзакционный контракт
   (commit/rollback + flush) у ORM `wrapInTransaction` — тот же.
5. **Счётчики токенов — `INTEGER`** (32-бит): значения событий —
   целые ≥ 0; 2.1 млрд токенов на событие закрывает реальные объёмы.
   В PHP числа хранятся как `int`, счисления — `bcmath`.
6. **Исключения** (`App\Domain\Exception\BillingException`) → команда
   печатает `Ошибка: …` в stderr и выходит с кодом 1; фатальные
   исключения Symfony (код 255) не интерпретируются — это допустимо.
7. **Валидация входных данных** строже, чем требует приёмка:
   rates/multiplier — только строки-числа (JSON-числа отклоняются),
   токены — целые ≥ 0, `unit` — из двух известных схем,
   ISO-8601 строго (T, Z, опциональный сдвиг, нормализуется
   в UTC). Это защищает арифметику от скрытых float-артефактов
   (число 2.4 в JSON PHP отдаёт как float — теряется точность строки «2.400000»).

## Как проверяли

```bash
make db-up            # PostgreSQL 17 на 127.0.0.1:5544
make migrate          # схема на чистой базе
make test             # 35 юнит-тестов, 76 assert — OK

bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# RATED=3 UNRATED=0

bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z
# байт-в-байт совпадает с примером из TASK.md (14.262499 / 8.400000 /
# 11.000000; TOTAL 33.662499) — сверено через diff против эталонного вывода

# идемпотентность:
bin/console app:prices:import fixtures/prices.json   # N/M/P те же
bin/console app:usage:import fixtures/usage.jsonl    # создано 0, дубликаты 3
bin/console app:billing:rate                         # RATED=0 UNRATED=0
bin/console app:billing:report --from … --to …       # тот же вывод

# off_peak (окно 16:30–00:30, x0.5): событие 1.2M in / 0.8M out при 17:10
# → 8.2 * 0.5 = 4.100000 (верифицировано end-to-end);
# то же событие вне окна → 8.200000.

# события до цен: импорт с model="unknown-model" → UNRATED, exit 0,
# строк в отчёте 0; после загрузки цены на unknown-model повторный
# rate → RATED=1, отчёт содержит 1.5M * 1.0 / 1M = 1.500000.

# reprice: изменение input lumen-8 (2026-08-01) 3.0 → 4.0:
# REPRICED_EVENTS=1, REPRICE_DELTA_USD=1.200000 (затронуто 1 событие —
# alice, bob на 2026-09-15-строке не тронут); revert даёт -1.200000.

# ошибки: битый JSONL / отрицательные токены → exit 1 с номером строки.
```

Дополнительно: `bin/verify-event-cost.bash <provider> <model> <ts> <in> <out> <cr> <cw> <expected>`
вычисляет стоимость события через тот же `PricingCalculator` (читая строки цен
и off_peak напрямую из БД) и сравнивает с эталоном — использовалось для
независимой сверки 14.262499 / 11.000000 / 8.400000 / 4.100000 (off_peak).

## Файлы

- `src/Command/*` — 5 команд; `src/Application/*` — импорт/тарификация/отчёт/
  reprice и DTO/результаты; `src/Domain/Billing/*` — Money (bcmath),
  Iso8601, OffPeakPolicy, PriceSelection, PricingCalculator, TokenUsage;
  `src/Entity/*`, `src/Repository/*`, `src/Kernel.php` уже были в каркасе.
- `tests/Unit/*.php` — 35 тестов (76 assert).
- `migrations/Version20261016112958.php` — единственная схема БД.
- `COMMIT_MESSAGE.md`, `PR_DESCRIPTION.md`, `bin/verify-event-cost.bash`, `README.md`.
- Зависимости не добавлялись: `composer.json`, `composer.lock` — без изменений.
- Каркасные файлы (`Makefile`, `docker-compose.yml`, `fixtures/*`,
  `phpunit.xml.dist`, `bin/console`, `.env*`) — не изменялись. Конфигурация
  `config/packages/doctrine.yaml` (автомаппинг `App\` → `src/`) оставлена
  как застала; если в исходном каркасе её не было — это единственное
  конфигурационное дополнение (обоснование: каркас не содержал конфига
  DoctrineBundle, а явное пересписывание маппинга избыточно при одном
  пространстве имён).

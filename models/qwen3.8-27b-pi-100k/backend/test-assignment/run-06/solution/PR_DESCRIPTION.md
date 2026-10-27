# PR: учёт расходов на LLM-токены

Итоговая реализация тестового задания `TASK.md` (полное ТЗ + бонус).
Symfony 7 console-приложение, PostgreSQL 17, Doctrine ORM, bcmath для денег.

## Что сделано

1. **Импорт справочника цен** — `bin/console app:prices:import <file.json>`
   - proвайдеры: upsert по `code`, `name`/`off_peak` перезаписываются;
   - модели: upsert по `(provider, code)`;
   - строки цен: upsert по `(provider, model, valid_from)` — повторный импорт
     идемпотентен, изменение тарифа меняет значение той же строки;
   - валидация: обязательные поля, UTC-время `...Z`, bcmath-строки без знака
     «-», схемы `per_1m_tokens` / `per_1m_tokens_unified`; `currency` опционален
     (по умолчанию `USD`);
   - любая ошибка — откат всего файла (транзакция), выход ≠ 0.

2. **Импорт событий** — `bin/console app:usage:import <file.jsonl>`
   - дедупликация по `(provider, provider_request_id)` (уникальный индекс в
     БД + проверочная `count`-запись до in-memory-валидации);
   - вывод `inserted=N duplicates_skipped=M`;
   - невалидная строка (битый JSON, отрицательные/не-integer токены,
     не-UTC `ts`, отсутствующие поля) — откат всего файла, выход ≠ 0.

3. **Тарификация** — `bin/console app:billing:rate`
   - цена выбирается по `valid_from <= ts` (последняя подходящая);
   - off-peak: окно `[start, end)` по часам UTC провайдера, может пересекать
     полночь (16:30–00:30), множитель на итог;
   - обе схемы: раздельные тарифы на 4 категории и единый тариф на сумму
     токенов;
   **деньги**: все вычисления — bcmath со шкалой 24, **ровно одно**
   округление HALF_UP до 6 знаков в хвосте (`floor(x*10^6+0.5)/10^6`),
   float нигде не участвует;
   - событие без подходящей цены → `status='unrated'`, **не ошибка**; при
     following-проходе цены появляются — событие рассчитывается (повторная
     попытка встроена в `app:billing:rate`);
   - повторные прогоны ничего не пересчитывают;
   - вывод `RATED=N UNRATED=M`.

4. **Отчёт** — `bin/console app:billing:report [--from TS] [--to TS]`
   - CSV в stdout: `user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd`
     + строка `TOTAL`; группировка `(user, model)`, суммы токенов и стоимостей
     (без повторного округления слагаемых);
   - учитывает только `rated`; интервал по `ts` — `[from, to)`;
   - приёмочный пример TASK.md воспроизведён **байт-в-байт**:

   ```
   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
   u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
   u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
   u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
   TOTAL,,6200000,2300000,2500000,83333,33.662499
   ```

5. **Бонус — ретро-коррекция** — `bin/console app:billing:reprice <file>`
   - секции `providers`/`models` в файле коррекции не обязательны;
   - upsert строк по `(provider, model, valid_from)` для существующих в базе
     провайдера/модели;
   - пересчёт всех `rated`-событий, тарифицированных заменёнными строками,
     по актуальному справочнику (новые цены, off-peak, схема тарифа);
   - stdout ровно две строки: `REPRICED_EVENTS=<n>` и
     `REPRICE_DELTA_USD=<новая − старая, 6 знаков>`;
   - после reprice отчёт показывает пересчитанные суммы без задвоений.

6. **Тесты** (PHPUnit, 30 тестов / 105 ассерций, `make test` → OK)
   - `tests/Domain/` — калькулятор (обе схемы, HALF_UP на дробных долях,
     off-peak), окна off-peak (вкл./искл. границы, пересечение полуночи),
     парсер UTC-мгновений;
   - `tests/Integration/` — сквозные сценарии на реальной БД: приёмочный
     сценарий TASK.md (report байт-в-байт), повторные импорты/раунды
     (идемпотентность), unrated → rated после появления цены, границы
     off-peak-окна, reprice с точной дельтой и пересчитанным отчётом,
     дедупликация из разных файлов, `[from, to)`-фильтр отчёта, откат
     невалидных файлов (токены, ts, JSON, знак минус в цене).

## Ключевые решения и допущения

- `user` — зарезервированное слово PostgreSQL: колонка `usage_events`
  названа `user_login` (сущность/App-код продолжают работать с «user»).
- `currency` в строках цен — опциональное, по умолчанию `USD`
  (в ТЗ: «валюта — по заданию USD; поле в файле опционально»).
- Событие провайдера/модели, для которого нет подходящей по времени цены,
  — это `unrated`, а не ошибка импорта/тарификации (повторно пробует
  следующий `app:billing:rate`, `app:billing:reprice`).
- Импорт цен «заменяет» строки по `(provider, model, valid_from)` (upsert),
  а не дублирует — иначе повторный импорт ломал бы уникальность.
- Схему в БД создаёт **миграция** — накатывается `make migrate`
  (стандартно, без доп. вызова).
- Off-peak-окно: `[start, end)` по UTC-часам провайдера; при пересечении
  полуночи (`start > end`) окно = `[start, 24:00) ∪ [00:00, end)`.
- Дельта reprice — алгебраическая: `Σ(новые) − Σ(старые)`, 6 знаков
  (отрицательная печатается со знаком «−», положительная — без «+», как
  форматирует bcmath).
- Float не используется нигде в денежных путях: только bcmath-строки.
  `int`-токены складываются в SQL — целочисленно.

## Как проверяли

```bash
cp .env.example .env
make db-up                                  # PostgreSQL 17 (порт 5544)
make migrate                                # схема на чистой базе
vendor/bin/phpunit                          # 30 тестов OK

# Приёмка (шаги 3–5 TASK.md):
bin/console app:prices:import  fixtures/prices.json
bin/console app:usage:import   fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# → байт-в-байт совпадает с примером из TASK.md

# Повторно:
bin/console app:prices:import  fixtures/prices.json
bin/console app:usage:import   fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# → идентичный результат (идемпотентность)

# Бонус (reprice):
bin/console app:billing:reprice fixtures/reprice-correction-example.json
# REPRICED_EVENTS=<n>
# REPRICE_DELTA_USD=<x.xxxxxx>
```

## Отступления от каркаса

Каркас (`composer.json`, `docker-compose.yml`, `Makefile`, `phpunit.xml.dist`)
изменён **минимально**:

| Файл | Что/почему |
| --- | --- |
| `phpunit.xml.dist` | добавлены `<php>` — переменные окружения (`APP_ENV=test`, `APP_DEBUG=1`, `APP_SECRET`, `KERNEL_CLASS`) — штатная практика Symfony для тестов; без них `KernelTestCase` не запускается |
| `config/packages/test/framework.yaml` (новый) | `framework.test: true` — штатный механизм доступа к сервисам из тестов |
| `README.md` | расширено описание команд и расчёта (в каркасе было «скелет») |

Новый код — только в `src/`, `tests/`, `migrations/` (см. списки выше).
Зависимости не добавлялись (PHPUnit уже в `vendor/`).

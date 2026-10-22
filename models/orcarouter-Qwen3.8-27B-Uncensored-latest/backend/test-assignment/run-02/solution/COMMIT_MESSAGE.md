feat: учёт расходов на LLM-токены (приём, тарификация, отчёт, ретро-коррекция)

Реализовано консольное приложение на PHP 8.4+ / Symfony 7 / Doctrine ORM 3:
приём справочника цен и событий использования, точная десятичная
арифметика (bcmath), тарификация с учётом истории цен и оф-пика,
двойное списание, CSV-отчёт и бонусная ретро-коррекция цены.

Ключевые блоки
- Приём цен: `app:prices:import` (JSON, upsert по кодам), включая
  провайдеров с off_peak (окно по времени суток, множитель).
- Приём событий: `app:usage:import` (JSONL, дедупликация по
  `(provider, provider_request_id)`, частичная ошибка по строкам в stderr).
- Тарификация: `app:billing:rate` (выбор строки цен по `valid_from ≤ ts`,
  схемы per_1m_tokens / per_1m_tokens_unified, оф-пик, unrated с повтором).
- Отчёт: `app:billing:report --from=… --to=…` (CSV строго по спецификации,
  UTF-8, без unrated, TOTAL на конце).
- Ретро-коррекция (бонус): `app:billing:reprice` (upsert строки цен,
  пересчёт событий, `REPRICED_EVENTS` / `REPRICE_DELTA_USD`).

Технические решения
- Деньги — bcmath (scale 20), единственное округление HALF_UP до 6dp.
- `DateTimeImmutable` не проходит в параметры raw SQL; ввела хелпер
  `App\Support\SqlDateTime::utc()` (строка `Y-m-d H:i:sP`).
- Отказ от DBAL 4.5 `query()`, `transaction()`, `executeUpdate()`:
  `fetchAllAssociative()`, `wrapInTransaction()`, `executeStatement()`.
- Dynamic WHERE в `ReportService` (без `IS NULL OR` — Postgres 42P18).
- `fetchAssociative()` сравнивают с `false`, не с `null`.
- Миграция — чистый SQL в heredoc (Schema API в DBAL 4.5 для nullable
  column даёт ошибку).

Тесты
- 25 unit-тестов: `Decimal`, `OffPeak`, `BillingEngine` (25 тестов,
  31 ассертация, `vendor/bin/phpunit`).
- Полный прогон на чистом окружении (DROP DATABASE → make migrate →
  импорты → rate → report) — байт-в-байт совпадает с `expected.txt`.
- Проверены edge-кейсы: unrated, off-peak, дубли, reprice с дельтой 0,
  частичная ошибка JSONL (exit 1).

Изменённые файлы каркаса: добавлены 23 файла src/, 1 миграция,
3 теста (tests/), 2 отчётных файла. Зависимостей новых не добавлено.

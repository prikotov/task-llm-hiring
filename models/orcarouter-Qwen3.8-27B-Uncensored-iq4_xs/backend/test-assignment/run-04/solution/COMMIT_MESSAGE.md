feat: учёт расходов на LLM-токены — импорт, тарификация, CSV-отчёт, ретро-коррекция

Консольное приложение для учёта расходов на токены с двумя входными
потоками (справочник цен JSON, события использования JSONL):

- app:prices:import — идемпотентный upsert провайдеров, моделей и строк
  цен (ключи: code; (provider, code); (provider, model, valid_from));
- app:usage:import — приём событий с дедупликацией по
  (provider, provider_request_id): дубли игнорируются, код 0;
- app:billing:rate — тарификация нерассчитанных событий (с повторными
  попытками unrated), вывод RATED=<n> UNRATED=<m> в stdout, строки
  UNRATED <provider>/<id> <ts> no_price — в stderr;
- app:billing:report — CSV по (user, <provider>/<model>) за период
  [from, to), TOTAL-строка; unrated не попадают;
- app:billing:reprice (бонус) — ретро-коррекция строк цен с пересчётом
  затронутых событий: REPRICED_EVENTS=<n>, REPRICE_DELTA_USD=<x.xxxxxx>.

Ключевые решения:

- деньги — только bcmath (Money: строка, масштаб 20), float исключён;
  единственное округление — итог события до 6 знаков HALF_UP;
- выбор цены — строка с наибольшим valid_from ≤ ts (включительно);
- оф-пик — окно [start, end) минут UTC, переход через полночь,
  множитель применяется к стоимости всего события;
- reprice — перезапись стоимости затронутых событий (без задвоений в
  отчёте; повторяющаяся коррекция без изменений не трогает события).

Схема БД разворачивается миграцией (make migrate). Добавлены юнит-тесты
(Money, EventPricingCalculator, OffPeakWindow, BillingReportBuilder) —
28 тестов. Рабочий пример из TASK.md воспроизводится байт-в-байт.

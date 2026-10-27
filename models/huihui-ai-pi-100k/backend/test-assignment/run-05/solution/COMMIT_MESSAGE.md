# Учёт расходов на LLM-токены: консольное приложение

Реализованы потоки загрузки справочника цен и событий использования,
тарификация, CSV-отчёт по расходам и бонус-команда ретро-коррекции цен.

- `app:prices:import` — идемпотентный upsert провайдеров/моделей/строк цен
  (естественные ключи: `code`; `(provider, code)`; `(provider, model, valid_from)`)
- `app:usage:import` — приём событий (JSONL) с дедупликацией по
  `(provider, provider_request_id)` на уровне БД (`INSERT ... ON CONFLICT DO NOTHING`)
- `app:billing:rate` — тарификация всех unrated-событий; выбор цены — строка
  с наибольшим `valid_from <= ts`; схемы `per_1m_tokens` / `per_1m_tokens_unified`;
  оф-пиковый множитель; bcmath, единственное округление HALF_UP до 6 знаков;
  unrated — не ошибка, повторяются при каждом запуске
- `app:billing:report` — CSV в stdout: группировка по (user, provider/model),
  сортировка, период [from, to), unrated исключены, TOTAL-строка
- `app:billing:reprice` (бонус) — применение исправленных строк и пересчёт
  затронутых событий; `REPRICED_EVENTS` / `REPRICE_DELTA_USD` в stdout;
  итоговый отчёт обновлён без задвоений

Схема БД разворачивается миграцией (`make migrate`). Деньги — только
десятичные строки (bcmath), float запрещён. Вывод: данные — stdout,
диагностика — stderr. Юнит-тесты доменных правил (MoneyMath, OffPeak,
EventPricer) + E2E по фикстурам: отчёт сентября совпадает с эталоном
байт-в-байт.

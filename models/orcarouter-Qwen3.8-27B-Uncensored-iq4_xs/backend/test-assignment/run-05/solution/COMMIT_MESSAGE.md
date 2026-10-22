feat(billing): учёт расходов LLM-токенов — импорт, тарификация, CSV-отчёт, retro-коррекция цен

Консольное приложение (Symfony console, Doctrine ORM, PostgreSQL 17):
- app:prices:import / app:usage:import — идемпотентные импорты
  справочника и событий (JSONL, ON CONFLICT DO NOTHING), проверка
  корректности входных файлов, код 1 при ошибках;
- app:billing:rate — тарификация по времени события с учётом смены цен,
  двух схем тарификации (per-token / unified) и off-peak-окна в UTC;
  stdout строго RATED=<n> UNRATED=<m>, unrated-детали в stderr;
- app:billing:report — CSV по (user, model) + TOTAL, cost_usd с 6
  знаками, период [from, to); на закреплённом примере — точное
  совпадение байт-в-байт;
- app:billing:reprice (бонус) — upsert строк цен и пересчёт событий,
  тарифицированных затронутыми строками; stdout REPRICED_EVENTS=<n>
  и REPRICE_DELTA_USD=<x.xxxxxx>.

Деньги — только bcmath-строки (тип float не используется), единственное
округление — HALF_UP стоимости события до 6 знаков; выбор цены — строка
с наибольшим valid_from ≤ ts; рассчётанное событие фиксирует ключ
applied_price_valid_from. Новая схема — миграция Version20261020000001
(4 таблицы, ограничения, индексы). Файлы каркаса не затрагиваются,
зависимости не добавлялись; 32 юнит-теста (make test).

Подробности и допущения — в PR_DESCRIPTION.md.

feat(billing): учёт расходов на LLM-токены — импорт цен и событий, тарификация, отчёт, ретро-коррекция

Реализовано задание из TASK.md:

- app:prices:import — идемпотентная загрузка справочника (провайдеры,
  модели, история цен) из JSON;
- app:usage:import — приём событий использования из JSONL с дедупликацией
  по (provider, provider_request_id);
- app:billing:rate — тарификация по истории цен (per_1m_tokens /
  per_1m_tokens_unified), оф-пиковая скидка провайдера, точная
  десятичная арифметика (bcmath) с единственным HALF_UP-округлением
  стоимости события до 6 знаков; unrated-события повторяются в каждом
  запуске;
- app:billing:report — CSV по (user, model) за период --from/--to
  (включительно/исключительно), строго 6 знаков, TOTAL, вывод в stdout;
- app:billing:reprice (бонус) — ретро-коррекция строк цен с
  пересчётом уже тарифицированных событий, журналом price_corrections
  и выводами REPRICED_EVENTS / REPRICE_DELTA_USD.

Схема разворачивается миграцией (migrations/Version20261017082358.php)
на чистой базе PostgreSQL 17. Приёмка по фикстурам воспроизводится
байт-в-байт. Добавлены PHPUnit-тесты (76 тестов, 106 ассерций):
Decimal, RateCalculator, OffPeakPolicy/TimeOfDay, парсеры цен и событий.

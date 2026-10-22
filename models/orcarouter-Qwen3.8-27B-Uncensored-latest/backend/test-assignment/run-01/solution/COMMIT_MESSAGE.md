# Commit message

```
feat(billing): учёт стоимости LLM-токенов: импорт тарифов и событий, тарификация, отчёт, ретро-коррекция

Пять консольных команд по TASK.md:

- app:prices:import <json> — импорт/актуализация каталога (провайдеры,
  модели, история цен incl. off-peak-окна); идемпотентен (upsert по
  (provider, model, valid_from));
- app:usage:import <jsonl> — импорт событий; идемпотентен по
  (provider, provider_request_id);
- app:billing:rate — тарификация событий: цена по valid_from <= ts
  (последняя подходящая), пер-токен и unified схемы, off-peak-множитель
  (окно с обходом полуночи, полуинтервал), все вычисления — bcmath,
  единственное округление HALF_UP до 6 знаков на событии; нерассчитанные
  события остаются pending, в stderr — одна строка на событие;
- app:billing:report --from <t> --to <t> — CSV в stdout (заголовок,
  user+model, токены, cost, TOTAL), сортировка user→model, байт-в-байт
  как в примере TASK.md;
- app:billing:reprice <json> — замена строки цены; пересчёт затронутых
  событий; stdout: REPRICED_EVENTS=<n>, REPRICE_DELTA_USD=<x.xxxxxx>;
  журнал в reprice_entries.

Схема: миграция Version20261020120000InitialSchema (5 таблиц; колонка
«user» названа user_id — зарезервированное слово PostgreSQL).
Домен: Money/OffPeakWindow/RateCalculator на bcmath; парсеры входных
файлов с валидацией (ISO-8601 UTC, HH:MM, деньги 0..6 знаков, int-токены).
Тесты: unit (Money, OffPeakWindow, RateCalculator, парсеры) и сквозной
контекстный тест (импорт → rate → report → реимпорт → reprice) против
живой PostgreSQL; при отсутствии БД прокси/пропускается.
```

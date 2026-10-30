billing: учёт расходов на LLM-токены (импорт, тарификация, отчёт, reprice)

Реализовано задание из TASK.md целиком, включая бонус.

- Консольные команды:
  * app:prices:import  — импорт/обновление справочника цен (upsert по
    (provider, model, valid_from); провайдеры/модели — по code/коду модели;
    off-peak окна провайдеров обновляются по code), атомарность файла;
  * app:usage:import   — импорт событий JSONL с дедупликацией по
    (provider, provider_request_id), вывод inserted=/duplicates_skipped=;
  * app:billing:rate   — тарификация нерассчитанных событий по последней
    цене valid_from <= ts, off-peak оконный множитель, обе схемы тарифных
    единиц, ровно одно округление HALF_UP до 6 знаков (bcmath, float нет);
  * app:billing:report — CSV по (user, model) + TOTAL, интервал [from, to);
  * app:billing:reprice — ретро-коррекция цены + пересчёт событий,
    тарифицированных заменёнными строками: REPRICED_EVENTS= / REPRICE_DELTA_USD=.
- Слой: App\Domain\Billing (PricingCalculator, OffPeakWindow, PriceUnit,
  UtcInstant, Decimals, EventStatus), App\Entity\Billing,
  App\Repository\Billing, App\Service{,/Billing}, App\Command.
- Схема БД — миграция migrations/Version20260915090000.php (колонка user_login,
  т.к. user — зарезервировано в PostgreSQL).
- Тесты: 30 (105 assertions) — доменные + интеграционные
  (приёмочный сценарий TASK.md байт-в-байт, идемпотентность, окна off-peak,
  unrated→rated, reprice с дельтой, дедупликация, откат невалидного файла,
  фильтры отчёта). make test — OK.

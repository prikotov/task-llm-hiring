feat: учёт расходов на LLM-токены (billing)

Реализовано консольное приложение для тарификации LLM-токенов по фиксированным
историям цен и журналам событий:

- домен `Money`/`RateCalculator`/`OffPeakWindow` — точная десятичная
  арифметика (bcmath) без float; округление HALF_UP — ровно одно, по
  итогу события, до 6 знаков;
- сущности `Provider`, `Model`, `Price` (история цен),
  `UsageEvent` (событие), `UsageRating` (журнал тарификации);
- команды `app:prices:import`, `app:usage:import` (идемпотентные
  импорты с дедупликацией по (provider, provider_request_id)),
  `app:billing:rate` (тарификация pending-событий, RATED/UNRATED-сводка),
  `app:billing:report` (CSV-отчёт по (user, model), сортировка, TOTAL),
  `app:billing:reprice` (бонус: ретро-коррекция цены с дельтой,
  идиомпотентный повторный запуск);
- схема БД разворачивается на чистой базе через
  `doctrine:schema:create`;
- юнит-тесты на Money/RateCalculator/OffPeakWindow + интеграционные
  тесты импорта, rate, report, reprice, unrated.

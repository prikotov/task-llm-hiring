feat: учёт расходов на LLM-токены по событиям использования

Реализация задания по учёту расходов на LLM:
  * импорт цен (providers / models / prices) и idempotency по
    (provider, code), (provider, model, valid_from);
  * импорт событий использования (JSONL) с дедупликацией по
    (provider, provider_request_id);
  * тарификация событий по схеме per_1m_tokens / per_1m_tokens_unified
    с учётом окна оф-пика провайдера (может переходить через полночь),
    точная десятичная арифметика, HALF_UP-округление до 6 знаков;
  * CSV-отчёт по (user, model) с суммой по событиям группы и
    TOTAL-строкой; период [from, to);
  * команда app:billing:reprice — ретро-коррекция цены с
    пересчётом событий, закреплённых за данной строкой.

Точки, за которые отвечает код:

- бcmath-математика (запрет float для денег);
- идемпотентность приёма (повторные поставки не меняют данных);
- unrated-события не попадают в отчёт и могут быть пересчитаны
  повторным запуском app:billing:rate;
- окно оф-пика [start, end) может переходить через полночь
  (напр. 16:30 → 00:30).

Приёмка (на чистой базе, после `make db-up && make migrate`):

    $ bin/console app:prices:import fixtures/prices.json
    providers: 2, models: 3, prices: 4
    $ bin/console app:usage:import fixtures/usage.jsonl
    строк: 3, вставлено: 3, дубли: 0
    $ bin/console app:billing:rate
    RATED=3 UNRATED=0
    $ bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
    user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
    u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
    u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
    u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
    TOTAL,,6200000,2300000,2500000,83333,33.662499

Устойчивость: повторный импорт тех же файлов + rate + report — результат не
меняется.

Unit-тесты — tests/ (8 тестов: EventPricing + OffPeakWindow).

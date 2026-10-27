Учёт расходов на LLM-токены: тарификация, идемпотентный приём, отчёт

Консольное приложение (Symfony Console + Doctrine ORM + PostgreSQL 17)
считает расходы на токены LLM с историей цен, двумя схемами тарификации,
оф-пиковой скидкой в окне UTC, точной десятичной арифметикой (bcmath,
float для денег не используется) и идемпотентным приёмом данных.

Команды:
  bin/console app:prices:import <prices.json>
  bin/console app:usage:import <usage.jsonl>
  bin/console app:billing:rate                     (RATED=<n> UNRATED=<m> в stdout;
                                                    unrated-события — в stderr)
  bin/console app:billing:report [--from=ISO] [--to=ISO]   (CSV в stdout)
  bin/console app:billing:reprice <corrections.json>       (бонус: ретро-коррекция цен)

Ключевые правила:
  - цена события — строка с наибольшим valid_from ≤ ts (включительно);
  - per_1m_tokens — ставки по видам input/output/cache_read/cache_write;
    per_1m_tokens_unified — единый rate_any;
  - оф-пик: окно [start, end) UTC (возможен переход через полночь),
    множитель применяется ко всей стоимости события;
  - ровно одно округление HALF_UP до 6 знаков на итог события;
  - суммы отчёта — сумма уже округлённых стоимостей, без повторного
    округления слагаемых;
  - идемпотентность: провайдеры по code, модели по (provider, code),
    строки цен по (provider, model, valid_from), события по
    (provider, provider_request_id) — повторная доставка дубля
    игнорируется, unrated-события пере-тарифицируются при каждом
    запуске, пока для них нет подходящей строки цены.

Приёмка: пример из TASK.md на fixtures/ воспроизводится в точности
(см. PR_DESCRIPTION.md); юнит-тесты домена — `vendor/bin/phpunit`
(10 тестов, 28 ассерций).

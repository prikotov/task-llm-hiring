Биллинг LLM-токенов: PHP/Symfony/Doctrine

* Доменная модель (PriceSheet, Provider, Model, UsageEvent, Money)
  и точная арифметика bcmath: без float, масштаб 40, ROUND_HALF_UP
  до 6 знаков после точки.
* Импорт: провайдеры/модели/тарифы из JSON (upsert по
  (provider, model, valid_from)), события-использования из JSONL
  (детект дублей по (provider, provider_request_id)).
* Тарификация: выбор последней строки тарифа с valid_from ≤ ts,
  учёт off-peak окна провайдера (вкл. окно с пересечением
  полночи), единая ставка per_1m_tokens_unified или
  раздельные input/output/cache_read/cache_write.
* Отчёт в CSV: суммирование по (user, model) + строка TOTAL,
  фильтр по [from, to), без unrated.
* Bonus: `app:billing:reprice` — применить исправленные тарифы
  и пересчитать rated-события, вернув число и дельту.
* Адаптеры: PostgreSQL 17 + Doctrine ORM (PSR-4 App\), бинарные
  команды bin/console.
* Тесты: 15 тестов MoneyTest (HALF_UP), полный acceptance-тест
  через fixtures.

feat: учёт расходов на LLM-токены — полное решение

Реализовано консольное приложение для учёта расходов на LLM-токены:

- Импорт справочника цен (провайдеры, модели, строки цен) из JSON
- Импорт событий использования из JSONL с дедупликацией
  по (provider, provider_request_id)
- Тарификация событий с учётом:
  - выбора цены по valid_from (наибольшая ≤ ts)
  - двух схем тарификации (per_1m_tokens, per_1m_tokens_unified)
  - оф-пиковой скидки (включая окно, переходящее через полночь)
  - точной десятичной арифметики (bcmath, масштаб 20)
  - округления HALF_UP до 6 знаков
- CSV-отчёт с группировкой по (user, model) и итоговой строкой TOTAL
- Ретро-коррекция цен (app:billing:reprice) с пересчётом
  затронутых событий

Новые файлы:
- migrations/Version20260915000001.php — схема БД
- src/Domain/Money.php — десятичная арифметика
- src/Domain/OffPeakWindow.php — логика оф-пикового окна
- src/Service/BillingEngine.php — расчёт стоимости события
- src/Service/PricesImporter.php — импорт цен
- src/Service/UsageImporter.php — импорт событий
- src/Service/RateEngine.php — тарификация
- src/Service/ReportGenerator.php — генерация отчёта
- src/Service/RepriceEngine.php — ретро-коррекция
- src/Command/ (5 команд)

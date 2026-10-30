feat(billing): учёт расходов LLM-токенов — импорт справочника и событий, тарификация, CSV-отчёт, reprice

Консольное приложение (Symfony Console + Doctrine ORM + PostgreSQL)
для учёта расходов на LLM-токены по заданию TASK.md.

- Домен на точных десятичных (bcmath, без float): Money (HALF_UP до 6
  знаков, промежуточный масштаб 24), OffPeak (UTC-окно/ночное),
  PriceSpec (per_1m × 4 ключа или unified "any"), PricingCalculator.
- Сущности и миграция: providers, models, price_rows (история цен с
  valid_from), usage_events (уникальный ключ provider + request_id).
- Команды:
    app:prices:import   — загрузка JSON-справочника (идемпотентно);
    app:usage:import    — загрузка JSONL событий, дубликаты игнорируются;
    app:billing:rate    — тарификация unrated-событий (в т.ч. повторная);
    app:billing:report  — CSV-отчёт (stdout/`--out`), байт-в-байт как в примере;
    app:billing:reprice — бонус: ретро-коррекция цены и пересчёт затронутых событий.
- Строгий ввод: ISO-8601 UTC (round-trip проверка даты), ставки только
  строки, токены — целые ≥ 0; невалидный файл → сообщение и exit 1.
- Тесты: 36 доменных + 4 функциональных (полный acceptance, идемпотентность,
  unrated→rated при поздней цене, reprice, ошибки ввода) — 40/40, 84 assertions.
- Файлы отчёта: COMMIT_MESSAGE.md и PR_DESCRIPTION.md — созданы в корне.

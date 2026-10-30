feat: учёт расходов на LLM-токены (импорт цен/событий, тарификация, отчёт, reprice)

- Pricing: провайдеры с оф-пик окном, модели, история цен с двумя схемами
  тарификации (per_1m_tokens, per_1m_tokens_unified); идемпотентный upsert.
- Usage: приём JSONL-событий с дедупликацией по (provider, provider_request_id)
  через уникальный индекс и ON CONFLICT DO NOTHING.
- Billing: тарификация по строке с наибольшим valid_from <= ts, оф-пик,
  bcmath (scale 24), одно округление события HALF_UP до 6 знаков; повтор
  для unrated; CSV-отчёт за период [from, to).
- Бонус: app:billing:reprice — ретро-коррекция цены с пересчётом затронутых
  событий и журналом cost_adjustment.
- Миграция схемы БД, юнит-тесты доменной логики.

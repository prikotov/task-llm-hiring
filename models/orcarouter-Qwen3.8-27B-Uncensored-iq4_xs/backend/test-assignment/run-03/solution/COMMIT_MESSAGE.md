feat: реализовать учёт расходов на LLM-токены

- Доменная модель: Provider (с оф-пиковым окном), Model, PriceLine
  (per-token / unified, история цен по valid_from), UsageEvent
  (естественный ключ (provider, provider_request_id), статусы rated/unrated).
- Бэйкинг-логика на bcmath (float запрещён): точные промежуточные вычисления
  (scale 16), единственное округление стоимости события до 6 знаков HALF_UP.
- Команды: app:prices:import, app:usage:import (дедупликация),
  app:billing:rate, app:billing:report (CSV, --from/--to),
  app:billing:reprice (бонусная ретро-коррекция цен).
- Миграция схемы БД разворачивается на чистой базе (make migrate).
- Тесты: Money (HALF_UP), оф-пик (вкл. переход через полночь и границы),
  расчёт стоимости (per-token/unified/оф-пик/нулевые токены) — 27 OK.

# mimo-v2.6-flash-off (xiaomi подписка, via pi)

## Характеристики

- Модель: `mimo-v2.6-flash` — быстрая MiMo на подписке Xiaomi
  (token plan SGP), custom id через `--provider xiaomi-token-plan-sgp`
- Транспорт: `knowledge-map/runner_pi.py` (pi -p --no-session per
  клетка, `--mode json` отдаёт usage)
- Thinking off у xiaomi честно отключает размышление: 0 reasoning-
  токенов на смоуке и 0 thinking-текста в прогоне

## Карта знаний PHP (2026-11-12, 1 прогон off)

- База (L1–L8): **209/240 = 87.1%**
- Глубина (L9/L10): **41/56 = 73.2%**
- Всего: **250/296 = 84.5%**
- Токены: 30k входных + 15k выходных — самый экономный прогон карты
- Скорость: ~4.8 с/клетку, 23.8 мин на 296 клеток

Профиль: середина доски — рядом с orca off (84.8 базы / 73.9 Глубины)
и huihui on. Быстро и дёшево, на фронтире L9/L10 слабее.

Результаты: knowledge-map/run-01 (report.html, session.json,
answers.csv).

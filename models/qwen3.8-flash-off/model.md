# qwen3.8-flash-off (alibabacloud qwen-token-plan, via pi)

## Характеристики

- Модель: `qwen3.8-flash` — быстрая Qwen на подписке alibabacloud
  (Model Studio token plan), custom id через
  `--provider qwen-token-plan-individual`
- Транспорт: `knowledge-map/runner_pi.py` (pi -p --no-session per
  клетка, `--mode json` отдаёт usage)
- Нюанс: `--thinking off` у flash не выключает размышление полностью —
  бэкенд оставляет лёгкое (level low, ~39 reasoning-токенов на клетку);
  зафиксировано как off с оговоркой

## Карта знаний PHP (2026-11-12, 1 прогон off/low-quirk)

- База (L1–L8): **223/240 = 92.9%**
- Глубина (L9/L10): **51/56 = 91.1%** — второй эшелон доски (после
  qwen4-max-preview 96.4 и deepseek/Haiku 94.6–96.4)
- Всего: **274/296 = 92.6%**
- Токены: 55k входных + 68k выходных (68k — с учётом лёгкого
  reasoning на каждой клетке)
- Скорость: ~5 с/клетку, 25.2 мин на 296 клеток

Любопытно: Глубина (91.1%) заметно выше базы (92.9% базы против
qwen3.8:27b low 93.8 на ru) — flash на фронтире знаний удерживается
близко к флагманам. Сравнение с qwen3.8:27b low (Глубина 83.9 на ru):
flash +7.2 п.п.

Результаты: knowledge-map/run-01 (report.html, session.json,
answers.csv).

# qwen3.8-flash (alibabacloud qwen-token-plan)

## Характеристики

- Модель: `qwen3.8-flash` — быстрая Qwen на подписке alibabacloud
  (Model Studio token plan), custom id через
  `--provider qwen-token-plan-individual`
- Транспорт: `knowledge-map/runner_pi.py` (pi -p --no-session per
  клетка, `--mode json` отдаёт usage)
- **Нюанс off (исправлено 2026-11-12)**: pi не передаёт alibabacloud
  `enable_thinking:false` — запрошенный off эскалируется в low
  (~39 reasoning-токенов на клетку); «off»-прогон от 2026-11-12
  фактически был low и перенесён в run-02 этой серии. Нативная шкала
  провайдера: off (enable_thinking:false) / low / medium / high —
  будущие прогоны off/medium пойдут через прямой транспорт
  runner_cloud.py

## Карта знаний PHP (2026-11-12, 2 прогона low)

- run-01 (low): база **224/240 = 93.3%**, Глубина **51/56 = 91.1%**,
  всего **275/296 = 92.9%**; 55k вх + 68k вых; 24 мин
- run-02 (low, изначально запущен как «off»): база **223/240 = 92.9%**,
  Глубина **51/56 = 91.1%**, всего **274/296 = 92.6%**; 55k вх + 68k вых;
  25.2 мин
- Два прогона low совпадают в пределах шума (±0.4 п.п. база, Глубина
  равная) — как и положено прогонам одного уровня; «off ≈ low» из
  первого сравнения было сравнением двух прогонов low

Любопытно: Глубина (91.1%) выше базы (93%) — flash на фронтире знаний
удерживается близко к флагманам (qwen3.8:27b low на ru: Глубина 83.9,
flash +7.2 п.п.).

Результаты: knowledge-map/run-01, run-02 (report.html, session.json,
answers.csv).

# openai/gpt-6-luna (OpenRouter)

## Характеристики

- Провайдер: OpenRouter (`openai/gpt-6-luna`), доступ через прокси
  (WAF OpenRouter режет прямой доступ); ключ —
  `OPENROUTER_KEY_FILE`, траты — журнал `reports/openrouter-spend.csv`.
- Контекст: 1 050 000; цены OpenRouter (2026-11-08): $0.10/1M вх,
  $0.50/1M вых; supported_parameters: reasoning, reasoning_effort,
  structured_outputs и др.
- Тестовая операция: `--api openrouter` раннера карты
  (knowledge-map/runner_map.py), reasoning_effort low (стандарт облачных
  серий).

## Карта знаний PHP (knowledge-map, 2026-11-08) — калибровка потолка, «разок»

Один прогон run-01 × 296 клеток (база 240 + deep v4 56), ru, effort low —
**второй калибровочный якорь сверху** после deepseek-flash:

- База L1–L8: **96.2%** (231/240).
- Глубина: **94.6%** — L9 **100%**, L10 **89.3%**.
- JSON-дисциплина: 100% (серых 0); время 995 с (~3.4 с/клетка);
  41 221 вх / 21 952 вых ток; стоимость **$0.0151** ($0.011 база +
  $0.004 deep) — cheap-тир OpenRouter, дороже deepseek-flash не оказалось.
- Промахи в глубине (3): T11-L10 (перезапись авто-ключа при yield from),
  T12-L10 (бит E_STRICT), T18-L10 (счёт «выполнено + failure» = 2,1) —
  **ровно те же три клетки, что провалил deepseek-flash** (0/5 там):
  независимое подтверждение, что это честный машинный «край берега»,
  а не артефакт грейдера.
- Полная шкала глубины: deepseek 94.3 / Luna 94.6 → huihui medium
  87.1–90.0 → huihui off/low 70.4–88.6 → medgemma 59.6–62.9 →
  gemma4:e2b 35.0–36.8. Два сильных якоря сошлись в 94–95% на L10
  88–89% — потолок зоны измерен, сатурации нет.
- Effort medium (run-02, второй «разок»): база **98.3%** (лучше low),
  Глубина **92.9%** (L10 85.7 — хуже low 89.3): medium точнее на базе,
  но T21-L10 перевернулась ✓→✗ (Twig-приоритет — «передумал»). Твёрдое
  ядро промахов неизменно: T11/T12/T18-L10 у low и medium, те же у
  deepseek-flash. $0.018 (1201 с). Вывод: effort medium сатурацию базы
  снимает (98.3%), глубина сильных моделей стоит на 93–95% независимо
  от effort.
- Серия из одиночных прогонов: в сводном отчёте серий не участвует
  (нужно ≥2 на один think), карты и цифры — run-01 (low),
  run-02 (medium), report.html в каждом.
Детали: knowledge-map/run-01 (session.json, report.html),
reports/openrouter-spend.csv.

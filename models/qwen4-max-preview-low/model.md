# qwen4-max-preview-low (alibabacloud qwen-token-plan, via pi)

## Характеристики

- Модель: `qwen4-max-preview` — флагманская Qwen следующего поколения
  на подписке alibabacloud (Model Studio token plan); custom id через
  `--provider qwen-token-plan-individual`
- Транспорт: `knowledge-map/runner_pi.py` (pi -p --no-session per клетка,
  тот же протокол и грейдинг, что в runner_map; `--mode json` отдаёт
  usage: input/output/cacheRead/reasoning)
- Шкала мышления модели: off → xhigh (проверено, high маппится в xhigh);
  этот прогон — low

## Карта знаний PHP (2026-11-12, 1 прогон low)

- База (L1–L8): **237/240 = 98.8%**
- Глубина (L9/L10): **54/56 = 96.4%**
- Всего: **291/296 = 98.3%**
- Токены: 54k входных + 33k выходных (reasoning-токены алibaba считает
  отдельно и в output не включает — реальный расход чуть выше)
- Скорость: ~5 с/клетку, 25.7 мин на 296 клеток

Сравнение с off-прогоном (qwen4-max-preview-off): база +1.7 п.п.
(98.8 против 97.1), Глубина равная (96.4), токены даже чуть меньше —
на этом тесте low лучше off по качеству без роста расхода. Ошибки:
3 клетки базы (PHPUnit-детали, Twig-исключение, типы) и 2 Глубины.

Верх лидерборда карты: 98.3% всего — выше solo-прогонов sol (98.2) и
всех локальных/облачных серий.

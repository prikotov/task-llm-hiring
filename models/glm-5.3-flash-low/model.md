# glm-5.3-flash-low (z.ai подписка)

## Характеристики

- Транспорт: `knowledge-map/runner_cloud.py` (прямой API);
  low = `thinking enabled + reasoning_effort low`
- **off у модели не существует** (документация z.ai, GLM-5.3/Flash):
  «thinking.type only supports enabled; thinking cannot be disabled»
  ([Deep Thinking](https://docs.z.ai/guides/capabilities/thinking),
  [GLM-5.3-Flash](https://docs.z.ai/guides/vlm/glm-5.3-flash)). Прогоны,
  запрошенные как off (thinking.type=disabled), провайдер выполнил как
  форсированное минимальное мышление — фактический уровень low; по
  решению владельца серия объединена с low: run-01…15 — «чистый» low,
  run-16…30 — бывшие «off» (relabel в session.json). Итого 10 прогонов
  на язык, 30 в серии
- Уровень medium задокументирован не был (в доках уровни усилия
  low/high/max), но принимается провайдером и даёт отдельное,
  промежуточно-высокое поведение — см. glm-5.3-flash-medium

## Карта знаний PHP (2026-11-13, 10 прогонов × ru/en/zh)

База L1–L8 (240) / Глубина L9·L10 (по 28 клеток на прогон), среднее
за 10 прогонов:

- ru: база 94.2, L9 87.1 / L10 69.6
- en: база 95.9, L9 92.1 / L10 74.3
- zh: база 94.2, L9 90.7 / L10 75.0

- Бывшие «off» и «чистый» low статистически неразличимы (база ±1.4,
  L9 ±3.4 п.п. между блоками) — как и ожидается для одного уровня
- Стабильность: флипов 52–63 из 296
- Скорость: 12–17 мин на прогон, 15–16k выходных токенов

Результаты: knowledge-map/run-01…30.

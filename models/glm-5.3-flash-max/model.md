# glm-5.3-flash-max (z.ai подписка)

## Характеристики

- Транспорт: `knowledge-map/runner_cloud.py` (прямой API);
  max = `thinking enabled + reasoning_effort max` — документированный
  дефолт модели ([Core Parameters](https://docs.z.ai/guides/overview/concept-param):
  «GLM-5.3 and GLM-5.3-FLASH models only support max, high, low»)
- Статус: пробный прогон (1, ru) для оценки затрат — решение о полной
  матрице (5×3 ≈ 15 прогонов, ~10 ч, ≈$1.05 API-эквивалент) по команде

## Карта знаний PHP (2026-11-14, 1 прогон ru)

- База (L1–L8): **233/240 = 97.1%**
- Глубина: **L9 28/28 = 100%** (впервые на карте), **L10 25/28 = 89.3%**
  (уровень qwen4-max-preview low)
- Всего: **286/296 = 96.6%**
- Токены: 42.8k вх + 127.2k вых, из них reasoning **112.3k (88%
  выхода)** — max почти всё «думает»
- Время: 40.7 мин (low ~15, medium ~17)
- Руминаций (≥10k reasoning): 2
- Стоимость: ≈ **$0.07** за прогон по API-тарифам z.ai ($0.15/1M вх,
  $0.50/1M вых) — ~3–4× medium, ~5× low; по подписке — 170k токенов
  квоты

Профиль: max закрывает L9 полностью и поднимает L10 на уровень
флагманов; цена — 2.4× медленнее medium и 8× больше выходных токенов.

Результаты: knowledge-map/run-01 (report.html, session.json,
answers.csv).

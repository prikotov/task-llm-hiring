# huihui_ai/Qwen3.8-abliterated:latest

Источник: ollama на nserv (`http://nserv:11434`).

- Архитектура/семейство: qwen35
- Параметры: 27.3B
- Квантизация: Q4_K_M
- Контекст: 262144
- Размер: 16.5 GB
- Возможности: completion, vision, tools, thinking
- Мышление: та же ручка, что у qwen3.8 — `false / low / medium / xhigh`,
  дефолт `medium` (проверено на nserv, ollama 0.40.0; `/api/show` →
  `thinking.values`). Серии backend run-01…10 — `think: false` поверх
  дефолтного `medium`, т.е. напрямую сопоставимы с qwen3.8 `think: false`.
- Дайджест: `bdc5e13cd1645bc4…`
- Обновлён: 2026-10-12

Статус: папка создана, тестов пока не было. Результаты — рядом, в этой папке.

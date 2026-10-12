# huihui_ai/Qwen3.8-abliterated:latest

Источник: ollama на ollama-host (`http://ollama-host:11434`).

- Архитектура/семейство: qwen35
- Параметры: 27.3B
- Квантизация: Q4_K_M
- Контекст: 262144
- Размер: 16.5 GB
- Возможности: completion, vision, tools, thinking
- Мышление: та же ручка, что у qwen3.8 — `false / low / medium / xhigh`,
  дефолт `medium` (проверено на ollama-host, ollama 0.40.0; `/api/show` →
  `thinking.values`). Выставлять — полем верхнего уровня тела `/api/chat`
  (не в `options`), уровень строкой:
  `curl http://ollama-host:11434/api/chat -d '{"model":"huihui_ai/Qwen3.8-abliterated:latest","messages":[…],"think":"low"}'`.
  Серии backend run-01…10 — `think: false` поверх дефолтного `medium`,
  т.е. напрямую сопоставимы с qwen3.8 `think: false`.
- Дайджест: `bdc5e13cd1645bc4…`
- Обновлён: 2026-10-12

Статус: папка создана, тестов пока не было. Результаты — рядом, в этой папке.

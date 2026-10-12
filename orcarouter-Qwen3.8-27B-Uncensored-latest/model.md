# orcarouter/Qwen3.8-27B-Uncensored:latest

Источник: ollama на ollama-host (`http://ollama-host:11434`).

- Архитектура/семейство: qwen35
- Параметры: 27.3B
- Квантизация: Q4_K_M
- Контекст: 262144
- Размер: 16.5 GB
- Возможности: completion, vision, tools, thinking
- Мышление: параметр `think` — только `false / true`, градаций уровня нет
  (в отличие от базовой qwen3.8: у unc-сборок шкала `low/medium/xhigh`
  потеряна), дефолт **включено** (проверено на ollama-host, ollama 0.40.0;
  `/api/show` → `thinking.values`). Серии backend run-01…10 — `think: false`,
  дефолт отключён сознательно.
- Дайджест: `6fac2f98fdf716f2…`
- Обновлён: 2026-10-12

Статус: папка создана, тестов пока не было. Результаты — рядом, в этой папке.

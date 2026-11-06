# clef:latest

Decision-модель яруса 1 (судья собесов, `interview/score.py`) — с 2026-11-06
и как испытуемая: карта знаний PHP (`knowledge-map/run-01`).

Источник: ollama на ollama-host (`http://ollama-host:11434`).

- Архитектура/семейство: qwen35
- Параметры: 9.1B
- Квантизация: Q8_0
- Размер: 16.7 GB
- Возможности: decision, vision (thinking нет)
- Дайджест: `c4cc0553f8e183629aa9…`

Статус: 2026-11-06 модель на сервере в неподдерживаемом формате —
`/api/chat` отвечает «does not support chat», `/v1/systemone` — «was
downloaded in a format that is no longer supported»; `/api/pull clef:latest`
дайджест не обновил. Прогон карты отложен до переустановки модели на
сервере; раннер готов (`knowledge-map/runner_map.py --protocol decision`).

# task-llm-hiring

Отбор LLM-кандидатов в проект [TasK](https://task.ai-aid.pro/): скриптованные
собеседования по ролям, конвейер судейства с проверкой фактов и сырые
протоколы всех прогонов.

Первая отработанная роль — PHP-бэкендер (PHP 8, Symfony, Doctrine,
PostgreSQL, DDD, чистая архитектура, тестирование, конкурентность, git):
35 вопросов с уточнениями (итого 41 ответ), из них три провокации с ложной
предпосылкой, два мини-код-ревью и финал собеса. 17 серий кандидатов:
локальные модели на ollama, облачные SOTA через OpenRouter, GigaChat и
YandexGPT через родные API, DeepSeek через родной API.

## Как это работает

- **Раннеры** ведут собес по вопроснику: один собес = один чат, без system
  prompt и инструментов; раннер читает вопросник и шлёт вопросы по одному в
  API модели, ответы дописываются в историю чата. Прогоны изолированы:
  модель выгружается из памяти до и после каждого прогона, параметры
  генерации дефолтные, seed не фиксируется.
- **Судьи** оценивают протоколы постфактум, тремя ярусами: эмбеддинги bge-m3
  (бесплатный фильтр дна), decision-модель clef (балл, вероятность
  галлюцинаций, самоотчёт о зоне незнания), изолированный судья
  GLM-5.3-Flash, проверяющий факты запуском кода (PHP, PostgreSQL в docker).
- **Рубрики** — карточки эталонов по каждому вопросу, шкала 0–4.
- Итоговые метрики считаются только по вопросам на знания (30 ответов из
  41): поле `knowledge` в вопроснике размечает их.

## Структура

| Путь | Что лежит |
|---|---|
| `interview/roles/backend/questions.json` | вопросник бэкендера (v2); старые версии рядом как `questions.vN.json` |
| `interview/runner*.py` | раннеры собесов: ollama, OpenRouter, родные API (GigaChat, YandexGPT, DeepSeek) |
| `interview/score.py` | оценка ярусом 1: балл, галлюцинации и эскалация, одним запросом на ответ |
| `interview/judge_glm.py` | судья яруса 2: GLM-5.3-Flash с проверкой фактов кодом |
| `interview/judge*.py` | эксперименты сравнения decision-моделей; `judge_openrouter.py` deprecated (пробный путь, не развивается; провайдеры — в score.py) |
| `interview/rubrics/backend/` | карточки эталонов судьи по вопросам |
| `interview/build_report.py` | сборка итогового отчёта из сырых данных |
| `<модель>/backend/run-XX/` | протоколы собесов: `session.json` (messages, thinking, тайминги) и `transcript.md` |
| `<модель>/backend/judgments/` | вердикты судей (clef, GLM-5.3-Flash) и сводные CSV |
| `reports/` | кросс-модельные агрегаты и своды |

## Быстрый старт

Скрипты не содержат адресов серверов и путей к ключам — всё берётся из
переменных окружения (без них скрипт падает с подсказкой, какую задать):

| Переменная | Что задаёт | Кто читает |
|---|---|---|
| `OLLAMA_BASE_URL` | адрес сервера ollama (дефолт `http://localhost:11434`) | `runner.py`, `score.py`, `embed_compare.py` |
| `MODELS_RU_ENV` | путь к env-файлу с ключами GigaChat/Яндекс/DeepSeek (`GIGACHAT_AUTHORIZATION_KEY`, `YANDEXFM_API_KEY`, `DEEPSEEK_API_KEY`) | `runner_ru_cloud.py` |
| `YANDEXFM_FOLDER_ID` | ID каталога Яндекса для `gpt://<folder>/yandexgpt/latest` | `runner_ru_cloud.py` |
| `OPENROUTER_KEY_FILE` | путь к файлу с ключом OpenRouter | `runner_openrouter.py`, `judge_openrouter.py` |
| `OPENROUTER_PROXY_FILE` / `OPENROUTER_PROXY` | файл с адресом прокси / прямой адрес прокси | `runner_openrouter.py`, `judge_openrouter.py` |

```bash
# собес локальной модели с сервера ollama, 5 независимых прогонов
python3 interview/runner.py --questions interview/roles/backend/questions.json \
    --model qwen3.8:27b --runs 5

# оценка ярусом 1: decision-модель clef по протоколу прогона,
# одним запросом на ответ: балл, галлюцинации и эскалационные признаки
python3 interview/score.py --run qwen3.8/backend/run-01 --judge clef
```

Судья яруса 2 (GLM-5.3-Flash) запускается скриптом `interview/judge_glm.py`
через подписку Z.AI и checking фактов кодом; он не воспроизводится без
подписки, но все его вердикты лежат в `<модель>/backend/judgments/`.

## Статус

Роль backend отработана: 17 серий, вопросник v2, сплошное судейство ярусом 2
по вопросам на знания. Дальше — другие роли и тестовое задание.

## Автор

Дмитрий Прикотов: [prikotov.pro](https://prikotov.pro/) ·
[Telegram @prikotov](https://t.me/prikotov).
Итоговый отчёт по роли backend: `reports/REPORT.html`
(собирается `interview/build_report.py` из сырых данных этого репозитория).

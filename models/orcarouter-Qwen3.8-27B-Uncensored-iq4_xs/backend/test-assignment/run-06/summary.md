# Прогон: qwen3.8-unc-iq4xs:pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8-unc-iq4xs:pi-100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; шкала модели: [False, True]; maxTokens 8192 |
| Длительность | 90 мин 00 с (exit 143, остановлен) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 0 строк, 21 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_60495" for connection named default  |
| разворачивание схемы | ✅ pass | method=schema_update |
| импорт прайса | ❌ fail | illing/fixtures/prices.json'". Message: "There are no commands defined in the "a |
| импорт событий | ❌ fail | lling/fixtures/usage-base.jsonl'". Message: "There are no commands defined in th |
| тарификация (счётчики) | ❌ fail | в stdout нет 'RATED=<n> UNRATED=<m>': '' |
| unrated в stderr | ❌ fail | ожидались ['UNRATED prism/h13 2026-07-31T23:00:00Z no_price'], получено [] |
| отчёт: базовый сценарий | ❌ fail | код 1: o='2026-10-01T00:00:00Z'". Message: "There are no commands defined in the |
| импорт дублей | ❌ fail | fixtures/usage-duplicates.jsonl'". Message: "There are no commands defined in th |
| тарификация после дублей | ❌ fail | stdout: '' |
| отчёт: идемпотентность | ❌ fail | код 1: o='2026-10-01T00:00:00Z'". Message: "There are no commands defined in the |
| бонус: вывод reprice | ➖ skip | команды app:billing:reprice нет (бонус не выполнялся) |
| бонус: отчёт после коррекции | ➖ skip | команды app:billing:reprice нет (бонус не выполнялся) |
| изменения каркаса (инфо) | ✅ pass | нет изменений |
| артефакты commit/PR | ❌ fail | отсутствуют/пусты: COMMIT_MESSAGE.md, PR_DESCRIPTION.md |

## Ярус 0.5 — детерминированные метрики

| Метрика | Значение |
|---|---|
| phpstan level 8 | 0 ошибок |
| phpmd | 0 нарушений |
| phpcs PSR12 | 0 (0 ошибок, 0 предупреждений) |
| Размер | 1 файлов, 27 LOC |
| Классы/методы | 1 / 3 |
| WMC (сумма) | 5 (худший класс: `App\Kernel`) |
| Худший метод | CC 3, LOC 9 |
| Превышения порогов | cc>10: 0, loc>50: 0, wmc>50: 0, lcom4>1: 0 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 22 (9 / 11) |
| Tool-calls (12) | bash: 11, read: 1 |
| Thinking | 9 блоков, 24,880 символов |
| Файлы | 0 касаний, 0 уникальных |
| Bash по категориям | {'read/ls/grep': 3, 'console прочее': 2, 'docker': 1, 'tests': 1, 'psql/db': 4} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 0 |
| Исходы тестов подряд | — |
| ~Транскрипт | 58,297 символов |
| Вызовов API | 9 (генерация 1 мин 03 с) |
| Токены | вход 11,540 + выход 7,527 (reasoning 0), cache-read 94,750; всего 113,817 |
| Пик контекста | 19,060 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=12

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

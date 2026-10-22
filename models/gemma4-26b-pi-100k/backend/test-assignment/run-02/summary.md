# Прогон: gemma4:26b-pi-100k

| Параметр | Значение |
|---|---|
| Модель | `gemma4:26b-pi-100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; шкала модели: [False, True]; maxTokens 8192 |
| Длительность | 0 мин 36 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 647 строк, 30 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_54117" for connection named default  |
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
| phpstan level 8 | 7 ошибок |
| phpmd | 3 нарушений |
| phpcs PSR12 | 0 (0 ошибок, 0 предупреждений) |
| Размер | 9 файлов, 353 LOC |
| Классы/методы | 9 / 48 |
| WMC (сумма) | 50 (худший класс: `App\Entity\UsageEvent`) |
| Худший метод | CC 3, LOC 21 |
| Превышения порогов | cc>10: 0, loc>50: 0, wmc>50: 0, lcom4>1: 4 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 39 (19 / 18) |
| Tool-calls (18) | bash: 5, edit: 1, read: 4, write: 8 |
| Thinking | 19 блоков, 8,759 символов |
| Файлы | 9 касаний, 8 уникальных |
| Bash по категориям | {'read/ls/grep': 2, 'console прочее': 2, 'прочее': 1} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 0 |
| Исходы тестов подряд | — |
| ~Транскрипт | 39,664 символов |
| Вызовов API | 19 (генерация 0 мин 35 с) |
| Токены | вход 11,621 + выход 9,742 (reasoning 0), cache-read 185,430; всего 206,793 |
| Пик контекста | 20,923 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=18

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

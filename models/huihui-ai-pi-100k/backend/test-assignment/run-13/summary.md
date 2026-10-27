# Прогон: huihui-ai:pi-100k

| Параметр | Значение |
|---|---|
| Модель | `huihui-ai:pi-100k` (провайдер ollama) |
| Thinking / контекст | off / 102400 токенов; шкала модели: [False, 'low', 'medium', 'xhigh']; maxTokens 32768 |
| Длительность | 2 мин 53 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 1069 строк, 34 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_76897" for connection named default  |
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
| phpstan level 8 | 19 ошибок |
| phpmd | 35 нарушений |
| phpcs PSR12 | 11 (9 ошибок, 2 предупреждений) |
| Размер | 14 файлов, 694 LOC |
| Классы/методы | 14 / 73 |
| WMC (сумма) | 116 (худший класс: `App\Domain\Money\Cost`) |
| Худший метод | CC 9, LOC 42 |
| Превышения порогов | cc>10: 0, loc>50: 0, wmc>50: 0, lcom4>1: 8 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 134 (59 / 73) |
| Tool-calls (73) | bash: 44, edit: 7, read: 4, write: 18 |
| Thinking | 0 блоков, 0 символов |
| Файлы | 25 касаний, 15 уникальных |
| Bash по категориям | {'read/ls/grep': 1, 'docker': 1, 'console прочее': 1, 'psql/db': 1, 'прочее': 34, 'php': 5, 'composer': 1} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 5 |
| Исходы тестов подряд | — |
| Отладочные принты в коде | var_dump/dd/dump: 5 |
| ~Транскрипт | 48,764 символов |
| Вызовов API | 59 (генерация 2 мин 48 с) |
| Токены | вход 25,216 + выход 26,431 (reasoning 0), cache-read 1,530,158; всего 1,581,805 |
| Пик контекста | 47,280 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=73

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

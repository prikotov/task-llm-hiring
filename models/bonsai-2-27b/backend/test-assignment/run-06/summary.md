# Прогон: bonsai-2:27b

| Параметр | Значение |
|---|---|
| Модель | `bonsai-2:27b` (провайдер ollama) |
| Thinking / контекст | off / 102400 токенов; шкала модели: [False, 'low', 'medium', 'xhigh']; maxTokens 32768 |
| Длительность | 3 мин 30 с (exit 1) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 314 строк, 25 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_61164" for connection named default  |
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
| phpstan level 8 | 15 ошибок |
| phpmd | 27 нарушений |
| phpcs PSR12 | 4 (0 ошибок, 4 предупреждений) |
| Размер | 5 файлов, 218 LOC |
| Классы/методы | 5 / 19 |
| WMC (сумма) | 32 (худший класс: `App\Domain\Decimal`) |
| Худший метод | CC 7, LOC 35 |
| Превышения порогов | cc>10: 0, loc>50: 0, wmc>50: 0, lcom4>1: 1 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 121 (60 / 59) |
| Tool-calls (59) | bash: 48, edit: 2, read: 1, write: 8 |
| Thinking | 0 блоков, 0 символов |
| Файлы | 10 касаний, 4 уникальных |
| Bash по категориям | {'read/ls/grep': 10, 'docker': 3, 'tests': 1, 'console прочее': 4, 'make': 1, 'прочее': 2, 'php': 27} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 22 |
| Исходы тестов подряд | — |
| Отладочные принты в коде | var_dump/dd/dump: 22 |
| ~Транскрипт | 37,015 символов |
| Вызовов API | 60 (генерация 2 мин 56 с) |
| Токены | вход 49,239 + выход 17,432 (reasoning 0), cache-read 1,105,015; всего 1,171,686 |
| Пик контекста | 32,758 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 3 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=59

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

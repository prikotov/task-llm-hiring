# Прогон: ornith-1.5:35b-ctx100k

| Параметр | Значение |
|---|---|
| Модель | `ornith-1.5:35b-ctx100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; maxTokens 8192 |
| Длительность | 90 мин 00 с (exit 143, остановлен) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 0 строк, 21 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_34870" for connection named default  |
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
| Сообщений (assistant/toolResult) | 19 (7 / 10) |
| Tool-calls (11) | bash: 6, read: 5 |
| Thinking | 7 блоков, 411 символов |
| Файлы | 0 касаний, 0 уникальных |
| Bash по категориям | {'прочее': 3, 'tests': 1, 'composer': 1, 'psql/db': 1} |
| Запуски тестов | 1 ({'?': 1}) |
| Самопроверка | debug_prints_in_code: 0 |
| Исходы тестов подряд | ? |
| ~Транскрипт | 24,411 символов |
| Вызовов API | 7 (генерация 0 мин 11 с) |
| Токены | вход 11,346 + выход 916 (reasoning 0), cache-read 43,905; всего 56,167 |
| Пик контекста | 11,504 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=11

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

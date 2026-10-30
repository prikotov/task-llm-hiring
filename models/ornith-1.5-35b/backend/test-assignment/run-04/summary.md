# Прогон: ornith-1.5:35b-ctx100k-tuned

| Параметр | Значение |
|---|---|
| Модель | `ornith-1.5:35b-ctx100k-tuned` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; maxTokens 8192 |
| Длительность | 3 мин 33 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 899 строк, 32 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_51566" for connection named default  |
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
| phpstan level 8 | 32 ошибок |
| phpmd | 21 нарушений |
| phpcs PSR12 | 28 (26 ошибок, 2 предупреждений) |
| Размер | 11 файлов, 478 LOC |
| Классы/методы | 11 / 33 |
| WMC (сумма) | 79 (худший класс: `App\Domain\Service\PriceBookImporter`) |
| Худший метод | CC 9, LOC 36 |
| Превышения порогов | cc>10: 0, loc>50: 0, wmc>50: 0, lcom4>1: 4 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 212 (98 / 112) |
| Tool-calls (112) | bash: 46, edit: 10, read: 22, write: 34 |
| Thinking | 98 блоков, 43,269 символов |
| Файлы | 44 касаний, 15 уникальных |
| Bash по категориям | {'прочее': 34, 'tests': 1, 'psql/db': 1, 'console прочее': 8, 'composer': 1, 'lint php -l': 1} |
| Запуски тестов | 1 ({'?': 1}) |
| Самопроверка | debug_prints_in_code: 19 |
| Исходы тестов подряд | ? |
| Отладочные принты в коде | var_dump/dd/dump: 19 |
| ~Транскрипт | 125,517 символов |
| Вызовов API | 98 (генерация 3 мин 26 с) |
| Токены | вход 59,888 + выход 44,294 (reasoning 0), cache-read 3,331,140; всего 3,435,322 |
| Пик контекста | 59,815 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=112

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

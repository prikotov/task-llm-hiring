# Прогон: medgemma:27b-ctx100k

| Параметр | Значение |
|---|---|
| Модель | `medgemma:27b-ctx100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; maxTokens 8192 |
| Длительность | 0 мин 00 с (exit 1) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 0 строк, 21 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_92276" for connection named default  |
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
| Сообщений (assistant/toolResult) | 3 (1 / 0) |
| Tool-calls (0) |  |
| Thinking | 0 блоков, 0 символов |
| Файлы | 0 касаний, 0 уникальных |
| Bash по категориям | {} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 0 |
| Исходы тестов подряд | — |
| ~Транскрипт | 0 символов |
| Вызовов API | 1 (генерация —) |
| Токены | вход 0 + выход 0 (reasoning 0), cache-read 0; всего 0 |
| Пик контекста | 0 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

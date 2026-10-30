# Прогон: qwen3.8-unc:pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8-unc:pi-100k` (провайдер ollama) |
| Thinking / контекст | off / 102400 токенов; шкала модели: [False, True]; maxTokens 8192 |
| Длительность | 20 мин 34 с (exit 143, остановлен) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 498 строк, 22 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_86713" for connection named default  |
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
| phpstan level 8 | 12 ошибок |
| phpmd | 23 нарушений |
| phpcs PSR12 | 20 (20 ошибок, 0 предупреждений) |
| Размер | 2 файлов, 405 LOC |
| Классы/методы | 2 / 36 |
| WMC (сумма) | 141 (худший класс: `App\Billing\ValueObject\Decimal`) |
| Худший метод | CC 15, LOC 36 |
| Превышения порогов | cc>10: 2, loc>50: 0, wmc>50: 1, lcom4>1: 1 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 88 (42 / 44) |
| Tool-calls (45) | bash: 34, edit: 1, read: 4, write: 6 |
| Thinking | 0 блоков, 0 символов |
| Файлы | 7 касаний, 3 уникальных |
| Bash по категориям | {'read/ls/grep': 12, 'tests': 1, 'console прочее': 3, 'psql/db': 1, 'php': 9, 'прочее': 3, 'lint php -l': 5} |
| Запуски тестов | 0 ({}) |
| Самопроверка | debug_prints_in_code: 19 |
| Исходы тестов подряд | — |
| Отладочные принты в коде | var_dump/dd/dump: 19 |
| ~Транскрипт | 62,745 символов |
| Вызовов API | 42 (генерация 3 мин 47 с) |
| Токены | вход 57,148 + выход 35,268 (reasoning 0), cache-read 991,693; всего 1,084,109 |
| Пик контекста | 57,033 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 0, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=45

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

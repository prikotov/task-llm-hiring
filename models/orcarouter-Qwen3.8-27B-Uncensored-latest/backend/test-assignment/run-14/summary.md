# Прогон: qwen3.8-unc:pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8-unc:pi-100k` (провайдер ollama) |
| Thinking / контекст | off / 102400 токенов; шкала модели: [False, True]; maxTokens 8192 |
| Длительность | 18 мин 08 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 946 строк, 29 файлов |

## Ярус 0 — чекер: 2/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_39986" for connection named default  |
| разворачивание схемы | ✅ pass | method=migrations |
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
| phpstan level 8 | 44 ошибок |
| phpmd | 29 нарушений |
| phpcs PSR12 | 28 (27 ошибок, 1 предупреждений) |
| Размер | 8 файлов, 603 LOC |
| Классы/методы | 8 / 40 |
| WMC (сумма) | 108 (худший класс: `App\Shared\Persistence\Repository`) |
| Худший метод | CC 11, LOC 58 |
| Превышения порогов | cc>10: 1, loc>50: 1, wmc>50: 0, lcom4>1: 1 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 667 (317 / 348) |
| Tool-calls (348) | bash: 242, edit: 25, glob: 2, read: 30, write: 49 |
| Thinking | 0 блоков, 0 символов |
| Файлы | 74 касаний, 22 уникальных |
| Bash по категориям | {'read/ls/grep': 60, 'console прочее': 21, 'docker': 2, 'psql/db': 40, 'tests': 7, 'php': 11, 'прочее': 64, 'composer': 2, 'git': 1, 'lint php -l': 14, 'console app:*': 20} |
| Запуски тестов | 4 ({'?': 1, 'pass': 3}) |
| Самопроверка | debug_prints_in_code: 45 |
| Исходы тестов подряд | ? → pass → pass → pass |
| Отладочные принты в коде | var_dump/dd/dump: 45 |
| ~Транскрипт | 570,870 символов |
| Вызовов API | 317 (генерация 14 мин 44 с) |
| Токены | вход 336,332 + выход 107,314 (reasoning 0), cache-read 15,125,685; всего 15,569,331 |
| Пик контекста | 86,317 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 3, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=97, 05-10мин=92, 10-15мин=108, 15-20мин=51

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

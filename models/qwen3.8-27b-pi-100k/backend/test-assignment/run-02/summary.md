# Прогон: qwen3.8:27b-pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8:27b-pi-100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; шкала модели: [False, 'low', 'medium', 'xhigh']; maxTokens 32768 |
| Длительность | 34 мин 53 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 3422 строк, 64 файлов |

## Ярус 0 — чекер: 11/11 обязательных, бонус 0/2 (pass)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_92626" for connection named default  |
| разворачивание схемы | ✅ pass | method=migrations |
| импорт прайса | ✅ pass | app:prices:import: OK providers created 2 updated 0; models created 3; price row |
| импорт событий | ✅ pass | app:usage:import: OK inserted=18 duplicates=0  |
| тарификация (счётчики) | ✅ pass | получено RATED=17 UNRATED=1, ожидалось 17/1 |
| unrated в stderr | ✅ pass | ожидались ['UNRATED prism/h13 2026-07-31T23:00:00Z no_price'], получено ['UNRATE |
| отчёт: базовый сценарий | ✅ pass |  |
| импорт дублей | ✅ pass | app:usage:import: OK inserted=1 duplicates=3  |
| тарификация после дублей | ✅ pass | stdout: 'RATED=1 UNRATED=1' |
| отчёт: идемпотентность | ✅ pass |  |
| бонус: вывод reprice | ❌ fail | stdout: '', stderr: '[critical] Error thrown while running command "-n app:billi |
| бонус: отчёт после коррекции | ❌ fail | - u-alice,prism/lumen-mini,1000000,500000,0,0,0.787500; - u-bob,prism/lumen-mini |
| изменения каркаса (инфо) | ✅ pass | нет изменений |
| артефакты commit/PR | ✅ pass |  |

## Ярус 0.5 — детерминированные метрики

| Метрика | Значение |
|---|---|
| phpstan level 8 | 53 ошибок |
| phpmd | 93 нарушений |
| phpcs PSR12 | 90 (77 ошибок, 13 предупреждений) |
| Размер | 36 файлов, 1751 LOC |
| Классы/методы | 36 / 131 |
| WMC (сумма) | 256 (худший класс: `App\Application\PricesImportService`) |
| Худший метод | CC 11, LOC 136 |
| Превышения порогов | cc>10: 1, loc>50: 2, wmc>50: 0, lcom4>1: 15 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 476 (214 / 260) |
| Tool-calls (260) | bash: 98, edit: 57, read: 46, write: 59 |
| Thinking | 214 блоков, 373,329 символов |
| Файлы | 116 касаний, 44 уникальных |
| Bash по категориям | {'read/ls/grep': 1, 'tests': 10, 'прочее': 36, 'console прочее': 9, 'docker': 5, 'psql/db': 8, 'lint php -l': 7, 'console app:*': 20, 'composer': 1, 'static-analysis': 1} |
| Запуски тестов | 10 ({'?': 5, 'fail': 1, 'pass': 4}) |
| Самопроверка | debug_prints_in_code: 19 |
| Исходы тестов подряд | ? → ? → ? → ? → fail → ? → pass → pass → pass → pass |
| Отладочные принты в коде | var_dump/dd/dump: 19 |
| ~Транскрипт | 697,777 символов |
| Вызовов API | 214 (генерация 30 мин 32 с) |
| Токены | вход 315,322 + выход 194,508 (reasoning 0), cache-read 12,510,952; всего 13,020,782 |
| Пик контекста | 88,156 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 4, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=9, 05-10мин=46, 10-15мин=33, 15-20мин=26, 20-25мин=40, 25-30мин=54, 30-35мин=52

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

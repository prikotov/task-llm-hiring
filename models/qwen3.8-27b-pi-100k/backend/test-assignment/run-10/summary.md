# Прогон: qwen3.8:27b-pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8:27b-pi-100k` (провайдер ollama) |
| Thinking / контекст | off / 102400 токенов; шкала модели: [False, 'low', 'medium', 'xhigh']; maxTokens 32768 |
| Длительность | 21 мин 45 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 3332 строк, 53 файлов |

## Ярус 0 — чекер: 7/11 обязательных, бонус 0/2 (fail)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_82320" for connection named default  |
| разворачивание схемы | ✅ pass | method=migrations |
| импорт прайса | ✅ pass | provs=2 models=3 price_rows=4\n |
| импорт событий | ✅ pass | total=18 new=18 dup=0 bad=0  |
| тарификация (счётчики) | ✅ pass | получено RATED=17 UNRATED=1, ожидалось 17/1 |
| unrated в stderr | ❌ fail | ожидались ['UNRATED prism/h13 2026-07-31T23:00:00Z no_price'], получено ['UNRATE |
| отчёт: базовый сценарий | ❌ fail | - u-bob,prism/lumen-mini,3000000,3000000,0,0,4.000000; - u-carol,prism/lumen-min |
| импорт дублей | ✅ pass | total=4 new=1 dup=3 bad=0  |
| тарификация после дублей | ❌ fail | stdout: 'RATED=1 UNRATED=0' |
| отчёт: идемпотентность | ❌ fail | - u-bob,prism/lumen-mini,3000000,3000000,0,0,4.000000; - u-carol,prism/lumen-min |
| бонус: вывод reprice | ❌ fail | stdout: 'REPRICED_EVENTS=7\nREPRICE_DELTA_USD=-0.820500', stderr: '' |
| бонус: отчёт после коррекции | ❌ fail | - u-alice,prism/lumen-mini,1000000,500000,0,0,0.787500; - u-bob,prism/lumen-mini |
| изменения каркаса (инфо) | ✅ pass | изменены: phpunit.xml.dist |
| артефакты commit/PR | ✅ pass |  |

## Ярус 0.5 — детерминированные метрики

| Метрика | Значение |
|---|---|
| phpstan level 8 | 90 ошибок |
| phpmd | 137 нарушений |
| phpcs PSR12 | 22 (6 ошибок, 16 предупреждений) |
| Размер | 23 файлов, 1718 LOC |
| Классы/методы | 23 / 128 |
| WMC (сумма) | 314 (худший класс: `App\Application\Import\PricesParser`) |
| Худший метод | CC 18, LOC 90 |
| Превышения порогов | cc>10: 3, loc>50: 4, wmc>50: 0, lcom4>1: 17 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 951 (468 / 481) |
| Tool-calls (481) | bash: 279, edit: 84, read: 59, write: 59 |
| Thinking | 0 блоков, 0 символов |
| Файлы | 143 касаний, 34 уникальных |
| Bash по категориям | {'read/ls/grep': 24, 'tests': 28, 'прочее': 146, 'psql/db': 9, 'docker': 1, 'lint php -l': 16, 'php': 7, 'console прочее': 7, 'console app:*': 33, 'static-analysis': 7, 'composer': 1} |
| Запуски тестов | 25 ({'?': 8, 'pass': 6, 'fail': 11}) |
| Самопроверка | debug_prints_in_code: 72 |
| Исходы тестов подряд | ? → pass → pass → fail → pass → ? → fail → fail → ? → ? → fail → fail → ? → fail → fail → fail → fail → fail → ? → fail → ? → ? → pass → pass → pass |
| Отладочные принты в коде | var_dump/dd/dump: 72 |
| ~Транскрипт | 322,401 символов |
| Вызовов API | 468 (генерация 18 мин 14 с) |
| Токены | вход 223,064 + выход 142,027 (reasoning 0), cache-read 24,529,393; всего 24,894,484 |
| Пик контекста | 87,059 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 3, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=111, 05-10мин=125, 10-15мин=113, 15-20мин=117, 20-25мин=15

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

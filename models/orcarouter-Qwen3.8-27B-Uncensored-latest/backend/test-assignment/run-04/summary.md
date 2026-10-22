# Прогон: qwen3.8-unc:pi-100k

| Параметр | Значение |
|---|---|
| Модель | `qwen3.8-unc:pi-100k` (провайдер ollama) |
| Thinking / контекст | max / 102400 токенов; шкала модели: [False, True]; maxTokens 8192 |
| Длительность | 11 мин 58 с (exit 0) |
| Версия TASK | `a9998d7fc38418eb` |
| Объём решения | дифф 2795 строк, 56 файлов |

## Ярус 0 — чекер: 11/11 обязательных, бонус 2/2 (pass)

| Сценарий | Статус | Детали |
|---|---|---|
| создание БД | ✅ pass | Created database "chk_72256" for connection named default  |
| разворачивание схемы | ✅ pass | method=migrations |
| импорт прайса | ✅ pass | prices: 2 provider(s), 3 model(s), 4 price row(s) upserted (file /home/dp/MyProj |
| импорт событий | ✅ pass | usage: 18 line(s), 18 imported, 0 duplicate(s) skipped (file /home/dp/MyProjects |
| тарификация (счётчики) | ✅ pass | получено RATED=17 UNRATED=1, ожидалось 17/1 |
| unrated в stderr | ✅ pass | ожидались ['UNRATED prism/h13 2026-07-31T23:00:00Z no_price'], получено ['UNRATE |
| отчёт: базовый сценарий | ✅ pass |  |
| импорт дублей | ✅ pass | usage: 4 line(s), 1 imported, 3 duplicate(s) skipped (file /home/dp/MyProjects/M |
| тарификация после дублей | ✅ pass | stdout: 'RATED=1 UNRATED=1' |
| отчёт: идемпотентность | ✅ pass |  |
| бонус: вывод reprice | ✅ pass | stdout: 'REPRICED_EVENTS=7\nREPRICE_DELTA_USD=-0.778500', stderr: '' |
| бонус: отчёт после коррекции | ✅ pass |  |
| изменения каркаса (инфо) | ✅ pass | нет изменений |
| артефакты commit/PR | ✅ pass |  |

## Ярус 0.5 — детерминированные метрики

| Метрика | Значение |
|---|---|
| phpstan level 8 | 34 ошибок |
| phpmd | 50 нарушений |
| phpcs PSR12 | 45 (29 ошибок, 16 предупреждений) |
| Размер | 28 файлов, 1467 LOC |
| Классы/методы | 28 / 116 |
| WMC (сумма) | 250 (худший класс: `App\Import\PricesCatalogParser`) |
| Худший метод | CC 10, LOC 84 |
| Превышения порогов | cc>10: 0, loc>50: 4, wmc>50: 1, lcom4>1: 12 |

## Поведенческий профиль (по сессии pi)

| Что | Значение |
|---|---|
| Сообщений (assistant/toolResult) | 262 (117 / 143) |
| Tool-calls (143) | bash: 71, edit: 31, read: 3, write: 38 |
| Thinking | 117 блоков, 112,076 символов |
| Файлы | 69 касаний, 35 уникальных |
| Bash по категориям | {'read/ls/grep': 35, 'tests': 6, 'console прочее': 7, 'psql/db': 3, 'lint php -l': 3, 'console app:*': 11, 'php': 5, 'git': 1} |
| Запуски тестов | 5 ({'fail': 1, '?': 3, 'pass': 1}) |
| Самопроверка | debug_prints_in_code: 29 |
| Исходы тестов подряд | fail → ? → ? → ? → pass |
| Отладочные принты в коде | var_dump/dd/dump: 29 |
| ~Транскрипт | 216,203 символов |
| Вызовов API | 117 (генерация 11 мин 18 с) |
| Токены | вход 88,008 + выход 77,902 (reasoning 0), cache-read 5,716,823; всего 5,882,733 |
| Пик контекста | 86,420 токенов (лимит прогона 102400) |
| Управление контекстом | автокомпактов: 1, правок истории: 0 |
| Стоимость | $0.0000 (локальная модель — 0; тариф для сравнения задаётся при необходимости) |

Таймлайн (tool-calls по 5 мин): 00-05мин=42, 05-10мин=66, 10-15мин=35

---
_Карточка собрана автоматически (summarize.py); ревью судьи (оси A/B по RUBRIC.md) ещё не проводилось._

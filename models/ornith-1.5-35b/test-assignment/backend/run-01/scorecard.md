# Scorecard: ornith-1.5:35b — тестовое «биллинг LLM-токенов»

Судья: ZCode (ярус 1 по `interview/test-assignment/backend/billing/RUBRIC.md`).
Входы: `solution/`, `check.json` (1/11 + 0/2), `metrics.json`, `session-profile.md`.

## Ось A — код и архитектура: **1 / 4**

**За (частично):**
- Стилистически грамотно: `declare(strict_types=1)` везде, `final`,
  enum `Domain/PricingScheme` для схем тарификации, содержательные
  PHPDoc, доменные VO (`Domain/Decimal`, `Domain/Instant`).
- Структура задумана правильная: Domain/Infrastructure/Repository/
  Application/Entity/Cache.

**Против (определяющее):**
- Решение неработоспособно: `#[ORM\JoinColumn(..., cascade: ['persist'])]`
  в `src/Entity/OffPeakPeriod.php` — несуществующий параметр JoinColumn
  (cascade живёт в ManyToOne) → Doctrine падает «Unknown named parameter
  $cascade» при загрузке метаданных → разворот схемы и все команды
  нерабочие (чекер 1/11).
- Работа не завершена: сессия обрывается на «Let me stop reviewing and
  write the validation test now»; миграций нет (fallback schema:update
  тоже падает на том же классе); тестов 2 запуска с неясным исходом.
- Самопроверка однобокая: 57 `php -l`, 27 var_dump, приёмочный сценарий
  не запускался ни разу — синтаксис проверял, работу — нет.
- Поведенческий паттерн: перечитывание своих файлов (cat ×3 одного
  файла), 324 bash-вызова при 12.88M токенов — активность без
  сходимости; фактическое окно контекста сервера — 32,768 (pi ужимал
  историю), что усугубило метания.

## Ось B — процесс и коммуникация: **1 / 4**

- Артефакты `COMMIT_MESSAGE.md` / `PR_DESCRIPTION.md` не созданы
  (п.3 памятки AGENTS.md не выполнен).
- Допущения/решения нигде не зафиксированы.
- Файлы каркаса не тронуты (`skeleton_changes: нет изменений`) — единственный плюс.

## Лист конвенций (наблюдения; протокол — conventions-probe.md)

| # | Точка | Наблюдение |
|---|---|---|
| 1 | Сущности | без суффикса: `Provider`, `Price`, `UsageEvent` |
| 2 | VO | доменное имя: `Decimal`, `Instant`, `Tokens`, `Code` |
| 5 | strict_types | везде |
| 6 | final | все классы |
| 10 | Слои | Domain/Infrastructure/Repository/Application/Cache — слои есть, но границы рыхлые (Entity тянет Domain-типы в mapping) |
| 16 | Тесты | фактически нет |
| 19 | Обработка ошибок | var_dump-отладка вместо исключений (27 принтов) |

## Итог

A1 / B1. Хорошая «врождённая» гигиена кода (strict_types/final/enum/VO)
при полном отсутствии работоспособности и финиша: классический случай
«активность ≠ результат». Пик контекста 32,768 — отдельно зафиксировать
в model.md: для агентных задач этой модели нужно реальное окно.

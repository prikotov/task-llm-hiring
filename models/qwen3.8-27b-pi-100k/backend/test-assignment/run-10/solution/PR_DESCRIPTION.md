# Billing: импорт цен, тарификация событий, отчёт, ретро-коррекция

## Что сделано

Реализована вся функциональность из TASK.md:

- **Импорт справочника цен** — `app:prices:import`: парсинг `providers`, `models`, `prices` из JSON; идемпотентный upsert по естественным ключам; валидация (дубликаты в файле — ошибка, неизвестный провайдер — ошибка).
- **Импорт событий** — `app:usage:import`: построчный парсинг JSONL; идемпотентность по `(provider, provider_request_id)`; строки с ошибками (невалидный JSON, неизвестный провайдер) пропускаются с выводом `BAD_LINE=`; порядок событий в файле не используется — статусы и отчёт по `(user, model)` не зависят от порядка.
- **Тарификация** — `app:billing:rate`: для каждого события выбирается строка цены с наибольшим `valid_from ≤ ts`; стоимость считается через точную десятичную арифметику (`Decimal`, на `bcmath`); оф-пик по провайдеру учитывается; результат округляется HALF_UP до 6 знаков; события без цены помечаются `unrated`.
- **Отчёт** — `app:billing:report --from --to`: агрегация по `(user, model)`; токены и стоимость (у последних рейтингов); итоговая строка `TOTAL`.
- **Rетро-коррекция** — `app:billing:reprice <file>`: обновление цен и пересчёт затронутых событий; добавляются новые записи `usage_ratings` (история); `REPRICE_DELTA_USD` — разность суммы старых и новых стоимостей.
- **Тесты** — 20 PHPUnit-тестов: `Decimal`-арифметика, `OffPeakWindow`, `Parser` (JSON/JSONL), billing engine, полный приёмочный сценарий.

## Структура

```
src/
  Domain/Billing/  Decimal.php, EventPricingService.php, OffPeakWindow.php
  Entity/          Provider, Model, Price, UsageEvent, UsageRating
  Application/
    Import/        PricesParser, UsageParser, CatalogRepository,
                   PricesImportService, UsageImportService, ImportException
    Billing/       RateService, RepriceService
  Command/         PricesImportCommand, UsageImportCommand,
                   RateCommand, ReportCommand, RepriceCommand
tests/             AcceptanceTest, BillingEngineTest, DecimalTest,
                   OffPeakWindowTest, ParserTest, KernelTestCase
fixtures/          prices.json, usage.jsonl
```

## Отклонения

- `KernelTestCase` (tests/KernelTestCase.php): базовый класс для интеграционных тестов, наследует `Symfony\Bundle\FrameworkBundle\Test\KernelTestCase`. Не из коробки в skeleton.
- `.env.test`: локальный файл окружения для тестов.
- `phpunit.xml.dist` обновлён: добавлен `KERNEL_CLASS=App\Kernel` server-параметр.
- `Rater.php` (src/Application/Billing/): вспомогательный класс, использовался ранее в ходе разработки; текущая логика полностью в `RateService`.

## Проверка

```bash
make test  # → OK (20 tests, 69 assertions)
```

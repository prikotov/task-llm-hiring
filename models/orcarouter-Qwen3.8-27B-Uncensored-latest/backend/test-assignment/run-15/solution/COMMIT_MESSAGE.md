feat: реализован полный цикл биллинга LLM-токенов

Доменная модель (DDD):
- Money (bcmath scale 20, HALF_UP до 6 знаков в конце)
- Price (per_1m_tokens / per_1m_tokens_unified)
- OffPeakWindow (окно с переходом через полночь, start inclusive, end exclusive)
- Provider / Model / PriceLine / UsageEvent (сущности + репозитории)

Сервисы и команды:
- App\Import\PriceImportService — импорт цен idempotent (unique key)
- App\Import\UsageImportService — импорт событий с дедупликацией
  (unique key: provider + provider_request_id)
- App\Service\RatingService — тарификация: выбор цены по valid_from
  (включительно), off-peak дисконт, точная десятичная арифметика
- bin/console app:prices:import — загрузка цен из JSON
- bin/console app:usage:import — загрузка событий из JSONL
- bin/console app:billing:rate — расчёт стоимости всех pending/unrated
- bin/console app:billing:report — CSV-отчёт (user, model, токены, cost_usd)
  с фильтрацией --from/--to (ISO-8601 UTC)

Инфраструктура:
- PostgreSQL 17, Doctrine ORM 2, миграции (migrations/)
- config/services.yaml — DI wiring, repositories через ServiceEntityRepository
- docker-compose.yml — db:postgres:17, php:8.4-cli

Тесты:
- tests/Domain/ValueObject/MoneyTest.php (10 тестов: точность, HALF_UP, format)
- tests/Domain/ValueObject/OffPeakWindowTest.php (9 тестов: границы, полночь)
- tests/Domain/ValueObject/PriceTest.php (7 тестов: per_1m, unified, ошибки)

Приёмка:
✅ app:prices:import fixtures/prices.json — без ошибок
✅ app:usage:import fixtures/usage.jsonl — без ошибок
✅ app:billing:rate — RATED=3 UNRATED=0
✅ app:billing:report --from=... --to=... — байт-в-байт совпадает с примером
✅ Повторный импорт + reprice + report — результат не меняется (idempotency)

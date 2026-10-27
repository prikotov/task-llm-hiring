.PHONY: up down logs shell composer lint schema create-test-db import-prices import-usage billing-rate billing-report

# --- Docker / БД ---
up:
	docker compose up -d

down:
	docker compose stop

logs:
	docker compose logs -f postgres

# --- Composer ---
composer:
	composer install --no-interaction

# --- Контроль качества кода (vendor/bin/php-cs-fixer, vendor/bin/phpstan) ---
lint:
	vendor/bin/php-cs-fixer fix --dry-run --diff
	vendor/bin/phpstan analyse --memory-limit=512M

# --- Схема БД (упрощённо: создание схемы по сущностям ORM) ---
schema:
	bin/console doctrine:database:create --if-not-exists
	bin/console orm:schema-tool:create --force

# --- БД для тестов ---
create-test-db:
	bin/console doctrine:database:create --env=test --if-not-exists
	bin/console orm:schema-tool:create --env=test --force

# --- Приёмо-контрольные команды (примеры) ---
import-prices:
	bin/console app:prices:import fixtures/prices.json

import-usage:
	bin/console app:usage:import fixtures/usage.jsonl

billing-rate:
	bin/console app:billing:rate

billing-report:
	bin/console app:billing:report

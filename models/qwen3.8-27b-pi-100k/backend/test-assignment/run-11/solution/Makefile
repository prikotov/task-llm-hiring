COMPOSE = docker compose

.PHONY: db-up db-down migrate console test help

db-up: ## Поднять PostgreSQL 17 (порт из .env, по умолчанию 5544)
	$(COMPOSE) up -d
	@until $(COMPOSE) exec -T db pg_isready -U app -d app >/dev/null 2>&1; do sleep 1; done
	@echo "PostgreSQL готов"

db-down: ## Остановить PostgreSQL
	$(COMPOSE) down

migrate: ## Создать БД (если нет) и развернуть схему ORM
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:create -n

console: ## Открыть список команд
	bin/console

test: ## PHPUnit
	vendor/bin/phpunit

help: ## Показать справку
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  make %-10s %s\n", $$1, $$2}'

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Базовая схема учёта расходов на LLM-токены.
 *
 * - providers            провайдеры (unique code) + оф-пик окно/множитель;
 * - models               модели (unique provider_id + code);
 * - price_rows           строки цен (unique model_id + valid_from),
 *                        ставки — decimal-строки в JSON;
 * - usage_events         события (unique provider + provider_request_id),
 *                        стоимость — decimal-строка (float для денег не
 *                        используется); `user_ref` — идентификатор
 *                        пользователя (название колонки обходит PG-резерв
 *                        «user»).
 *
 * Даты — UTC: все временные значения (valid_from, ts) хранятся в UTC
 * (TIMESTAMPTZ, все операции — с моментами, приведёнными к UTC).
 */
final class Version20261016125000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE providers (
            id SERIAL NOT NULL,
            code VARCHAR(64) NOT NULL,
            name VARCHAR(255) NOT NULL,
            off_peak_start VARCHAR(5) DEFAULT NULL,
            off_peak_end VARCHAR(5) DEFAULT NULL,
            off_peak_multiplier VARCHAR(32) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PROVIDERS_CODE ON providers (code)');

        $this->addSql('CREATE TABLE models (
            id SERIAL NOT NULL,
            provider_id INT NOT NULL,
            code VARCHAR(64) NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_MODELS_PROVIDER_CODE ON models (provider_id, code)');
        $this->addSql('ALTER TABLE models ADD CONSTRAINT FK_MODELS_PROVIDER FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE price_rows (
            id SERIAL NOT NULL,
            model_id INT NOT NULL,
            valid_from TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            currency VARCHAR(3) NOT NULL,
            unit VARCHAR(32) NOT NULL,
            rates JSONB NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PRICE_ROWS_MODEL_VALID_FROM ON price_rows (model_id, valid_from)');
        $this->addSql('ALTER TABLE price_rows ADD CONSTRAINT FK_PRICE_ROWS_MODEL FOREIGN KEY (model_id) REFERENCES models (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_PRICE_ROWS_MODEL_VALID_FROM_READ ON price_rows (model_id, valid_from DESC)');

        $this->addSql('CREATE TABLE usage_events (
            id SERIAL NOT NULL,
            provider VARCHAR(64) NOT NULL,
            provider_request_id VARCHAR(255) NOT NULL,
            model VARCHAR(64) NOT NULL,
            user_ref VARCHAR(255) NOT NULL,
            ts TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            tokens_input BIGINT NOT NULL,
            tokens_output BIGINT NOT NULL,
            tokens_cache_read BIGINT NOT NULL,
            tokens_cache_write BIGINT NOT NULL,
            status VARCHAR(16) NOT NULL,
            price_row_id INT DEFAULT NULL,
            base_cost VARCHAR(64) DEFAULT NULL,
            off_peak_applied BOOLEAN DEFAULT NULL,
            cost_usd VARCHAR(64) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USAGE_EVENTS_PROVIDER_REQUEST ON usage_events (provider, provider_request_id)');
        $this->addSql('ALTER TABLE usage_events ADD CONSTRAINT FK_USAGE_EVENTS_PRICE_ROW FOREIGN KEY (price_row_id) REFERENCES price_rows (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_USAGE_EVENTS_STATUS ON usage_events (status)');
        $this->addSql('CREATE INDEX IDX_USAGE_EVENTS_TS ON usage_events (ts)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE usage_events DROP CONSTRAINT FK_USAGE_EVENTS_PRICE_ROW');
        $this->addSql('DROP TABLE usage_events');
        $this->addSql('ALTER TABLE price_rows DROP CONSTRAINT FK_PRICE_ROWS_MODEL');
        $this->addSql('DROP TABLE price_rows');
        $this->addSql('ALTER TABLE models DROP CONSTRAINT FK_MODELS_PROVIDER');
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE providers');
    }
}

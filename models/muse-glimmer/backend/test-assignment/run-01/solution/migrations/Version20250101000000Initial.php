<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101000000Initial extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for billing';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE providers (
            code VARCHAR(50) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            off_peak_start TIME NULL,
            off_peak_end TIME NULL,
            off_peak_multiplier DECIMAL(10,6) NULL
        )');

        $this->addSql('CREATE TABLE models (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(50) NOT NULL,
            code VARCHAR(50) NOT NULL,
            CONSTRAINT uniq_provider_model UNIQUE (provider_code, code)
        )');

        $this->addSql('CREATE TABLE prices (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(50) NOT NULL,
            model_code VARCHAR(50) NOT NULL,
            valid_from TIMESTAMP WITH TIME ZONE NOT NULL,
            currency VARCHAR(10) NOT NULL,
            unit VARCHAR(50) NOT NULL,
            rate_input DECIMAL(20,6) NULL,
            rate_output DECIMAL(20,6) NULL,
            rate_cache_read DECIMAL(20,6) NULL,
            rate_cache_write DECIMAL(20,6) NULL,
            rate_any DECIMAL(20,6) NULL,
            CONSTRAINT uniq_price UNIQUE (provider_code, model_code, valid_from)
        )');

        $this->addSql('CREATE INDEX idx_prices_provider_model_valid ON prices (provider_code, model_code, valid_from DESC)');

        $this->addSql('CREATE TABLE usage_events (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(50) NOT NULL,
            provider_request_id VARCHAR(100) NOT NULL,
            model_code VARCHAR(50) NOT NULL,
            user_id VARCHAR(100) NOT NULL,
            ts TIMESTAMP WITH TIME ZONE NOT NULL,
            tokens_input BIGINT NOT NULL,
            tokens_output BIGINT NOT NULL,
            tokens_cache_read BIGINT NOT NULL,
            tokens_cache_write BIGINT NOT NULL,
            cost_usd DECIMAL(20,6) NULL,
            CONSTRAINT uniq_provider_request UNIQUE (provider_code, provider_request_id)
        )');

        $this->addSql('CREATE INDEX idx_usage_events_cost_null ON usage_events (id) WHERE cost_usd IS NULL');
        $this->addSql('CREATE INDEX idx_usage_events_ts ON usage_events (ts)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE usage_events');
        $this->addSql('DROP TABLE prices');
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE providers');
    }
}

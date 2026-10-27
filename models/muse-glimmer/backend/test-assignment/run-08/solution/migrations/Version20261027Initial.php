<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027Initial extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for LLM billing';
    }

    public function up(Schema $schema): void
    {
        // providers
        $this->addSql('CREATE TABLE providers (
            id SERIAL PRIMARY KEY,
            code VARCHAR(64) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            off_peak_window_start TIME,
            off_peak_window_end TIME,
            off_peak_multiplier NUMERIC(10,6)
        )');

        // models
        $this->addSql('CREATE TABLE models (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(64) NOT NULL,
            code VARCHAR(64) NOT NULL,
            UNIQUE(provider_code, code),
            FOREIGN KEY(provider_code) REFERENCES providers(code) ON DELETE CASCADE
        )');

        // prices
        $this->addSql('CREATE TABLE prices (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(64) NOT NULL,
            model_code VARCHAR(64) NOT NULL,
            valid_from TIMESTAMP NOT NULL,
            currency VARCHAR(3) NOT NULL,
            unit VARCHAR(64) NOT NULL,
            rates JSONB NOT NULL,
            UNIQUE(provider_code, model_code, valid_from),
            FOREIGN KEY(provider_code) REFERENCES providers(code) ON DELETE CASCADE
        )');

        // usage_events
        $this->addSql('CREATE TABLE usage_events (
            id SERIAL PRIMARY KEY,
            provider_code VARCHAR(64) NOT NULL,
            provider_request_id VARCHAR(128) NOT NULL,
            model_code VARCHAR(64) NOT NULL,
            user_name VARCHAR(128) NOT NULL,
            ts TIMESTAMP NOT NULL,
            tokens_input BIGINT NOT NULL,
            tokens_output BIGINT NOT NULL,
            tokens_cache_read BIGINT NOT NULL,
            tokens_cache_write BIGINT NOT NULL,
            cost NUMERIC(18,6),
            UNIQUE(provider_code, provider_request_id)
        )');

        // indexes for performance
        $this->addSql('CREATE INDEX idx_prices_provider_model_valid_from ON prices (provider_code, model_code, valid_from DESC)');
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

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration as BaseMigration;

final class Version20260915000001 extends BaseMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for LLM billing: providers, models, prices, usage_events';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE providers (
                code VARCHAR(64) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                off_peak_window_start VARCHAR(5),
                off_peak_window_end VARCHAR(5),
                off_peak_multiplier VARCHAR(30)
            )'
        );

        $this->addSql(
            'CREATE TABLE models (
                provider_code VARCHAR(64) NOT NULL,
                code VARCHAR(64) NOT NULL,
                PRIMARY KEY (provider_code, code)
            )'
        );

        $this->addSql(
            'CREATE TABLE prices (
                provider_code VARCHAR(64) NOT NULL,
                model_code VARCHAR(64) NOT NULL,
                valid_from TIMESTAMPTZ NOT NULL,
                currency VARCHAR(3) NOT NULL,
                unit VARCHAR(32) NOT NULL,
                rates JSONB NOT NULL,
                PRIMARY KEY (provider_code, model_code, valid_from)
            )'
        );

        $this->addSql(
            "CREATE TABLE usage_events (
                id BIGSERIAL PRIMARY KEY,
                provider_code VARCHAR(64) NOT NULL,
                provider_request_id VARCHAR(255) NOT NULL,
                model_code VARCHAR(64) NOT NULL,
                user_id VARCHAR(255) NOT NULL,
                ts TIMESTAMPTZ NOT NULL,
                tokens_input BIGINT NOT NULL DEFAULT 0,
                tokens_output BIGINT NOT NULL DEFAULT 0,
                tokens_cache_read BIGINT NOT NULL DEFAULT 0,
                tokens_cache_write BIGINT NOT NULL DEFAULT 0,
                status VARCHAR(16) NOT NULL DEFAULT 'pending',
                cost VARCHAR(30),
                price_valid_from TIMESTAMPTZ,
                UNIQUE (provider_code, provider_request_id)
            )"
        );

        $this->addSql(
            'CREATE INDEX idx_usage_events_status ON usage_events (status)'
        );
        $this->addSql(
            'CREATE INDEX idx_usage_events_ts ON usage_events (ts)'
        );
        $this->addSql(
            'CREATE INDEX idx_prices_lookup ON prices (provider_code, model_code, valid_from DESC)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS usage_events');
        $this->addSql('DROP TABLE IF EXISTS prices');
        $this->addSql('DROP TABLE IF EXISTS models');
        $this->addSql('DROP TABLE IF EXISTS providers');
    }
}

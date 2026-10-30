<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260101000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE providers (
                id BIGSERIAL PRIMARY KEY,
                code TEXT NOT NULL,
                name TEXT NOT NULL,
                off_peak_start_min INTEGER NULL,
                off_peak_end_min INTEGER NULL,
                off_peak_multiplier VARCHAR(64) NULL,
                CONSTRAINT uq_providers_code UNIQUE (code)
            );
            SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE models (
                id BIGSERIAL PRIMARY KEY,
                provider_id BIGINT NOT NULL REFERENCES providers (id),
                code TEXT NOT NULL,
                CONSTRAINT uq_models_provider_code UNIQUE (provider_id, code)
            );
            SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE price_rows (
                id BIGSERIAL PRIMARY KEY,
                provider_id BIGINT NOT NULL REFERENCES providers (id),
                model_id BIGINT NOT NULL REFERENCES models (id),
                valid_from TIMESTAMPTZ NOT NULL,
                unit VARCHAR(32) NOT NULL,
                currency VARCHAR(3) NOT NULL DEFAULT 'USD',
                rate_input NUMERIC(20,6) NULL,
                rate_output NUMERIC(20,6) NULL,
                rate_cache_read NUMERIC(20,6) NULL,
                rate_cache_write NUMERIC(20,6) NULL,
                rate_any NUMERIC(20,6) NULL,
                CONSTRAINT uq_price_rows UNIQUE (provider_id, model_id, valid_from)
            );
            SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE usage_events (
                id BIGSERIAL PRIMARY KEY,
                provider_code TEXT NOT NULL,
                provider_request_id TEXT NOT NULL,
                model_code TEXT NOT NULL,
                "user_id" TEXT NOT NULL,
                ts TIMESTAMPTZ NOT NULL,
                tokens_input BIGINT NOT NULL DEFAULT 0,
                tokens_output BIGINT NOT NULL DEFAULT 0,
                tokens_cache_read BIGINT NOT NULL DEFAULT 0,
                tokens_cache_write BIGINT NOT NULL DEFAULT 0,
                status VARCHAR(16) NOT NULL,
                price_row_id BIGINT NULL REFERENCES price_rows (id),
                cost_usd NUMERIC(20,6) NULL,
                rated_at TIMESTAMPTZ NULL,
                created_at TIMESTAMPTZ NOT NULL,
                CONSTRAINT uq_usage_events UNIQUE (provider_code, provider_request_id)
            );
            SQL);

        $this->addSql('CREATE INDEX ix_usage_events_status ON usage_events (status)');
        $this->addSql('CREATE INDEX ix_usage_events_ts ON usage_events (ts)');
        $this->addSql('CREATE INDEX ix_usage_events_status_ts ON usage_events (status, ts)');
        $this->addSql('CREATE INDEX ix_usage_events_price_row ON usage_events (price_row_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS usage_events');
        $this->addSql('DROP TABLE IF EXISTS price_rows');
        $this->addSql('DROP TABLE IF EXISTS models');
        $this->addSql('DROP TABLE IF EXISTS providers');
    }
}

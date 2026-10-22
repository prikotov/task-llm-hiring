<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

final class Version20261020000001 extends \Doctrine\Migrations\AbstractMigration
{
    public function getDescription(): string
    {
        return 'Начальная схема учёта расходов на LLM-токены';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS providers (
                id SERIAL PRIMARY KEY,
                code VARCHAR(128) NOT NULL,
                name VARCHAR(255) NOT NULL,
                off_peak_start VARCHAR(5) DEFAULT NULL,
                off_peak_end VARCHAR(5) DEFAULT NULL,
                off_peak_multiplier VARCHAR(32) DEFAULT NULL,
                CONSTRAINT uniq_providers_code UNIQUE (code)
            )
            SQL,
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS models (
                id SERIAL PRIMARY KEY,
                provider VARCHAR(128) NOT NULL,
                code VARCHAR(128) NOT NULL,
                CONSTRAINT uniq_models_provider_code UNIQUE (provider, code)
            )
            SQL,
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS price_rows (
                id SERIAL PRIMARY KEY,
                provider VARCHAR(128) NOT NULL,
                model VARCHAR(128) NOT NULL,
                valid_from TIMESTAMP(6) NOT NULL,
                currency VARCHAR(3) NOT NULL,
                unit VARCHAR(32) NOT NULL,
                rates_json TEXT NOT NULL,
                CONSTRAINT uniq_price_rows_provider_model_from UNIQUE (provider, model, valid_from)
            )
            SQL,
        );
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_price_rows_lookup ON price_rows (provider, model, valid_from)');
        $this->addSql(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS usage_events (
                id SERIAL PRIMARY KEY,
                provider VARCHAR(128) NOT NULL,
                provider_request_id VARCHAR(512) NOT NULL,
                model VARCHAR(128) NOT NULL,
                "user" VARCHAR(255) NOT NULL,
                ts TIMESTAMP(6) NOT NULL,
                tokens_input BIGINT NOT NULL,
                tokens_output BIGINT NOT NULL,
                tokens_cache_read BIGINT NOT NULL,
                tokens_cache_write BIGINT NOT NULL,
                rated BOOLEAN NOT NULL DEFAULT FALSE,
                cost NUMERIC(28, 6) DEFAULT NULL,
                applied_price_valid_from TIMESTAMP(6) DEFAULT NULL,
                CONSTRAINT uniq_usage_events_provider_request UNIQUE (provider, provider_request_id),
                CONSTRAINT ck_usage_events_tokens_nonneg
                    CHECK (tokens_input >= 0 AND tokens_output >= 0 AND tokens_cache_read >= 0 AND tokens_cache_write >= 0)
            )
            SQL,
        );
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_usage_events_rated ON usage_events (rated)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_usage_events_ts ON usage_events (ts)');
        $this->addSql(
            'CREATE INDEX IF NOT EXISTS idx_usage_events_applied_price ON usage_events (provider, model, applied_price_valid_from)',
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS usage_events');
        $this->addSql('DROP TABLE IF EXISTS price_rows');
        $this->addSql('DROP TABLE IF EXISTS models');
        $this->addSql('DROP TABLE IF EXISTS providers');
    }
}

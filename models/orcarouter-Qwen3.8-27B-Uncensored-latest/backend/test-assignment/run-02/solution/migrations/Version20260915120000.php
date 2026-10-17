<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Начальная схема: справочник цен и журнал расходов (PostgreSQL 17).
 * Значения-деньги — NUMERIC(28,6); строки rates — JSON со строковыми
 * десятичными значениями (float в схеме не используется).
 */
final class Version20260915120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create billing reference and usage schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "CREATE TABLE providers (
                code      VARCHAR(128) NOT NULL,
                name      VARCHAR(255) NOT NULL,
                off_peak  JSON,
                PRIMARY KEY (code)
            )"
        );

        $this->addSql(
            "CREATE TABLE models (
                id            SERIAL PRIMARY KEY,
                provider_code VARCHAR(128) NOT NULL,
                code          VARCHAR(128) NOT NULL,
                CONSTRAINT uq_models_provider_code UNIQUE (provider_code, code)
            )"
        );

        $this->addSql(
            "CREATE TABLE price_lines (
                id            SERIAL PRIMARY KEY,
                provider_code VARCHAR(128) NOT NULL,
                model_code    VARCHAR(128) NOT NULL,
                valid_from    TIMESTAMP NOT NULL,
                currency      VARCHAR(8) NOT NULL DEFAULT 'USD',
                unit          VARCHAR(32) NOT NULL,
                rates         JSON NOT NULL DEFAULT '{}'::json,
                CONSTRAINT uq_price_lines_provider_model_valid_from
                    UNIQUE (provider_code, model_code, valid_from)
            )"
        );
        $this->addSql(
            'CREATE INDEX ix_price_lines_lookup ON price_lines (provider_code, model_code, valid_from DESC)'
        );

        $this->addSql(
            "CREATE TABLE usage_events (
                id                    SERIAL PRIMARY KEY,
                provider_code         VARCHAR(128) NOT NULL,
                provider_request_id   VARCHAR(255) NOT NULL,
                model_code            VARCHAR(128) NOT NULL,
                user_id               VARCHAR(255) NOT NULL,
                ts                    TIMESTAMP NOT NULL,
                tokens_input          BIGINT NOT NULL DEFAULT 0,
                tokens_output         BIGINT NOT NULL DEFAULT 0,
                tokens_cache_read     BIGINT NOT NULL DEFAULT 0,
                tokens_cache_write    BIGINT NOT NULL DEFAULT 0,
                status                VARCHAR(16) NOT NULL DEFAULT 'unrated',
                cost                  NUMERIC(28, 6),
                price_line_id         INTEGER,
                rated_at              TIMESTAMP,
                CONSTRAINT uq_usage_events_provider_request
                    UNIQUE (provider_code, provider_request_id)
            )"
        );
        $this->addSql('CREATE INDEX ix_usage_events_status ON usage_events (status)');
        $this->addSql('CREATE INDEX ix_usage_events_ts ON usage_events (ts)');
        $this->addSql('CREATE INDEX ix_usage_events_price_line ON usage_events (price_line_id)');

        $this->addSql(
            "CREATE TABLE reprice_corrections (
                id         SERIAL PRIMARY KEY,
                event_id   INTEGER NOT NULL,
                old_cost   NUMERIC(28, 6) NOT NULL,
                new_cost   NUMERIC(28, 6) NOT NULL,
                created_at TIMESTAMP NOT NULL
            )"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS reprice_corrections');
        $this->addSql('DROP TABLE IF EXISTS usage_events');
        $this->addSql('DROP TABLE IF EXISTS price_lines');
        $this->addSql('DROP TABLE IF EXISTS models');
        $this->addSql('DROP TABLE IF EXISTS providers');
    }
}

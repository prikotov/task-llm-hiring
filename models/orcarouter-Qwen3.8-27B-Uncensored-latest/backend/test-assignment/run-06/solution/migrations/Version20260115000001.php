<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Начальная схема: справочник (провайдеры, модели, история цен)
 * и журнал событий использования с результатами тарификации.
 *
 * Деньги: только точные десятичные (DECIMAL-as-TEXT ставки) и целые
 * микров юниты (BIGINT) — float в схеме нет.
 */
final class Version20260115000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE providers (
            id SERIAL PRIMARY KEY,
            code VARCHAR(128) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            off_peak_start VARCHAR(5) NULL,
            off_peak_end VARCHAR(5) NULL,
            off_peak_multiplier VARCHAR(32) NULL
        )');

        $this->addSql('CREATE TABLE models (
            id SERIAL PRIMARY KEY,
            provider VARCHAR(128) NOT NULL,
            code VARCHAR(128) NOT NULL,
            CONSTRAINT uniq_model_provider_code UNIQUE (provider, code)
        )');

        $this->addSql('CREATE TABLE price_rows (
            id SERIAL PRIMARY KEY,
            provider VARCHAR(128) NOT NULL,
            model VARCHAR(128) NOT NULL,
            valid_from TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            currency VARCHAR(3) NOT NULL,
            unit VARCHAR(32) NOT NULL,
            rates_json TEXT NOT NULL,
            CONSTRAINT uniq_price_provider_model_from UNIQUE (provider, model, valid_from)
        )');

        $this->addSql('CREATE TABLE usage_events (
            id SERIAL PRIMARY KEY,
            provider VARCHAR(128) NOT NULL,
            provider_request_id VARCHAR(512) NOT NULL,
            model VARCHAR(128) NOT NULL,
            user_id VARCHAR(255) NOT NULL,
            ts TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            tokens_input BIGINT NOT NULL,
            tokens_output BIGINT NOT NULL,
            tokens_cache_read BIGINT NOT NULL,
            tokens_cache_write BIGINT NOT NULL,
            status VARCHAR(16) NOT NULL,
            cost_micros BIGINT NULL,
            applied_price_id INTEGER NULL,
            rated_at TIMESTAMP(0) WITH TIME ZONE NULL,
            CONSTRAINT uniq_usage_provider_request UNIQUE (provider, provider_request_id),
            CONSTRAINT fk_usage_applied_price FOREIGN KEY (applied_price_id) REFERENCES price_rows (id) ON DELETE SET NULL,
            CONSTRAINT ck_usage_tokens_nonneg CHECK (
                tokens_input >= 0 AND tokens_output >= 0
                AND tokens_cache_read >= 0 AND tokens_cache_write >= 0
            )
        )');

        $this->addSql('CREATE INDEX idx_usage_rating_queue ON usage_events (status, id)');
        $this->addSql("CREATE INDEX idx_usage_report ON usage_events (status, user_id, provider, model, ts)");
        $this->addSql('CREATE INDEX idx_price_pick ON price_rows (provider, model, valid_from DESC)');
        $this->addSql('CREATE INDEX idx_usage_applied_price ON usage_events (applied_price_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE usage_events');
        $this->addSql('DROP TABLE price_rows');
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE providers');
    }
}

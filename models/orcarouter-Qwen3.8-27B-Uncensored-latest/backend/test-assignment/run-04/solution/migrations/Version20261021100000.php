<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261021100000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE providers (
                code VARCHAR(64) NOT NULL,
                name VARCHAR(255) NOT NULL,
                off_peak_window_start VARCHAR(5) DEFAULT NULL,
                off_peak_window_end VARCHAR(5) DEFAULT NULL,
                off_peak_multiplier NUMERIC(30, 10) DEFAULT NULL,
                PRIMARY KEY (code)
            );
        ');
        $this->addSql('
            CREATE TABLE models (
                id BIGSERIAL NOT NULL,
                provider VARCHAR(64) NOT NULL,
                code VARCHAR(64) NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_models_provider FOREIGN KEY (provider) REFERENCES providers (code)
            );
        ');
        $this->addSql('CREATE UNIQUE INDEX uniq_models_provider_code ON models (provider, code);');
        $this->addSql('
            CREATE TABLE price_rows (
                id BIGSERIAL NOT NULL,
                provider VARCHAR(64) NOT NULL,
                model VARCHAR(64) NOT NULL,
                valid_from TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                currency VARCHAR(16) NOT NULL,
                unit VARCHAR(32) NOT NULL,
                rate_input NUMERIC(30, 10) DEFAULT NULL,
                rate_output NUMERIC(30, 10) DEFAULT NULL,
                rate_cache_read NUMERIC(30, 10) DEFAULT NULL,
                rate_cache_write NUMERIC(30, 10) DEFAULT NULL,
                rate_any NUMERIC(30, 10) DEFAULT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_price_rows_provider FOREIGN KEY (provider) REFERENCES providers (code),
                CONSTRAINT fk_price_rows_model FOREIGN KEY (provider, model) REFERENCES models (provider, code)
            );
        ');
        $this->addSql('CREATE UNIQUE INDEX uniq_price_rows_provider_model_valid_from ON price_rows (provider, model, valid_from);');
        $this->addSql('
            CREATE TABLE usage_events (
                id BIGSERIAL NOT NULL,
                provider VARCHAR(64) NOT NULL,
                provider_request_id VARCHAR(128) NOT NULL,
                model VARCHAR(64) NOT NULL,
                "user" VARCHAR(128) NOT NULL,
                ts TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                tokens_input BIGINT NOT NULL,
                tokens_output BIGINT NOT NULL,
                tokens_cache_read BIGINT NOT NULL,
                tokens_cache_write BIGINT NOT NULL,
                status VARCHAR(16) NOT NULL DEFAULT \'unrated\',
                price_row_id BIGINT DEFAULT NULL,
                cost NUMERIC(30, 10) DEFAULT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_usage_events_price_row FOREIGN KEY (price_row_id) REFERENCES price_rows (id),
                CONSTRAINT chk_usage_events_status CHECK (status IN (\'unrated\', \'rated\')),
                CONSTRAINT chk_usage_events_tokens CHECK (
                    tokens_input >= 0 AND tokens_output >= 0
                    AND tokens_cache_read >= 0 AND tokens_cache_write >= 0
                )
            );
        ');
        $this->addSql('CREATE UNIQUE INDEX uniq_usage_events_provider_request ON usage_events (provider, provider_request_id);');
        $this->addSql('CREATE INDEX idx_usage_events_rating ON usage_events (provider, model, status);');
        $this->addSql('CREATE INDEX idx_usage_events_report ON usage_events ("user", provider, model, ts);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS usage_events;');
        $this->addSql('DROP TABLE IF EXISTS price_rows;');
        $this->addSql('DROP TABLE IF EXISTS models;');
        $this->addSql('DROP TABLE IF EXISTS providers;');
    }
}

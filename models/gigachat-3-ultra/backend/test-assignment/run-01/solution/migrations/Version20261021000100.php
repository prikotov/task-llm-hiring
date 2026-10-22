<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261021000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for LLM billing: providers, models, prices, usage events.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE providers (code VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, off_peak_window_start VARCHAR(5) DEFAULT NULL, off_peak_window_end VARCHAR(5) DEFAULT NULL, off_peak_multiplier NUMERIC(10, 6) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE models (id SERIAL PRIMARY KEY, provider_code VARCHAR(64) NOT NULL, code VARCHAR(64) NOT NULL, CONSTRAINT UNIQ_MODEL_PROVIDER_CODE UNIQUE (provider_code, code), FOREIGN KEY (provider_code) REFERENCES providers (code) ON DELETE CASCADE)');
        $this->addSql('CREATE TABLE prices (id SERIAL PRIMARY KEY, provider_code VARCHAR(64) NOT NULL, model_id INT NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, currency VARCHAR(3) NOT NULL, unit VARCHAR(32) NOT NULL, input_rate NUMERIC(12, 6) NOT NULL, output_rate NUMERIC(12, 6) NOT NULL, cache_read_rate NUMERIC(12, 6) NOT NULL, cache_write_rate NUMERIC(12, 6) NOT NULL, unified_rate NUMERIC(12, 6) DEFAULT NULL, CONSTRAINT UNIQ_PRICE_PROVIDER_MODEL_VALID_FROM UNIQUE (provider_code, model_id, valid_from), FOREIGN KEY (provider_code) REFERENCES providers (code) ON DELETE CASCADE, FOREIGN KEY (model_id) REFERENCES models (id) ON DELETE CASCADE)');
        $this->addSql('CREATE TABLE usage_events (id SERIAL PRIMARY KEY, provider_code VARCHAR(64) NOT NULL, provider_request_id VARCHAR(128) NOT NULL, model_id INT NOT NULL, user_id VARCHAR(128) NOT NULL, ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tokens_input BIGINT NOT NULL, tokens_output BIGINT NOT NULL, tokens_cache_read BIGINT NOT NULL, tokens_cache_write BIGINT NOT NULL, cost_usd NUMERIC(16, 6) DEFAULT NULL, CONSTRAINT UNIQ_EVENT_PROVIDER_REQUEST_ID UNIQUE (provider_code, provider_request_id), FOREIGN KEY (provider_code) REFERENCES providers (code) ON DELETE CASCADE, FOREIGN KEY (model_id) REFERENCES models (id) ON DELETE CASCADE)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE usage_events');
        $this->addSql('DROP TABLE prices');
        $schema->dropTable('models');
        $this->addSql('DROP TABLE providers');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020122132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE usage_records (provider_code VARCHAR(255) NOT NULL, provider_request_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, cost NUMERIC(20, 6) DEFAULT NULL, PRIMARY KEY (provider_code, provider_request_id))');
        $this->addSql('CREATE INDEX rec_lookup ON usage_records (provider_code, provider_request_id)');
        $this->addSql('ALTER TABLE models DROP CONSTRAINT fk_e4d63009a53a8aa');
        $this->addSql('ALTER TABLE models DROP CONSTRAINT models_pkey');
        $this->addSql('DROP INDEX uniq_model');
        $this->addSql('DROP INDEX idx_e4d63009a53a8aa');
        $this->addSql('ALTER TABLE models ADD provider_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE models ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE models DROP id');
        $this->addSql('ALTER TABLE models DROP provider_id');
        $this->addSql('CREATE INDEX model_uid ON models (provider_code, code)');
        $this->addSql('ALTER TABLE models ADD PRIMARY KEY (provider_code, code)');
        $this->addSql('ALTER TABLE price_rows DROP CONSTRAINT fk_77d9061ba53a8aa');
        $this->addSql('ALTER TABLE price_rows DROP CONSTRAINT fk_77d9061b7975b7e7');
        $this->addSql('DROP INDEX idx_77d9061ba53a8aa');
        $this->addSql('ALTER TABLE price_rows DROP CONSTRAINT price_rows_pkey');
        $this->addSql('DROP INDEX uniq_price_row');
        $this->addSql('DROP INDEX idx_77d9061b7975b7e7');
        $this->addSql('ALTER TABLE price_rows ADD provider_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE price_rows ADD model_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rates JSON NOT NULL');
        $this->addSql('ALTER TABLE price_rows DROP id');
        $this->addSql('ALTER TABLE price_rows DROP rate_input');
        $this->addSql('ALTER TABLE price_rows DROP rate_output');
        $this->addSql('ALTER TABLE price_rows DROP rate_cache_read');
        $this->addSql('ALTER TABLE price_rows DROP rate_cache_write');
        $this->addSql('ALTER TABLE price_rows DROP rate_any');
        $this->addSql('ALTER TABLE price_rows DROP provider_id');
        $this->addSql('ALTER TABLE price_rows DROP model_id');
        $this->addSql('CREATE INDEX price_lookup ON price_rows (provider_code, model_code, valid_from)');
        $this->addSql('ALTER TABLE price_rows ADD PRIMARY KEY (provider_code, model_code, valid_from)');
        $this->addSql('ALTER TABLE providers DROP CONSTRAINT providers_pkey');
        $this->addSql('DROP INDEX uniq_provider_code');
        $this->addSql('ALTER TABLE providers ADD window_start VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE providers ADD window_end VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE providers DROP id');
        $this->addSql('ALTER TABLE providers DROP off_peak_start');
        $this->addSql('ALTER TABLE providers DROP off_peak_end');
        $this->addSql('ALTER TABLE providers ALTER off_peak_multiplier TYPE NUMERIC(10, 6)');
        $this->addSql('ALTER TABLE providers ADD PRIMARY KEY (code)');
        $this->addSql('ALTER TABLE usage_events DROP CONSTRAINT usage_events_pkey');
        $this->addSql('DROP INDEX uniq_usage_event');
        $this->addSql('ALTER TABLE usage_events ADD provider_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usage_events ADD model_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usage_events DROP id');
        $this->addSql('ALTER TABLE usage_events DROP provider_id');
        $this->addSql('ALTER TABLE usage_events DROP model');
        $this->addSql('ALTER TABLE usage_events DROP rated');
        $this->addSql('ALTER TABLE usage_events DROP cost_usd');
        $this->addSql('ALTER TABLE usage_events DROP price_row_id');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_input TYPE INT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_output TYPE INT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_cache_read TYPE INT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_cache_write TYPE INT');
        $this->addSql('ALTER TABLE usage_events RENAME COLUMN ts TO event_ts');
        $this->addSql('CREATE INDEX ev_user_ts ON usage_events ("user", event_ts)');
        $this->addSql('ALTER TABLE usage_events ADD PRIMARY KEY (provider_code, provider_request_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE usage_records');
        $this->addSql('ALTER TABLE models DROP CONSTRAINT models_pkey');
        $this->addSql('DROP INDEX model_uid');
        $this->addSql('ALTER TABLE models ADD id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE models ADD provider_id INT NOT NULL');
        $this->addSql('ALTER TABLE models DROP provider_code');
        $this->addSql('ALTER TABLE models DROP name');
        $this->addSql('ALTER TABLE models ADD CONSTRAINT fk_e4d63009a53a8aa FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_model ON models (provider_id, code)');
        $this->addSql('CREATE INDEX idx_e4d63009a53a8aa ON models (provider_id)');
        $this->addSql('ALTER TABLE models ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE price_rows DROP CONSTRAINT price_rows_pkey');
        $this->addSql('DROP INDEX price_lookup');
        $this->addSql('ALTER TABLE price_rows ADD id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rate_input NUMERIC(20, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rate_output NUMERIC(20, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rate_cache_read NUMERIC(20, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rate_cache_write NUMERIC(20, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_rows ADD rate_any NUMERIC(20, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_rows ADD provider_id INT NOT NULL');
        $this->addSql('ALTER TABLE price_rows ADD model_id INT NOT NULL');
        $this->addSql('ALTER TABLE price_rows DROP provider_code');
        $this->addSql('ALTER TABLE price_rows DROP model_code');
        $this->addSql('ALTER TABLE price_rows DROP rates');
        $this->addSql('ALTER TABLE price_rows ADD CONSTRAINT fk_77d9061ba53a8aa FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE price_rows ADD CONSTRAINT fk_77d9061b7975b7e7 FOREIGN KEY (model_id) REFERENCES models (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_77d9061ba53a8aa ON price_rows (provider_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_price_row ON price_rows (provider_id, model_id, valid_from)');
        $this->addSql('CREATE INDEX idx_77d9061b7975b7e7 ON price_rows (model_id)');
        $this->addSql('ALTER TABLE price_rows ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE providers DROP CONSTRAINT providers_pkey');
        $this->addSql('ALTER TABLE providers ADD id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE providers ADD off_peak_start VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE providers ADD off_peak_end VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE providers DROP window_start');
        $this->addSql('ALTER TABLE providers DROP window_end');
        $this->addSql('ALTER TABLE providers ALTER off_peak_multiplier TYPE NUMERIC(20, 6)');
        $this->addSql('CREATE UNIQUE INDEX uniq_provider_code ON providers (code)');
        $this->addSql('ALTER TABLE providers ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE usage_events DROP CONSTRAINT usage_events_pkey');
        $this->addSql('DROP INDEX ev_user_ts');
        $this->addSql('ALTER TABLE usage_events ADD id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE usage_events ADD provider_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usage_events ADD model VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usage_events ADD rated BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE usage_events ADD cost_usd NUMERIC(40, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE usage_events ADD price_row_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usage_events DROP provider_code');
        $this->addSql('ALTER TABLE usage_events DROP model_code');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_input TYPE BIGINT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_output TYPE BIGINT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_cache_read TYPE BIGINT');
        $this->addSql('ALTER TABLE usage_events ALTER tokens_cache_write TYPE BIGINT');
        $this->addSql('ALTER TABLE usage_events RENAME COLUMN event_ts TO ts');
        $this->addSql('CREATE UNIQUE INDEX uniq_usage_event ON usage_events (provider_id, provider_request_id)');
        $this->addSql('ALTER TABLE usage_events ADD PRIMARY KEY (id)');
    }
}

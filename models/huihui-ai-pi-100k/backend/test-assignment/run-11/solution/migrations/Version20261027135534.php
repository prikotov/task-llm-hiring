<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261027135534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // the public schema is always present in PostgreSQL; do not create it
        $this->addSql('CREATE TABLE models (provider VARCHAR(64) NOT NULL, code VARCHAR(64) NOT NULL, PRIMARY KEY (provider, code))');
        $this->addSql('CREATE UNIQUE INDEX uq_models_provider_code ON models (provider, code)');
        $this->addSql('CREATE TABLE price_rows (provider VARCHAR(64) NOT NULL, model VARCHAR(64) NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, currency VARCHAR(8) DEFAULT NULL, payload JSON NOT NULL, PRIMARY KEY (provider, model, valid_from))');
        $this->addSql('CREATE UNIQUE INDEX uq_price_rows_key ON price_rows (provider, model, valid_from)');
        $this->addSql('CREATE TABLE providers (code VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, off_peak_window_start VARCHAR(8) DEFAULT NULL, off_peak_window_end VARCHAR(8) DEFAULT NULL, off_peak_multiplier VARCHAR(8) DEFAULT NULL, PRIMARY KEY (code))');
        $this->addSql('CREATE TABLE rated_events (provider VARCHAR(64) NOT NULL, provider_request_id VARCHAR(64) NOT NULL, model VARCHAR(64) NOT NULL, usr VARCHAR(64) NOT NULL, ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tokens_input INT NOT NULL, tokens_output INT NOT NULL, tokens_cache_read INT NOT NULL, tokens_cache_write INT NOT NULL, cost_usd VARCHAR(40) NOT NULL, original_cost_usd VARCHAR(40) DEFAULT NULL, PRIMARY KEY (provider, provider_request_id))');
        $this->addSql('CREATE UNIQUE INDEX uq_rated_events_key ON rated_events (provider, provider_request_id)');
        $this->addSql('CREATE TABLE usage_events (provider VARCHAR(64) NOT NULL, provider_request_id VARCHAR(64) NOT NULL, model VARCHAR(64) NOT NULL, usr VARCHAR(64) NOT NULL, ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tokens_input INT NOT NULL, tokens_output INT NOT NULL, tokens_cache_read INT NOT NULL, tokens_cache_write INT NOT NULL, PRIMARY KEY (provider, provider_request_id))');
        $this->addSql('CREATE UNIQUE INDEX uq_usage_events_key ON usage_events (provider, provider_request_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE price_rows');
        $this->addSql('DROP TABLE providers');
        $this->addSql('DROP TABLE rated_events');
        $this->addSql('DROP TABLE usage_events');
    }
}

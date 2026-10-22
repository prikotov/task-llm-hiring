<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260901000000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create LLM billing schema'; }
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE providers (code VARCHAR(100) PRIMARY KEY, name VARCHAR(255) NOT NULL, window_start VARCHAR(5), window_end VARCHAR(5), multiplier NUMERIC(30,16))");
        $this->addSql("CREATE TABLE models (id BIGSERIAL PRIMARY KEY, provider_code VARCHAR(100) NOT NULL REFERENCES providers(code), code VARCHAR(150) NOT NULL, UNIQUE(provider_code, code))");
        $this->addSql("CREATE TABLE prices (id BIGSERIAL PRIMARY KEY, model_id BIGINT NOT NULL REFERENCES models(id), valid_from TIMESTAMPTZ NOT NULL, currency VARCHAR(3) NOT NULL, unit VARCHAR(40) NOT NULL, rates JSONB NOT NULL, UNIQUE(model_id, valid_from))");
        $this->addSql("CREATE TABLE usage_events (id BIGSERIAL PRIMARY KEY, provider_code VARCHAR(100) NOT NULL, request_id VARCHAR(255) NOT NULL, model_code VARCHAR(150) NOT NULL, user_id VARCHAR(255) NOT NULL, event_ts TIMESTAMPTZ NOT NULL, tokens_input BIGINT NOT NULL, tokens_output BIGINT NOT NULL, tokens_cache_read BIGINT NOT NULL, tokens_cache_write BIGINT NOT NULL, cost NUMERIC(30,6), rated BOOLEAN NOT NULL DEFAULT FALSE, UNIQUE(provider_code, request_id))");
        $this->addSql('CREATE INDEX usage_events_report_idx ON usage_events (event_ts)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE usage_events'); $this->addSql('DROP TABLE prices'); $this->addSql('DROP TABLE models'); $this->addSql('DROP TABLE providers');
    }
}

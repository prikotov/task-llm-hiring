<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Начальная схема: справочник цен (providers, models, price_rows)
 * и журнал событий использования (usage_events).
 */
final class Version20261017120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Справочник цен и журнал событий использования (LLM-биллинг)';
    }

    public function up(Schema $schema): void
    {
        $providers = $schema->createTable('providers');
        $providers->addColumn('id', 'integer', ['autoincrement' => true]);
        $providers->addColumn('code', 'string', ['length' => 128]);
        $providers->addColumn('name', 'string', ['length' => 255]);
        $providers->addColumn('off_peak_window_start', 'string', ['length' => 5]);
        $providers->addColumn('off_peak_window_end', 'string', ['length' => 5]);
        $providers->addColumn('off_peak_multiplier', 'decimal', ['precision' => 24, 'scale' => 16]);
        $providers->setPrimaryKey(['id']);
        foreach (['off_peak_window_start', 'off_peak_window_end', 'off_peak_multiplier'] as $nullable) {
            $providers->getColumn($nullable)->setNotnull(false);
        }
        $providers->addUniqueIndex(['code'], 'uq_providers_code');

        $models = $schema->createTable('models');
        $models->addColumn('id', 'integer', ['autoincrement' => true]);
        $models->addColumn('provider_id', 'integer');
        $models->addColumn('code', 'string', ['length' => 128]);
        $models->setPrimaryKey(['id']);
        $models->addUniqueIndex(['provider_id', 'code'], 'uq_models_provider_code');
        $models->addForeignKeyConstraint('providers', ['provider_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_E4D63009A53A8AA');

        $priceRows = $schema->createTable('price_rows');
        $priceRows->addColumn('id', 'integer', ['autoincrement' => true]);
        $priceRows->addColumn('model_id', 'integer');
        $priceRows->addColumn('valid_from', 'datetime_immutable');
        $priceRows->addColumn('currency', 'string', ['length' => 8]);
        $priceRows->addColumn('unit', 'string', ['length' => 32]);
        $priceRows->addColumn('rates', 'json');
        $priceRows->setPrimaryKey(['id']);
        $priceRows->addUniqueIndex(['model_id', 'valid_from'], 'uq_price_rows_model_valid_from');
        $priceRows->addForeignKeyConstraint('models', ['model_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_77D9061B7975B7E7');

        $events = $schema->createTable('usage_events');
        $events->addColumn('id', 'integer', ['autoincrement' => true]);
        $events->addColumn('provider_code', 'string', ['length' => 128]);
        $events->addColumn('provider_request_id', 'string', ['length' => 128]);
        $events->addColumn('model_code', 'string', ['length' => 128]);
        $events->addColumn('user_name', 'string', ['length' => 255]);
        $events->addColumn('ts', 'datetime_immutable');
        $events->addColumn('ts_raw', 'string', ['length' => 64]);
        $events->addColumn('tokens_input', 'integer');
        $events->addColumn('tokens_output', 'integer');
        $events->addColumn('tokens_cache_read', 'integer');
        $events->addColumn('tokens_cache_write', 'integer');
        $events->addColumn('status', 'string', ['length' => 16]);
        $events->addColumn('cost_usd', 'decimal', ['precision' => 38, 'scale' => 6]);
        $events->addColumn('price_row_id', 'integer');
        $events->getColumn('cost_usd')->setNotnull(false);
        $events->getColumn('price_row_id')->setNotnull(false);
        $events->addColumn('created_at', 'datetime_immutable');
        $events->setPrimaryKey(['id']);
        $events->addUniqueIndex(['provider_code', 'provider_request_id'], 'uq_usage_events_provider_request');
        $events->addIndex(['status'], 'idx_usage_events_status');
        $events->addIndex(['ts'], 'idx_usage_events_ts');
        $events->addForeignKeyConstraint('price_rows', ['price_row_id'], ['id'], ['onDelete' => 'SET NULL'], 'fk_usage_events_price_row');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE usage_events');
        $this->addSql('DROP TABLE price_rows');
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE providers');
    }
}

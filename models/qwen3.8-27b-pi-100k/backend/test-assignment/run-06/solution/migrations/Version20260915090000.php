<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260915090000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $providers = $schema->createTable('providers');
        $providers->addColumn('id', 'integer', ['autoincrement' => true]);
        $providers->addColumn('code', 'string', ['length' => 64]);
        $providers->addColumn('name', 'string', ['length' => 255]);
        $providers->addColumn('off_peak_window_start', 'string', ['length' => 5, 'notnull' => false]);
        $providers->addColumn('off_peak_window_end', 'string', ['length' => 5, 'notnull' => false]);
        $providers->addColumn('off_peak_multiplier', 'string', ['length' => 32, 'notnull' => false]);
        $providers->addUniqueIndex(['code'], 'uniq_providers_code');
        $providers->setPrimaryKey(['id']);

        $models = $schema->createTable('models');
        $models->addColumn('id', 'integer', ['autoincrement' => true]);
        $models->addColumn('provider_id', 'integer');
        $models->addColumn('code', 'string', ['length' => 140]);
        $models->addUniqueIndex(['provider_id', 'code'], 'uniq_models_provider_code');
        $models->setPrimaryKey(['id']);
        $models->addForeignKeyConstraint('providers', ['provider_id'], ['id'], ['ON DELETE' => 'CASCADE']);

        $priceRows = $schema->createTable('price_rows');
        $priceRows->addColumn('id', 'integer', ['autoincrement' => true]);
        $priceRows->addColumn('model_id', 'integer');
        $priceRows->addColumn('valid_from', 'datetime', ['comment' => 'UTC, включительно']);
        $priceRows->addColumn('currency', 'string', ['length' => 8]);
        $priceRows->addColumn('unit', 'string', ['length' => 32]);
        $priceRows->addColumn('rates', 'text', ['comment' => 'JSON-объект со строковыми десятичными ставками (без float)']);
        $priceRows->addUniqueIndex(['model_id', 'valid_from'], 'uniq_price_rows_model_valid_from');
        $priceRows->setPrimaryKey(['id']);
        $priceRows->addForeignKeyConstraint('models', ['model_id'], ['id'], ['ON DELETE' => 'CASCADE']);

        $usageEvents = $schema->createTable('usage_events');
        $usageEvents->addColumn('id', 'integer', ['autoincrement' => true]);
        $usageEvents->addColumn('provider', 'string', ['length' => 64]);
        $usageEvents->addColumn('provider_request_id', 'string', ['length' => 128]);
        $usageEvents->addColumn('model', 'string', ['length' => 64]);
        $usageEvents->addColumn('user_login', 'string', ['length' => 255]);
        $usageEvents->addColumn('ts', 'datetime', ['comment' => 'UTC']);
        $usageEvents->addColumn('tokens_input', 'bigint');
        $usageEvents->addColumn('tokens_output', 'bigint');
        $usageEvents->addColumn('tokens_cache_read', 'bigint');
        $usageEvents->addColumn('tokens_cache_write', 'bigint');
        $usageEvents->addColumn('status', 'string', ['length' => 16, 'default' => 'pending']);
        $usageEvents->addColumn('price_row_id', 'integer', ['notnull' => false]);
        $usageEvents->addColumn('cost_usd', 'string', ['length' => 40, 'notnull' => false, 'comment' => 'итоговая стоимость USD, 6 знаков, decimal string']);
        $usageEvents->addUniqueIndex(['provider', 'provider_request_id'], 'uniq_usage_events_provider_request');
        $usageEvents->addIndex(['status'], 'idx_usage_events_status');
        $usageEvents->addIndex(['ts'], 'idx_usage_events_ts');
        $usageEvents->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('usage_events');
        $schema->dropTable('price_rows');
        $schema->dropTable('models');
        $schema->dropTable('providers');
    }
}

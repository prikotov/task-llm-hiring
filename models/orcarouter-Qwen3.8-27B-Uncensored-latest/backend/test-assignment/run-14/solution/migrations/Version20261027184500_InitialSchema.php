<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Базовая схема БД: провайдеры, модели, строки цен, события использования.
 */
final class Version20261027184500_InitialSchema extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $providers = $schema->createTable('billing_providers');
        $providers->addColumn('code', 'string', ['length' => 64]);
        $providers->addColumn('name', 'string', ['length' => 255]);
        $providers->addColumn('off_peak_start_min', 'integer', ['unsigned' => true, 'notnull' => false]);
        $providers->addColumn('off_peak_end_min', 'integer', ['unsigned' => true, 'notnull' => false]);
        $providers->addColumn('off_peak_multiplier', 'string', ['length' => 32, 'notnull' => false]);
        $providers->setPrimaryKey(['code']);

        $models = $schema->createTable('billing_models');
        $models->addColumn('provider', 'string', ['length' => 64]);
        $models->addColumn('code', 'string', ['length' => 128]);
        $models->setPrimaryKey(['provider', 'code']);

        $prices = $schema->createTable('billing_price_rows');
        $prices->addColumn('id', 'integer', ['autoincrement' => true]);
        $prices->addColumn('provider', 'string', ['length' => 64]);
        $prices->addColumn('model', 'string', ['length' => 128]);
        $prices->addColumn('valid_from', 'datetime_immutable');
        $prices->addColumn('currency', 'string', ['length' => 8]);
        $prices->addColumn('unit', 'string', ['length' => 32]);
        $prices->addColumn('rates_json', 'string', ['length' => 2048]);
        $prices->addUniqueIndex(['provider', 'model', 'valid_from'], 'uq_price_rows');
        $prices->setPrimaryKey(['id']);

        $events = $schema->createTable('billing_usage_events');
        $events->addColumn('id', 'integer', ['autoincrement' => true]);
        $events->addColumn('provider', 'string', ['length' => 64]);
        $events->addColumn('provider_request_id', 'string', ['length' => 128]);
        $events->addColumn('user_name', 'string', ['length' => 255]);
        $events->addColumn('ts_utc', 'datetime_immutable');
        $events->addColumn('model_code', 'string', ['length' => 128]);
        $events->addColumn('tokens_input', 'bigint');
        $events->addColumn('tokens_output', 'bigint');
        $events->addColumn('tokens_cache_read', 'bigint');
        $events->addColumn('tokens_cache_write', 'bigint');
        $events->addColumn('rating_state', 'string', ['length' => 8]);
        $events->addColumn('exact_cost', 'string', ['length' => 64, 'notnull' => false]);
        $events->addColumn('currency', 'string', ['length' => 8, 'notnull' => false]);
        $events->addColumn('off_peak_applied', 'boolean', ['default' => false]);
        $events->addColumn('created_at', 'datetime_immutable');
        $events->addColumn('updated_at', 'datetime_immutable');
        $events->addUniqueIndex(['provider', 'provider_request_id'], 'uq_usage_events');
        $events->addIndex(['rating_state'], 'idx_usage_state');
        $events->addIndex(['ts_utc'], 'idx_usage_ts');
        $events->setPrimaryKey(['id']);

        $reprice = $schema->createTable('billing_reprice_runs');
        $reprice->addColumn('id', 'integer', ['autoincrement' => true]);
        $reprice->addColumn('price_row_ids_json', 'string', ['length' => 4096]);
        $reprice->addColumn('events_count', 'integer', ['unsigned' => true]);
        $reprice->addColumn('delta_usd', 'string', ['length' => 64]);
        $reprice->addColumn('created_at', 'datetime_immutable');
        $reprice->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('billing_reprice_runs');
        $schema->dropTable('billing_usage_events');
        $schema->dropTable('billing_price_rows');
        $schema->dropTable('billing_models');
        $schema->dropTable('billing_providers');
    }
}

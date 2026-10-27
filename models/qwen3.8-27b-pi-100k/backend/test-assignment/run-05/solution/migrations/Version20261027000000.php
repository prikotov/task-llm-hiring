<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Начальная схема: провайдеры, модели, строки цен, события использования';
    }

    public function up(Schema $schema): void
    {
        $provider = $schema->createTable('provider');
        $provider->addColumn('id', 'integer', ['autoincrement' => true]);
        $provider->addColumn('code', 'string', ['length' => 255]);
        $provider->addColumn('name', 'string', ['length' => 255]);
        $provider->addColumn('off_peak_window_start', 'string', ['length' => 5, 'notnull' => false]);
        $provider->addColumn('off_peak_window_end', 'string', ['length' => 5, 'notnull' => false]);
        $provider->addColumn('off_peak_multiplier', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $provider->setPrimaryKey(['id']);
        $provider->addUniqueIndex(['code'], 'uniq_provider_code');

        $model = $schema->createTable('model');
        $model->addColumn('id', 'integer', ['autoincrement' => true]);
        $model->addColumn('provider_id', 'integer');
        $model->addColumn('code', 'string', ['length' => 255]);
        $model->setPrimaryKey(['id']);
        $model->addForeignKeyConstraint('provider', ['provider_id'], ['id'], ['onDelete' => 'CASCADE']);
        $model->addUniqueIndex(['provider_id', 'code'], 'uniq_model_provider_code');

        $priceRow = $schema->createTable('price_row');
        $priceRow->addColumn('id', 'integer', ['autoincrement' => true]);
        $priceRow->addColumn('model_id', 'integer');
        $priceRow->addColumn('valid_from', 'datetime_immutable');
        $priceRow->addColumn('currency', 'string', ['length' => 8]);
        $priceRow->addColumn('unit', 'string', ['length' => 32]);
        $priceRow->addColumn('rate_input', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $priceRow->addColumn('rate_output', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $priceRow->addColumn('rate_cache_read', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $priceRow->addColumn('rate_cache_write', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $priceRow->addColumn('rate_any', 'decimal', ['precision' => 16, 'scale' => 6, 'notnull' => false]);
        $priceRow->setPrimaryKey(['id']);
        $priceRow->addForeignKeyConstraint('model', ['model_id'], ['id'], ['onDelete' => 'CASCADE']);
        $priceRow->addUniqueIndex(['model_id', 'valid_from'], 'uniq_price_row_model_valid_from');

        $usageEvent = $schema->createTable('usage_event');
        $usageEvent->addColumn('id', 'bigint', ['autoincrement' => true]);
        $usageEvent->addColumn('provider', 'string', ['length' => 255]);
        $usageEvent->addColumn('provider_request_id', 'string', ['length' => 255]);
        $usageEvent->addColumn('model', 'string', ['length' => 255]);
        $usageEvent->addColumn('user_name', 'string', ['length' => 255]);
        $usageEvent->addColumn('ts', 'datetime_immutable');
        $usageEvent->addColumn('tokens_input', 'bigint');
        $usageEvent->addColumn('tokens_output', 'bigint');
        $usageEvent->addColumn('tokens_cache_read', 'bigint');
        $usageEvent->addColumn('tokens_cache_write', 'bigint');
        $usageEvent->addColumn('status', 'string', ['length' => 16]);
        $usageEvent->addColumn('cost_usd', 'decimal', ['precision' => 20, 'scale' => 6, 'notnull' => false]);
        $usageEvent->addColumn('price_row_id', 'integer', ['notnull' => false]);
        $usageEvent->setPrimaryKey(['id']);
        $usageEvent->addForeignKeyConstraint('price_row', ['price_row_id'], ['id'], ['onDelete' => 'SET NULL']);
        $usageEvent->addUniqueIndex(['provider', 'provider_request_id'], 'uniq_usage_event_provider_request');
        $usageEvent->addIndex(['status'], 'idx_usage_event_status');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('usage_event');
        $schema->dropTable('price_row');
        $schema->dropTable('model');
        $schema->dropTable('provider');
    }
}

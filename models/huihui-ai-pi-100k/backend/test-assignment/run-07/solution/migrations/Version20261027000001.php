<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Изначальная схема: справочник цен (providers/models/prices), журнал
 * событий использования (usage_events) и журнал ретро-коррекций (reprice_log).
 *
 * Все деньги хранятся десятичными строками (VARCHAR), все времена —
 * timestamptz (UTC). Числа токенов — bigint.
 */
final class Version20261027000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('providers')) {
            $providers = $schema->createTable('providers');
            $providers->addColumn('code', Types::STRING, ['length' => 64]);
            $providers->addColumn('name', Types::STRING, ['length' => 255]);
            $providers->addColumn('off_peak_window_start', Types::STRING, ['length' => 5, 'notnull' => false]);
            $providers->addColumn('off_peak_window_end', Types::STRING, ['length' => 5, 'notnull' => false]);
            $providers->addColumn('off_peak_multiplier', Types::STRING, ['length' => 30, 'notnull' => false]);
            $providers->setPrimaryKey(['code']);
        }

        if (!$schema->hasTable('models')) {
            $models = $schema->createTable('models');
            $models->addColumn('provider_code', Types::STRING, ['length' => 64]);
            $models->addColumn('code', Types::STRING, ['length' => 64]);
            $models->setPrimaryKey(['provider_code', 'code']);
        }

        if (!$schema->hasTable('prices')) {
            $prices = $schema->createTable('prices');
            $prices->addColumn('provider_code', Types::STRING, ['length' => 64]);
            $prices->addColumn('model_code', Types::STRING, ['length' => 64]);
            $prices->addColumn('valid_from', Types::DATETIMETZ_IMMUTABLE);
            $prices->addColumn('currency', Types::STRING, ['length' => 3, 'default' => 'USD']);
            $prices->addColumn('unit', Types::STRING, ['length' => 32]);
            $prices->addColumn('rates', Types::JSONB);
            $prices->setPrimaryKey(['provider_code', 'model_code', 'valid_from']);
        }

        if (!$schema->hasTable('usage_events')) {
            $events = $schema->createTable('usage_events');
            $events->addColumn('id', Types::BIGINT, ['autoincrement' => true]);
            $events->addColumn('provider_code', Types::STRING, ['length' => 64]);
            $events->addColumn('provider_request_id', Types::STRING, ['length' => 255]);
            $events->addColumn('model_code', Types::STRING, ['length' => 64]);
            $events->addColumn('user_id', Types::STRING, ['length' => 255]);
            $events->addColumn('ts', Types::DATETIMETZ_IMMUTABLE);
            $events->addColumn('tokens_input', Types::BIGINT, ['default' => 0]);
            $events->addColumn('tokens_output', Types::BIGINT, ['default' => 0]);
            $events->addColumn('tokens_cache_read', Types::BIGINT, ['default' => 0]);
            $events->addColumn('tokens_cache_write', Types::BIGINT, ['default' => 0]);
            $events->addColumn('status', Types::STRING, ['length' => 16, 'default' => 'pending']);
            $events->addColumn('cost_usd', Types::STRING, ['length' => 30, 'notnull' => false]);
            $events->addColumn('price_valid_from', Types::DATETIMETZ_IMMUTABLE, ['notnull' => false]);
            $events->setPrimaryKey(['id']);
            $events->addUniqueIndex(
                ['provider_code', 'provider_request_id'],
                'uq_usage_events_provider_request'
            );
            $events->addIndex(['status'], 'idx_usage_events_status');
            $events->addIndex(['ts'], 'idx_usage_events_ts');
            $events->addIndex(
                ['provider_code', 'model_code', 'price_valid_from'],
                'idx_usage_events_reprice'
            );
        }

        if (!$schema->hasTable('reprice_log')) {
            $log = $schema->createTable('reprice_log');
            $log->addColumn('id', Types::BIGINT, ['autoincrement' => true]);
            $log->addColumn('provider_code', Types::STRING, ['length' => 64]);
            $log->addColumn('provider_request_id', Types::STRING, ['length' => 255]);
            $log->addColumn('old_cost_usd', Types::STRING, ['length' => 30]);
            $log->addColumn('new_cost_usd', Types::STRING, ['length' => 30]);
            $log->addColumn('delta_usd', Types::STRING, ['length' => 30]);
            $log->addColumn('applied_at', Types::DATETIMETZ_IMMUTABLE);
            $log->setPrimaryKey(['id']);
        }
    }

    public function down(Schema $schema): void
    {
        foreach (['reprice_log', 'usage_events', 'prices', 'models', 'providers'] as $name) {
            if ($schema->hasTable($name)) {
                $schema->dropTable($name);
            }
        }
    }
}

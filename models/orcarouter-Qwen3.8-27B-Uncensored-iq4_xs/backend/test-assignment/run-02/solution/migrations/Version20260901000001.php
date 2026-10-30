<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Types\Types;

/**
 * Стартовая схема: справочник цен (provider, model, price_line)
 * и журнал использования (usage_event).
 */
final class Version20260901000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $provider = $schema->createTable('provider');
        $provider->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $provider->addColumn('code', Types::STRING)->setLength(64);
        $provider->addColumn('name', Types::STRING)->setLength(255);
        $provider->addColumn('off_peak_window_start', Types::INTEGER)->setNotnull(false);
        $provider->addColumn('off_peak_window_end', Types::INTEGER)->setNotnull(false);
        $provider->addColumn('off_peak_multiplier', Types::STRING)->setLength(32)->setNotnull(false);
        $provider->addUniqueIndex(['code'], 'uniq_provider_code');
        $provider->setPrimaryKey(['id']);

        $model = $schema->createTable('model');
        $model->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $model->addColumn('provider_id', Types::INTEGER);
        $model->addColumn('code', Types::STRING)->setLength(64);
        $model->addUniqueIndex(['provider_id', 'code'], 'uniq_model_provider_code');
        $model->addForeignKeyConstraint('provider', ['provider_id'], ['id']);
        $model->setPrimaryKey(['id']);

        $priceLine = $schema->createTable('price_line');
        $priceLine->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $priceLine->addColumn('provider_id', Types::INTEGER);
        $priceLine->addColumn('model_id', Types::INTEGER);
        $priceLine->addColumn('valid_from', Types::DATETIME_IMMUTABLE);
        $priceLine->addColumn('currency', Types::STRING)->setLength(8);
        $priceLine->addColumn('unit', Types::STRING)->setLength(32);
        $priceLine->addColumn('rates_json', Types::TEXT);
        $priceLine->addUniqueIndex(['provider_id', 'model_id', 'valid_from'], 'uniq_price_line');
        $priceLine->addForeignKeyConstraint('provider', ['provider_id'], ['id']);
        $priceLine->addForeignKeyConstraint('model', ['model_id'], ['id']);
        $priceLine->setPrimaryKey(['id']);

        $usageEvent = $schema->createTable('usage_event');
        $usageEvent->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $usageEvent->addColumn('provider', Types::STRING)->setLength(64);
        $usageEvent->addColumn('provider_request_id', Types::STRING)->setLength(128);
        $usageEvent->addColumn('model', Types::STRING)->setLength(64);
        $usageEvent->addColumn('user_code', Types::STRING)->setLength(128);
        $usageEvent->addColumn('ts', Types::DATETIME_IMMUTABLE);
        $usageEvent->addColumn('tokens_input', Types::INTEGER);
        $usageEvent->addColumn('tokens_output', Types::INTEGER);
        $usageEvent->addColumn('tokens_cache_read', Types::INTEGER);
        $usageEvent->addColumn('tokens_cache_write', Types::INTEGER);
        $usageEvent->addColumn('status', Types::STRING)->setLength(16);
        $usageEvent->addColumn('cost', Types::STRING)->setLength(32)->setNotnull(false);
        $usageEvent->addColumn('price_line_id', Types::INTEGER)->setNotnull(false);
        $usageEvent->addUniqueIndex(['provider', 'provider_request_id'], 'uniq_usage_event_key');
        $usageEvent->addIndex(['status'], 'idx_usage_event_status');
        $usageEvent->addIndex(['ts'], 'idx_usage_event_ts');
        $usageEvent->addForeignKeyConstraint('price_line', ['price_line_id'], ['id']);
        $usageEvent->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        foreach (['usage_event', 'price_line', 'model', 'provider'] as $table) {
            if ($schema->hasTable($table)) {
                $schema->dropTable($table);
            }
        }
    }
}

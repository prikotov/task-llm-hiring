<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Начальная схема учёта расходов на LLM-токены.
 *
 * - providers — справочник провайдеров (code уникален), оф-пик — на уровне провайдера;
 * - models — справочник моделей (уникальны в разрезе провайдера);
 * - price_rows — строки цен, действующие с valid_from (уникальны в разрезе (модель, valid_from));
 * - usage_events — журнал событий (уникальны по (provider, provider_request_id));
 *   стоимость — строкой с 6 знаками (float для денег запрещён).
 */
final class Version20261027120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial billing schema: providers, models, price rows, usage events';
    }

    public function up(Schema $schema): void
    {
        $providers = $schema->createTable('providers');
        $providers->addColumn('id', 'integer');
        $providers->setPrimaryKey(['id']);
        $providers->getColumn('id')->setAutoincrement(true);
        $providers->addColumn('code', 'string', ['length' => 64]);
        $providers->addColumn('name', 'string', ['length' => 255]);
        $providers->addColumn('off_peak_start', 'string', ['length' => 5, 'notnull' => false]);
        $providers->addColumn('off_peak_end', 'string', ['length' => 5, 'notnull' => false]);
        $providers->addColumn('off_peak_multiplier', 'string', ['length' => 32, 'notnull' => false]);
        $providers->addUniqueIndex(['code'], 'uniq_providers_code');

        $models = $schema->createTable('models');
        $models->addColumn('id', 'integer');
        $models->setPrimaryKey(['id']);
        $models->getColumn('id')->setAutoincrement(true);
        $models->addColumn('provider', 'integer');
        $models->addColumn('code', 'string', ['length' => 128]);
        $models->addForeignKeyConstraint('providers', ['provider'], ['id'], ['onDelete' => 'RESTRICT']);
        $models->addUniqueIndex(['provider', 'code'], 'uniq_models_provider_code');

        $priceRows = $schema->createTable('price_rows');
        $priceRows->addColumn('id', 'integer');
        $priceRows->setPrimaryKey(['id']);
        $priceRows->getColumn('id')->setAutoincrement(true);
        $priceRows->addColumn('model', 'integer');
        $priceRows->addColumn('valid_from', 'datetime');
        $priceRows->addColumn('currency', 'string', ['length' => 8]);
        $priceRows->addColumn('unit', 'string', ['length' => 32]);
        $priceRows->addColumn('rates', 'json');
        $priceRows->addForeignKeyConstraint('models', ['model'], ['id'], ['onDelete' => 'RESTRICT']);
        $priceRows->addUniqueIndex(['model', 'valid_from'], 'uniq_price_rows_model_valid_from');

        $events = $schema->createTable('usage_events');
        $events->addColumn('id', 'integer');
        $events->setPrimaryKey(['id']);
        $events->getColumn('id')->setAutoincrement(true);
        $events->addColumn('provider', 'string', ['length' => 64]);
        $events->addColumn('provider_request_id', 'string', ['length' => 190]);
        $events->addColumn('model', 'string', ['length' => 128]);
        $events->addColumn('user_ref', 'string', ['length' => 190]);
        $events->addColumn('ts', 'datetime');
        $events->addColumn('tokens_input', 'bigint');
        $events->addColumn('tokens_output', 'bigint');
        $events->addColumn('tokens_cache_read', 'bigint');
        $events->addColumn('tokens_cache_write', 'bigint');
        $events->addColumn('status', 'string', ['length' => 16]);
        $events->addColumn('price_row_id', 'integer', ['notnull' => false]);
        $events->addColumn('cost_usd', 'string', ['length' => 40, 'notnull' => false]);
        $events->addColumn('rated_at', 'datetime', ['notnull' => false]);
        $events->addUniqueIndex(['provider', 'provider_request_id'], 'uniq_usage_events_key');
        $events->addIndex(['status', 'ts'], 'idx_usage_events_status_ts');
        // Референциальная целостность держится на уровне приложения
        // (строки цены не удаляются); в маппинге это обычный индексированный ключ.
        $events->addIndex(['price_row_id'], 'idx_usage_events_price_row_id');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('usage_events');
        $schema->dropTable('price_rows');
        $schema->dropTable('models');
        $schema->dropTable('providers');
    }
}

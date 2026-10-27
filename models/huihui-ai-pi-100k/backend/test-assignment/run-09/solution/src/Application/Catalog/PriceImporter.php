<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Идемпотентный upsert справочника: провайдеры по code,
 * модели по (provider, code), строки цен по (provider, model, valid_from).
 */
final class PriceImporter
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogWriter $writer,
    ) {
    }

    public function import(string $file): void
    {
        $data = self::readFile($file);
        $providers = CatalogParser::parseProviders($data['providers'] ?? []);
        $models = CatalogParser::parseModels($data['models'] ?? []);
        $prices = CatalogParser::parsePrices($data['prices'] ?? []);

        \App\Infrastructure\Transactor::run($this->em, function () use ($providers, $models, $prices) {
            $providerEntities = [];
            foreach ($providers as $p) {
                $providerEntities[$p['code']] = $this->writer->upsertProvider($p['code'], $p['name'], $p['offPeak']);
            }
            // материализуем id сгенерированных провайдеров до использования в FK
            $this->em->flush();
            $providerIds = [];
            foreach ($providerEntities as $code => $entity) {
                $providerIds[$code] = $entity->getId();
            }

            foreach ($models as $m) {
                $providerId = $providerIds[$m['provider']] ?? $this->writer->providerId($m['provider']);
                if ($providerId === null) {
                    throw new ImportException(sprintf('models: провайдер "%s" не найден в справочнике', $m['provider']));
                }
                $this->writer->upsertModel($providerId, $m['code']);
            }
            // модели пока в "pending"-состоянии; строки цен ищем по natural key
            $this->em->flush();

            foreach ($prices as $prow) {
                $providerId = $providerIds[$prow['provider']] ?? $this->writer->providerId($prow['provider']);
                if ($providerId === null) {
                    throw new ImportException(sprintf('prices: провайдер "%s" не найден в справочнике', $prow['provider']));
                }
                $modelId = $this->writer->modelId($providerId, $prow['model']);
                if ($modelId === null) {
                    throw new ImportException(sprintf('prices: модель "%s/%s" не найдена в справочнике', $prow['provider'], $prow['model']));
                }
                $this->writer->upsertPriceRow(
                    $providerId,
                    $modelId,
                    $prow['validFrom'],
                    $prow['currency'],
                    $prow['rates'],
                );
            }
        });
    }

    private static function readFile(string $file): array
    {
        if (!is_file($file)) {
            throw new ImportException("Файл не найден: $file");
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new ImportException("Не удалось прочитать файл: $file");
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new ImportException('Некорректный JSON: верхний уровень должен быть объектом; ' . json_last_error_msg());
        }

        return $data;
    }
}

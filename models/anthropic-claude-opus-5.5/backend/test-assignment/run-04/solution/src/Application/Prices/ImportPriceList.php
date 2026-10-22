<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PricingUnit;
use App\Domain\Pricing\Provider;
use App\Infrastructure\Doctrine\Entity\ModelEntity;
use App\Infrastructure\Doctrine\Entity\ProviderEntity;
use App\Infrastructure\Doctrine\Repository\ModelRepository;
use App\Infrastructure\Doctrine\Repository\PriceRowRepository;
use App\Infrastructure\Doctrine\Repository\ProviderRepository;
use App\Infrastructure\Json\PriceListData;
use App\Infrastructure\Json\PriceListReader;
use App\Infrastructure\Json\UsageEventReader;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

/**
 * Импорт справочника: провайдеры (по code), модели (по provider+code),
 * строки цен (по provider+model+valid_from). Один проход — одна транзакция.
 */
final class ImportPriceList
{
    public function __construct(
        private readonly PriceListReader $reader,
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $priceRows,
    ) {
    }

    /**
     * @return array{providers: int, models: int, prices: int}
     */
    public function __invoke(string $path): array
    {
        $data = $this->reader->read($path);

        $this->em->beginTransaction();
        try {
            $providerEntities = $this->importProviders($data);
            $modelEntities = $this->importModels($data, $providerEntities);
            $priceCount = $this->importPrices($data, $providerEntities, $modelEntities);

            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }

        return [
            'providers' => count($data->providers),
            'models' => count($data->models),
            'prices' => $priceCount,
        ];
    }

    /**
     * @return array<string, ProviderEntity>
     */
    private function importProviders(PriceListData $data): array
    {
        $result = [];
        foreach ($data->providers as $row) {
            $offPeak = null === $row['off_peak']
                ? null
                : OffPeakWindow::fromStrings($row['off_peak']['window_start'], $row['off_peak']['window_end'], $row['off_peak']['multiplier']);

            $provider = new Provider($row['code'], $row['name'], $offPeak);
            $result[$provider->code] = $this->providers->upsert($provider);
        }

        return $result;
    }

    /**
     * @param array<string, ProviderEntity> $providers
     *
     * @return array<string, ModelEntity> ключ — "<provider>/<code>"
     */
    private function importModels(PriceListData $data, array $providers): array
    {
        $result = [];
        foreach ($data->models as $row) {
            $provider = $this->requireProvider($providers, $row['provider'], sprintf('модель "%s"', $row['code']));
            $result[$row['provider'] . '/' . $row['code']] = $this->models->upsert($provider, $row['code']);
        }

        return $result;
    }

    /**
     * @param array<string, ProviderEntity> $providers
     * @param array<string, ModelEntity>    $models
     */
    private function importPrices(PriceListData $data, array $providers, array $models): int
    {
        $count = 0;
        foreach ($data->prices as $row) {
            $provider = $this->requireProvider($providers, $row['provider'], 'строку цен');
            $qualified = $row['provider'] . '/' . $row['model'];
            $model = $models[$qualified] ?? $this->models->upsert($provider, $row['model']);
            $models[$qualified] = $model;

            $priceRow = new PriceRow(
                $provider->toDomain(),
                $model->toDomain(),
                UsageEventReader::parseTimestamp($row['valid_from']),
                PricingUnit::fromString($row['unit']),
                $row['rates'],
                $row['currency'],
            );

            $this->priceRows->upsert($model, $priceRow);
            ++$count;
        }

        return $count;
    }

    /**
     * @param array<string, ProviderEntity> $providers
     */
    private function requireProvider(array $providers, string $code, string $context): ProviderEntity
    {
        return $providers[$code]
            ?? throw new RuntimeException(sprintf('Провайдер "%s" не объявлен в файле (нужен для %s).', $code, $context));
    }
}

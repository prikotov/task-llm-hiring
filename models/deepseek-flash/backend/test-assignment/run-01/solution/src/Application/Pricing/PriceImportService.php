<?php

declare(strict_types=1);

namespace App\Application\Pricing;

use App\Application\Import\PriceFileData;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\RateCard;
use App\Infrastructure\Import\PriceFileParser;
use App\Infrastructure\Persistence\Doctrine\Entity\Price;
use App\Infrastructure\Persistence\Doctrine\Entity\Provider;
use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
use App\Infrastructure\Persistence\Doctrine\Repository\PriceRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderModelRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

use function sprintf;

/**
 * Idempotent upsert of the price catalogue.
 */
final readonly class PriceImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProviderRepository $providers,
        private ProviderModelRepository $models,
        private PriceRepository $prices,
        private PriceFileParser $parser,
    ) {
    }

    public function importFile(string $path): void
    {
        $data = $this->parser->parse($path);
        $this->import($data);
    }

    public function import(PriceFileData $data): void
    {
        $this->entityManager->wrapInTransaction(function () use ($data): void {
            $providers = $this->upsertProviders($data);
            $models = $this->upsertModels($data, $providers);
            $this->upsertPrices($data, $providers, $models);
        });
    }

    /**
     * @return array<string, Provider>
     */
    private function upsertProviders(PriceFileData $data): array
    {
        $providers = [];

        foreach ($data->providers as $definition) {
            $provider = $providers[$definition->code] ?? $this->providers->findByCode($definition->code);
            if ($provider === null) {
                $provider = new Provider($definition->code, $definition->name);
                $this->entityManager->persist($provider);
            }

            $provider->updateDetails($definition->name, $definition->offPeak);
            $providers[$definition->code] = $provider;
        }

        $this->entityManager->flush();

        return $providers;
    }

    /**
     * @param array<string, Provider> $providers
     * @return array<string, ProviderModel>
     */
    private function upsertModels(PriceFileData $data, array $providers): array
    {
        $models = [];

        foreach ($data->models as $definition) {
            $provider = $providers[$definition->providerCode]
                ?? $this->providers->findByCode($definition->providerCode);

            if ($provider === null) {
                throw new InvalidArgumentException(sprintf(
                    'Cannot import model "%s/%s": provider is not defined.',
                    $definition->providerCode,
                    $definition->code,
                ));
            }

            $key = $definition->providerCode . "\0" . $definition->code;
            $model = $models[$key] ?? $this->models->findByProviderAndCode($provider, $definition->code);
            if ($model === null) {
                $model = new ProviderModel($provider, $definition->code);
                $this->entityManager->persist($model);
            }

            $models[$key] = $model;
        }

        $this->entityManager->flush();

        return $models;
    }

    /**
     * @param array<string, Provider> $providers
     * @param array<string, ProviderModel> $models
     */
    private function upsertPrices(PriceFileData $data, array $providers, array $models): void
    {
        /** @var array<string, Price> $priceCache */
        $priceCache = [];

        foreach ($data->prices as $definition) {
            $provider = $providers[$definition->providerCode]
                ?? $this->providers->findByCode($definition->providerCode);
            if ($provider === null) {
                throw new InvalidArgumentException(sprintf(
                    'Cannot import price: provider "%s" is not defined.',
                    $definition->providerCode,
                ));
            }

            $modelKey = $definition->providerCode . "\0" . $definition->modelCode;
            $model = $models[$modelKey]
                ?? $this->models->findByProviderAndCode($provider, $definition->modelCode);
            if ($model === null) {
                throw new InvalidArgumentException(sprintf(
                    'Cannot import price: model "%s/%s" is not defined.',
                    $definition->providerCode,
                    $definition->modelCode,
                ));
            }

            [$input, $output, $cacheRead, $cacheWrite, $any] = self::rateStrings($definition->rateCard);
            $priceKey = $definition->providerCode . "\0" . $definition->modelCode
                . '|' . $definition->validFrom->format('U.u');
            $price = $priceCache[$priceKey] ?? $this->prices->findForModelAt($model, $definition->validFrom);

            if ($price === null) {
                $price = new Price(
                    $model,
                    $definition->validFrom,
                    $definition->currency,
                    $definition->rateCard->unit,
                    $input,
                    $output,
                    $cacheRead,
                    $cacheWrite,
                    $any,
                );
                $this->entityManager->persist($price);
            }

            $priceCache[$priceKey] = $price;
            $price->update(
                $definition->currency,
                $definition->rateCard->unit,
                $input,
                $output,
                $cacheRead,
                $cacheWrite,
                $any,
            );
        }

        $this->entityManager->flush();
    }

    /**
     * @return array{0: ?string, 1: ?string, 2: ?string, 3: ?string, 4: ?string}
     */
    private static function rateStrings(RateCard $card): array
    {
        return match ($card->unit) {
            PriceUnit::PerMillionTokens => [
                $card->input?->format(18),
                $card->output?->format(18),
                $card->cacheRead?->format(18),
                $card->cacheWrite?->format(18),
                null,
            ],
            PriceUnit::PerMillionTokensUnified => [
                null,
                null,
                null,
                null,
                $card->any?->format(18),
            ],
        };
    }
}

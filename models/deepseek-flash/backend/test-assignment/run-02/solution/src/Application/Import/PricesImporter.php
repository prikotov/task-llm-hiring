<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\Money\Money;
use App\Domain\Pricing\Entity\Price;
use App\Domain\Pricing\Entity\ProviderModel;
use App\Domain\Pricing\ValueObject\OffPeakWindow;
use App\Domain\Pricing\ValueObject\PricingUnit;
use App\Domain\Provider\Entity\Provider;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Throwable;

final readonly class PricesImporter
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function import(string $path): void
    {
        $data = $this->parseFile($path);

        $this->em->wrapInTransaction(function () use ($data): void {
            $this->importProviders($data['providers']);
            $this->importModels($data['models']);
            $this->importPrices($data['prices']);
        });
    }

    /**
     * Применяет только исправленные строки цен. Провайдеры и модели
     * берутся из уже загруженного справочника; секции providers/models
     * файла коррекции игнорируются.
     *
     * @return list<Price> строки, содержимое которых реально изменилось
     */
    public function importCorrections(string $path): array
    {
        $data = $this->parseFile($path);

        return $this->em->wrapInTransaction(function () use ($data): array {
            return $this->importPrices($data['prices']);
        });
    }

    /**
     * @return array{providers: list<array<string, mixed>>, models: list<array<string, mixed>>, prices: list<array<string, mixed>>}
     */
    private function parseFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('File "%s" does not exist or is not readable.', $path));
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new ImportException(sprintf('Unable to read file "%s".', $path));
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ImportException('Invalid JSON: '.$exception->getMessage(), 0, $exception);
        }

        if (!is_array($data)) {
            throw new ImportException('Price history must be a JSON object.');
        }

        $providers = $this->listField($data, 'providers');
        $models = $this->listField($data, 'models');
        $prices = $this->listField($data, 'prices');

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<array<string, mixed>>
     */
    private function listField(array $data, string $field): array
    {
        $value = $data[$field] ?? [];
        if (!is_array($value) || !array_is_list($value)) {
            throw new ImportException(sprintf('Field "%s" must be an array.', $field));
        }

        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                throw new ImportException(sprintf('Element %d of "%s" must be an object.', $index, $field));
            }
        }

        /** @var list<array<string, mixed>> $value */
        return $value;
    }

    /** @param list<array<string, mixed>> $providers */
    private function importProviders(array $providers): void
    {
        $repository = $this->em->getRepository(Provider::class);

        foreach ($providers as $index => $row) {
            $code = $this->requiredString($row, 'code', sprintf('providers[%d]', $index));
            $name = $this->requiredString($row, 'name', sprintf('providers[%d]', $index));

            $provider = $repository->findOneBy(['code' => $code]);
            if ($provider === null) {
                $provider = new Provider($code, $name);
                $this->em->persist($provider);
            } else {
                $provider->rename($name);
            }

            $provider->changeOffPeak($this->parseOffPeak($row['off_peak'] ?? null, sprintf('providers[%d]', $index)));
        }

        $this->em->flush();
    }

    /** @param list<array<string, mixed>> $models */
    private function importModels(array $models): void
    {
        $providerRepository = $this->em->getRepository(Provider::class);
        $modelRepository = $this->em->getRepository(ProviderModel::class);

        foreach ($models as $index => $row) {
            $context = sprintf('models[%d]', $index);
            $providerCode = $this->requiredString($row, 'provider', $context);
            $code = $this->requiredString($row, 'code', $context);

            $provider = $providerRepository->findOneBy(['code' => $providerCode]);
            if ($provider === null) {
                throw new ImportException(sprintf('%s references unknown provider "%s".', $context, $providerCode));
            }

            $model = $modelRepository->findOneBy(['provider' => $provider, 'code' => $code]);
            if ($model === null) {
                $this->em->persist(new ProviderModel($provider, $code));
            }
        }

        $this->em->flush();
    }

    /**
     * @param list<array<string, mixed>> $prices
     *
     * @return list<Price> добавленные или изменённые строки цен
     */
    private function importPrices(array $prices): array
    {
        $providerRepository = $this->em->getRepository(Provider::class);
        $modelRepository = $this->em->getRepository(ProviderModel::class);
        $priceRepository = $this->em->getRepository(Price::class);
        $changed = [];

        foreach ($prices as $index => $row) {
            $context = sprintf('prices[%d]', $index);
            $providerCode = $this->requiredString($row, 'provider', $context);
            $modelCode = $this->requiredString($row, 'model', $context);
            $validFrom = $this->parseDate($row['valid_from'] ?? null, $context.'.valid_from');
            $currency = $this->requiredString($row, 'currency', $context);
            $unitValue = $this->requiredString($row, 'unit', $context);
            $unit = PricingUnit::tryFrom($unitValue);

            if ($unit === null) {
                throw new ImportException(sprintf('%s has unsupported unit "%s".', $context, $unitValue));
            }

            $rates = $this->parseRates($row['rates'] ?? null, $unit, $context);

            $provider = $providerRepository->findOneBy(['code' => $providerCode]);
            if ($provider === null) {
                throw new ImportException(sprintf('%s references unknown provider "%s".', $context, $providerCode));
            }

            $model = $modelRepository->findOneBy(['provider' => $provider, 'code' => $modelCode]);
            if ($model === null) {
                throw new ImportException(sprintf('%s references unknown model "%s/%s".', $context, $providerCode, $modelCode));
            }

            $price = $priceRepository->findOneBy([
                'providerModel' => $model,
                'validFrom' => $validFrom,
            ]);

            if ($price === null) {
                $price = new Price($model, $validFrom, $currency, $unit, $rates);
                $this->em->persist($price);
                $changed[] = $price;
            } elseif ($price->replaceContents($currency, $unit, $rates)) {
                $changed[] = $price;
            }
        }

        $this->em->flush();

        return $changed;
    }

    private function parseOffPeak(mixed $value, string $context): ?OffPeakWindow
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new ImportException(sprintf('%s.off_peak must be an object or null.', $context));
        }

        $start = $this->requiredString($value, 'window_start', $context.'.off_peak');
        $end = $this->requiredString($value, 'window_end', $context.'.off_peak');
        $multiplier = $this->requiredString($value, 'multiplier', $context.'.off_peak');

        if (!Money::isDecimal($multiplier) || str_starts_with($multiplier, '-')) {
            throw new ImportException(sprintf('%s.off_peak.multiplier must be a non-negative decimal string.', $context));
        }

        try {
            return new OffPeakWindow($start, $end, $multiplier);
        } catch (\InvalidArgumentException $exception) {
            throw new ImportException(sprintf('%s.off_peak: %s', $context, $exception->getMessage()), 0, $exception);
        }
    }

    private function parseDate(mixed $value, string $field): DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s must be an ISO-8601 string.', $field));
        }

        try {
            return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'));
        } catch (Throwable $exception) {
            throw new ImportException(sprintf('%s has invalid date "%s".', $field, $value), 0, $exception);
        }
    }

    /**
     * @return array<string, string>
     */
    private function parseRates(mixed $value, PricingUnit $unit, string $context): array
    {
        if (!is_array($value)) {
            throw new ImportException(sprintf('%s.rates must be an object.', $context));
        }

        $required = $unit === PricingUnit::Per1mTokens
            ? ['input', 'output', 'cache_read', 'cache_write']
            : ['any'];

        $rates = [];
        foreach ($required as $kind) {
            if (!array_key_exists($kind, $value)) {
                throw new ImportException(sprintf('%s.rates.%s is required.', $context, $kind));
            }

            $rate = $value[$kind];
            if (!is_string($rate) || !Money::isDecimal($rate) || str_starts_with($rate, '-')) {
                throw new ImportException(sprintf('%s.rates.%s must be a non-negative decimal string.', $context, $kind));
            }

            $rates[$kind] = $rate;
        }

        return $rates;
    }

    /** @param array<string, mixed> $row */
    private function requiredString(array $row, string $field, string $context): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s.%s must be a non-empty string.', $context, $field));
        }

        return $value;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Application\Import\ModelDefinition;
use App\Application\Import\PriceDefinition;
use App\Application\Import\PriceFileData;
use App\Application\Import\ProviderDefinition;
use App\Domain\Money\Decimal;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\RateCard;
use InvalidArgumentException;
use JsonException;

use function array_key_exists;
use function array_is_list;
use function file_get_contents;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;

final class PriceFileParser
{
    private const int JSON_DEPTH = 512;

    public function parse(string $path): PriceFileData
    {
        $contents = @file_get_contents($path);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Cannot read price file "%s".', $path));
        }

        try {
            $document = json_decode($contents, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException(sprintf(
                'Invalid JSON in "%s": %s',
                $path,
                $exception->getMessage(),
            ), 0, $exception);
        }

        if (!is_array($document) || ($document !== [] && array_is_list($document))) {
            throw new InvalidArgumentException(sprintf('Price file "%s" must contain a JSON object.', $path));
        }

        $providers = $this->parseProviders($this->section($document, 'providers', $path));
        $models = $this->parseModels($this->section($document, 'models', $path));
        $prices = $this->parsePrices($this->section($document, 'prices', $path));

        return new PriceFileData($providers, $models, $prices);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function section(array $document, string $name, string $path): array
    {
        if (!array_key_exists($name, $document)) {
            return [];
        }

        $section = $document[$name];
        if (!is_array($section) || !array_is_list($section)) {
            throw new InvalidArgumentException(sprintf('Section "%s" in "%s" must be a JSON array.', $name, $path));
        }

        foreach ($section as $index => $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidArgumentException(sprintf(
                    'Item %d in section "%s" must be a JSON object.',
                    $index,
                    $name,
                ));
            }
        }

        /** @var list<array<string, mixed>> $section */
        return $section;
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<ProviderDefinition>
     */
    private function parseProviders(array $items): array
    {
        $providers = [];

        foreach ($items as $item) {
            $code = $this->requiredString($item, 'code', 'provider');
            $name = $this->requiredString($item, 'name', sprintf('provider "%s"', $code));
            $offPeak = $this->parseOffPeak($item['off_peak'] ?? null, $code);

            $providers[] = new ProviderDefinition($code, $name, $offPeak);
        }

        return $providers;
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<ModelDefinition>
     */
    private function parseModels(array $items): array
    {
        $models = [];

        foreach ($items as $item) {
            $models[] = new ModelDefinition(
                $this->requiredString($item, 'provider', 'model'),
                $this->requiredString($item, 'code', 'model'),
            );
        }

        return $models;
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<PriceDefinition>
     */
    private function parsePrices(array $items): array
    {
        $prices = [];

        foreach ($items as $item) {
            $providerCode = $this->requiredString($item, 'provider', 'price');
            $modelCode = $this->requiredString($item, 'model', sprintf('price of provider "%s"', $providerCode));
            $validFrom = Iso8601::parse(
                $this->requiredString($item, 'valid_from', sprintf('price %s/%s', $providerCode, $modelCode)),
                'valid_from',
            );
            $currency = $this->requiredString($item, 'currency', sprintf('price %s/%s', $providerCode, $modelCode));
            if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
                throw new InvalidArgumentException(sprintf(
                    'Price %s/%s has invalid currency "%s".',
                    $providerCode,
                    $modelCode,
                    $currency,
                ));
            }

            $unitValue = $this->requiredString($item, 'unit', sprintf('price %s/%s', $providerCode, $modelCode));
            $unit = PriceUnit::tryFrom($unitValue);
            if ($unit === null) {
                throw new InvalidArgumentException(sprintf(
                    'Price %s/%s has unknown unit "%s".',
                    $providerCode,
                    $modelCode,
                    $unitValue,
                ));
            }

            if (!isset($item['rates']) || !is_array($item['rates']) || array_is_list($item['rates'])) {
                throw new InvalidArgumentException(sprintf(
                    'Price %s/%s must contain a "rates" object.',
                    $providerCode,
                    $modelCode,
                ));
            }

            $rates = $item['rates'];
            $rateCard = match ($unit) {
                PriceUnit::PerMillionTokens => RateCard::perToken(
                    $this->rate($rates, 'input', $providerCode, $modelCode),
                    $this->rate($rates, 'output', $providerCode, $modelCode),
                    $this->rate($rates, 'cache_read', $providerCode, $modelCode),
                    $this->rate($rates, 'cache_write', $providerCode, $modelCode),
                ),
                PriceUnit::PerMillionTokensUnified => RateCard::unified(
                    $this->rate($rates, 'any', $providerCode, $modelCode),
                ),
            };

            $prices[] = new PriceDefinition($providerCode, $modelCode, $validFrom, $currency, $rateCard);
        }

        return $prices;
    }

    private function parseOffPeak(mixed $value, string $providerCode): ?OffPeakWindow
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value) || array_is_list($value)) {
            throw new InvalidArgumentException(sprintf(
                'Off-peak settings of provider "%s" must be an object or null.',
                $providerCode,
            ));
        }

        return OffPeakWindow::fromStrings(
            $this->requiredString($value, 'window_start', sprintf('off-peak of provider "%s"', $providerCode)),
            $this->requiredString($value, 'window_end', sprintf('off-peak of provider "%s"', $providerCode)),
            $this->requiredString($value, 'multiplier', sprintf('off-peak of provider "%s"', $providerCode)),
        );
    }

    /**
     * @param array<string, mixed> $rates
     */
    private function rate(array $rates, string $name, string $providerCode, string $modelCode): Decimal
    {
        if (!array_key_exists($name, $rates) || !is_string($rates[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Price %s/%s must contain decimal string rate "%s".',
                $providerCode,
                $modelCode,
                $name,
            ));
        }

        $rate = Decimal::fromString($rates[$name]);
        if ($rate->isNegative()) {
            throw new InvalidArgumentException(sprintf(
                'Rate "%s" of price %s/%s must be non-negative.',
                $name,
                $providerCode,
                $modelCode,
            ));
        }

        return $rate;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function requiredString(array $item, string $key, string $context): string
    {
        if (!array_key_exists($key, $item) || !is_string($item[$key]) || $item[$key] === '') {
            throw new InvalidArgumentException(sprintf(
                'Field "%s" is required as a non-empty string in %s.',
                $key,
                $context,
            ));
        }

        return $item[$key];
    }
}

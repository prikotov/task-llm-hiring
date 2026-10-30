<?php

declare(strict_types=1);

namespace App\Billing\Import;

use App\Billing\Enum\PricingUnit;
use App\Billing\Import\Dto\ModelInput;
use App\Billing\Import\Dto\PriceRowInput;
use App\Billing\Import\Dto\PricesFile;
use App\Billing\Import\Dto\ProviderInput;
use App\Billing\ValueObject\OffPeak;
use App\Billing\ValueObject\Price;
use stdClass;

/**
 * Разбор и валидация JSON-файла истории цен → DTO.
 *
 * Формат: { "providers": [...], "models": [...], "prices": [...] } (секции могут отсутствовать).
 *
 * @throws ImportException
 */
final class PricesFileParser
{
    public function parse(string $json): PricesFile
    {
        try {
            $root = json_decode($json, true, 64, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('корень файла: некорректный JSON: %s', $e->getMessage()));
        }
        $root = Json::array($root, 'корень файла', nonEmpty: false);

        $providers = $this->parseProviders(self::section($root, 'providers'));
        $models = $this->parseModels(self::section($root, 'models'));
        $priceRows = $this->parsePriceRows(self::section($root, 'prices'));

        return new PricesFile($providers, $models, $priceRows);
    }

    /**
     * @param mixed $root
     *
     * @throws ImportException
     */
    private static function section(array $root, string $name): array
    {
        if (!\array_key_exists($name, $root)) {
            return [];
        }

        return Json::array($root[$name], sprintf('секция "%s"', $name));
    }

    /**
     * @throws ImportException
     */
    private function parseProviders(array $rows): array
    {
        $out = [];
        $seen = [];
        foreach ($rows as $i => $row) {
            $context = sprintf('providers[%d]', $i);
            $row = Json::array($row, $context, nonEmpty: true);

            $code = Json::stringField($row, 'code', $context);
            if (isset($seen[$code])) {
                throw new ImportException(sprintf('%s: дублирующийся код провайдера "%s".', $context, $code));
            }
            $seen[$code] = true;

            $name = Json::stringField($row, 'name', $context);

            $offPeak = null;
            if (\array_key_exists('off_peak', $row)) {
                if ($row['off_peak'] === null) {
                    // явное отсутствие скидки
                } elseif (!\is_array($row['off_peak'])) {
                    throw new ImportException(sprintf('%s: off_peak должен быть null или объектом.', $context));
                } else {
                    $op = $row['off_peak'];
                    $opContext = $context.'.off_peak';
                    $op = Json::array($op, $opContext, nonEmpty: true);
                    $offPeak = OffPeak::fromInput(
                        Json::stringField($op, 'window_start', $opContext),
                        Json::stringField($op, 'window_end', $opContext),
                        Json::decimalField($op, 'multiplier', $opContext),
                    );
                }
            }

            $out[] = new ProviderInput($code, $name, $offPeak);
        }

        return $out;
    }

    /**
     * @throws ImportException
     */
    private function parseModels(array $rows): array
    {
        $out = [];
        $seen = [];
        foreach ($rows as $i => $row) {
            $context = sprintf('models[%d]', $i);
            $row = Json::array($row, $context, nonEmpty: true);

            $provider = Json::stringField($row, 'provider', $context);
            $code = Json::stringField($row, 'code', $context);
            $key = $provider.'|'.$code;
            if (isset($seen[$key])) {
                throw new ImportException(sprintf('%s: дублирующаяся пара (provider, code) "%s / %s".', $context, $provider, $code));
            }
            $seen[$key] = true;

            $out[] = new ModelInput($provider, $code);
        }

        return $out;
    }

    /**
     * @throws ImportException
     */
    private function parsePriceRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $i => $row) {
            $context = sprintf('prices[%d]', $i);
            $row = Json::array($row, $context, nonEmpty: true);

            $provider = Json::stringField($row, 'provider', $context);
            $model = Json::stringField($row, 'model', $context);
            $validFrom = Json::utcDateTime($row['valid_from'] ?? null, $context, 'valid_from');

            $currency = 'USD';
            if (\array_key_exists('currency', $row)) {
                $currency = Json::stringField($row, 'currency', $context);
            }

            $unit = Json::stringField($row, 'unit', $context);
            $pricingUnit = PricingUnit::fromString($unit);

            $rates = Json::array($row['rates'] ?? null, $context, nonEmpty: true);
            foreach ($rates as $key => $value) {
                Json::decimalField(['v' => $value], 'v', $context.'['.($key instanceof stdClass ? '(obj)' : (string) $key).']');
            }
            $price = Price::create($pricingUnit, \array_map(static fn ($v) => (string) $v, $rates));

            $out[] = new PriceRowInput($provider, $model, $validFrom, $currency, $price);
        }

        return $out;
    }
}

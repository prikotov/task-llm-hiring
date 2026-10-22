<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Application\Exception\InputException;
use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Model\Provider;
use App\Billing\Domain\Model\RateCard;
use App\Billing\Domain\Model\TariffUnit;
use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Pricing\PriceRow;

/**
 * Разбор файла истории цен.
 *
 * Файл сначала проверяется целиком и превращается в доменные объекты, и только
 * потом записывается — поэтому «некорректный JSON — ошибка, код 1» не оставляет
 * базу в полуимпортированном состоянии.
 */
final class PriceFileParser
{
    public function parse(string $path): PriceFile
    {
        $raw = @file_get_contents($path);

        if (false === $raw) {
            throw new InputException(sprintf('Не удалось прочитать файл "%s"', $path));
        }

        try {
            $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputException(sprintf('Некорректный JSON в "%s": %s', $path, $e->getMessage()), previous: $e);
        }

        if (!is_array($data)) {
            throw new InputException(sprintf('Ожидался JSON-объект в "%s"', $path));
        }

        $providers = $this->parseProviders($data['providers'] ?? []);
        $models = $this->parseModels($data['models'] ?? []);
        $prices = $this->parsePrices($data['prices'] ?? [], $providers, $models);

        return new PriceFile($providers, $models, $prices);
    }

    /**
     * @return list<Provider>
     */
    private function parseProviders(mixed $section): array
    {
        if (!is_array($section)) {
            throw new InputException('Секция "providers" должна быть массивом');
        }

        $providers = [];

        foreach ($section as $index => $item) {
            if (!is_array($item)) {
                throw new InputException(sprintf('providers[%s]: ожидался объект', $index));
            }

            $code = $this->requireString($item, 'code', sprintf('providers[%s]', $index));
            $name = isset($item['name']) ? $this->requireString($item, 'name', sprintf('providers[%s]', $index)) : $code;

            $offPeak = null;
            $offPeakRaw = $item['off_peak'] ?? null;

            if (null !== $offPeakRaw) {
                if (!is_array($offPeakRaw)) {
                    throw new InputException(sprintf('providers[%s].off_peak: ожидался объект или null', $index));
                }

                try {
                    $offPeak = OffPeakWindow::fromStrings(
                        $this->requireString($offPeakRaw, 'window_start', sprintf('providers[%s].off_peak', $index)),
                        $this->requireString($offPeakRaw, 'window_end', sprintf('providers[%s].off_peak', $index)),
                        $this->requireString($offPeakRaw, 'multiplier', sprintf('providers[%s].off_peak', $index)),
                    );
                } catch (\InvalidArgumentException|MoneyException $e) {
                    throw new InputException(sprintf('providers[%s].off_peak: %s', $index, $e->getMessage()), previous: $e);
                }
            }

            try {
                $providers[] = new Provider($code, $name, $offPeak);
            } catch (\InvalidArgumentException $e) {
                throw new InputException(sprintf('providers[%s]: %s', $index, $e->getMessage()), previous: $e);
            }
        }

        return $providers;
    }

    /**
     * @return list<ModelReference>
     */
    private function parseModels(mixed $section): array
    {
        if (!is_array($section)) {
            throw new InputException('Секция "models" должна быть массивом');
        }

        $models = [];

        foreach ($section as $index => $item) {
            if (!is_array($item)) {
                throw new InputException(sprintf('models[%s]: ожидался объект', $index));
            }

            try {
                $models[] = new ModelReference(
                    $this->requireString($item, 'provider', sprintf('models[%s]', $index)),
                    $this->requireString($item, 'code', sprintf('models[%s]', $index)),
                );
            } catch (\InvalidArgumentException $e) {
                throw new InputException(sprintf('models[%s]: %s', $index, $e->getMessage()), previous: $e);
            }
        }

        return $models;
    }

    /**
     * @param list<Provider>       $providers
     * @param list<ModelReference> $models
     *
     * @return list<PriceRow>
     */
    private function parsePrices(mixed $section, array $providers, array $models): array
    {
        if (!is_array($section)) {
            throw new InputException('Секция "prices" должна быть массивом');
        }

        $prices = [];

        foreach ($section as $index => $item) {
            $where = sprintf('prices[%s]', $index);

            if (!is_array($item)) {
                throw new InputException($where.': ожидался объект');
            }

            $model = new ModelReference(
                $this->requireString($item, 'provider', $where),
                $this->requireString($item, 'model', $where),
            );

            $validFrom = $this->parseTimestamp($this->requireString($item, 'valid_from', $where), $where.'.valid_from');
            $currency = strtoupper($this->requireString($item, 'currency', $where));
            $unitRaw = $this->requireString($item, 'unit', $where);
            $unit = TariffUnit::tryFrom($unitRaw)
                ?? throw new InputException(sprintf('%s.unit: неизвестная схема тарификации "%s"', $where, $unitRaw));

            if (!is_array($item['rates'] ?? null)) {
                throw new InputException($where.'.rates: ожидался объект');
            }

            $rates = [];

            foreach ($item['rates'] as $key => $value) {
                if (!is_string($value)) {
                    throw new InputException(sprintf('%s.rates.%s: ставка должна быть строкой', $where, (string) $key));
                }

                try {
                    $rates[(string) $key] = Money::fromString($value);
                } catch (MoneyException $e) {
                    throw new InputException(sprintf('%s.rates.%s: %s', $where, (string) $key, $e->getMessage()), previous: $e);
                }
            }

            try {
                $prices[] = new PriceRow(null, $model, $validFrom, $currency, RateCard::of($unit, $rates));
            } catch (\InvalidArgumentException $e) {
                throw new InputException(sprintf('%s: %s', $where, $e->getMessage()), previous: $e);
            }
        }

        return $prices;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function requireString(array $item, string $key, string $where): string
    {
        if (!isset($item[$key]) || !is_string($item[$key])) {
            throw new InputException(sprintf('%s.%s: обязательная строка отсутствует', $where, $key));
        }

        return $item[$key];
    }

    private function parseTimestamp(string $value, string $where): \DateTimeImmutable
    {
        if (1 !== preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)) {
            throw new InputException(sprintf('%s: ожидался момент UTC вида 2026-08-01T00:00:00Z, получено "%s"', $where, $value));
        }

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $value, new \DateTimeZone('UTC'));

        if (false === $parsed || $parsed->format('Y-m-d\TH:i:s\Z') !== $value) {
            throw new InputException(sprintf('%s: некорректная дата "%s"', $where, $value));
        }

        return $parsed;
    }
}

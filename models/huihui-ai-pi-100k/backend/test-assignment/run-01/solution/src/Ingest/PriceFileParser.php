<?php

declare(strict_types=1);

namespace App\Ingest;

use App\Domain\Decimal;
use App\Domain\DomainException;
use App\Domain\OffPeak;
use App\Entity\PriceRow;

/**
 * Разбор файла истории цен (JSON). Формат — секция «История цен» TASK.md.
 */
final class PriceFileParser
{
    /**
     * @throws DomainException
     */
    public function parse(string $path): PriceFile
    {
        if (!is_file($path)) {
            throw new DomainException(sprintf('Файл не найден: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new DomainException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        try {
            $data = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new DomainException('Некорректный JSON: '.$e->getMessage(), 0, $e);
        }

        if (!\is_array($data) || !\is_array($data['providers'] ?? null) || !array_is_list($data['providers'] ?? [])
            || !\is_array($data['models'] ?? null) || !array_is_list($data['models'] ?? [])
            || !\is_array($data['prices'] ?? null) || !array_is_list($data['prices'] ?? [])) {
            throw new DomainException('Ожидаются массивы "providers", "models", "prices"');
        }

        $providerCodes = [];
        $providers = [];
        foreach ($data['providers'] as $i => $row) {
            $where = sprintf('providers[%d]', $i);
            $code = $this->stringField($row, 'code', $where);
            $name = $this->stringField($row, 'name', $where);
            if (isset($providerCodes[$code])) {
                throw new DomainException(sprintf('%s: дублирующийся код "%s"', $where, $code));
            }
            $providerCodes[$code] = true;

            $offPeak = null;
            $offPeakSpecified = false;
            if (array_key_exists('off_peak', $row)) {
                $offPeakSpecified = true;
                if ($row['off_peak'] === null) {
                    $offPeak = null;
                } elseif (\is_array($row['off_peak'])) {
                    $op = $row['off_peak'];
                    $opWhere = sprintf('providers[%d].off_peak', $i);
                    $start = OffPeak::parseWindow($this->stringField($op, 'window_start', $opWhere));
                    $end = OffPeak::parseWindow($this->stringField($op, 'window_end', $opWhere));
                    $multiplier = $this->decimalField($op, 'multiplier', $opWhere);
                    $offPeak = new OffPeak($start, $end, $multiplier);
                } else {
                    throw new DomainException(sprintf('%s: off_peak должен быть null или объектом', $where));
                }
            }

            $providers[$code] = new ProviderData($code, $name, $offPeak, $offPeakSpecified);
        }

        $models = [];
        foreach ($data['models'] as $i => $row) {
            $where = sprintf('models[%d]', $i);
            $provider = $this->stringField($row, 'provider', $where);
            $code = $this->stringField($row, 'code', $where);
            if (!isset($providerCodes[$provider])) {
                throw new DomainException(sprintf('%s: неизвестный провайдер "%s"', $where, $provider));
            }
            $key = $provider."\x00".$code;
            if (isset($models[$key])) {
                throw new DomainException(sprintf('%s: дублирующаяся модель %s/%s', $where, $provider, $code));
            }
            $models[$key] = new ModelRef($provider, $code);
        }

        $prices = [];
        foreach ($data['prices'] as $i => $row) {
            $where = sprintf('prices[%d]', $i);
            $provider = $this->stringField($row, 'provider', $where);
            $model = $this->stringField($row, 'model', $where);
            if (!isset($providerCodes[$provider])) {
                throw new DomainException(sprintf('%s: неизвестный провайдер "%s"', $where, $provider));
            }
            $key = $provider."\x00".$model;
            if (!isset($models[$key])) {
                throw new DomainException(sprintf('%s: неизвестная модель %s/%s', $where, $provider, $model));
            }

            $validFrom = $this->parseUtc(
                $this->stringField($row, 'valid_from', $where),
                $where.' .valid_from'
            );
            $unit = $this->stringField($row, 'unit', $where);
            if (!\in_array($unit, [PriceRow::UNIT_PER_TOKEN, PriceRow::UNIT_UNIFIED], true)) {
                throw new DomainException(sprintf('%s: неизвестная схема тарификации "%s"', $where, $unit));
            }

            $ratesRaw = $row['rates'] ?? null;
            if (!\is_array($ratesRaw)) {
                throw new DomainException(sprintf('%s: "rates" должен быть объектом', $where));
            }

            $required = $unit === PriceRow::UNIT_PER_TOKEN ? PriceRow::RATES_PER_TOKEN : [PriceRow::RATE_UNIFIED];
            $rates = [];
            foreach ($required as $kind) {
                if (!array_key_exists($kind, $ratesRaw)) {
                    throw new DomainException(sprintf('%s: в "rates" отсутствует "%s" для схемы %s', $where, $kind, $unit));
                }
                $rates[$kind] = $this->decimalField($ratesRaw, $kind, $where.' .rates');
            }
            foreach (array_keys($ratesRaw) as $extra) {
                if (!\in_array($extra, $required, true)) {
                    throw new DomainException(sprintf('%s: лишняя сумма "%s" для схемы %s', $where, $extra, $unit));
                }
            }

            $pk = $key."\x00".$validFrom->format('Y-m-d\TH:i:s\Z');
            if (isset($prices[$pk])) {
                throw new DomainException(sprintf('%s: дублирующая строка для %s/%s с %s', $where, $provider, $model, $validFrom->format(\DateTimeInterface::ATOM)));
            }
            $prices[$pk] = new PriceEntry($provider, $model, $validFrom, $this->currency($row, $where), $unit, $rates);
        }

        return new PriceFile($providers, $models, $prices);
    }

    private function currency(array $row, string $where): string
    {
        $value = $row['currency'] ?? 'USD';
        if (!\is_string($value) || $value === '') {
            throw new DomainException(sprintf('%s: "currency" должна быть непустой строкой', $where));
        }

        return $value;
    }

    private function stringField(array $row, string $field, string $where): string
    {
        $value = $row[$field] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new DomainException(sprintf('%s: поле "%s" должно быть непустой строкой', $where, $field));
        }

        return $value;
    }

    private function decimalField(array $row, string $field, string $where): string
    {
        $value = $row[$field] ?? null;
        if (!\is_string($value) && !\is_int($value)) {
            throw new DomainException(sprintf('%s: поле "%s" должно быть десятичной строкой', $where, $field));
        }
        $value = (string) $value;
        if (!preg_match('/^\d+(\.\d{1,12})?$/', $value)) {
            throw new DomainException(sprintf('%s: "%s" — некорректное десятичное значение "%s"', $where, $field, $value));
        }

        return Decimal::canonicalMoney($value);
    }

    private function parseUtc(string $value, string $where): \DateTimeImmutable
    {
        if (!preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(Z|[+-]\d{2}:?\d{2})?$/', $value)) {
            throw new DomainException(sprintf('%s: "%s" — ожидается ISO-8601 с чётким смещением', $where, $value));
        }

        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new DomainException(sprintf('%s: "%s" — некорректная дата', $where, $value), 0, $e);
        }
    }
}

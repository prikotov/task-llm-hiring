<?php

declare(strict_types=1);

namespace App\Support;

use App\Billing\Pricing\PriceUnit;
use App\Billing\Pricing\PricingCalculator;
use App\Shared\Exception\BadInputException;

/**
 * Полевые доступы к JSON-записям входных файлов: строки, целые,
 * объект «rates». Любое несоответствие — BadInputException с подсказкой
 * о поле (контекст вида «prices[2].rates.input»).
 */
final class SpecReader
{
    public static function requiredString(array $item, string $key, string $context, int $maxLength = 255): string
    {
        if (!array_key_exists($key, $item)) {
            throw new BadInputException("{$context}: отсутствует обязательное поле «{$key}»");
        }

        $value = $item[$key];
        if (!is_string($value) || $value === '' || mb_strlen($value) > $maxLength) {
            throw new BadInputException(sprintf(
                '%s.%s: ожидается непустая строка (не длиннее %d символов)',
                $context,
                $key,
                $maxLength,
            ));
        }

        return $value;
    }

    public static function nonNegativeInt(array $item, string $key, string $context): int
    {
        if (!array_key_exists($key, $item)) {
            throw new BadInputException("{$context}: отсутствует обязательное поле «{$key}»");
        }

        $value = $item[$key];
        if (!is_int($value) || $value < 0) {
            throw new BadInputException("{$context}.{$key}: ожидается неотрицательное целое число");
        }

        return $value;
    }

    /**
     * Цены видами: возвращает только ключи, требуемые схемой ($unit);
     * лишние ключи игнорируются (допущение зафиксировано в PR).
     *
     * @return array<string, string>
     */
    public static function rates(array $item, PriceUnit $unit, string $context): array
    {
        if (!array_key_exists('rates', $item)) {
            throw new BadInputException("{$context}: отсутствует обязательное поле «rates»");
        }

        $rates = $item['rates'];
        if (!is_array($rates) || array_is_list($rates)) {
            throw new BadInputException("{$context}.rates: ожидается объект вида «ключ → цена»");
        }

        $kinds = $unit === PriceUnit::Per1mTokensUnified ? ['any'] : PricingCalculator::KINDS;
        $result = [];
        foreach ($kinds as $kind) {
            if (!array_key_exists($kind, $rates)) {
                throw new BadInputException(sprintf(
                    '%s.rates: отсутствует цена вида «%s» (обязательна для схемы %s)',
                    $context,
                    $kind,
                    $unit->value,
                ));
            }

            $result[$kind] = self::decimalValue($rates[$kind], "{$context}.rates.{$kind}");
        }

        return $result;
    }

    private static function decimalValue(mixed $value, string $context): string
    {
        // Спецификация просит десятичные строки; целые/числовые JSON-значения
        // принимаем дополнительно — допущение зафиксировано в PR_DESCRIPTION.md.
        if (is_int($value)) {
            $value = (string) $value;
        } elseif (is_float($value)) {
            if (!is_finite($value) || $value < 0) {
                throw new BadInputException("{$context}: ожидается неотрицательная цена");
            }
            $value = (string) $value;
        } elseif (!is_string($value)) {
            throw new BadInputException("{$context}: ожидается цена — десятичная строка (не число JSON)");
        }

        DecimalLiteral::assertNonNegative($value, $context);

        return $value;
    }
}

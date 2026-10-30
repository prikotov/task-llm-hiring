<?php

declare(strict_types=1);

namespace App\Support;

use App\Shared\Exception\BadInputException;

/**
 * Валидация десятичных литералов из входных файлов: цены и множители —
 * неотрицательные десятичные строки (без экспоненты, без знака).
 *
 * Границы цифр подобраны так, чтобы значения гарантированно помещались
 * в колонки БД (numeric(18,12) для цен/множителей) и чтобы итоговые
 * стоимости событий помещались в numeric(38,6).
 */
final class DecimalLiteral
{
    private const PATTERN = '/^(\d+)(?:\.(\d+))?$/';

    public static function assertNonNegative(
        string $value,
        string $context,
        int $maxIntegerDigits = 6,
        int $maxFractionDigits = 12,
    ): void {
        if (preg_match(self::PATTERN, $value, $m) !== 1) {
            throw new BadInputException(sprintf(
                '%s: ожидалась неотрицательная десятичная строка (например "3.000000"): «%s»',
                $context,
                $value,
            ));
        }

        if (strlen($m[1]) > $maxIntegerDigits) {
            throw new BadInputException(sprintf(
                '%s: целая часть не длиннее %d цифр',
                $context,
                $maxIntegerDigits,
            ));
        }

        if (strlen($m[2] ?? '') > $maxFractionDigits) {
            throw new BadInputException(sprintf(
                '%s: не более %d знаков после десятичной точки',
                $context,
                $maxFractionDigits,
            ));
        }
    }
}

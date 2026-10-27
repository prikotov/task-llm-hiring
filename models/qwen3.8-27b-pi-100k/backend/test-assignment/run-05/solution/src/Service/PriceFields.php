<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PriceUnit;
use App\Shared\ImportException;
use App\Shared\InputValidator;

/** Разбор полей строки цен из JSON (общий для импорта и коррекции). */
final class PriceFields
{
    /** @param array<string, mixed> $entry */
    public function parse(array $entry, string $ctx): array
    {
        $unit = InputValidator::requireString($entry, 'unit', $ctx);
        if (!in_array($unit, PriceUnit::ALL, true)) {
            throw new ImportException(sprintf('%s: неизвестная схема тарификации "%s"', $ctx, $unit));
        }

        $rates = $entry['rates'] ?? null;
        if (!is_array($rates)) {
            throw new ImportException(sprintf('%s: поле "rates" должно быть объектом', $ctx));
        }
        $rates = array_map(static fn (mixed $v) => (string) $v, $rates);

        if ($unit === PriceUnit::PER_1M_TOKENS_UNIFIED) {
            if (!isset($rates['any'])) {
                throw new ImportException(sprintf('%s: схема unified требует ставку "any"', $ctx));
            }

            return [$unit, null, null, null, null, InputValidator::decimal($rates['any'], $ctx.': rates.any')];
        }

        $fields = ['input', 'output', 'cache_read', 'cache_write'];
        $values = [];
        foreach ($fields as $field) {
            if (!isset($rates[$field])) {
                throw new ImportException(sprintf('%s: схема per_1m_tokens требует ставку "%s"', $ctx, $field));
            }
            $values[] = InputValidator::decimal($rates[$field], sprintf('%s: rates.%s', $ctx, $field));
        }

        return [$unit, ...$values, null];
    }
}

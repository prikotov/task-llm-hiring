<?php

declare(strict_types=1);

namespace App\Application\Billing;

/**
 * Строка отчёта по паре (user, model).
 */
final class ReportRow
{
    /**
     * @param list<int> $tokens [input, output, cache_read, cache_write]
     * @param string    $costUsd сумма уже округлённых стоимостей событий, ровно 6 знаков
     */
    public function __construct(
        public readonly string $user,
        public readonly string $model,
        public readonly array $tokens,
        public readonly string $costUsd,
    ) {
    }
}

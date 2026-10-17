<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Строка отчёта: агрегат по паре (user, model).
 */
final class ReportRow
{
    public function __construct(
        public readonly string $user,
        public readonly string $model,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
        public readonly string $costUsd,
    ) {
    }
}

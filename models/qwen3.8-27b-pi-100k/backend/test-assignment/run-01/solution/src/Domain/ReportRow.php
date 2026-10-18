<?php

declare(strict_types=1);

namespace App\Domain;

/** Строка отчёта: уже рассчитанное событие (после периодического фильтра). */
final class ReportRow
{
    public function __construct(
        public readonly string $user,
        public readonly string $provider,
        public readonly string $model,
        public readonly int $tokensInput,
        public readonly int $tokensOutput,
        public readonly int $tokensCacheRead,
        public readonly int $tokensCacheWrite,
        /** Округлённая до 6 знаков стоимость события (десятичная строка). */
        public readonly string $costUsd,
    ) {
    }
}

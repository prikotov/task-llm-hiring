<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Строка отчёта (группа (user, provider/model)): суммы токенов
 * и сумма уже округлённых до 6 знаков стоимостей событий группы.
 */
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
        public readonly Money $cost,
    ) {
    }

    /** Значение столбца «model» в отчёте: <provider>/<code>. */
    public function modelDisplayName(): string
    {
        return $this->provider . '/' . $this->model;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Decimal;

/**
 * Стратегия расчёта стоимости конкретного события.
 *
 * Контекст (оф-пик) инжектится через calculateWithDiscount.
 */
interface RateStrategy
{
    /**
     * Расчёт стоимости за заданные количества токенов
     * (input, output, cache_read, cache_write) до применения оф-пика.
     *
     * @param array<string,int> $tokens
     * @return Decimal стоимость (COMPUTE_SCALE)
     */
    public function calculate(array $tokens): Decimal;

    /**
     * Расчёт стоимости с учётом множителя скидки (оф-пик).
     *
     * @param array<string,int> $tokens
     * @param string|null $multiplier
     * @return Decimal стоимость (COMPUTE_SCALE)
     */
    public function calculateWithDiscount(array $tokens, ?string $multiplier): Decimal;
}

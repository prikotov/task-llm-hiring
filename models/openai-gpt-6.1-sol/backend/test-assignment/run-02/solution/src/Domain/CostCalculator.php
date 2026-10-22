<?php

declare(strict_types=1);

namespace App\Domain;

final class CostCalculator
{
    public function calculate(TokenUsage $tokens, Price $price, string $multiplier): string
    {
        // Ставка содержит 6 дробных знаков; деление на миллион добавляет ещё 6.
        $weighted = '0';
        foreach (TokenUsage::KINDS as $kind) {
            $rate = $price->rates[$price->unit === Price::UNIFIED ? 'any' : $kind];
            $weighted = bcadd($weighted, bcmul((string) $tokens->$kind, $rate, 6), 6);
        }
        $base = bcdiv($weighted, '1000000', 12);
        $dot = strpos($multiplier, '.');
        $scale = 12 + ($dot === false ? 0 : strlen($multiplier) - $dot - 1);
        $exact = bcmul($base, $multiplier, $scale);

        // Единственное округление, после всех операций (PHP >= 8.4).
        return bcround($exact, 6, \RoundingMode::HalfAwayFromZero);
    }
}

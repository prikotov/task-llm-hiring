<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Расчёт «сырой» (до округления) стоимости события по строке цен.
 *
 * Две схемы тарификации:
 *  - per_1m_tokens:           Σ tokens_вид × rate_вид / 1e6
 *  - per_1m_tokens_unified:   Σ tokens × rate_any / 1e6
 *
 * Плюс опциональный множитель оф-пека провайдера.
 * Все вычисления — bcmath без округления (масштаб MoneyMath::SCALE).
 */
final class PricingEngine
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    private const ONE_MILLION = '1000000';

    public const REQUIRED_RATE_KEYS = [
        self::UNIT_PER_TOKEN => ['input', 'output', 'cache_read', 'cache_write'],
        self::UNIT_UNIFIED => ['any'],
    ];

    public static function validUnits(): array
    {
        return array_keys(self::REQUIRED_RATE_KEYS);
    }

    /**
     * @param array<string, string> $rates  ключи вида → десятичная строка цены
     * @param string|null          $offPeakWindowStart 'HH:MM' либо null
     * @param string|null          $offPeakWindowEnd   'HH:MM' либо null
     * @param string|null          $offPeakMultiplier  десятичная строка либо null
     * @param string               $timeOfDayUtc       'HH:MM'
     *
     * @throws DomainException если схема/ставки некорректны
     */
    public function eventCostBeforeRounding(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        string $unit,
        array $rates,
        ?string $offPeakWindowStart,
        ?string $offPeakWindowEnd,
        ?string $offPeakMultiplier,
        string $timeOfDayUtc,
    ): string {
        $this->assertRatesConsistent($unit, $rates);

        switch ($unit) {
            case self::UNIT_UNIFIED:
                $totalTokens = $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite;
                $cost = bcdiv(bcmul((string) $totalTokens, $rates['any'], MoneyMath::SCALE), self::ONE_MILLION, MoneyMath::SCALE);
                break;

            case self::UNIT_PER_TOKEN:
                $cost = '0';
                foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
                    $tokens = match ($kind) {
                        'input' => $tokensInput,
                        'output' => $tokensOutput,
                        'cache_read' => $tokensCacheRead,
                        'cache_write' => $tokensCacheWrite,
                    };
                    $cost = bcadd($cost, bcdiv(bcmul((string) $tokens, $rates[$kind], MoneyMath::SCALE), self::ONE_MILLION, MoneyMath::SCALE), MoneyMath::SCALE);
                }
                break;
        }

        if ($offPeakWindowStart !== null && $offPeakWindowEnd !== null && $offPeakMultiplier !== null) {
            $window = OffPeakWindow::parse($offPeakWindowStart, $offPeakWindowEnd);
            if ($window->contains($timeOfDayUtc)) {
                $cost = bcmul($cost, $offPeakMultiplier, MoneyMath::SCALE);
            }
        }

        return $cost;
    }

    /** @param array<string, string> $rates */
    public static function assertRatesConsistent(string $unit, array $rates): void
    {
        $required = self::REQUIRED_RATE_KEYS[$unit] ?? null;
        if ($required === null) {
            throw new DomainException(sprintf('Неизвестная схема тарификации "%s"', $unit));
        }

        $missing = array_diff($required, array_keys($rates));
        if ($missing !== []) {
            throw new DomainException(sprintf(
                'В строке цен схемы "%s" нет ставок: %s',
                $unit,
                implode(', ', $missing),
            ));
        }
    }

    /** @param array<string, mixed> $rates */
    public static function assertRatesAreDecimalStrings(array $rates): void
    {
        foreach ($rates as $key => $value) {
            if (!is_string($value) || !preg_match('/^\d+(\.\d{1,6})?$/', $value)) {
                throw new DomainException(sprintf(
                    'Ставка "%s" должна быть десятичной строкой с не более чем 6 знаками после точки',
                    (string) $key,
                ));
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Строка истории цен: (provider, model, valid_from) — цена,
 * действующая с момента valid_from до следующей.
 *
 * unit:
 *   - per_1m_tokens: 4 вида токенов (input, output, cache_read, cache_write)
 *   - per_1m_tokens_unified: единый rate_any
 */
final class PriceLine
{
    public string $provider;
    public string $model;
    public string $validFrom;
    public string $currency;
    public string $unit;

    /** per_1m_tokens: { input, output, cache_read, cache_write } */
    public array $rates;
    /** per_1m_tokens_unified: { any } */
    public array $ratesUnified;

    private function __construct(
        string $provider,
        string $model,
        string $validFrom,
        string $currency,
        string $unit,
        array $rates
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
        $this->ratesUnified = $rates;
    }

    public static function fromData(array $row): self
    {
        return new self(
            (string) $row['provider'],
            (string) $row['model'],
            (string) $row['valid_from'],
            (string) $row['currency'],
            (string) $row['unit'],
            $row['rates']
        );
    }

    /**
     * Точная стоимость события до применения оф-пика.
     * Возвращает строку в рабочем масштабе (18 знаков).
     */
    public function eventCost(UsageEvent $event): string
    {
        $million = '1000000';

        if ($this->unit === 'per_1m_tokens') {
            $inputRate  = (string) ($this->rates['input'] ?? '0');
            $outputRate = (string) ($this->rates['output'] ?? '0');
            $crRate     = (string) ($this->rates['cache_read'] ?? '0');
            $cwRate     = (string) ($this->rates['cache_write'] ?? '0');

            $cost = '0';
            if ($event->tokensInput !== 0) {
                $cost = Decimal::add($cost, Decimal::div(Decimal::mul((string) $event->tokensInput, $inputRate), $million));
            }
            if ($event->tokensOutput !== 0) {
                $cost = Decimal::add($cost, Decimal::div(Decimal::mul((string) $event->tokensOutput, $outputRate), $million));
            }
            if ($event->tokensCacheRead !== 0) {
                $cost = Decimal::add($cost, Decimal::div(Decimal::mul((string) $event->tokensCacheRead, $crRate), $million));
            }
            if ($event->tokensCacheWrite !== 0) {
                $cost = Decimal::add($cost, Decimal::div(Decimal::mul((string) $event->tokensCacheWrite, $cwRate), $million));
            }

            return $cost;
        }

        // per_1m_tokens_unified
        $anyRate = (string) ($this->rates['any'] ?? '0');
        $totalTokens = $event->tokensInput + $event->tokensOutput + $event->tokensCacheRead + $event->tokensCacheWrite;
        if ($totalTokens === 0) {
            return '0';
        }
        return Decimal::div(Decimal::mul((string) $totalTokens, $anyRate), $million);
    }
}

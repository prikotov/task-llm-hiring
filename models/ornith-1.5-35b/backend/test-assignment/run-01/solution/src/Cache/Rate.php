<?php

declare(strict_types=1);

namespace App\Cache;

use App\Domain\Decimal;
use App\Entity\Price;
use App\Infrastructure\RateStrategy;

/**
 * Расчётная ставка для одной строки цен.
 *
 * Кэшируется по естественному ключу строки цены — один объект Rate
 * на один Price (сгенерированный id не важен).
 *
 * calculate — сумма по видам токен-видов (per_1m_tokens) или единая
 * цена за любой токен (per_1m_tokens_unified).
 * calculateWithDiscount — то же, с умножением на множитель оф-пика.
 */
final class Rate implements RateStrategy
{
    private Price $price;

    /** @var array<string,Decimal> */
    private array $rates;

    private bool $isUnified;

    public function __construct(Price $price)
    {
        $this->price = $price;
        $this->rates = [];
        foreach ($price->getRates() as $type => $raw) {
            $this->rates[$type] = Decimal::fromString($raw);
        }
        $this->isUnified = $price->isUnified();
    }

    /**
     * Стоимость за заданные количества токен-видов до оф-пика:
     *   per_1m_tokens     — sum(tokens_вид * rate_вид) / 1_000_000
     *   per_1m_tokens_unified — sum(tokens) * rate_any / 1_000_000
     *
     * @param array<string,int> $tokens [input, output, cache_read, cache_write]
     */
    public function calculate(array $tokens): Decimal
    {
        if ($this->isUnified) {
            $total = 0;
            foreach ($tokens as $t) {
                $total += $t;
            }
            return $this->rate('any')->mul((string) $total)->div('1000000');
        }

        $result = Decimal::fromInt(0);
        foreach (['input', 'output', 'cache_read', 'cache_write'] as $type) {
            $count = (int) ($tokens[$type] ?? 0);
            if ($count === 0) {
                continue;
            }
            $result = $result->add($this->rate($type)->mul((string) $count)->div('1000000')->toString());
        }
        return $result;
    }

    public function calculateWithDiscount(array $tokens, ?string $multiplier): Decimal
    {
        if (null !== $multiplier && '' !== $multiplier) {
            return $this->calculate($tokens)->mul($multiplier);
        }
        return $this->calculate($tokens);
    }

    private function rate(string $type): Decimal
    {
        if (!isset($this->rates[$type])) {
            throw new \RuntimeException("no rate for token type: $type");
        }
        return $this->rates[$type];
    }
}

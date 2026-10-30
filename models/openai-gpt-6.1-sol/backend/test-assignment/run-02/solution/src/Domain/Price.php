<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_price')]
#[ORM\UniqueConstraint(name: 'price_model_start', columns: ['model_id', 'valid_from'])]
class Price
{
    public const SPLIT = 'per_1m_tokens';
    public const UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public private(set) ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)]
        public private(set) Model $model,
        #[ORM\Column(type: 'utc_instant')]
        public private(set) \DateTimeImmutable $validFrom,
        #[ORM\Column(length: 3)]
        public private(set) string $currency,
        #[ORM\Column(length: 32)]
        public private(set) string $unit,
        #[ORM\Column(type: 'json')]
        public private(set) array $rates,
    ) {
        self::validate($currency, $unit, $rates);
        $this->rates = self::normalizeRates($rates);
    }

    public static function validate(string $currency, string $unit, array $rates): void
    {
        if ($currency !== 'USD' || !in_array($unit, [self::SPLIT, self::UNIFIED], true)) {
            throw new \InvalidArgumentException('Поддерживаются только USD и известные схемы тарификации.');
        }
        $keys = $unit === self::SPLIT ? TokenUsage::KINDS : ['any'];
        $actual = array_keys($rates);
        sort($actual);
        sort($keys);
        if ($actual !== $keys) {
            throw new \InvalidArgumentException('Набор ставок не соответствует схеме тарификации.');
        }
        foreach ($rates as $rate) {
            if (!is_string($rate) || !preg_match('/\A[0-9]+\.[0-9]{6}\z/', $rate)) {
                throw new \InvalidArgumentException('Ставки должны быть неотрицательными строками с шестью знаками после точки.');
            }
        }
    }

    public function replace(string $currency, string $unit, array $rates): bool
    {
        self::validate($currency, $unit, $rates);
        $rates = self::normalizeRates($rates);
        if ($this->currency === $currency && $this->unit === $unit && $this->rates === $rates) {
            return false;
        }
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;

        return true;
    }

    private static function normalizeRates(array $rates): array
    {
        foreach ($rates as $kind => $rate) {
            $rates[$kind] = bcadd($rate, '0', 6);
        }
        ksort($rates);

        return $rates;
    }
}

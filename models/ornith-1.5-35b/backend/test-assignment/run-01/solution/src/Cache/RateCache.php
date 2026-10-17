<?php

declare(strict_types=1);

namespace App\Cache;

use App\Domain\Decimal;
use App\Entity\Price;

use Throwable;

/**
 * Кэш расчётных ставок: (provider_id, model_id, valid_from) => Rate.
 *
 * Очистка кэша обязательна перед загрузкой истории цен.
 * Для сгенерированных строк (id === null) ключ вычисляется по
 * (provider_id, model_id, valid_from); перед flush id не обязателен.
 */
final class RateCache
{
    /** @var array<int, Rate> */
    private array $byKey = [];

    /**
     * @return array<int, Rate>
     */
    public function all(): array
    {
        return $this->byKey;
    }

    /**
     * Сериализуем по естественному ключу, не зависящему от id.
     *
     * @param Price $price
     * @return string
     */
    private function key(Price $price): string
    {
        return implode('|', [
            (string) $price->getProvider()->getId(),
            (string) ($price->getModel()->getCode()),
            $price->getValidFrom()->format(\DateTimeInterface::ATOM),
        ]);
    }

    public function has(Price $price): bool
    {
        return array_key_exists($this->key($price), $this->byKey);
    }

    public function set(Price $price): void
    {
        $this->byKey[$this->key($price)] = new Rate($price);
    }

    public function invalidate(Price $price): void
    {
        unset($this->byKey[$this->key($price)]);
    }

    public function invalidateAll(): void
    {
        $this->byKey = [];
    }

    /**
     * @param callable():Price $producer
     * @param int[] $types
     * @return array<string,Decimal>
     */
    public function forType(Price $price, array $types, callable $producer): array
    {
        $result = [];
        foreach ($types as $type) {
            $rate = $this->rateFor($price, $type, $producer);
            $result[$type] = $rate;
        }
        return $result;
    }

    /**
     * @param callable():Rate $producer
     * @return Rate
     */
    public function rateFor(Price $price, string $type, callable $producer): Rate
    {
        $cacheKey = $this->key($price) . ':' . $type;
        if (!array_key_exists($cacheKey, $this->byKey)) {
            /** @var Rate $rate
             */
            $rate = $producer($price, $type);
            $this->byKey[$cacheKey] = $rate;
        }
        /** @var Rate $cached
         */
        $cached = $this->byKey[$cacheKey];
        return $cached;
    }
}

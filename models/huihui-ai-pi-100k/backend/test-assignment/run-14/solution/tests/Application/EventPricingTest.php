<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Billing\EventPricing;
use App\Domain\OffPeakWindow;
use App\Domain\PricingUnit;
use App\Domain\UsageTokens;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EventPricingTest extends TestCase
{
    private function provider(bool $offPeak = false, string $multiplier = '0.500000'): Provider
    {
        $p = new Provider(null, 'test-provider', 'Test Provider');
        if ($offPeak) {
            $p->hasOffPeak = true;
            // window 16:30–00:30 (990–30), multiplier $multiplier
            $p->offPeakStartMinutes = 990;
            $p->offPeakEndMinutes = 30;
            $p->offPeakMultiplier = $multiplier;
        }

        return $p;
    }

    private function model(Provider $provider): Model
    {
        return new Model($provider, 'model-a');
    }

    private function pricePerToken(Model $model, array $rates): Price
    {
        $p = new Price($model, new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $p->setCurrency('USD');
        $p->setRates(PricingUnit::PerToken->value, $rates);

        return $p;
    }

    private function priceUnified(Model $model, string $any): Price
    {
        $p = new Price($model, new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $p->setCurrency('USD');
        $p->setRates(PricingUnit::Unified->value, ['any' => $any]);

        return $p;
    }

    #[Test]
    public function perTokenSimple(): void
    {
        $provider = $this->provider();
        $model = $this->model($provider);
        $price = $this->pricePerToken($model, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $expected = (new EventPricing())->price(
            new UsageTokens(1_200_000, 800_000, 2_500_000, 83_333),
            $price,
            null,
        );

        self::assertSame('14.262499', $expected);
    }

    #[Test]
    public function perTokenOffPeak(): void
    {
        // Оф-пик: 0.5 ×
        // Base 14.26249875 × 0.5 = 7.131249375 → HALF_UP 6 → 7.131249.
        $offpeak = new OffPeakWindow(990, 30, '0.500000');
        $provider = $this->provider(offPeak: true);
        $model = $this->model($provider);
        $price = $this->pricePerToken($model, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $expected = (new EventPricing())->price(
            new UsageTokens(1_200_000, 800_000, 2_500_000, 83_333),
            $price,
            $offpeak,
        );

        self::assertSame('7.131249', $expected);
    }

    #[Test]
    public function unified(): void
    {
        $provider = $this->provider();
        $model = $this->model($provider);
        $price = $this->priceUnified($model, '2.400000');

        $expected = (new EventPricing())->price(
            new UsageTokens(3_000_000, 500_000, 0, 0),
            $price,
            null,
        );

        self::assertSame('8.400000', $expected);
    }

    #[Test]
    public function zeroTokens(): void
    {
        $provider = $this->provider();
        $model = $this->model($provider);
        $price = $this->pricePerToken($model, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $expected = (new EventPricing())->price(
            new UsageTokens(0, 0, 0, 0),
            $price,
            null,
        );

        self::assertSame('0.000000', $expected);
    }

    #[Test]
    public function halfUpRounding(): void
    {
        // Строка «14.26249875» в событии e1 из TASK.md — это
        // 1.2M×3 + 0.8M×12 + 2.5M×0.3 + 83333×3.75/1M = 14.26249875 → HALF_UP 6 → 14.262499.
        // 75 — ровно 0.75 в 7-м знаке, HALF_UP: 875 → округлить 6-й знак вверх по 7-му.
        $provider = $this->provider();
        $model = $this->model($provider);
        $price = $this->pricePerToken($model, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        $expected = (new EventPricing())->price(
            new UsageTokens(1_200_000, 800_000, 2_500_000, 83_333),
            $price,
            null,
        );

        self::assertSame('14.262499', $expected);
    }
}

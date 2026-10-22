<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PriceRow;
use App\Service\BillingEngine;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Тест чистого движка тарификации: пер-токен, unified, оф-пик и
 * единственное округление. Примеры воспроизводят примеры из TASK.md.
 */
final class BillingEngineTest extends TestCase
{
    private const PER_TOKEN_RATES = [
        'input' => '3.000000',
        'output' => '12.000000',
        'cache_read' => '0.300000',
        'cache_write' => '3.750000',
    ];

    private function engine(): BillingEngine
    {
        return new BillingEngine();
    }

    public function test_e1_per_token_peak(): void
    {
        // e1: per_1m_tokens, пик, нет оф-пика -> 14.262499
        $cost = $this->engine()->cost(
            PriceRow::UNIT_PER_TOKEN,
            self::PER_TOKEN_RATES,
            1_200_000,
            800_000,
            2_500_000,
            83_333,
            null,
            null,
            null,
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );
        self::assertSame('14.262499', $cost);
    }

    public function test_e2_per_token_new_row(): void
    {
        // e2: смена цены с 12:00, новая строка -> 11.000000
        $cost = $this->engine()->cost(
            PriceRow::UNIT_PER_TOKEN,
            ['input' => '1.500000', 'output' => '8.000000', 'cache_read' => '0.100000', 'cache_write' => '1.000000'],
            2_000_000,
            1_000_000,
            0,
            0,
            null,
            null,
            null,
            new DateTimeImmutable('2026-09-15T12:00:00Z'),
        );
        self::assertSame('11.000000', $cost);
    }

    public function test_e3_unified(): void
    {
        // e3: unified -> 8.400000
        $cost = $this->engine()->cost(
            PriceRow::UNIT_UNIFIED,
            ['any' => '2.400000'],
            3_000_000,
            500_000,
            0,
            0,
            null,
            null,
            null,
            new DateTimeImmutable('2026-09-20T17:10:00Z'),
        );
        self::assertSame('8.400000', $cost);
    }

    public function test_off_peak_applies_multiplier(): void
    {
        // base = (1M*10 + 1M*20 + 1M*30 + 1M*40)/1M = 100
        $base = $this->engine()->cost(
            PriceRow::UNIT_PER_TOKEN,
            ['input' => '10', 'output' => '20', 'cache_read' => '30', 'cache_write' => '40'],
            1_000_000,
            1_000_000,
            1_000_000,
            1_000_000,
            '16:30',
            '00:30',
            '0.5',
            new DateTimeImmutable('2026-09-15T17:00:00Z'),
        );
        self::assertSame('50.000000', $base);
    }

    public function test_off_peak_not_applied_outside_window(): void
    {
        // та же модель, но событие вне окна -> без скидки
        $base = $this->engine()->cost(
            PriceRow::UNIT_PER_TOKEN,
            ['input' => '10', 'output' => '20', 'cache_read' => '30', 'cache_write' => '40'],
            1_000_000,
            1_000_000,
            1_000_000,
            1_000_000,
            '16:30',
            '00:30',
            '0.5',
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );
        self::assertSame('100.000000', $base);
    }

    public function test_zero_tokens_round_to_zero(): void
    {
        $cost = $this->engine()->cost(
            PriceRow::UNIT_PER_TOKEN,
            self::PER_TOKEN_RATES,
            0,
            0,
            0,
            0,
            null,
            null,
            null,
            new DateTimeImmutable('2026-09-15T11:00:00Z'),
        );
        self::assertSame('0.000000', $cost);
    }
}

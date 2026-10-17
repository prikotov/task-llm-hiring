<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\PricingUnit;
use PHPUnit\Framework\TestCase;

final class PricingUnitTest extends TestCase
{
    public function testFromApiString(): void
    {
        self::assertSame(PricingUnit::PerMillionPerKind, PricingUnit::fromApiString('per_1m_tokens'));
        self::assertSame(PricingUnit::PerMillionUnified, PricingUnit::fromApiString('per_1m_tokens_unified'));
    }

    public function testRequiredRateKeys(): void
    {
        self::assertSame(
            ['input', 'output', 'cache_read', 'cache_write'],
            PricingUnit::PerMillionPerKind->requiredRateKeys(),
        );
        self::assertSame(['any'], PricingUnit::PerMillionUnified->requiredRateKeys());
    }

    public function testUnknownUnitRejected(): void
    {
        $this->expectException(\ValueError::class);

        PricingUnit::fromApiString('per_request');
    }
}

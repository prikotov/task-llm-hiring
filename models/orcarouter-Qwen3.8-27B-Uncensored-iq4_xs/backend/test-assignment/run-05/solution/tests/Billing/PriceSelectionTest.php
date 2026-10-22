<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Billing\PriceCatalog;
use App\Domain\Catalog\PriceRow;
use App\Domain\Catalog\RateUnit;
use PHPUnit\Framework\TestCase;

final class PriceSelectionTest extends TestCase
{
    private function row(string $validFrom): PriceRow
    {
        $row = new PriceRow();
        $row->setProvider('prism');
        $row->setModel('lumen-8');
        $row->setValidFrom(new \DateTimeImmutable($validFrom.'Z'));
        $row->set('USD', RateUnit::Per1mTokens, [
            'input' => '3.000000',
            'output' => '12.000000',
            'cache_read' => '0.300000',
            'cache_write' => '3.750000',
        ]);

        return $row;
    }

    public function testLatestRowNotAfterEventTs(): void
    {
        $early = $this->row('2026-08-01T00:00:00');
        $late = $this->row('2026-09-15T12:00:00');
        $rows = [$early, $late];

        // 11:00 — смена цены в 12:00 ещё не наступила (пример e1)
        self::assertSame($early, PriceCatalog::selectFrom($rows, new \DateTimeImmutable('2026-09-15T11:59:59Z')));
        // 12:00:00 — valid_from включительно (пример e2)
        self::assertSame($late, PriceCatalog::selectFrom($rows, new \DateTimeImmutable('2026-09-15T12:00:00Z')));
    }

    public function testNoRowYetMeansUnrated(): void
    {
        $early = $this->row('2026-08-01T00:00:00');

        self::assertNull(PriceCatalog::selectFrom([$early], new \DateTimeImmutable('2026-07-15T00:00:00Z')));
        self::assertNull(PriceCatalog::selectFrom([], new \DateTimeImmutable('2026-07-15T00:00:00Z')));
    }
}

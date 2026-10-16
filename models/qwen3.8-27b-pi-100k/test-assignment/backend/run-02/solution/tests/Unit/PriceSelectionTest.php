<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Billing\PriceSelection;
use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PriceSelectionTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        $this->model = new Model(new Provider('prism', 'Prism AI'), 'lumen-8');
    }

    private function row(string $validFrom, string $any = '1.000000'): Price
    {
        return new Price($this->model, new DateTimeImmutable($validFrom), 'USD', Price::UNIT_PER_TOKEN, [
            'input' => $any,
            'output' => $any,
            'cache_read' => $any,
            'cache_write' => $any,
        ]);
    }

    public function testPicksLatestRowNotAfterEventTime(): void
    {
        $rows = [
            $this->row('2026-08-01T00:00:00Z'),
            $this->row('2026-09-15T12:00:00Z'),
            $this->row('2026-11-01T00:00:00Z'), // ещё не действует — браться не должна
        ];

        $atChange = PriceSelection::best(new DateTimeImmutable('2026-09-15T12:00:00Z'), $rows);
        self::assertSame('2026-09-15 12:00:00', $atChange->validFrom()->format('Y-m-d H:i:s')); // valid_from включительно

        $beforeChange = PriceSelection::best(new DateTimeImmutable('2026-09-15T11:59:59Z'), $rows);
        self::assertSame('2026-08-01 00:00:00', $beforeChange->validFrom()->format('Y-m-d H:i:s'));

        $afterNewer = PriceSelection::best(new DateTimeImmutable('2026-12-01T00:00:00Z'), $rows);
        self::assertSame('2026-11-01 00:00:00', $afterNewer->validFrom()->format('Y-m-d H:i:s'));
    }

    public function testEmptySetYieldsNull(): void
    {
        self::assertNull(PriceSelection::best(new DateTimeImmutable('2026-09-15T12:00:00Z'), []));
    }

    public function testRowsOnlyInFutureYieldNull(): void
    {
        self::assertNull(PriceSelection::best(
            new DateTimeImmutable('2026-07-01T00:00:00Z'),
            [$this->row('2026-08-01T00:00:00Z')],
        ));
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Usage\TokenBreakdown;
use PHPUnit\Framework\TestCase;

final class TokenBreakdownTest extends TestCase
{
    public function testNegativeTokensRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TokenBreakdown(-1, 0, 0, 0);
    }

    public function testSumWith(): void
    {
        $a = new TokenBreakdown(1, 2, 3, 4);
        $b = new TokenBreakdown(10, 20, 30, 40);
        $sum = $a->sumWith($b);
        $this->assertSame(11, $sum->input);
        $this->assertSame(22, $sum->output);
        $this->assertSame(33, $sum->cacheRead);
        $this->assertSame(44, $sum->cacheWrite);
    }

    public function testTotal(): void
    {
        $td = new TokenBreakdown(1000000, 2000000, 3000000, 4000000);
        $this->assertSame('10000000.000000000000000000000000', $td->total()->amount());
        $this->assertSame('10000000.000000', $td->total()->format());
    }
}

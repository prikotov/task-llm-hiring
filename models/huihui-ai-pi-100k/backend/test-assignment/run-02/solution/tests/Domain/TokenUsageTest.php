<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\TokenUsage;
use PHPUnit\Framework\TestCase;

final class TokenUsageTest extends TestCase
{
    public function testTotalAndByKind(): void
    {
        $usage = new TokenUsage(1200000, 800000, 2500000, 83333);

        self::assertSame(1200000, $usage->input);
        self::assertSame(4583333, $usage->total());
        self::assertSame(
            ['input' => 1200000, 'output' => 800000, 'cache_read' => 2500000, 'cache_write' => 83333],
            $usage->byKind(),
        );
    }

    public function testNegativeTokensRejected(): void
    {
        $this->expectException(\ValueError::class);

        new TokenUsage(-1, 0, 0, 0);
    }
}

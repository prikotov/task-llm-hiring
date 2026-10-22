<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Import\UsageEventsParser;
use App\Shared\ImportException;
use PHPUnit\Framework\TestCase;

final class UsageEventsParserTest extends TestCase
{
    private UsageEventsParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UsageEventsParser();
    }

    public function testValidLine(): void
    {
        $lines = [];
        $no = 0;
        foreach (\file(__DIR__ . '/../../fixtures/usage.jsonl', \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES) as $line) {
            $lines[] = $this->parser->parseLine($line, ++$no);
        }

        self::assertCount(3, $lines);

        $first = $lines[0];
        self::assertSame('e1', $first->providerRequestId);
        self::assertSame('u-alice', $first->user);
        self::assertSame(83333, $first->tokensCacheWrite);
        self::assertSame('2026-09-15T11:00:00+00:00', $first->ts->format('c'));
    }

    public function testTokensMustBeNonNegativeIntegers(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parseLine(\json_encode([
            'provider' => 'p1',
            'provider_request_id' => 'r1',
            'model' => 'm1',
            'user' => 'u1',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => -1,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]), 7);
    }

    public function testBadTimestampRejected(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parseLine(\json_encode([
            'provider' => 'p1',
            'provider_request_id' => 'r1',
            'model' => 'm1',
            'user' => 'u1',
            'ts' => '15.09.2026 11:00',
            'tokens_input' => 1,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]), 7);
    }

    public function testBrokenJsonRejected(): void
    {
        $this->expectException(ImportException::class);

        $this->parser->parseLine('{"provider": "p1",', 7);
    }
}

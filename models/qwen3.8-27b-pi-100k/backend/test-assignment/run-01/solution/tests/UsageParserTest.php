<?php

declare(strict_types=1);

namespace App\Tests;

use App\Import\ImportException;
use App\Import\UsageParser;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class UsageParserTest extends TestCase
{
    private string $validLine = '{"provider": "prism", "provider_request_id": "e1", "model": "lumen-8", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1, "tokens_output": 1, "tokens_cache_read": 1, "tokens_cache_write": 1}';

    public function testValidLineIsParsed(): void
    {
        $line = (new UsageParser())->parseLine($this->validLine, 1);

        self::assertSame('e1', $line->requestId);
        self::assertSame('2026-09-15T11:00:00+00:00', $line->ts->format(DateTimeImmutable::ATOM));
    }

    public function testOffsetTimestampIsRejected(): void
    {
        $json = str_replace('11:00:00Z', '13:00:00+02:00', $this->validLine);

        $this->expectException(ImportException::class);
        (new UsageParser())->parseLine($json, 1);
    }

    public function testNegativeTokensAreRejected(): void
    {
        $json = str_replace('"tokens_input": 1', '"tokens_input": -1', $this->validLine);

        $this->expectException(ImportException::class);
        (new UsageParser())->parseLine($json, 1);
    }

    public function testFractionalTokensAreRejected(): void
    {
        $json = str_replace('"tokens_input": 1', '"tokens_input": 1.5', $this->validLine);

        $this->expectException(ImportException::class);
        (new UsageParser())->parseLine($json, 1);
    }

    public function testFirstInvalidLineFailsWholeFile(): void
    {
        $content = $this->validLine . "\n" . str_replace('"ts": "2026-09-15T11:00:00Z"', '"ts": "nope"', $this->validLine);

        $this->expectException(ImportException::class);
        (new UsageParser())->parseFile($content);
    }

    public function testEmptyLinesAreIgnored(): void
    {
        $lines = (new UsageParser())->parseFile("\n" . $this->validLine . "\n\n");

        self::assertCount(1, $lines);
    }
}

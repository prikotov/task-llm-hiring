<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\UsageParser;
use App\Exception\ImportException;
use PHPUnit\Framework\TestCase;

final class UsageParserTest extends TestCase
{
    private UsageParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UsageParser();
    }

    public function testValidLineIsParsed(): void
    {
        $row = $this->parser->parseLine(
            '{"provider": "prism", "provider_request_id": "e1", "model": "lumen-8", '
            . '"user": "u-alice", "ts": "2026-09-15T11:00:00Z", '
            . '"tokens_input": 1200000, "tokens_output": 800000, "tokens_cache_read": 2500000, "tokens_cache_write": 83333}'
        );

        self::assertSame('prism', $row->provider);
        self::assertSame('e1', $row->providerRequestId);
        self::assertSame('u-alice', $row->user);
        self::assertSame(1_200_000, $row->tokensInput);
        self::assertSame(83_333, $row->tokensCacheWrite);
        self::assertSame('2026-09-15 11:00:00', $row->ts->format('Y-m-d H:i:s'));
    }

    public function testMissingTokenFieldsDefaultToZero(): void
    {
        $row = $this->parser->parseLine(
            '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z"}'
        );

        self::assertSame(0, $row->tokensInput);
        self::assertSame(0, $row->tokensOutput);
        self::assertSame(0, $row->tokensCacheRead);
        self::assertSame(0, $row->tokensCacheWrite);
    }

    public function testBlankLinesAreSkipped(): void
    {
        $line = '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z"}';
        $rows = $this->parser->parseFile("\n\n" . $line . "\n\n");

        self::assertCount(1, $rows);
    }

    public function testBrokenLineFailsTheFileAndNamesItsLine(): void
    {
        $line = '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z"}';

        try {
            $this->parser->parseFile($line . "\n{oops}\n");
            self::fail('ожидалась ImportException');
        } catch (ImportException $e) {
            self::assertStringContainsString('Строка 2', $e->getMessage());
        }
    }

    public function testNegativeTokensAreRejected(): void
    {
        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/tokens_input/');

        $this->parser->parseLine(
            '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", '
            . '"ts": "2026-09-15T11:00:00Z", "tokens_input": -5}'
        );
    }

    public function testNonIntegerTokensAreRejected(): void
    {
        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/tokens_output/');

        $this->parser->parseLine(
            '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", '
            . '"ts": "2026-09-15T11:00:00Z", "tokens_output": 12.5}'
        );
    }

    public function testNonUtcTimestampIsRejected(): void
    {
        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/ts/');

        $this->parser->parseLine(
            '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", '
            . '"ts": "15.09.2026 11:00"}'
        );
    }

    public function testEmptyUserIsRejected(): void
    {
        $this->expectException(ImportException::class);
        $this->expectExceptionMessageMatches('/user/');

        $this->parser->parseLine(
            '{"provider": "p", "provider_request_id": "r", "model": "m", "user": "", "ts": "2026-09-15T11:00:00Z"}'
        );
    }
}

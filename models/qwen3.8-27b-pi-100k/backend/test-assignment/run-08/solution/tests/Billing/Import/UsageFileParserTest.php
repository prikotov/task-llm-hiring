<?php

declare(strict_types=1);

namespace App\Tests\Billing\Import;

use App\Billing\Import\ImportException;
use App\Billing\Import\UsageFileParser;
use PHPUnit\Framework\TestCase;

final class UsageFileParserTest extends TestCase
{
    private UsageFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UsageFileParser();
    }

    private const LINE1 = '{"provider": "prism", "provider_request_id": "e1", "model": "lumen-8", '
        . '"user": "u-alice", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1200000, '
        . '"tokens_output": 800000, "tokens_cache_read": 2500000, "tokens_cache_write": 83333}';

    public function testParsesLinesAndIgnoresBlankOnes(): void
    {
        $events = $this->parser->parse(self::LINE1 . "\n\n   \n" . self::LINE1);

        self::assertCount(2, $events);
        self::assertSame('e1', $events[0]->providerRequestId);
        self::assertSame('u-alice', $events[0]->user);
        self::assertSame('UTC', $events[0]->ts->getTimezone()->getName());
        self::assertSame('2026-09-15T11:00:00', $events[0]->ts->format('Y-m-d\TH:i:s'));
        self::assertSame(1200000, $events[0]->tokens->input);
        self::assertSame(83333, $events[0]->tokens->cacheWrite);
    }

    public function testZeroTokensAreValid(): void
    {
        $json = str_replace(
            '1200000, "tokens_output": 800000, "tokens_cache_read": 2500000, "tokens_cache_write": 83333',
            '0, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0',
            self::LINE1,
        );
        $events = $this->parser->parse($json);

        self::assertSame(0, $events[0]->tokens->total());
    }

    public function testNegativeTokensRejected(): void
    {
        $json = str_replace('"tokens_input": 1200000', '"tokens_input": -1', self::LINE1);
        $this->expectParseError($json, 'токены < 0');
    }

    public function testFractionalTokensRejected(): void
    {
        $json = str_replace('"tokens_input": 1200000', '"tokens_input": 1.5', self::LINE1);
        $this->expectParseError($json, 'дробные токены');
    }

    public function testMissingFieldRejected(): void
    {
        $json = str_replace('"user": "u-alice", ', '', self::LINE1);
        $this->expectParseError($json, 'нет поля user');
    }

    public function testInvalidLineKeepsLineNumber(): void
    {
        $content = self::LINE1 . "\n{ криво";
        try {
            $this->parser->parse($content);
            self::fail('ожидался ImportException');
        } catch (ImportException $e) {
            self::assertStringContainsString('строка 2', $e->getMessage());
        }
    }

    private function expectParseError(string $json, string $reason): void
    {
        try {
            $this->parser->parse($json);
            self::fail(sprintf('ожидался ImportException (%s)', $reason));
        } catch (ImportException) {
            self::assertTrue(true);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Entity\UsageEvent;
use App\Exception\ImportException;
use App\Import\UsageLineParser;
use PHPUnit\Framework\TestCase;

final class UsageLineParserTest extends TestCase
{
    public function testParsesMinimalEvent(): void
    {
        $event = UsageLineParser::parse(self::line(), 'test');

        self::assertSame('prism', $event->getProviderCode());
        self::assertSame('r-1', $event->getProviderRequestId());
        self::assertSame('lumen-8', $event->getModelCode());
        self::assertSame('u-alice', $event->getUser());
        self::assertSame('2026-09-18T11:07:29Z', $event->getTs()->format('Y-m-d\TH:i:s\Z'));
        self::assertSame(UsageEvent::STATUS_PENDING, $event->getStatus());
        self::assertFalse($event->isRated());
        self::assertNull($event->getCostUsd());
    }

    public function testTokenTotals(): void
    {
        $line = self::line();
        $line['tokens_input'] = 1;
        $line['tokens_output'] = 2;
        $line['tokens_cache_read'] = 4;
        $line['tokens_cache_write'] = 8;

        $event = UsageLineParser::parse($line, 'test');

        self::assertSame([
            'input' => 1,
            'output' => 2,
            'cache_read' => 4,
            'cache_write' => 8,
        ], $event->tokens());
    }

    public function testNegativeTokensAreRejected(): void
    {
        $line = self::line();
        $line['tokens_input'] = -1;

        $this->expectException(ImportException::class);
        UsageLineParser::parse($line, 'test');
    }

    public function testFloatTokensAreRejected(): void
    {
        $line = self::line();
        $line['tokens_output'] = 1.5;

        $this->expectException(ImportException::class);
        UsageLineParser::parse($line, 'test');
    }

    public function testMissingUserIsRejected(): void
    {
        $line = self::line();
        unset($line['user']);

        $this->expectException(ImportException::class);
        UsageLineParser::parse($line, 'test');
    }

    public function testBadTimestampIsRejected(): void
    {
        $line = self::line();
        $line['ts'] = '18/09/2026 11:07';

        $this->expectException(ImportException::class);
        UsageLineParser::parse($line, 'test');
    }

    /** @return array<string, mixed> */
    private static function line(): array
    {
        return [
            'provider' => 'prism',
            'provider_request_id' => 'r-1',
            'model' => 'lumen-8',
            'user' => 'u-alice',
            'ts' => '2026-09-18T11:07:29Z',
            'tokens_input' => 1_000,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];
    }
}

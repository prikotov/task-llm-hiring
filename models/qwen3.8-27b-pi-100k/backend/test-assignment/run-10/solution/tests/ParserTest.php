<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Тесты парсера JSONL-событий (без БД).
 */
final class UsageParserTest extends TestCase
{
    private function parser(): \App\Application\Import\UsageParser
    {
        return new \App\Application\Import\UsageParser();
    }

    public function testUsageParser_valid(): void
    {
        $raw = file_get_contents('fixtures/usage.jsonl');
        self::assertNotFalse($raw);

        $parsed = $this->parser()->parse($raw);
        self::assertCount(3, $parsed);
        self::assertSame('prism', $parsed[0]['provider']);
        self::assertSame(1200000, $parsed[0]['tokens_input']);
        self::assertSame(2500000, $parsed[0]['tokens_cache_read']);
    }

    public function testUsageParser_badJsonLine(): void
    {
        $raw = <<<'JSONL'
{"provider": "prism", "provider_request_id": "e1", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1, "tokens_output": 2, "tokens_cache_read": 0, "tokens_cache_write": 0}
not-json
JSONL;

        $this->expectException(\App\Application\Import\ImportException::class);
        $this->parser()->parse($raw);
    }

    public function testUsageParser_negativeToken(): void
    {
        $raw = <<<'JSONL'
{"provider": "prism", "provider_request_id": "e1", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": -1, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0}
JSONL;

        $this->expectException(\App\Application\Import\ImportException::class);
        $this->parser()->parse($raw);
    }

    public function testUsageParser_missingRequired(): void
    {
        $raw = <<<'JSONL'
{"provider": "prism", "provider_request_id": "e1", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1}
JSONL;

        $this->expectException(\App\Application\Import\ImportException::class);
        $this->parser()->parse($raw);
    }
}

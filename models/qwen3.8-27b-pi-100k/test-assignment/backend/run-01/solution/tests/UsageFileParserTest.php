<?php

declare(strict_types=1);

namespace App\Tests;

use App\Import\ImportException;
use App\Import\UsageFileParser;
use PHPUnit\Framework\TestCase;

final class UsageFileParserTest extends TestCase
{
    private UsageFileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UsageFileParser();
    }

    private function validLine(): string
    {
        return '{"provider":"p","provider_request_id":"r1","model":"m","user":"u","ts":"2026-09-15T11:00:00Z","tokens_input":1200000,"tokens_output":800000,"tokens_cache_read":2500000,"tokens_cache_write":83333}';
    }

    private function withChange(string $line, string $field, mixed $value): string
    {
        $d = json_decode($line, true);

        $d[$field] = $value;

        return json_encode($d, JSON_UNESCAPED_UNICODE);
    }

    private function assertLineRejected(string $line, string $needle): void
    {
        $caught = null;
        try {
            $this->parser->parseLine($line, 1);
        } catch (ImportException $e) {
            $caught = $e;
        }

        self::assertNotNull($caught, 'ожидалась ImportException (фрагмент "'.$needle.'")');
        self::assertStringContainsString($needle, (string) $caught);
        self::assertStringContainsString('Строка 1', (string) $caught);
    }

    public function testValidLine(): void
    {
        $spec = $this->parser->parseLine($this->validLine(), 1);

        self::assertSame('p', $spec->getProvider());
        self::assertSame('r1', $spec->getProviderRequestId());
        self::assertSame('m', $spec->getModel());
        self::assertSame('u', $spec->getUser());
        self::assertSame('2026-09-15T11:00:00+00:00', $spec->getTs()->format('c'));
        self::assertSame(1200000, $spec->getTokensInput());
        self::assertSame(83333, $spec->getTokensCacheWrite());
    }

    public function testFileWithBlankLinesAndTrailingNewline(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'usage');
        file_put_contents($path, $this->validLine()."\n\n  \n".$this->validLine()."\n");

        try {
            $specs = $this->parser->parseFile($path);
            self::assertCount(2, $specs);
        } finally {
            unlink($path);
        }
    }

    public function testBrokenJson(): void
    {
        $this->assertLineRejected('{oops', 'JSON');
    }

    public function testMissingUser(): void
    {
        $d = json_decode($this->validLine(), true);
        unset($d['user']);
        $this->assertLineRejected(json_encode($d, JSON_UNESCAPED_UNICODE), 'user');
    }

    public function testFloatTokens(): void
    {
        $this->assertLineRejected($this->withChange($this->validLine(), 'tokens_input', 1.5), 'tokens_input');
    }

    public function testNegativeTokens(): void
    {
        $this->assertLineRejected($this->withChange($this->validLine(), 'tokens_output', -3), 'tokens_output');
    }

    public function testNonUtcTimestamp(): void
    {
        $this->assertLineRejected($this->withChange($this->validLine(), 'ts', '2026-09-15T11:00:00+03:00'), 'ts');
    }

    public function testNumericTokensAsStringsAreAllowed(): void
    {
        $d = json_decode($this->validLine(), true);
        $d['tokens_input'] = '1200000';

        $spec = $this->parser->parseLine(json_encode($d), 1);
        self::assertSame(1200000, $spec->getTokensInput());
    }
}

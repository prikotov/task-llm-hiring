<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\DomainException;
use App\Ingest\UsageLineParser;
use PHPUnit\Framework\TestCase;

final class UsageLineParserTest extends TestCase
{
    private UsageLineParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UsageLineParser();
    }

    public function testValidLine(): void
    {
        $line = json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'req-1',
            'model' => 'lumen-8',
            'user' => 'alice',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 1200000,
            'tokens_output' => 800000,
            'tokens_cache_read' => 2500000,
            'tokens_cache_write' => 83333,
        ]);
        $ev = $this->parser->parse($line, 1);
        self::assertSame('prism', $ev->provider);
        self::assertSame('req-1', $ev->providerRequestId);
        self::assertSame('lumen-8', $ev->model);
        self::assertSame('alice', $ev->user);
        self::assertSame('2026-09-15T11:00:00+00:00', $ev->occurredAt->format(\DateTimeInterface::ATOM));
        self::assertSame(1200000, $ev->tokensInput);
    }

    public function testMissingTokensField(): void
    {
        $this->expectException(DomainException::class);
        $line = json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'req-1',
            'model' => 'lumen-8',
            'user' => 'alice',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_output' => 1,
        ]);
        $this->parser->parse($line, 2);
    }

    public function testInvalidTs(): void
    {
        $this->expectException(DomainException::class);
        $line = json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'req-1',
            'model' => 'lumen-8',
            'user' => 'alice',
            'ts' => 'not-a-date',
            'tokens_input' => 1,
            'tokens_output' => 1,
            'tokens_cache_read' => 1,
            'tokens_cache_write' => 1,
        ]);
        $this->parser->parse($line, 3);
    }

    public function testInvalidProvider(): void
    {
        $this->expectException(DomainException::class);
        $line = json_encode([
            'provider' => '',
            'provider_request_id' => 'req-1',
            'model' => 'lumen-8',
            'user' => 'alice',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 1,
            'tokens_output' => 1,
            'tokens_cache_read' => 1,
            'tokens_cache_write' => 1,
        ]);
        $this->parser->parse($line, 4);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Import;

use App\Infrastructure\Import\UsageFileParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function iterator_to_array;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class UsageFileParserTest extends TestCase
{
    private UsageFileParser $parser;

    /** @var list<string> */
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        $this->parser = new UsageFileParser();
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            @unlink($file);
        }
    }

    public function testParsesFixture(): void
    {
        $events = iterator_to_array($this->parser->parse(dirname(__DIR__, 3) . '/fixtures/usage.jsonl'));

        self::assertCount(3, $events);
        self::assertSame('prism', $events[0]->provider);
        self::assertSame('e1', $events[0]->providerRequestId);
        self::assertSame('u-alice', $events[0]->user);
        self::assertSame(1_200_000, $events[0]->usage->input);
        self::assertSame(83_333, $events[0]->usage->cacheWrite);
        self::assertSame('2026-09-15T11:00:00+00:00', $events[0]->occurredAt->format('c'));
    }

    public function testSkipsEmptyLines(): void
    {
        $file = $this->temporaryFile("\n" . <<<'JSONL'
            {"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 0, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0}

            JSONL);

        self::assertCount(1, iterator_to_array($this->parser->parse($file)));
    }

    public function testRejectsMalformedJsonLine(): void
    {
        $file = $this->temporaryFile(<<<'JSONL'
            {"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 0, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0}
            {"provider": "p", broken}
            JSONL);

        $this->expectException(InvalidArgumentException::class);
        iterator_to_array($this->parser->parse($file));
    }

    public function testRejectsNonIntegerTokens(): void
    {
        $file = $this->temporaryFile(<<<'JSONL'
            {"provider": "p", "provider_request_id": "r", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1.5, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0}
            JSONL);

        $this->expectException(InvalidArgumentException::class);
        iterator_to_array($this->parser->parse($file));
    }

    private function temporaryFile(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'billing-usage-');
        if ($file === false) {
            self::fail('Cannot create a temporary file.');
        }

        file_put_contents($file, $contents);
        $this->temporaryFiles[] = $file;

        return $file;
    }
}

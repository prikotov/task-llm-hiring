<?php

declare(strict_types=1);

namespace App\Tests\Json;

use App\Infrastructure\Json\UsageEventReader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UsageEventReaderTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = tempnam(sys_get_temp_dir(), 'usage');
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    private function write(string $content): string
    {
        file_put_contents($this->file, $content);

        return $this->file;
    }

    public function testReadsValidLinesAndSkipsBlankOnes(): void
    {
        $path = $this->write(implode("\n", [
            '{"provider":"prism","provider_request_id":"e1","model":"lumen-8","user":"u-a","ts":"2026-09-15T11:00:00Z","tokens_input":10,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}',
            '',
            '{"provider":"sfera","provider_request_id":"e1","model":"gyro-pro","user":"u-b","ts":"2026-09-15T11:00:00Z","tokens_input":0,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}',
        ]));

        $events = (new UsageEventReader())->read($path);

        self::assertCount(2, $events);
        self::assertSame('prism', $events[0]->providerCode);
        self::assertSame('sfera', $events[1]->providerCode);
        // Одинаковые provider_request_id у разных провайдеров — разные события.
        self::assertNotSame($events[0]->key(), $events[1]->key());
    }

    public function testBrokenJsonFailsWithLineNumber(): void
    {
        $path = $this->write("{not json}\n");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Строка 1');

        (new UsageEventReader())->read($path);
    }

    public function testNonUtcTimestampIsRejected(): void
    {
        $path = $this->write('{"provider":"p","provider_request_id":"1","model":"m","user":"u","ts":"2026-09-15T11:00:00+03:00","tokens_input":0,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}');

        $this->expectException(InvalidArgumentException::class);

        (new UsageEventReader())->read($path);
    }

    public function testNegativeTokensAreRejected(): void
    {
        $path = $this->write('{"provider":"p","provider_request_id":"1","model":"m","user":"u","ts":"2026-09-15T11:00:00Z","tokens_input":-1,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}');

        $this->expectException(InvalidArgumentException::class);

        (new UsageEventReader())->read($path);
    }

    public function testImpossibleDateIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UsageEventReader::parseTimestamp('2026-02-30T00:00:00Z');
    }
}

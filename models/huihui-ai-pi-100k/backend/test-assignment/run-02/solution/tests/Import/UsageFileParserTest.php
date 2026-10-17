<?php

declare(strict_types=1);

namespace App\Tests\Import;

use App\Exception\ImportException;
use App\Import\UsageFileParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UsageFileParserTest extends TestCase
{
    public function testParsesFixtureFile(): void
    {
        $events = UsageFileParser::parseFile(dirname(__DIR__, 2) . '/fixtures/usage.jsonl');

        self::assertCount(3, $events);

        $first = $events[0];
        self::assertSame('prism', $first->provider);
        self::assertSame('e1', $first->providerRequestId);
        self::assertSame('lumen-8', $first->model);
        self::assertSame('u-alice', $first->user);
        self::assertSame('2026-09-15T11:00:00+00:00', $first->ts->format('Y-m-d\TH:i:sP'));
        self::assertSame(1200000, $first->tokens->input);
        self::assertSame(4583333, $first->tokens->total());
        self::assertSame("prism\x1Ee1", $first->deduplicationKey());
    }

    /**
     * Дубли (provider, provider_request_id) в пределах файла: первое вхождение побеждает.
     */
    public function testDuplicatesWithinFileAreSkipped(): void
    {
        $lines = [
            '{"provider": "prism", "provider_request_id": "d1", "model": "lumen-8", "user": "u1", '
            . '"ts": "2026-10-01T00:00:00Z", "tokens_input": 100}',
            '{"provider": "prism", "provider_request_id": "d1", "model": "lumen-8", "user": "u-other", '
            . '"ts": "2026-10-02T00:00:00Z", "tokens_input": 999}',
        ];

        $events = UsageFileParser::parseFile(self::tempFile(implode("\n", $lines)));

        self::assertCount(1, $events);
        self::assertSame('u1', $events[0]->user);
        self::assertSame(100, $events[0]->tokens->input);
    }

    public function testBlankLinesAreIgnored(): void
    {
        $content = "\n"
            . '{"provider": "prism", "provider_request_id": "b1", "model": "lumen-8", "user": "u1", "ts": "2026-10-01T00:00:00Z"}'
            . "\n\n  \n";

        $events = UsageFileParser::parseFile(self::tempFile($content));

        self::assertCount(1, $events);
        // отсутствующие поля токенов — ноль
        self::assertSame(0, $events[0]->tokens->input);
        self::assertSame(0, $events[0]->tokens->total());
    }

    #[DataProvider('invalidEventLines')]
    public function testInvalidEventRejected(string $json, string $why): void
    {
        $this->expectException(ImportException::class);

        UsageFileParser::parseFile(self::tempFile($json));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalidEventLines(): iterable
    {
        $base = '{"provider": "prism", "provider_request_id": "x", "model": "lumen-8", "user": "u1", '
            . '"ts": "2026-10-01T00:00:00Z"}';

        yield 'битый JSON' => ['{oops', 'json'];
        yield 'отсутствует user' => ['{"provider": "prism", "provider_request_id": "x", "model": "m", "ts": "2026-10-01T00:00:00Z"}', 'user'];
        yield 'negatives tokens' => [str_replace('Z"}', 'Z", "tokens_input": -5}', $base), 'tokens'];
        yield 'битый ts' => [str_replace('2026-10-01T00:00:00Z', '01.10.2026 00:00', $base), 'ts'];
    }

    public function testMissingFileRejected(): void
    {
        $this->expectException(ImportException::class);

        UsageFileParser::parseFile('/dev/null/definitely/missing.jsonl');
    }

    private function tempFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'usage-') . '.jsonl';

        file_put_contents($path, $content);

        return $path;
    }
}

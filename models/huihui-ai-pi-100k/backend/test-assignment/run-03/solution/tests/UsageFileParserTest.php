<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Exception\FileFormatException;
use App\Application\Usage\UsageFileParser;
use PHPUnit\Framework\TestCase;

final class UsageFileParserTest extends TestCase
{
    public function testValidEventsAreParsed(): void
    {
        $file = self::writeTemp(implode("\n", [
            self::eventJson('e1'),
            '',
            self::eventJson('e2', ts: '2026-09-15T14:00:00+02:00'),
            '   ',
        ]));

        $specs = UsageFileParser::fromFile($file);

        self::assertCount(2, $specs);
        self::assertSame('e1', $specs[0]->providerRequestId);
        self::assertSame(1200000, $specs[0]->tokensInput);
        self::assertSame('2026-09-15T11:00:00Z', $specs[0]->ts->format('Y-m-d\TH:i:s\Z'));
        // +02:00 нормализуется в UTC.
        self::assertSame('2026-09-15T12:00:00Z', $specs[1]->ts->format('Y-m-d\TH:i:s\Z'));
    }

    public function testEmptyFileIsEmpty(): void
    {
        self::assertSame([], UsageFileParser::fromFile(self::writeTemp('')));
        self::assertSame([], UsageFileParser::fromFile(self::writeTemp(" \n\n")));
    }

    public function testDuplicateRequestsInOneFileAreDeduplicated(): void
    {
        // Внутри одного файла первая запись побеждает, вторая — повторная доставка.
        $file = self::writeTemp(self::eventJson('dup')."\n".self::eventJson('dup'));
        $specs = UsageFileParser::fromFile($file);

        self::assertCount(1, $specs);
        self::assertSame('dup', $specs[0]->providerRequestId);
    }

    public function testNonObjectLineIsRejected(): void
    {
        self::assertParseFails('hello');
        self::assertParseFails('[1, 2]');
    }

    public function testMissingRequiredFieldIsRejected(): void
    {
        self::assertParseFails('{"provider": "prism", "provider_request_id": "e1", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1}');
    }

    public function testNegativeTokensAreRejected(): void
    {
        self::assertParseFails(str_replace('1200000', '-5', self::eventJson('e1')));
    }

    public function testFractionalTokensAreRejected(): void
    {
        self::assertParseFails(str_replace('"tokens_input": 1200000', '"tokens_input": 1200000.5', self::eventJson('e1')));
    }

    public function testBadTimestampIsRejected(): void
    {
        self::assertParseFails(str_replace('"ts": "2026-09-15T11:00:00Z"', '"ts": "2026-09-15 11:00:00"', self::eventJson('e1')));
    }

    public function testZeroTokensAreAllowed(): void
    {
        $specs = UsageFileParser::fromFile(self::writeTemp(self::eventJson('zero', tokens: [0, 0, 0, 0])));
        self::assertSame(0, $specs[0]->tokensInput);
    }

    private static function assertParseFails(string $jsonl): void
    {
        $threw = false;
        try {
            UsageFileParser::fromFile(self::writeTemp($jsonl));
        } catch (FileFormatException) {
            $threw = true;
        }
        self::assertTrue($threw, 'Ожидалось FileFormatException');
    }

    private static function writeTemp(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'usage');
        file_put_contents($path, $content);

        return $path;
    }

    private static function eventJson(string $id, string $ts = '2026-09-15T11:00:00Z', array $tokens = [1200000, 800000, 2500000, 83333]): string
    {
        [$input, $output, $cacheRead, $cacheWrite] = $tokens;

        return '{"provider": "prism", "provider_request_id": "'.$id.'", "model": "lumen-8", "user": "u-alice", "ts": "'.$ts.'", "tokens_input": '.$input.', "tokens_output": '.$output.', "tokens_cache_read": '.$cacheRead.', "tokens_cache_write": '.$cacheWrite.'}';
    }
}

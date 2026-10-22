<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Usage\UsageFileParser;
use App\Exception\InvalidUsageFileException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UsageFileParserTest extends TestCase
{
    private function parser(): UsageFileParser
    {
        return new UsageFileParser();
    }

    public function testValidEvents(): void
    {
        $content = json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'e1',
            'model' => 'lumen-8',
            'user' => 'u-alice',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 1200000,
            'tokens_output' => 800000,
            'tokens_cache_read' => 2500000,
            'tokens_cache_write' => 83333,
        ])."\n"
            .json_encode([
            'provider' => 'sfera',
            'provider_request_id' => 'e3',
            'model' => 'gyro-pro',
            'user' => 'u-alice',
            'ts' => '2026-09-20T17:10:00Z',
            'tokens_input' => 3000000,
            'tokens_output' => 500000,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ])."\n"
        ;

        $specs = $this->parser()->parse($content);

        self::assertCount(2, $specs);
        self::assertSame('prism', $specs[0]->provider);
        self::assertSame('e1', $specs[0]->providerRequestId);
        self::assertSame(2500000, $specs[0]->tokens->cacheRead);
        self::assertSame('2026-09-15T11:00:00+00:00', $specs[0]->ts->format('Y-m-d\TH:i:sP'));
    }

    public function testAllZeroTokensAllowed(): void
    {
        $line = json_encode([
            'provider' => 'sfera',
            'provider_request_id' => 'z1',
            'model' => 'gyro-pro',
            'user' => 'u-x',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]);

        $specs = $this->parser()->parse($line."\n");

        self::assertCount(1, $specs);
        self::assertSame(0, $specs[0]->tokens->input);
    }

    public function testBlankLinesAreIgnored(): void
    {
        $line = json_encode([
            'provider' => 'prism',
            'provider_request_id' => 'b1',
            'model' => 'lumen-8',
            'user' => 'u-x',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 1,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ]);

        $specs = $this->parser()->parse($line."\n\n   \n".$line."\n");

        self::assertCount(2, $specs);
    }

    public function testInvalidLineFailsWholeFile(): void
    {
        $this->expectException(InvalidUsageFileException::class);

        $this->parser()->parse('{"provider": "prism"}'."\n".'{"provider": "prism", "provider_request_id": "ok", "model": "m", "user": "u", "ts": "2026-09-15T11:00:00Z", "tokens_input": 1, "tokens_output": 0, "tokens_cache_read": 0, "tokens_cache_write": 0}');
    }

    #[DataProvider("provideInvalidEventFields")]
    public function testInvalidEventField(string $label, array $overrides): void
    {
        $event = [
            'provider' => 'prism',
            'provider_request_id' => 'x1',
            'model' => 'lumen-8',
            'user' => 'u-x',
            'ts' => '2026-09-15T11:00:00Z',
            'tokens_input' => 1,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ];
        foreach ($overrides as $k => $v) {
            if ($v === null) {
                unset($event[$k]);
            } else {
                $event[$k] = $v;
            }
        }

        $this->expectException(InvalidUsageFileException::class);
        $this->parser()->parse(json_encode($event)."\n");
    }

    public static function provideInvalidEventFields(): iterable
    {
        yield 'missing provider' => ['missing provider', ['provider' => null]];
        yield 'bad ts offset' => ['bad ts offset', ['ts' => '2026-09-15T11:00:00+00:00']];
        yield 'bad ts no Z' => ['bad ts no Z', ['ts' => '2026-09-15 11:00:00']];
        yield 'negative tokens' => ['negative tokens', ['tokens_input' => -1]];
        yield 'float tokens' => ['float tokens', ['tokens_output' => 1.5]];
        yield 'string tokens' => ['string tokens', ['tokens_cache_read' => '5']];
        yield 'empty user' => ['empty user', ['user' => '']];
        yield 'top-level array' => ['top-level array', ['provider' => ['prism']]];
    }
}

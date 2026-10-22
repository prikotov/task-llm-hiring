<?php

declare(strict_types=1);

namespace App\Usage\Application;

use App\Pricing\Domain\TokenUsage;
use App\Shared\InvalidInput;
use App\Shared\UtcTime;
use DateTimeImmutable;
use JsonException;
use Throwable;

/** Разобранная строка JSONL событий. */
final readonly class UsageLine
{
    private function __construct(
        public string $provider,
        public string $providerRequestId,
        public string $model,
        public string $user,
        public DateTimeImmutable $ts,
        public TokenUsage $tokens,
    ) {
    }

    public static function parse(string $line, int $lineNo): self
    {
        try {
            $d = json_decode($line, true, 16, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidInput("Строка $lineNo: некорректный JSON: ".$e->getMessage(), 0, $e);
        }
        if (!is_array($d)) {
            throw new InvalidInput("Строка $lineNo: ожидается JSON-объект");
        }
        $str = static function (string $k) use ($d, $lineNo): string {
            $v = $d[$k] ?? null;
            if (!is_string($v) || $v === '') {
                throw new InvalidInput("Строка $lineNo: поле \"$k\" должно быть непустой строкой");
            }

            return $v;
        };
        $int = static function (string $k) use ($d, $lineNo): int {
            $v = $d[$k] ?? null;
            if (!is_int($v) || $v < 0) {
                throw new InvalidInput("Строка $lineNo: поле \"$k\" должно быть целым >= 0");
            }

            return $v;
        };
        try {
            $ts = UtcTime::parse($str('ts'));
        } catch (InvalidInput $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new InvalidInput("Строка $lineNo: ".$e->getMessage(), 0, $e);
        }

        return new self(
            $str('provider'),
            $str('provider_request_id'),
            $str('model'),
            $str('user'),
            $ts,
            new TokenUsage($int('tokens_input'), $int('tokens_output'), $int('tokens_cache_read'), $int('tokens_cache_write')),
        );
    }
}

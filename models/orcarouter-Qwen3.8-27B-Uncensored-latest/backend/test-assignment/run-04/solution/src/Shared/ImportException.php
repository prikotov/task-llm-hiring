<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Raised when imported data (prices file, JSONL line, price correction) is
 * invalid and cannot be loaded.
 */
final class ImportException extends \RuntimeException
{
    public static function forFile(string $file, string $reason): self
    {
        return new self(\sprintf('invalid data in %s: %s', $file, $reason));
    }

    public static function forLine(int $line, string $reason): self
    {
        return new self(\sprintf('line %d: %s', $line, $reason));
    }
}

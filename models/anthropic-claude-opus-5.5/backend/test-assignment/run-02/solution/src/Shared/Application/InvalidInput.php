<?php

declare(strict_types=1);

namespace App\Shared\Application;

use RuntimeException;
use Throwable;

/** Ошибка во входных данных (файл, строка, опция). Команды превращают её в код выхода 1. */
final class InvalidInput extends RuntimeException
{
    public static function because(string $message, ?Throwable $previous = null): self
    {
        return new self($message, 0, $previous);
    }

    public static function atLine(int $line, string $message, ?Throwable $previous = null): self
    {
        return new self(sprintf('строка %d: %s', $line, $message), 0, $previous);
    }
}

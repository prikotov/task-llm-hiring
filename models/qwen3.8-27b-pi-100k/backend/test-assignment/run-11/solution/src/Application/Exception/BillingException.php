<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

final class BillingException extends RuntimeException
{
    public static function invalidValue(string $message): self
    {
        return new self($message);
    }

    public static function notFound(string $what, string $id): self
    {
        return new self(sprintf('%s не найден: %s', $what, $id));
    }

    public static function parseError(string $message): self
    {
        return new self($message);
    }
}

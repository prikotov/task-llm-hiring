<?php

declare(strict_types=1);

namespace App\Domain;

final class ImportDataException extends \RuntimeException
{
    public static function new(string $message): self
    {
        return new self($message);
    }
}

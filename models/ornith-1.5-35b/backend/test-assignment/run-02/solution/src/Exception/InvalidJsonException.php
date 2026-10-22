<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidJsonException extends \RuntimeException
{
    public function __construct(string $message = 'Invalid JSON', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

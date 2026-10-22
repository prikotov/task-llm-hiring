<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidJsonlLineException extends \RuntimeException
{
    public function __construct(int $lineNumber, string $content, ?\Throwable $previous = null)
    {
        parent::__construct('Invalid JSON on line '.$lineNumber.': '.$content, 0, $previous);
    }
}

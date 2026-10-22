<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/** Ошибка входецких данных/справочника с человеческим описанием причины. */
final class ImportException extends \RuntimeException
{
    public static function invalid(string $context, string $reason): self
    {
        return new self(sprintf('%s: %s', $context, $reason));
    }
}

<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка входных данных (некорректный JSON/JSONL, нарушение структуры).
 * Команды перехватывают её и завершаются с кодом 1.
 */
final class InputError extends \RuntimeException
{
    public static function because(string $string): self
    {
        return new self($string);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Exception;

/**
 * Ошибка входного файла (формат JSON/JSONL, поля, значения).
 * Текст сообщения — человекочитаемый, выводится в stderr.
 */
final class FileFormatException extends \RuntimeException
{
    public static function invalidFile(string $section, string $detail): self
    {
        return new self(sprintf('%s: %s', $section, $detail));
    }
}

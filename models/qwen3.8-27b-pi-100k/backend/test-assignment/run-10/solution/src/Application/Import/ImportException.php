<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Entity\Provider;

/**
 * Ошибки импорта.
 */
final class ImportException extends \RuntimeException
{
    public static function json(string $msg): self
    {
        return new self('Некорректный JSON: ' . $msg);
    }

    public static function jsonlLine(int $line): self
    {
        return new self(sprintf('Некорректная строка JSONL (строка %d).', $line));
    }

    public static function priceFormat(string $msg): self
    {
        return new self('Некорректный формат справочника цен: ' . $msg);
    }

    public static function missingProvider(string $code): self
    {
        return new self(sprintf('Провайдер "%s" не найден в базе.', $code));
    }

    public static function missingModel(Provider $p, string $code): self
    {
        return new self(sprintf('Модель "%s/%s" не найдена в базе.', $p->getCode(), $code));
    }
}

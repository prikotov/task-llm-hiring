<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Заведомо некорректный вход (файл, структура, значения).
 * Команды переводят её в сообщение в stderr и код выхода 1.
 */
final class ImportInputException extends \RuntimeException
{
    public static function field(string $where, string $message): self
    {
        return new self(sprintf('%s: %s', $where, $message));
    }
}

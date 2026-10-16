<?php

declare(strict_types=1);

namespace App\Import;

/**
 * Ошибка импорта (файл не найден, некорректный JSON/JSONL, неверные поля).
 * Команды переводят её в сообщение в stderr и код возврата 1.
 */
final class ImportException extends \RuntimeException
{
}

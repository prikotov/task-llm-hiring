<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка приёма данных (некорректный JSON/JSONL, валидация полей).
 * Команды ловят её и завершаются кодом 1.
 */
final class ImportException extends \RuntimeException
{
}

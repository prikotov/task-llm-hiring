<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка входа данных (файл цен / JSONL с событиями) — приводит
 * к коду возврата 1 и сообщению в stderr.
 */
final class ImportException extends \RuntimeException
{
}

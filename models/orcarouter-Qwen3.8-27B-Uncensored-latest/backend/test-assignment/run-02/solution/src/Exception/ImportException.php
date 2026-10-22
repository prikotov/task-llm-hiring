<?php

declare(strict_types=1);

namespace App\Exception;

/** Ошибка приёма данных (некорректный JSON/JSONL, нарушенные инварианты входа). */
final class ImportException extends \RuntimeException
{
}

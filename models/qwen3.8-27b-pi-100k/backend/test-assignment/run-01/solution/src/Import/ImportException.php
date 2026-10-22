<?php

declare(strict_types=1);

namespace App\Import;

use RuntimeException;

/** Ошибка входных данных (файл/строка некорректна) — команда завершается кодом 1. */
final class ImportException extends RuntimeException
{
}

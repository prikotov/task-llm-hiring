<?php

declare(strict_types=1);

namespace App\Shared;

use RuntimeException;

/** Ошибка валидации входных данных импорта (команда завершается с кодом 1). */
final class ImportException extends RuntimeException
{
}

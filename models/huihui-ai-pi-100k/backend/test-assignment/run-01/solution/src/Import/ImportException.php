<?php

declare(strict_types=1);

namespace App\Import;

use RuntimeException;

/** Ошибка импорта, из-за которой команда завершается с кодом 1. */
final class ImportException extends RuntimeException
{
}

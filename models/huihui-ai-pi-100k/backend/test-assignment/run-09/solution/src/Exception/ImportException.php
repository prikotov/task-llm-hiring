<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

/** Ошибка приёма входных данных (справочника цен или событий). */
final class ImportException extends RuntimeException
{
}

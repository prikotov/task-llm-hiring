<?php

declare(strict_types=1);

namespace App\Shared;

use RuntimeException;

/** Ошибка входных данных (некорректный файл/строка). */
final class InvalidInput extends RuntimeException
{
}

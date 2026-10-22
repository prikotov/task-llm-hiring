<?php

declare(strict_types=1);

namespace App\Support;

/** Ошибка разбора/валидации входных данных; текст подходит для stderr. */
final class ImportError extends \RuntimeException
{
}

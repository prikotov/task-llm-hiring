<?php

declare(strict_types=1);

namespace App\Billing\Import;

use RuntimeException;

/**
 * Ошибка разбора/валидации входных данных (файлы цен и событий).
 * Команды преобразуют её в сообщение в stderr и код выхода 1.
 */
final class ImportException extends RuntimeException
{
}

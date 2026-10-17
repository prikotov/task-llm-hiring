<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка обработки входных данных (файл/строка некорректны) —
 * команды завершаются кодом 1.
 */
class ImportException extends \RuntimeException
{
}

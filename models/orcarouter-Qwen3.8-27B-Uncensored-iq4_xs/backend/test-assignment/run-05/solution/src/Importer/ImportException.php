<?php

declare(strict_types=1);

namespace App\Importer;

/**
 * Ошибка импорта/коррекции: неккорректные данные файла.
 * Команды перехватывают её и завершаются кодом 1.
 */
final class ImportException extends \RuntimeException
{
}

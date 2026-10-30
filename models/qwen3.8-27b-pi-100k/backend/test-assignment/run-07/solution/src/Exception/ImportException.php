<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка валидации/принятия входных данных (импорты, reprice).
 * Команда печатает сообщение в stderr и завершается с кодом 1.
 */
final class ImportException extends \RuntimeException
{
}

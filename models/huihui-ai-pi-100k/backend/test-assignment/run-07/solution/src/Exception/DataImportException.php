<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка входных данных (файл импорта или исправления): сообщение безопасно
 * показывать в stderr, код возврата команды должен быть 1.
 */
final class DataImportException extends \RuntimeException
{
}

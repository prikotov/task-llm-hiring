<?php

declare(strict_types=1);

namespace App\Shared\Exception;

/**
 * Некорректные входные данные (файл, JSON, JSONL-строка, параметры
 * команд). Команды перехватывают исключение, печатают сообщение в stderr
 * и завершают работу с кодом 1.
 */
final class BadInputException extends \RuntimeException
{
}

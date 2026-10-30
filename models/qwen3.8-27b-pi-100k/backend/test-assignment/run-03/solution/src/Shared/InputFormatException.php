<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Некорректные входные данные (файл, JSON/JSONL, формат даты или числа).
 *
 * Команды перехватывают это исключение, печатают сообщение в stderr
 * и завершаются с кодом 1.
 */
final class InputFormatException extends \RuntimeException
{
}

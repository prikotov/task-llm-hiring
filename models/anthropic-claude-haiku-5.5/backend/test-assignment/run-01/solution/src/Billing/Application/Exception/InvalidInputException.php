<?php

declare(strict_types=1);

namespace App\Billing\Application\Exception;

use RuntimeException;

/** Некорректные входные данные (файл, строка, поле). Приводит к коду выхода 1. */
final class InvalidInputException extends RuntimeException
{
}

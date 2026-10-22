<?php

declare(strict_types=1);

namespace App\Billing\Application\Exception;

/**
 * Ошибка входных данных: некорректный файл, строка и т.п.
 * Команды превращают её в код выхода 1 и сообщение в stderr.
 */
final class InputException extends \RuntimeException
{
}

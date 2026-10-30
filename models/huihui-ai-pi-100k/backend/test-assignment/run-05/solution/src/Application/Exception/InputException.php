<?php

declare(strict_types=1);

namespace App\Application\Exception;

/**
 * Ошибка входных данных (файл, JSON, ISO-время, формат справочника).
 * Команды переводят её в код выхода 1 и сообщение в stderr.
 */
final class InputException extends \RuntimeException
{
}

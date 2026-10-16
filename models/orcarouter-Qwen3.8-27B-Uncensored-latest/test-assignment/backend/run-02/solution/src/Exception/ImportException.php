<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка данных входного файла (справочника цен или событий) —
 * приводит к коду выхода 1 и сообщению в stderr.
 */
class ImportException extends \RuntimeException
{
}

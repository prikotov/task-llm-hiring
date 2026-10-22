<?php

declare(strict_types=1);

namespace App\Domain\Common;

/** Ошибка входных данных (некорректный JSON/JSONL, неверные поля). */
final class ImportFormatException extends \RuntimeException
{
}

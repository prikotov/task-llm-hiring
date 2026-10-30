<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Ошибка входных данных (некорректный JSON/JSONL, нарушение формата,
 * ссылок на справочник). Команды перевода её в код выхода 1.
 */
final class DataImportException extends \RuntimeException
{
}

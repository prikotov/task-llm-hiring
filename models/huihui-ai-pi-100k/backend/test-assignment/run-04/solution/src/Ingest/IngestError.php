<?php

declare(strict_types=1);

namespace App\Ingest;

/** Ошибка входных данных (файл/строка некорректны). Обращается в код выхода 1. */
final class IngestError extends \RuntimeException
{
}

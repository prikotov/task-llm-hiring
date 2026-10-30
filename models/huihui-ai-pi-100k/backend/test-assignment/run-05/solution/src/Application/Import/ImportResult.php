<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\Exception\InputException;

/**
 * Итог загрузки событий использования.
 *
 * @readonly
 */
final class ImportResult
{
    public function __construct(
        /** Количество строк в файле. */
        public readonly int $fileLines,
        /** Количество событий, фактически вставленных в БД. */
        public readonly int $inserted,
    ) {
    }

    public function duplicated(): int
    {
        return $this->fileLines - $this->inserted;
    }
}

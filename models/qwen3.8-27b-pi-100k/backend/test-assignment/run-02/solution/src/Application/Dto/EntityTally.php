<?php

declare(strict_types=1);

namespace App\Application\Dto;

/** Счётчик созданных/изменённых записей при idempotent-импорте. */
final class EntityTally
{
    public function __construct(
        public int $created = 0,
        public int $updated = 0,
    ) {
    }
}

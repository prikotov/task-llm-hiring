<?php

declare(strict_types=1);

namespace App\Domain\Usage\ValueObject;

enum RateStatus: string
{
    case Unrated = 'unrated';
    case Rated = 'rated';
}

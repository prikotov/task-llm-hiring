<?php

declare(strict_types=1);

namespace App\Domain\Billing;

enum EventStatus: string
{
    case Pending = 'pending';
    case Rated = 'rated';
}

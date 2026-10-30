<?php

declare(strict_types=1);

namespace App\Entity;

final class UsageEventStatus
{
    public const NEW = 'new';
    public const UNRATED = 'unrated';
    public const RATED = 'rated';

    private function __construct()
    {
    }
}

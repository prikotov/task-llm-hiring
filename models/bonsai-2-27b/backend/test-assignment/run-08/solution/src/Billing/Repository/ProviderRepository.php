<?php

declare(strict_types=1);

namespace App\Billing\Repository;

use App\Entity\Provider;

/**
 * Справочник провайдеров.
 */
final class ProviderRepository
{
    private readonly \App\Entity\Repository\RepositoryFactory $factory;

    public function __construct(\App\Entity\Repository\RepositoryFactory $factory)
    {
        $this->factory = $factory;
    }
}

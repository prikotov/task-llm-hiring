<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventRepository;

/**
 * In-memory реализация UsageEventRepository для юнит-тестов.
 *
 * @internal
 */
final class InMemoryEvents implements UsageEventRepository
{
    /**
     * @param callable(): UsageEvent[] $supplier
     */
    public function __construct(private \Closure $supplier)
    {
    }

    /** @return UsageEvent[] */
    public function findUnrated(): array
    {
        return (array) ($this->supplier)();
    }
}

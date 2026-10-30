<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Журнал ретро-корректировки цен (bonus: app:billing:reprice).
 */
#[ORM\Entity]
#[ORM\Table(name: 'reprice_log')]
final class RepriceLogEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    public string $providerRequestId = '';

    #[ORM\Column(name: 'old_cost_usd', type: 'string', length: 30)]
    public string $oldCostUsd = '0';

    #[ORM\Column(name: 'new_cost_usd', type: 'string', length: 30)]
    public string $newCostUsd = '0';

    #[ORM\Column(name: 'delta_usd', type: 'string', length: 30)]
    public string $deltaUsd = '0';

    #[ORM\Column(name: 'applied_at', type: 'tz_date_string')]
    public string $appliedAt = '';
}

<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_event_rows')]
final class UsageEventRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $provider = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    public string $providerRequestId = '';

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $model = '';

    #[ORM\Column(type: Types::STRING, length: 255, name: 'user_id')]
    public string $user = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $ts;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 0)]
    public string $tokensInput = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 0)]
    public string $tokensOutput = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 0)]
    public string $tokensCacheRead = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 0)]
    public string $tokensCacheWrite = '0';
}

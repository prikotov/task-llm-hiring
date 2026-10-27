<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ratings')]
#[ORM\UniqueConstraint(name: 'uq_rating_event_id', columns: ['event_id'])]
final class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int|null $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    public int $eventId = 0;

    #[ORM\Column(type: Types::BIGINT)]
    public int $priceRowId = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 10)]
    public string $cost = '0';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $ratedAt;
}

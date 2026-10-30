<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Append-only журнал стоимости событий: каждая строка — «состояние
 * стоимости события на момент». Добавится: rate при первичной
 * тарификации, reprice — при ретро-коррекции цены.
 *
 * Текущая стоимость события = значение в его последнем рейтинге
 * (по id). Ранние строки — история для аудита (не удаляются).
 *
 * Журнал служит одновременно:
 *  - механизмом idempotentности `app:billing:rate` (event уже имеет
 *    последнюю строку → не пересчитываем),
 *  - источником отчёта (`app:billing:report`) — суммируется последнее
 *    значение на event,
 *  - журналом rетро-коррекции (reprice).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_ratings')]
#[ORM\Index(name: 'idx_usage_ratings_event', columns: ['event_id', 'id'])]
class UsageRating
{
    public const SOURCE_RATE = 'rate';
    public const SOURCE_REPRICE = 'reprice';

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsageEvent::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?UsageEvent $event = null;

    /** Стоимость на момент записи, десятичная строка 6 знаков (HALF_UP). */
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $amount = '0';

    /** ID строки прайса (usage_event), по которой посчитана стоимость. */
    #[ORM\Column(name: 'price_id', type: Types::BIGINT, nullable: true)]
    private ?int $priceId = null;

    /** Кто записал: 'rate' | 'reprice'. */
    #[ORM\Column(length: 16, nullable: false)]
    private string $source = self::SOURCE_RATE;

    /** UTC-момент записи. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->createdAt = $now;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function event(): ?UsageEvent
    {
        return $this->event;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function priceId(): ?int
    {
        return $this->priceId;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public static function create(
        UsageEvent $event,
        string $amount,
        ?int $priceId,
        string $source = self::SOURCE_RATE,
    ): self {
        $r = new self();
        $r->event = $event;
        $r->amount = $amount;
        $r->priceId = $priceId;
        $r->source = $source;
        return $r;
    }

    public function assign(UsageEvent $event): void
    {
        $this->event = $event;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Фактическое событие использования (запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id): повторная доставка
 * игнорируется при импорте.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\Index(name: 'idx_usage_event_ts', columns: ['ts'])]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_provider_request', columns: ['provider_id', 'provider_request_id'])]
class UsageEvent extends AbstractEntity
{
    public const STATUS_NEW = 'new';
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'usageEvents')]
        #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
        private Provider $provider,
        #[ORM\Column(type: 'string', length: 128)]
        private string $providerRequestId,
        #[ORM\ManyToOne(targetEntity: Model::class)]
        #[ORM\JoinColumn(name: 'model_id', nullable: false)]
        private Model $model,
        #[ORM\Column(type: 'string', length: 255)]
        private string $userName,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $ts,
        #[ORM\Column(type: 'integer')]
        private int $tokensInput,
        #[ORM\Column(type: 'integer')]
        private int $tokensOutput,
        #[ORM\Column(type: 'integer')]
        private int $tokensCacheRead,
        #[ORM\Column(type: 'integer')]
        private int $tokensCacheWrite,
        #[ORM\Column(type: 'string', length: 16)]
        private string $status = self::STATUS_NEW,
    ) {
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getUser(): string
    {
        return $this->userName;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * История расчётов события: текущая — последняя по `effective_from`.
     *
     * @var Collection<int, UsageRating>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: UsageRating::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $ratings;

    /**
     * @var Collection<int, UsageRating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }
}

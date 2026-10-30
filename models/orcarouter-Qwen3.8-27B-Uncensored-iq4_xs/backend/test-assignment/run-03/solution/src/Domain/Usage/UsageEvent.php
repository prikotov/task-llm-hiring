<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Model\PriceLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_event')]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_key', columns: ['provider', 'provider_request_id'])]
class UsageEvent
{
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /** @var string код провайдера */
    #[ORM\Column(type: 'string', length: 64)]
    private string $provider = '';

    /** @var string идентификатор запроса со стороны провайдера */
    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId = '';

    /** @var string код модели */
    #[ORM\Column(type: 'string', length: 64)]
    private string $model = '';

    /** @var string идентификатор пользователя */
    #[ORM\Column(name: 'user_code', type: 'string', length: 128)]
    private string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite = 0;

    /** @var string self::STATUS_* */
    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_UNRATED;

    /** @var string|null округлённая стоимость события (6 знаков после точки) */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $cost = null;

    #[ORM\ManyToOne(targetEntity: PriceLine::class)]
    #[ORM\JoinColumn(name: 'price_line_id', referencedColumnName: 'id', nullable: true)]
    private ?PriceLine $priceLine = null;

    public function __construct()
    {
        $this->ts = new \DateTimeImmutable('2000-01-01 00:00:00', new \DateTimeZone('UTC'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(string $providerRequestId): self
    {
        $this->providerRequestId = $providerRequestId;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function setTs(\DateTimeImmutable $ts): self
    {
        $this->ts = $ts;

        return $this;
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

    public function setTokens(int $input, int $output, int $cacheRead, int $cacheWrite): self
    {
        $this->tokensInput = $input;
        $this->tokensOutput = $output;
        $this->tokensCacheRead = $cacheRead;
        $this->tokensCacheWrite = $cacheWrite;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPriceLine(): ?PriceLine
    {
        return $this->priceLine;
    }

    public function setPriceLine(?PriceLine $priceLine): self
    {
        $this->priceLine = $priceLine;

        return $this;
    }
}

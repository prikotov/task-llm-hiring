<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Decimal;
use App\Domain\Repository\PriceRowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRowRepository::class)]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(
    name: 'uniq_price_row',
    columns: ['provider_id', 'model_id', 'valid_from'],
)]
class PriceRow
{
    /** масштаб bcadd/bcmul для промежуточных вычислений. */
    private const SCALE = 16;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    public Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    public Model $model;

    /**
     * Момент, с которого действует ставка (UTC), включительно.
     */
    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string')]
    public string $currency = 'USD';

    /** per_1m_tokens | per_1m_tokens_unified */
    #[ORM\Column(type: 'string')]
    public string $unit = 'per_1m_tokens';

    #[ORM\Column(name: 'rate_input', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $rateAny = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * ставка за 1 токен (сумма по видам, per_1m_tokens).
     */
    public function costPer1m(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): Decimal {
        $sum = $this->zero();

        $sum = $sum->add(bcmul((string) $tokensInput, $this->rateInput ?? '0', self::SCALE));
        $sum = $sum->add(bcmul((string) $tokensOutput, $this->rateOutput ?? '0', self::SCALE));
        $sum = $sum->add(bcmul((string) $tokensCacheRead, $this->rateCacheRead ?? '0', self::SCALE));
        $sum = $sum->add(bcmul((string) $tokensCacheWrite, $this->rateCacheWrite ?? '0', self::SCALE));

        return $sum;
    }

    /**
     * ставка за 1 токен единой цены (per_1m_tokens_unified).
     *
     * Возвращает sum(tokens) × rate_any в расчёте «за 1 000 000 токенов» —
     * Pricer делит результат на миллион.
     */
    public function costUnified(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): Decimal {
        $total = bcadd(
            bcadd((string) $tokensInput, (string) $tokensOutput, self::SCALE),
            bcadd((string) $tokensCacheRead, (string) $tokensCacheWrite, self::SCALE),
            self::SCALE,
        );

        return new Decimal(bcmul($total, $this->rateAny ?? '0', self::SCALE));
    }

    private function zero(): Decimal
    {
        return new Decimal('0');
    }
}

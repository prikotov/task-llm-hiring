<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\ProviderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    public string $code = '';

    #[ORM\Column(type: 'string')]
    public string $name = '';

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Model::class)]
    private $models;

    /**
     * начало оф-пикового окна (ЧЧ:ММ, UTC), включительно;
     * null — скидка отсутствует.
     */
    #[ORM\Column(name: 'off_peak_start', type: 'string', nullable: true)]
    public ?string $offPeakStart = null;

    /**
     * конец оф-пикового окна (ЧЧ:ММ, UTC), исключительно;
     * null — скидка отсутствует.
     */
    #[ORM\Column(name: 'off_peak_end', type: 'string', nullable: true)]
    public ?string $offPeakEnd = null;

    /**
     * множитель стоимости попавших в окно событий;
     * null — скидка отсутствует.
     */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'decimal', nullable: true, precision: 20, scale: 6)]
    public ?string $offPeakMultiplier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffPeak(): OffPeakWindow
    {
        return new OffPeakWindow(
            $this->offPeakStart,
            $this->offPeakEnd,
            $this->offPeakMultiplier,
        );
    }
}

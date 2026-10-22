<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_provider')]
class Provider
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    public private(set) ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 255, unique: true)]
        public private(set) string $code,
        #[ORM\Column(length: 255)]
        public private(set) string $name,
        #[ORM\Column(type: 'json', nullable: true)]
        private ?array $offPeak,
    ) {
    }

    public function update(string $name, ?array $offPeak): void
    {
        $this->name = $name;
        $this->offPeak = $offPeak;
    }

    public function multiplierAt(\DateTimeImmutable $instant): string
    {
        if ($this->offPeak === null) {
            return '1';
        }
        $window = new OffPeak($this->offPeak['window_start'], $this->offPeak['window_end'], $this->offPeak['multiplier']);

        return $window->contains($instant) ? $window->multiplier : '1';
    }
}

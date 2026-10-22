<?php

declare(strict_types=1);

namespace App\Billing\Domain\Pricing;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Model\RateCard;
use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Usage\TokenUsage;

/**
 * Строка истории цен: с момента valid_from (включительно) данные ставки
 * действуют для модели, пока их не сменит строка с более поздним valid_from.
 *
 * id нужен, чтобы уже рассчитанные события помнили, какой строкой они
 * тарифицированы — на этом строится ретро-коррекция цены.
 */
final readonly class PriceRow
{
    public function __construct(
        public ?int $id,
        public ModelReference $model,
        public \DateTimeImmutable $validFrom,
        public string $currency,
        public RateCard $rateCard,
    ) {
        if ('' === trim($currency)) {
            throw new \InvalidArgumentException('Валюта обязательна');
        }
    }

    public function isEffectiveAt(\DateTimeImmutable $at): bool
    {
        return $this->validFrom <= $at;
    }

    /**
     * Натуральный ключ справочника: (provider, model, valid_from).
     */
    public function naturalKey(): string
    {
        return $this->model->providerCode
            .'|'.$this->model->modelCode
            .'|'.$this->validFrom->format('Y-m-d\TH:i:s\Z');
    }

    public function withId(int $id): self
    {
        return new self($id, $this->model, $this->validFrom, $this->currency, $this->rateCard);
    }

    public function withRateCard(RateCard $rateCard): self
    {
        return new self($this->id, $this->model, $this->validFrom, $this->currency, $rateCard);
    }

    /**
     * Итоговая стоимость события: точные вычисления без округления,
     * затем одно округление HALF_UP до 6 знаков (TASK п. 4).
     */
    public function costFor(TokenUsage $usage, ?OffPeakWindow $offPeak): Money
    {
        $cost = $this->unroundedCost($usage);

        if (null !== $offPeak && $offPeak->multiplier->compareTo(Money::fromString('1')) !== 0) {
            $cost = $cost->multiply($offPeak->multiplier);
        }

        return $cost->roundHalfUp();
    }

    private function unroundedCost(TokenUsage $usage): Money
    {
        $million = Money::fromString('1000000', Money::INTERNAL_SCALE);

        if (\App\Billing\Domain\Model\TariffUnit::PerMillionUnified === $this->rateCard->unit()) {
            return Money::fromString((string) $usage->total(), Money::INTERNAL_SCALE)
                ->multiply($this->rateCard->rate('any'))
                ->divide($million);
        }

        $total = Money::zero(Money::INTERNAL_SCALE);

        $byKind = [
            'input' => $usage->input->value,
            'output' => $usage->output->value,
            'cache_read' => $usage->cacheRead->value,
            'cache_write' => $usage->cacheWrite->value,
        ];

        foreach ($byKind as $kind => $tokens) {
            $total = $total->add(
                Money::fromString((string) $tokens, Money::INTERNAL_SCALE)
                    ->multiply($this->rateCard->rate($kind))
                    ->divide($million),
            );
        }

        return $total;
    }
}

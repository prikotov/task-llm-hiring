<?php

declare(strict_types=1);

namespace App\Billing\Domain\Pricing;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Usage\TokenUsage;

/**
 * Справочник цен: строки истории цен + оф-пиковые окна провайдеров.
 * Доменный сервис отвечает на вопросы "какая строка действует" и
 * "сколько стоит событие" — без знания о том, откуда данные (БД, файл).
 */
final class TariffBook
{
    /**
     * @param iterable<PriceRow>            $priceRows
     * @param array<string, OffPeakWindow>  $offPeakByProvider
     */
    public function __construct(
        private readonly iterable $priceRows,
        private readonly array $offPeakByProvider,
    ) {
    }

    /**
     * Строка цен с наибольшим valid_from ≤ $at (TASK п. 1).
     * null — подходящей строки нет (событие unrated).
     */
    public function priceAt(ModelReference $model, \DateTimeImmutable $at): ?PriceRow
    {
        $best = null;

        foreach ($this->priceRows as $row) {
            if (!$row->model->equals($model) || !$row->isEffectiveAt($at)) {
                continue;
            }

            if (null === $best || $row->validFrom > $best->validFrom) {
                $best = $row;
            }
        }

        return $best;
    }

    public function offPeakFor(string $providerCode): ?OffPeakWindow
    {
        return $this->offPeakByProvider[$providerCode] ?? null;
    }

    /**
     * Стоимость события: выбор цены (п. 1), схема тарификации (п. 2),
     * оф-пик (п. 3), одно округление до 6 знаков (п. 4).
     * Возвращает null, если цена не найдена.
     */
    public function quote(ModelReference $model, TokenUsage $usage, \DateTimeImmutable $at): ?Quote
    {
        $row = $this->priceAt($model, $at);

        if (null === $row) {
            return null;
        }

        $offPeak = $this->offPeakFor($model->providerCode);

        return new Quote(
            row: $row,
            cost: $row->costFor($usage, $offPeak),
            appliedOffPeak: $offPeak?->contains($at) === true ? $offPeak : null,
        );
    }
}

/**
 * Оценка события: по какой строке и сколько. appliedOffPeak — только если
 * событие реально попало в окно (для наблюдаемости и отладки).
 */
final readonly class Quote
{
    public function __construct(
        public PriceRow $row,
        public Money $cost,
        public ?OffPeakWindow $appliedOffPeak,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\DecimalMath;
use App\Domain\Billing\OffPeakWindow;
use App\Domain\Billing\RateCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Бонус: ретро-коррекция цены. Применяет исправленные строки цен (upsert по
 * (provider, model, valid_from)), затем пересчитывает события, тарифицированные
 * заменёнными строками. Суммы отчёта не задваиваются (стоимость события
 * перезаписывается, события не дублируются).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceRowWriter $priceRowWriter,
        private readonly BillingRepository $repo,
        private readonly RateCalculator $calculator,
    ) {
    }

    /**
     * Возвращает [число пересчитанных событий, дельта в USD (6 знаков)].
     *
     * @return array{0: int, 1: string}
     */
    public function reprice(string $json): array
    {
        $data = json_decode($json, true, 128, \JSON_THROW_ON_ERROR);
        if (!\is_array($data)) {
            throw new \RuntimeException('Некорректный JSON: ожидается объект верхнего уровня');
        }

        $prices = Validators::list($data, 'prices');
        $touchedRowIds = [];

        foreach ($prices as $raw) {
            if (!\is_array($raw)) {
                throw new \DomainException('Строка цены коррекции должна быть объектом');
            }

            $row = $this->priceRowWriter->upsert($raw);

            if (null !== $row->getId()) {
                $touchedRowIds[] = $row->getId();
            }
        }

        $events = $this->repo->findRatedEventsByPriceRowIds($touchedRowIds);

        $oldSum = '0';
        $newSum = '0';
        $count = 0;

        foreach ($events as $event) {
            $row = $event->getPriceRow();
            if (null === $row) {
                continue;
            }

            $oldSum = DecimalMath::add($oldSum, $event->getCost() ?? '0');

            $offPeak = OffPeakWindow::fromProvider($row->getProvider());
            $newCost = $this->calculator->calculate($event, $row, $offPeak);
            $event->setCost($newCost->amount());

            $newSum = DecimalMath::add($newSum, $newCost->amount());
            $count++;
        }

        $this->em->flush();

        return [$count, DecimalMath::format6(DecimalMath::sub($newSum, $oldSum))];
    }
}

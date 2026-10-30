<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Import\PriceImportService;
use App\Domain\Money;
use App\Domain\PriceUnit;
use App\Infrastructure\CostRecord;
use App\Infrastructure\CostRecordRepository;
use App\Infrastructure\Model;
use App\Infrastructure\Provider;
use App\Infrastructure\PriceRow;
use App\Infrastructure\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (app:billing:reprice).
 *
 * Применяет новые строки цен (upsert), находит события,
 * расчёт которых был привязан к заменённой строке цен (rateKey),
 * пересчитывает их по новым ставкам, обновляя текущую стоимость.
 * Исходная стоимость (originalCost) остаётся — журнал из первого расчёта.
 */
final readonly class RepriceService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PricingCalculator $calculator,
    ) {
    }

    /**
     * @return array{events: int, delta: string}
     */
    public function repriceFromFile(string $file): array
    {
        $doc = PriceImportService::parseFile($file);

        if ($doc->prices === []) {
            throw new \InvalidArgumentException('Файл коррекции: «prices» пусто или отсутствует');
        }

        $providerRepo = $this->em->getRepository(Provider::class);
        $modelRepo = $this->em->getRepository(Model::class);
        $priceRepo = $this->em->getRepository(PriceRow::class);
        $costRepo = $this->em->getRepository(CostRecord::class);

        $events = 0;
        $delta = '0';

        foreach ($doc->prices as $p) {
            $provider = $providerRepo->findByCode($p->provider);
            if ($provider === null) {
                throw new \InvalidArgumentException(sprintf('Провайдер "%s" не найден в базе', $p->provider));
            }
            if ($modelRepo->findByKeys($p->provider, $p->model) === null) {
                throw new \InvalidArgumentException(sprintf('Модель "%s/%s" не найдена в базе', $p->provider, $p->model));
            }

            $validFrom = PriceImportService::parseUtc($p->validFrom);
            $validFromEpoch = (clone $validFrom)->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
            $rates = PriceUnit::normalizeRates($p->unit, $p->rates);

            $existing = $priceRepo->findOneBy([
                'provider' => $p->provider,
                'model' => $p->model,
                'validFromEpoch' => $validFromEpoch,
            ]);
            if ($existing === null) {
                $row = new PriceRow($p->provider, $p->model, $validFrom, $p->currency, $p->unit, $rates);
                $this->em->persist($row);
                $this->em->flush();
                $rateKey = PriceRow::rateKey($p->provider, $p->model, $validFromEpoch);
            } else {
                // Найдена строка — пересчитываем события, связанные с ней
                $rateKey = PriceRow::rateKey($p->provider, $p->model, $validFromEpoch);
                $row = $existing;

                // Обновить сами ставки, если они изменены
                $existing->update(currency: $p->currency, unit: $p->unit, rates: $rates);
            }

            // Найди все события (cost records) с этим rateKey
            $records = $costRepo->createQueryBuilder('c')
                ->where('c.rateKey = :key')
                ->setParameter('key', $rateKey)
                ->getQuery()
                ->getResult();

            /** @var CostRecord[] $records */
            foreach ($records as $record) {
                $event = $this->em->find(UsageEvent::class, $record->eventIdValue());
                if ($event === null) {
                    continue;
                }

                $newCost = $this->calculator->eventCost(
                    $event, $row, $provider
                )->roundedCost;
                $oldCost = $record->currentCostValue();

                $delta = Money::add(Money::add($delta, Money::mul('-1', $oldCost)), $newCost);

                $record->setCurrentCost($newCost);
                $record->setRateKey($rateKey);

                $events += 1;
            }
        }

        $this->em->flush();

        return [
            'events' => $events,
            'delta' => Money::format(Money::round($delta, 6)),
        ];
    }
}

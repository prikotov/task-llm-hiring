<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Billing;
use App\Billing\Entity\Model;
use App\Billing\Entity\PriceRow;
use App\Billing\Entity\Provider;
use App\Billing\Entity\UsageEvent;
use App\Billing\Import\Dto\PriceRowInput;
use App\Billing\Import\Dto\PricesFile;
use App\Billing\Import\ImportException;
use App\Billing\Pricing\RateCalculator;
use App\Billing\Repository\PriceRowRepository;
use App\Billing\Repository\UsageEventRepository;
use App\Billing\ValueObject\Decimal;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус): провайдер присылает исправленную строку цен.
 *
 *  - строки upsert'ятся по (provider, model, valid_from);
 *  - провайдер и модель обязаны уже существовать в справочнике (секции
 *    providers/models в файле коррекции игнорируются);
 *  - пересчитываются события, тарифицированные заменёнными строками
 *    (ссылка price_row_id сохранена на момент расчёта);
 *  - отчёт после reprice показывает пересчитанные суммы без задвоений
 *    (стоимость события перезаписывается, история событий не дублируется).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RateCalculator $calculator,
        private readonly PriceRowRepository $priceRows,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function reprice(PricesFile $file): RepriceResult
    {
        return $this->em->wrapInTransaction(function () use ($file): RepriceResult {
            /** @var array<int, PriceRow> $changedRows */
            $changedRows = [];

            foreach ($file->priceRows as $input) {
                $this->assertKnownProviderModel($input);

                $row = $this->priceRows->findByNaturalKey($input->provider, $input->model, $input->validFrom);
                if ($row === null) {
                    // Новая строка цен (впервые): на неё не могли ссылаться рассчитанные события.
                    $row = new PriceRow(
                        0,
                        $input->provider,
                        $input->model,
                        $input->validFrom,
                        $input->currency,
                        $input->price->unit->value,
                        self::encodeRates($input),
                    );
                    $this->em->persist($row);
                    continue;
                }

                if ($this->rowUnchanged($row, $input)) {
                    continue;
                }

                $row->apply($input);
                $changedRows[$row->id] = $row;
            }

            $this->em->flush();

            $delta = Decimal::zero();
            $count = 0;
            $now = new DateTimeImmutable();

            foreach ($changedRows as $rowId => $row) {
                $events = $this->events->findRatedByPriceRow($rowId);
                foreach ($events as $event) {
                    $old = Decimal::of($event->cost ?? '0');
                    $new = $this->eventCost($event, $row, $now);
                    $event->markRated($new, $row->id, $now);
                    $delta = $delta->add($new->sub($old));
                    $count++;
                }
            }

            $this->em->flush();

            return new RepriceResult($count, $delta->toFixed(Billing::COST_DECIMALS));
        });
    }

    /**
     * @throws ImportException
     */
    private function assertKnownProviderModel(PriceRowInput $input): void
    {
        if ($this->em->find(Provider::class, $input->provider) === null) {
            throw new ImportException(sprintf('Провайдер "%s" не найден в справочнике — добавьте его через app:prices:import.', $input->provider));
        }
        if ($this->em->find(Model::class, ['provider' => $input->provider, 'code' => $input->model]) === null) {
            throw new ImportException(sprintf('Модель "%s/%s" не найдена в справочнике — добавьте её через app:prices:import.', $input->provider, $input->model));
        }
    }

    private function rowUnchanged(PriceRow $row, PriceRowInput $input): bool
    {
        if ($row->currency !== $input->currency || $row->unit !== $input->price->unit->value) {
            return false;
        }
        /** @var array<string, mixed> $stored */
        $stored = json_decode($row->ratesJson, true, 64, JSON_THROW_ON_ERROR);
        foreach ($input->price->rates as $kind => $newRate) {
            if (!isset($stored[$kind]) || bccomp((string) $stored[$kind], $newRate->value(), 16) !== 0) {
                return false;
            }
        }

        return true;
    }

    private function eventCost(UsageEvent $event, PriceRow $row, DateTimeImmutable $now): Decimal
    {
        $provider = $this->em->find(Provider::class, $event->provider);
        $offPeak = $provider?->offPeak();
        $multiplier = $offPeak !== null && $offPeak->isOffPeak($event->ts) ? $offPeak->multiplier : null;

        // Окно оф-пика берём из актуального справочника на момент коррекции.
        return $this->calculator
            ->cost($row->price(), $event->usage(), $multiplier)
            ->round(Billing::COST_DECIMALS);
    }

    private static function encodeRates(PriceRowInput $input): string
    {
        $rates = [];
        foreach ($input->price->rates as $kind => $rate) {
            $rates[$kind] = $rate->value();
        }

        return json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}

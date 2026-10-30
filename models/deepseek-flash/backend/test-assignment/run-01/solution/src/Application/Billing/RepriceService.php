<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Import\PriceFileData;
use App\Domain\Money\Decimal;
use App\Domain\Pricing\CostCalculator;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\RateCard;
use App\Infrastructure\Import\PriceFileParser;
use App\Infrastructure\Persistence\Doctrine\Entity\Price;
use App\Infrastructure\Persistence\Doctrine\Entity\Provider;
use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
use App\Infrastructure\Persistence\Doctrine\Entity\UsageEvent;
use App\Infrastructure\Persistence\Doctrine\Entity\UsageEventReprice;
use App\Infrastructure\Persistence\Doctrine\Repository\PriceRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderModelRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\UsageEventRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

use function sprintf;

/**
 * Retroactive price correction.
 *
 * The correction file only supplies price rows; provider and model definitions
 * are deliberately resolved from the database. Already rated events in the
 * interval of every corrected/inserted row are recalculated. The current cost
 * is updated in place and every change is recorded in an audit journal.
 */
final readonly class RepriceService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PriceFileParser $parser,
        private ProviderRepository $providers,
        private ProviderModelRepository $models,
        private PriceRepository $prices,
        private UsageEventRepository $events,
        private CostCalculator $calculator,
    ) {
    }

    public function repriceFile(string $path): RepriceResult
    {
        return $this->reprice($this->parser->parse($path));
    }

    public function reprice(PriceFileData $data): RepriceResult
    {
        return $this->entityManager->wrapInTransaction(function () use ($data): RepriceResult {
            /** @var array<string, array{model: ProviderModel, validFrom: DateTimeImmutable}> $affected */
            $affected = [];
            /** @var array<string, Price> $priceCache */
            $priceCache = [];

            foreach ($data->prices as $definition) {
                $provider = $this->providers->findByCode($definition->providerCode);
                if ($provider === null) {
                    throw new InvalidArgumentException(sprintf(
                        'Cannot reprice: provider "%s" does not exist in the catalogue.',
                        $definition->providerCode,
                    ));
                }

                $model = $this->models->findByProviderAndCode($provider, $definition->modelCode);
                if ($model === null) {
                    throw new InvalidArgumentException(sprintf(
                        'Cannot reprice: model "%s/%s" does not exist in the catalogue.',
                        $definition->providerCode,
                        $definition->modelCode,
                    ));
                }

                [$input, $output, $cacheRead, $cacheWrite, $any] = self::rateStrings($definition->rateCard);
                $key = $definition->providerCode . "\0" . $definition->modelCode
                    . '|' . $definition->validFrom->format('U.u');
                $price = $priceCache[$key] ?? $this->prices->findForModelAt($model, $definition->validFrom);

                if ($price === null) {
                    $price = new Price(
                        $model,
                        $definition->validFrom,
                        $definition->currency,
                        $definition->rateCard->unit,
                        $input,
                        $output,
                        $cacheRead,
                        $cacheWrite,
                        $any,
                    );
                    $this->entityManager->persist($price);
                }

                $priceCache[$key] = $price;
                $price->update(
                    $definition->currency,
                    $definition->rateCard->unit,
                    $input,
                    $output,
                    $cacheRead,
                    $cacheWrite,
                    $any,
                );

                $affected[$key] = ['model' => $model, 'validFrom' => $definition->validFrom];
            }

            $this->entityManager->flush();

            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $repricedEvents = 0;
            $delta = Decimal::zero();

            /** @var array<string, Provider> $providerCache */
            $providerCache = [];

            foreach ($affected as $interval) {
                $model = $interval['model'];
                $validFrom = $interval['validFrom'];
                $next = $this->prices->findNextForModelAfter($model, $validFrom);
                $provider = $providerCache[$model->provider()->code()]
                    ??= $model->provider();

                foreach ($this->events->findRatedInInterval($model, $validFrom, $next?->validFrom()) as $event) {
                    $price = $this->prices->findApplicableForModel($model, $event->occurredAt());
                    if ($price === null) {
                        continue;
                    }

                    $oldCost = $event->costUsd();
                    if ($oldCost === null) {
                        continue;
                    }

                    $newCost = $this->calculator->calculate(
                        $event->usage(),
                        $price->rateCard(),
                        $provider->offPeakWindow(),
                        $event->occurredAt(),
                    );

                    ++$repricedEvents;
                    $delta = $delta->add($newCost->subtract($oldCost));

                    if (!$newCost->equals($oldCost)) {
                        $this->recordCorrection($event, $price, $oldCost, $newCost, $now);
                    }

                    $event->applyReprice($price, $newCost, $now);
                }
            }

            $this->entityManager->flush();

            return new RepriceResult($repricedEvents, $delta);
        });
    }

    private function recordCorrection(
        UsageEvent $event,
        Price $newPrice,
        Decimal $oldCost,
        Decimal $newCost,
        DateTimeImmutable $now,
    ): void {
        $oldPrice = $event->price();

        $this->entityManager->persist(new UsageEventReprice(
            $event,
            $oldPrice,
            $newPrice,
            $oldCost->format(6),
            $newCost->format(6),
            $now,
        ));
    }

    /**
     * @return array{0: ?string, 1: ?string, 2: ?string, 3: ?string, 4: ?string}
     */
    private static function rateStrings(RateCard $card): array
    {
        return match ($card->unit) {
            PriceUnit::PerMillionTokens => [
                $card->input?->format(18),
                $card->output?->format(18),
                $card->cacheRead?->format(18),
                $card->cacheWrite?->format(18),
                null,
            ],
            PriceUnit::PerMillionTokensUnified => [
                null,
                null,
                null,
                null,
                $card->any?->format(18),
            ],
        };
    }
}

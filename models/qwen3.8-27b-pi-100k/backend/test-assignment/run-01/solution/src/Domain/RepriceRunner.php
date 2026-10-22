<?php

declare(strict_types=1);

namespace App\Domain;

use App\Entity\EventRating;
use App\Entity\RepriceCorrection;
use App\Import\ImportException;
use App\Import\PriceCatalog;
use App\Import\PricesPayload;
use App\Repository\EventRatingRepository;
use App\Repository\ProviderRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Ретро-коррекция цен (бонус).
 *
 * 1. Строки из файла коррекции применяются upsert'ом по
 *    (provider, model, valid_from); провайдер и модель обязаны уже существовать
 *    в справочнике (секции providers/models файле не обязательны).
 * 2. Пересчитываются все уже рассчитанные события, тарифицированные по
 *    заменённым строкам, — с теми же правилами (схема, оф-пик, полу-округление
 *    HALF_UP до 6 знаков).
 * 3. event_ratings получает новые стоимости (отчёт не задваивается — отчёт
 *    читает актуальные стоимости), по каждому изменённому событию пишется
 *    аудит-запись reprice_corrections.
 *
 * n — число пересчитанных событий; дельта — новая сумма минус старая.
 */
final class RepriceRunner
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCatalog $priceCatalog,
        private readonly EventRatingRepository $eventRatingRepository,
        private readonly ProviderRepository $providerRepository,
    ) {
    }

    public function reprice(PricesPayload $payload): RepriceResult
    {
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            // Файл может содержать дубли одной и той же строки (provider, model, validFrom) —
            // последние выигрывают, каждая ключевая строка применяется один раз.
            $appliedRows = [];
            foreach ($payload->priceRows as $spec) {
                $key = $spec->provider."\x1f".$spec->model."\x1f".$spec->validFrom->getTimestamp();
                $appliedRows[$key] = $this->priceCatalog->upsert($spec)['row'];
            }

            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $delta = '0';
            $eventsRepriced = 0;

            foreach ($appliedRows as $priceRow) {
                $provider = $this->providerRepository->findByCode($priceRow->getProviderCode());
                $offPeak = $provider !== null && $provider->hasOffPeak() ? $provider->offPeakWindow() : null;
                $ratings = $this->eventRatingRepository->findByPriceRow($priceRow);

                foreach ($ratings as $rating) {
                    /** @var EventRating $rating */
                    $event = $rating->getEvent();
                    $newCost = CostCalculator::eventCost(
                        $priceRow->getUnit(),
                        $priceRow->getRates(),
                        $event->getTokensInput(),
                        $event->getTokensOutput(),
                        $event->getTokensCacheRead(),
                        $event->getTokensCacheWrite(),
                        $offPeak,
                        CostCalculator::minuteOfDayUtc($event->getTs()),
                    );

                    $oldCost = $rating->getCost();
                    if (Money::cmp($oldCost, $newCost) !== 0) {
                        $rating->setCost($newCost);
                        $this->em->persist(new RepriceCorrection($event, $priceRow, $oldCost, $newCost, $now));
                    }

                    ++$eventsRepriced;
                    $delta = Money::add($delta, Money::sub($newCost, $oldCost));
                }
            }

            $this->em->flush();
            $conn->commit();

            return new RepriceResult($eventsRepriced, Money::format($delta));
        } catch (ImportException $e) {
            $this->rollback($conn->isTransactionActive());
            throw $e;
        } catch (Throwable $e) {
            $this->rollback($conn->isTransactionActive());
            throw new ImportException('Корректировка цен завершена ошибкой: ' . $e->getMessage(), 0, $e);
        }
    }

    private function rollback(bool $inTransaction): void
    {
        if ($inTransaction) {
            $this->em->getConnection()->rollBack();
        }
    }
}

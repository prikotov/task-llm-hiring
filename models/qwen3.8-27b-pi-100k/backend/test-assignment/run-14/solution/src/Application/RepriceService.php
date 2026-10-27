<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Inbound\PricesInputParser;
use App\Entity\Price;
use App\Domain\Pricing\RateCalculator;
use App\Domain\Pricing\Money;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус):
 *  - upsert строки цен по (provider, model, valid_from) (провайдер и модель
 *    должны существовать в БД; секции providers/models в файле коррекции могут
 *    отсутствовать или быть пустыми);
 *  - пересчитывает события, тарифицированные этой строкой;
 *  - печатает n событий и дельту (новая - старая), 6 знаков.
 */
final class RepriceService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ModelRepository $models,
        private PriceRepository $prices,
        private ProviderRepository $providers,
        private UsageEventRepository $events,
    ) {
    }

    /**
     * Возвращает [n, deltaMoney].
     *
     * @throws \RuntimeException
     */
    public function reprice(string $jsonL, bool $requireExists = true): array
    {
        $data = PricesInputParser::parse($jsonL, false);

        $n = 0;
        $delta = new Money('0');
        $utc = new \DateTimeZone('UTC');

        foreach ($data['prices'] as $p) {
            $providerCode = $p['provider'];
            $modelCode = $p['model'];
            $validFrom = $p['valid_from'];

            // Провайдер и модель — только из уже загруженного справочника.
            if ($requireExists) {
                if ($this->providers->findByCode($providerCode) === null) {
                    throw new \RuntimeException(sprintf(
                        'reprice: provider "%s" not found. Import the corrected file into the reference first.',
                        $providerCode
                    ));
                }
                if ($this->models->findByProviderAndCode($providerCode, $modelCode) === null) {
                    throw new \RuntimeException(sprintf(
                        'reprice: model "%s/%s" not found. Import the corrected file into the reference first.',
                        $providerCode,
                        $modelCode
                    ));
                }
            }

            $existing = $this->prices->findByPKey($providerCode, $modelCode, $validFrom);
            if ($existing === null) {
                if ($requireExists) {
                    throw new \RuntimeException(sprintf(
                        'reprice: price row (%s/%s/%s) not found.',
                        $providerCode,
                        $modelCode,
                        $validFrom->format('c'),
                    ));
                }
                continue;
            }

            // Пересчёт до замены (старые суммы)
            $oldEvents = $this->events->findRatedByPriceRow($existing->id());
            $oldSum = new Money('0');
            foreach ($oldEvents as $e) {
                $oldSum = $oldSum->add(new Money((string) $e->costUsd()));
            }

            // Применяем новые ставки (upsert по (provider, model, valid_from)).
            $existing->replaceRates($p['currency'], $p['unit'], $p['rates']);
            $this->em->flush();

            // Пересчёт с новыми ставками.
            $provider = $this->providers->findByCode($providerCode);
            $offPeak = $provider !== null ? $provider->offPeak() : null;
            $calc = new RateCalculator($offPeak, $existing->pricedModel());

            $newSum = new Money('0');
            foreach ($oldEvents as $e) {
                $hhmmUtc = $e->ts()->setTimezone($utc)->format('H:i');
                $cost = $calc->price($e->tokens(), $hhmmUtc);
                $e->markRated($cost->format(), $existing->id());
                $newSum = $newSum->add($cost);
                $n++;
            }
            $delta = $delta->add($newSum->subtract($oldSum));
            $this->em->flush();
        }

        return [$n, $delta];
    }
}

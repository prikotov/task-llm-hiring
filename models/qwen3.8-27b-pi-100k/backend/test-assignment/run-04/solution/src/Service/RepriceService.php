<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\OffPeak;
use App\Domain\PriceSpec;
use App\Domain\PricingCalculator;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Бонус: ретро-коррекция цен (app:billing:reprice).
 *
 * Файл коррекции — та же структура; секции providers/models игнорируются
 * (провайдер и модель обязаны существовать в базе, а не в файле).
 * Строки цен применяем upsert-ом по (provider, model, valid_from); события,
 * тарифицированные заменёнными строками, пересчитываем по новым ставкам —
 * стоимость перезаписывается (вместе с priceRowId, т.к. id строки не меняется),
 * уже показанные «старые» суммы задвоениям не приводят.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceFileParser $parser,
        private readonly PricingCalculator $calculator,
    ) {}

    /**
     * @return array{events: int, delta: string} delta — new − old, ровно 6 знаков (может быть отрицательной)
     *
     * @throws ImportException
     */
    public function reprice(string $file): array
    {
        $data = $this->parser->parseFile($file);

        if ($data['prices'] === []) {
            throw new ImportException('секция "prices" файла коррекции пуста — нечего применять');
        }

        // дубли (provider, model, valid_from) в одном файле: последняя запись побеждает
        $unique = [];
        foreach ($data['prices'] as $i => $raw) {
            if (!\is_array($raw)) {
                throw new ImportException(sprintf('prices[%d]: запись должна быть объектом', $i));
            }

            $context = sprintf('prices[%d]', $i);
            $spec = $this->parser->parsePrice($raw, $context);
            $unique[sprintf('%s|%s|%s', $spec['provider'], $spec['model'], $spec['valid_from']->format('Y-m-d\TH:i:s.p'))] = $spec;
        }

        $priceRepo = $this->em->getRepository(PriceRow::class);
        $eventRepo = $this->em->getRepository(UsageEvent::class);
        $modelRepo = $this->em->getRepository(Model::class);
        $providerRepo = $this->em->getRepository(Provider::class);

        $recounted = 0;
        $affectedEventIds = [];
        $delta = '0';
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        foreach ($unique as $spec) {
            /** @var Provider|null $provider */
            $provider = $providerRepo->findOneBy(['code' => $spec['provider']]);
            if ($provider === null) {
                throw new ImportException(sprintf(
                    'провайдер "%s" не существует в базе (коррекция цен применяется только к уже загруженному справочнику)',
                    $spec['provider'],
                ));
            }

            /** @var Model|null $model */
            $model = $modelRepo->findOneBy(['provider' => $provider, 'code' => $spec['model']]);
            if ($model === null) {
                throw new ImportException(sprintf(
                    'модель "%s" провайдера "%s" не существует в базе (коррекция цен применяется только к уже загруженному справочнику)',
                    $spec['model'],
                    $spec['provider'],
                ));
            }

            /** @var PriceRow|null $row */
            $row = $priceRepo->findOneBy(['model' => $model, 'validFrom' => $spec['valid_from']]);
            $isNew = $row === null;

            /** @var list<UsageEvent> $events */
            $events = $isNew ? [] : $eventRepo->findBy([
                'status' => UsageEvent::STATUS_RATED,
                'priceRowId' => $row->id,
            ]);

            $row ??= new PriceRow();
            $row->model = $model;
            $row->validFrom = $spec['valid_from'];
            $row->currency = $spec['currency'];
            $row->unit = $spec['spec']->unit;
            $row->rates = $spec['spec']->rates;
            if ($isNew) {
                $this->em->persist($row);
            }
            $this->em->flush();

            $offPeak = ($provider->offPeakStart !== null && $provider->offPeakEnd !== null && $provider->offPeakMultiplier !== null)
                ? OffPeak::create($provider->offPeakStart, $provider->offPeakEnd, $provider->offPeakMultiplier)
                : null;

            foreach ($events as $event) {
                if (\in_array($event->id, $affectedEventIds, true)) {
                    continue; // дубль в файле — пересчитываем один раз
                }

                $newCost = $this->calculator->rate(
                    new PriceSpec($row->unit, $row->rates),
                    $event->tokensInput,
                    $event->tokensOutput,
                    $event->tokensCacheRead,
                    $event->tokensCacheWrite,
                    $offPeak,
                    $event->ts,
                );

                $delta = \bcadd($delta, \bcsub($newCost, (string) $event->costUsd, \App\Domain\Money::SCALE), \App\Domain\Money::SCALE);

                $event->markRated((int) $row->id, $newCost, $now);
                $affectedEventIds[] = (int) $event->id;
            }
            $recounted += $isNew ? 0 : count($events);

            $this->em->flush();
        }

        return ['events' => $recounted, 'delta' => \App\Domain\Money::toScale6($delta)];
    }
}

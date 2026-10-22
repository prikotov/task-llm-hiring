<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Event;
use App\Domain\Entity\Model;
use App\Domain\Entity\ModelPrice;
use App\Domain\Entity\Provider;
use App\Validation\PriceParser;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Loads price index and usage events into the database. Every run performs a
 * full idempotent upsert; duplicate usage events are dropped.
 */
final class ImportService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PriceIndex $priceIndex,
        private PriceParser $parser,
    ) {
    }

    public function importPrices(string $file): void
    {
        $parsed = $this->parser->parsePrices($file);

        foreach ($parsed['providers'] as $row) {
            $this->upsertProvider($row);
        }
        foreach ($parsed['models'] as $row) {
            $this->upsertModel($row);
        }
        foreach ($parsed['prices'] as $row) {
            $this->upsertPrice($row);
        }
        $this->em->flush();

        $this->priceIndex->setProviders($this->em->getRepository(Provider::class)->findAll());
        $this->priceIndex->setPrices($this->em->getRepository(ModelPrice::class)->findAll());
    }

    public function importEvents(string $file): void
    {
        $parsed = $this->parser->parseUsageFile($file);

        foreach ($parsed as $row) {
            $existing = $this->em->getRepository(Event::class)->findOneBy([
                'provider' => $row['provider'],
                'providerRequestId' => $row['provider_request_id'],
            ]);
            if (null !== $existing) {
                continue;
            }
            $this->persistEvent($row);
        }
        $this->em->flush();
    }

    /**
     * @param array<string,mixed> $row
     */
    private function persistEvent(array $row): void
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['providerCode' => $row['provider']]);
        $model = $this->em->getRepository(Model::class)->findOneBy(['provider.code' => $row['provider'], 'code' => $row['model']]);
        if (null === $provider || null === $model) {
            throw new \InvalidArgumentException('Event references unknown provider/model: '.$row['provider'].'/'.$row['model']);
        }
        $timestamp = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i:sP', $row['ts']);
        if (false === $timestamp) {
            throw new \InvalidArgumentException('Invalid ts for event '.$row['provider'].'/'.$row['provider_request_id']);
        }
        $ratingDate = $timestamp->format('Y-m-d');

        $totalCost = (string) (int) 0;
        $event = new Event(
            $row['provider'],
            $provider,
            $row['provider_request_id'],
            $model,
            $row['user'],
            $timestamp,
            $ratingDate,
            $row['tokens_input'],
            $row['tokens_output'],
            $row['tokens_cache_read'],
            $row['tokens_cache_write'],
            $totalCost,
        );
        $this->em->persist($event);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function upsertProvider(array $row): void
    {
        $existing = $this->em->getRepository(Provider::class)->findOneBy(['providerCode' => $row['code']]);
        if (null !== $existing) {
            $existing->name = $row['name'];
            $existing->offPeakStart = $row['off_peak']['window_start'] ?? null;
            $existing->offPeakEnd = $row['off_peak']['window_end'] ?? null;
            $existing->offPeakMultiplier = $row['off_peak'] ? $row['off_peak']['multiplier'] : null;

            return;
        }
        $provider = new Provider(
            $row['code'],
            $row['name'],
            $row['off_peak']['window_start'] ?? null,
            $row['off_peak']['window_end'] ?? null,
            $row['off_peak'] ? $row['off_peak']['multiplier'] : null,
        );
        $this->em->persist($provider);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function upsertModel(array $row): void
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['providerCode' => $row['provider']]);
        if (null === $provider) {
            throw new \InvalidArgumentException('Model references unknown provider: '.$row['provider']);
        }
        $existing = $this->em->getRepository(Model::class)->findOneBy(['provider.code' => $provider->providerCode, 'code' => $row['code']]);
        if (null !== $existing) {
            return;
        }
        $model = new Model($row['code'], $provider);
        $this->em->persist($model);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function upsertPrice(array $row): void
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i:sP', $row['valid_from']);
        if (false === $parsed) {
            throw new \InvalidArgumentException('Invalid valid_from for price: '.$row['valid_from']);
        }
        $existing = $this->em->getRepository(ModelPrice::class)->findOneBy([
            'provider' => $row['provider'],
            'model' => $row['model'],
            'validFrom' => $row['valid_from'],
        ]);
        if (null !== $existing) {
            return;
        }
        $model = $this->em->getRepository(Model::class)->findOneBy(['provider.code' => $row['provider'], 'code' => $row['model']]);
        if (null === $model) {
            throw new \InvalidArgumentException('Price references unknown model: '.$row['provider'].'/'.$row['model']);
        }
        $rates = $row['rates'];
        $price = new ModelPrice(
            $row['provider'],
            $row['model'],
            $parsed,
            'USD',
            $row['unit'],
            $rates['input'] ?? null,
            $rates['output'] ?? null,
            $rates['cache_read'] ?? null,
            $rates['cache_write'] ?? null,
            $rates['any'] ?? null,
        );
        $this->em->persist($price);
    }
}

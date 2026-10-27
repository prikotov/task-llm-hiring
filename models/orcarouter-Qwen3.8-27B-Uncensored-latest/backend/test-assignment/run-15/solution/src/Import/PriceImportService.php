<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\Model;
use App\Domain\PriceLine;
use App\Domain\Provider;
use App\Repository\PriceLineRepository;
use App\Domain\ValueObject\OffPeakWindow;
use App\Domain\ValueObject\Price;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

final class PriceImportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceLineRepository $priceLines,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     * @return int count of price line rows upserted
     */
    public function importFromPayload(array $data): int
    {
        $providers = (array) ($data['providers'] ?? []);
        foreach ($providers as $p) {
            $this->upsertProvider($p);
        }

        foreach ((array) ($data['models'] ?? []) as $m) {
            $this->upsertModel((string) ($m['provider'] ?? ''), (string) ($m['code'] ?? ''));
        }

        $count = 0;
        foreach ((array) ($data['prices'] ?? []) as $row) {
            $this->upsertPriceRow($row);
            $count++;
        }
        $this->em->flush();
        return $count;
    }

    /** @param array<string,mixed> $row */
    private function upsertProvider(array $row): Provider
    {
        $code = (string) ($row['code'] ?? '');
        if ($code === '') {
            throw new \InvalidArgumentException('provider row missing "code"');
        }
        $name = (string) ($row['name'] ?? $code);
        $repo = $this->em->getRepository(Provider::class);
        /** @var Provider|null $provider */
        $provider = $repo->findOneBy(['code' => $code]);
        if ($provider === null) {
            $provider = new Provider($code, $name);
            $this->em->persist($provider);
        }
        $offPeak = $row['off_peak'] ?? null;
        if ($offPeak === null) {
            $provider->setOffPeak(null);
            return $provider;
        }
        if (!is_array($offPeak)) {
            throw new \InvalidArgumentException('provider "off_peak" must be object or null');
        }
        $provider->setOffPeak(OffPeakWindow::fromPayload(
            (string) ($offPeak['window_start'] ?? ''),
            (string) ($offPeak['window_end'] ?? ''),
            (string) ($offPeak['multiplier'] ?? ''),
        ));
        return $provider;
    }

    private function upsertModel(string $providerCode, string $code): void
    {
        if ($providerCode === '' || $code === '') {
            throw new \InvalidArgumentException('model row missing "provider" or "code"');
        }
        $repo = $this->em->getRepository(Model::class);
        $find = $repo->findOneBy(['providerCode' => $providerCode, 'code' => $code]);
        if ($find === null) {
            $this->em->persist(new Model($providerCode, $code));
        }
    }

    /** @param array<string,mixed> $row */
    private function upsertPriceRow(array $row): void
    {
        $pc = (string) ($row['provider'] ?? '');
        $model = (string) ($row['model'] ?? '');
        $validFrom = $this->parseUtc((string) ($row['valid_from'] ?? ''));
        $price = Price::fromPayload($row);
        $existing = $this->priceLines->findByKey($pc, $model, $validFrom);
        if ($existing === null) {
            $this->em->persist(new PriceLine($pc, $model, $validFrom, $price));
        } else {
            $existing->setPrice($pc, $model, $validFrom, $price);
        }
    }

    private function parseUtc(string $iso): \DateTimeImmutable
    {
        if ($iso === '') {
            throw new \InvalidArgumentException('price row missing "valid_from"');
        }
        try {
            $dt = new \DateTimeImmutable($iso, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new \InvalidArgumentException("invalid valid_from: {$iso}");
        }
        return $dt;
    }
}

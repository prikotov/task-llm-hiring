<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\IsoDateTime;
use App\Domain\Billing\Unit;
use App\Entity\PriceRow;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Разбор и upsert строки цены по естественному ключу (provider, model, valid_from).
 *
 * Используется и импортом справочника, и коррекцией (reprice).
 */
final class PriceRowWriter
{
    public function __construct(
        private readonly BillingRepository $repo,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param array<string, mixed> $raw
     */
    public function upsert(array $raw): PriceRow
    {
        $providerCode = Validators::string($raw, 'provider');
        $modelCode = Validators::string($raw, 'model');
        $validFrom = IsoDateTime::parseUtc(Validators::string($raw, 'valid_from'));
        $unit = Validators::string($raw, 'unit');
        $currency = isset($raw['currency']) && \is_string($raw['currency']) ? $raw['currency'] : 'USD';
        $rates = self::parseRates($unit, Validators::assoc('rates', $raw['rates'] ?? null));

        if (!Unit::isValid($unit)) {
            throw new \DomainException(sprintf('Некорректная схема тарификации: "%s"', $unit));
        }

        if (null === $this->repo->findProviderByCode($providerCode)) {
            throw new \DomainException(sprintf('Неизвестный провайдер: "%s"', $providerCode));
        }

        $model = $this->repo->findModelByProviderCodeAndCode($providerCode, $modelCode);
        if (null === $model) {
            throw new \DomainException(sprintf('Неизвестная модель: "%s/%s"', $providerCode, $modelCode));
        }
        $provider = $model->getProvider();

        $row = $this->repo->findPriceRow($providerCode, $modelCode, $validFrom) ?? new PriceRow();

        if (null === $row->getId()) {
            $this->em->persist($row);
        }

        $row->setProvider($provider)
            ->setModel($model)
            ->setValidFrom($validFrom)
            ->setCurrency($currency)
            ->setUnit($unit)
            ->replaceRates($rates);

        return $row;
    }

    /**
     * @param array<string, mixed> $ratesRaw
     *
     * @return array<string, string> kind => decimal
     */
    private static function parseRates(string $unit, array $ratesRaw): array
    {
        $canonical = Unit::canonicalKinds($unit);
        $result = [];

        foreach ($canonical as $kind) {
            if (!\array_key_exists($kind, $ratesRaw)) {
                throw new \DomainException(sprintf('Отсутствует ставка вида "%s" для схемы "%s"', $kind, $unit));
            }

            if (!\is_string($ratesRaw[$kind]) && !\is_int($ratesRaw[$kind]) && !\is_float($ratesRaw[$kind])) {
                throw new \DomainException(sprintf('Ставка вида "%s" должна быть строкой/числом', $kind));
            }

            $result[$kind] = Validators::normalize((string) $ratesRaw[$kind], sprintf('ставка "%s"', $kind));
        }

        return $result;
    }
}

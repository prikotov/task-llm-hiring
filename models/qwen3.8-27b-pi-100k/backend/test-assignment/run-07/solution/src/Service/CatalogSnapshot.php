<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\OffPeakWindow;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Мгновенное снимок-справочника на время одного прогона rate/reprice:
 * провайдеры (с оф-пиком), модели и строки цен, отсортированные по
 * valid_from по убыванию для быстрого выбора цены.
 */
final class CatalogSnapshot
{
    /**
     * @param array<string, Provider> $providersByCode code => provider
     * @param array<string, list<PriceRow>> $priceRowsByModelKey «providerCode\0modelCode» => строки цен (valid_from DESC)
     */
    public function __construct(
        private readonly array $providersByCode,
        private readonly array $priceRowsByModelKey,
    ) {
    }

    public static function load(EntityManagerInterface $em): self
    {
        $providersByCode = [];
        foreach ($em->getRepository(Provider::class)->findAll() as $provider) {
            $providersByCode[$provider->getCode()] = $provider;
        }

        $rows = $em->createQuery(
            'SELECT pr FROM ' . PriceRow::class . ' pr'
        )->getResult();

        $priceRowsByModelKey = [];
        foreach ($rows as $row) {
            $key = $row->getModel()->getProvider()->getCode() . "\0" . $row->getModel()->getCode();
            $priceRowsByModelKey[$key][] = $row;
        }

        foreach ($priceRowsByModelKey as $key => $list) {
            usort($list, static fn (PriceRow $a, PriceRow $b): int => $b->getValidFrom() <=> $a->getValidFrom());
            $priceRowsByModelKey[$key] = $list;
        }

        return new self($providersByCode, $priceRowsByModelKey);
    }

    /**
     * Строка цены для события: максимальный valid_from <= ts;
     * null — если провайдер/модель из события неизвестны справочнику
     * либо ни одна строка не действует в момент события.
     */
    public function findPriceRow(string $providerCode, string $modelCode, \DateTimeInterface $ts): ?PriceRow
    {
        foreach ($this->priceRowsByModelKey[$providerCode . "\0" . $modelCode] ?? [] as $row) {
            if ($row->getValidFrom() <= $ts) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Оф-пик провайдера события (null — скидки нет или провайдер неизвестен).
     */
    public function offPeakFor(string $providerCode): ?OffPeakWindow
    {
        $provider = $this->providersByCode[$providerCode] ?? null;
        if ($provider === null
            || $provider->getOffPeakWindowStart() === null
            || $provider->getOffPeakWindowEnd() === null
            || $provider->getOffPeakMultiplier() === null) {
            return null;
        }

        return new OffPeakWindow(
            $provider->getOffPeakWindowStart(),
            $provider->getOffPeakWindowEnd(),
            $provider->getOffPeakMultiplier(),
        );
    }
}

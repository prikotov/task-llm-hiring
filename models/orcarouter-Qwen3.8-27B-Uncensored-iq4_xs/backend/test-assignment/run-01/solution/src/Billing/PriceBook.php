<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Индекс всех строк цен в памяти: (provider code, model code) ->
 * список строк, отсортированный по valid_from по возрастанию.
 *
 * Выбор цены для события — правая граница: строка с наибольшим
 * valid_from ≤ ts (п. 1).
 */
final class PriceBook
{
    /** @var array<string, list<array{row: PriceRow, window: ?OffPeakWindow}>> */
    private array $byProviderModel = [];

    /** @var array<string, ?OffPeakWindow> */
    private array $windows = [];

    public function __construct(EntityManagerInterface $em)
    {
        foreach ($em->getRepository(PriceRow::class)->findAll() as $row) {
            $provider = $row->getProvider();
            $key = $provider->getCode()."\x00".$row->getModel()->getCode();
            $this->byProviderModel[$key][] = [
                'row' => $row,
                'window' => $provider->offPeak(),
            ];
            $this->windows[$provider->getCode()] = $provider->offPeak();
        }

        foreach ($this->byProviderModel as $key => $list) {
            usort($list, static fn (array $a, array $b): int => $a['row']->getValidFrom() <=> $b['row']->getValidFrom());
            $this->byProviderModel[$key] = $list;
        }
    }

    /**
     * Строка цен, действовавшая на момент ts (valid_from включительно).
     *
     * @return array{row: PriceRow, window: ?OffPeakWindow}|null
     */
    public function findRate(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $ts,
    ) {
        $list = $this->byProviderModel[$providerCode."\x00".$modelCode] ?? null;
        if ($list === null) {
            return null;
        }

        $found = null;
        foreach ($list as $item) {
            if ($item['row']->getValidFrom() <= $ts) {
                $found = $item;
            } else {
                break;
            }
        }

        return $found;
    }

    /** Окно оф-пика провайдера (null — без скидки), если провайдер в справочнике. */
    public function offPeakOf(string $providerCode): ?OffPeakWindow
    {
        return $this->windows[$providerCode] ?? null;
    }
}

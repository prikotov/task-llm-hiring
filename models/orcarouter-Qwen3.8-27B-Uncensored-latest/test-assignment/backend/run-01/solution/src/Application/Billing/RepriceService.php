<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Entity\PriceRow;
use App\Domain\Billing\Entity\Provider;
use App\Domain\Billing\Entity\UsageEvent;
use App\Domain\Billing\Exception\BillingException;
use App\Domain\Billing\Pricing\BillingCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус).
 *
 * Механизм: «перезапись строки + точечный пересчёт затронутых событий».
 *
 *  1. обновлённая строка цен upsert-ится по (provider, model, valid_from);
 *     провайдер и модель должны существовать в уже загруженном справочнике
 *     (секции providers/models в файле коррекции могут отсутствовать);
 *  2. события, тарифицировавшиеся именно этой строкой
 *     (rated = true и event.applied_valid_from == valid_from строки),
 *     пересчитываются по новым ставкам с off-peak-скидкой по ts события
 *     (та же логика, что и штатное rate — см. RatingService);
 *  3. итог: количество пересчитанных событий и дельта «новая − старая»
 *     суммарной стоимостью (6 знаков).
 *
 * Отчёт после reprice без задвоений: события хранят финальный cost,
 * пересчёт перезаписывает cost той же записи события (её природный ключ
 * не меняется), а отчёт суммирует по ключу (user, model).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceImporter $importer,
        private readonly BillingCalculator $calculator,
    ) {
    }

    /**
     * @return array{n: int, delta: string} dельта — уже в 6-знаковом формате
     */
    public function reprice(string $path): array
    {
        $prices = $this->readPrices($path);

        $touched = [];
        $this->em->wrapInTransaction(function () use ($prices, &$touched): void {
            foreach ($prices as $item) {
                $row = $this->importer->upsertPriceRow((array) $item);
                $touched[] = $row;
            }
        });

        $recalculated = 0;
        $delta = '0';
        $this->em->wrapInTransaction(function () use ($touched, &$recalculated, &$delta): void {
            foreach ($touched as $row) {
                /** @var UsageEvent[] $events */
                $events = $this->em->getRepository(UsageEvent::class)->findBy([
                    'provider' => $row->provider,
                    'model' => $row->model,
                    'rated' => true,
                    'appliedValidFrom' => $row->validFrom,
                ]);

                foreach ($events as $event) {
                    $old = $event->cost ?? '0';
                    $event->cost = $this->recalc($event, $row);
                    $delta = \bcsub($delta, \bcsub($event->cost, $old, 20), 20);
                    $recalculated++;
                }
            }

            $this->em->flush();
        });

        // Сумма/дельта уже сложена из 6-значных стоимостей — просто нормализуем формат к 6 знакам.
        return ['n' => $recalculated, 'delta' => $this->normalizeSix($delta)];
    }

    private function recalc(UsageEvent $event, PriceRow $row): string
    {
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->provider]);
        $offPeak = $provider !== null ? $provider->getOffPeak() : null;

        return $this->calculator->rate($event, $row, $offPeak);
    }

    private function normalizeSix(string $value): string
    {
        $noSign = ltrim($value, '-');
        $isZero = (int) $noSign === 0;
        $parts = explode('.', $noSign);
        $int = ltrim($parts[0] ?? '0', '0');
        $int = $int === '' ? '0' : $int;
        $frac = str_pad($parts[1] ?? '0', 6, '0');
        $frac = substr($frac, 0, 6);
        $sign = (str_starts_with($value, '-') && !$isZero) ? '-' : '';

        return $sign.$int.'.'.$frac;
    }

    /** @return list<array<string, mixed>> */
    private function readPrices(string $path): array
    {
        if (!\is_file($path)) {
            throw new BillingException(sprintf('Файл не найден: %s', $path));
        }
        $raw = \file_get_contents($path);
        if ($raw === false) {
            throw new BillingException(sprintf('Не удалось прочитать файл: %s', $path));
        }
        $data = \json_decode($raw, true);
        if (!\is_array($data) || !isset($data['prices']) || !\is_array($data['prices']) || $data['prices'] === []) {
            throw new BillingException('reprice: секция "prices" обязательна и не может быть пустой.');
        }

        return $data['prices'];
    }
}

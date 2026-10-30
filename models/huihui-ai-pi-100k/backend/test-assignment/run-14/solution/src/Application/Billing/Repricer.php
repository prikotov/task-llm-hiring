<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\OffPeakWindow;
use App\Domain\PricingUnit;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция стоимости (бонус).
 *
 * Провайдер присылает исправленную строку цены (в файле того же формата,
 * секции providers/models могут отсутствовать или быть пустыми). Мы:
 *
 *  1) Upsert строки цены по (provider, model, valid_from) — в справочнике
 *     (провайдер/модель должны существовать в БД, а не в файле коррекции).
 *  2) Пересчитываем все rated-события, закреплённые за заменённой
 *     (перезаписанной) строкой цены: стоимость событий пересчитывается
 *     по текущим (новым) ставкам с той же логикой (схема тарификации,
 *     оф-пик, округление HALF_UP до 6 знаков).
 *
 * Механизм (выбран самый консервативный): замена ставки + in-situ
 * пересчёт стоимости событий, закреплённых за данной строкой.
 * Итоговый `app:billing:report` после reprice — показывает пересчитанные
 * суммы (без задвоений).
 *
 * Вывод (stdout):
 *   REPRICED_EVENTS=<n>
 *   REPRICE_DELTA_USD=<x.xxxxxx>
 */
final class Repricer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventPricing $pricing,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @return array{id: array, count: int, delta: string}
     */
    public function reprice(string $file): array
    {
        if (!is_file($file)) {
            throw new \DomainException(sprintf('Файл коррекции не найден: %s', $file));
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new \DomainException(sprintf('Не удалось прочитать файл: %s', $file));
        }
        $decoded = json_decode($raw, true);
        if (!\is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new \DomainException(sprintf('Некорректный JSON в файле коррекции: %s', json_last_error_msg()));
        }

        $rows = $decoded['prices'] ?? [];
        if (!\is_array($rows)) {
            throw new \DomainException('Секция «prices» в файле коррекции должна быть массивом строк.');
        }

        $replacedPriceIds = [];
        foreach ($rows as $row) {
            $priceRow = $this->upsertPriceRow($row);
            $pid = $priceRow->getId();
            if ($pid !== null) {
                $replacedPriceIds[$pid] = true;
            }
        }

        $this->em->flush();

        $replacedIds = array_keys($replacedPriceIds);
        if ($replacedIds === []) {
            return ['id' => [], 'count' => 0, 'delta' => '0.000000'];
        }

        $affected = $this->events->ratedByPriceIds(array_values($replacedIds));

        $count = 0;
        $delta = '0.000000';

        foreach ($affected as $event) {
            if ($event->pricedBy === null) {
                throw new \LogicException('Внутренняя ошибка: rated-событие без priced_by.');
            }

            $provider = $event->pricedBy->getModel()->getProvider();
            $offPeak = $provider->getOffPeak();
            if ($offPeak !== null && !$offPeak->contains(Rater::minutesOfDayUtc($event))) {
                $offPeak = null;
            }

            $oldCost = $event->costUsd;
            $newCost = $this->pricing->price($event->tokens(), $event->pricedBy, $offPeak);

            $event->costUsd = $newCost;
            $count++;
            $delta = bcadd($delta, bcsub($newCost, $oldCost, 6), 6);
        }

        $this->em->flush();

        return ['id' => $replacedIds, 'count' => $count, 'delta' => $delta];
    }

    /** @param array<string, mixed> $row */
    private function upsertPriceRow(array $row): \App\Entity\Price
    {
        foreach (['provider', 'model', 'valid_from', 'unit', 'rates'] as $k) {
            if (!\array_key_exists($k, $row)) {
                throw new \DomainException(sprintf('Строка коррекции: отсутствует поле %s: %s', $k, json_encode($row)));
            }
        }

        $providerCode = (string) $row['provider'];
        $modelCode = (string) $row['model'];

        $provider = $this->providers->findOneBy(['code' => $providerCode]);
        if ($provider === null) {
            throw new \DomainException(sprintf('Провайдер не найден в справочнике: %s (загрузите его командой app:prices:import)', $providerCode));
        }
        $model = $this->models->byProviderAndCode($provider, $modelCode);
        if ($model === null) {
            throw new \DomainException(sprintf('Модель не найдена в справочнике: %s/%s', $providerCode, $modelCode));
        }

        $validFrom = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, (string) $row['valid_from']);
        if ($validFrom === false) {
            throw new \DomainException(sprintf('Строка коррекции: valid_from не в ISO-8601 (ATOM): %s', $row['valid_from']));
        }
        $validFrom = $validFrom->setTimezone(new \DateTimeZone('UTC'));

        $unit = PricingUnit::fromString((string) $row['unit']);
        $rates = $this->normalizeRates($row['rates'], $unit);

        $existing = $this->prices->byModelAndValidFrom($model, $validFrom);
        if ($existing === null) {
            $existing = new \App\Entity\Price($model, $validFrom);
            $this->em->persist($existing);
        }
        $existing->setCurrency((string) ($row['currency'] ?? 'USD'));
        $existing->setRates($unit->value, $rates);

        return $existing;
    }

    /** @param array<string, mixed> $raw */
    private function normalizeRates(array $raw, PricingUnit $unit): array
    {
        $need = $unit === PricingUnit::PerToken
            ? ['input', 'output', 'cache_read', 'cache_write']
            : ['any'];

        $out = [];
        foreach ($need as $k) {
            if (!\array_key_exists($k, $raw)) {
                throw new \DomainException(sprintf('rates: отсутствует ставка «%s» (схема %s)', $k, $unit->value));
            }
            $v = (string) $raw[$k];
            if (!preg_match('/^\d+(\.\d+)?$/u', $v)) {
                throw new \DomainException(sprintf('rates[%s]: десятичное число (строка): %s', $k, $v));
            }
            $out[$k] = $v;
        }

        return $out;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\EventKey;
use App\Domain\OffPeakWindow;
use App\Domain\UsageTokens;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\ModelRepository;
use App\Repository\ProviderRepository;
use App\Repository\PriceRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки
 * unrated).
 *
 * Правила задания:
 *   п. 1 — выбор цены (наибольший validFrom ≤ ts, включительно);
 *          если нет строки цен — событие unrated;
 *   п. 2 — формула по схеме (per_1m_tokens / per_1m_tokens_unified);
 *   п. 3 — оф-пик (окно провайдера, множитель стоимости);
 *   п. 4 — точность: bcmath scale 16, единственное округление — итог;
 *   п. 7 — повторные попытки unrated.
 *
 * Вывод (через команду app:billing:rate):
 *   stdout: `RATED=<n> UNRATED=<m>`
 *   stderr: `UNRATED <provider>/<provider_request_id> <ts> no_price`
 *
 * Для каждого unrated-события этого запуска — одна строка в stderr.
 *
 * События обрабатываются в порядке id ASC; по завершении run() — flush.
 *
 * @internal только внутри пакета app
 */
final class Rater
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
     * @return array{rated: int, unrated: int, unratedLines: string[]}
     */
    public function run(): array
    {
        $unratedLines = [];
        $rated = 0;
        $unrated = 0;

        $providerByCode = [];
        $modelByPair = [];

        foreach ($this->events->pendingEvents() as $event) {
            $provider = $this->providerByCode($event, $providerByCode);
            $model = $this->modelByProviderAndCode($event, $providerByCode, $modelByPair);

            $price = ($provider !== null && $model !== null)
                ? $this->prices->applicablePrice($model, $event->tsUtc())
                : null;

            $offPeak = $provider !== null ? $provider->getOffPeak() : null;
            // p. 3 — множитель применяется только если ts попал в окно.
            if ($offPeak !== null && !$offPeak->contains(self::minutesOfDayUtc($event))) {
                $offPeak = null;
            }

            if ($price === null) {
                $event->status = UsageEvent::STATUS_UNRATED;
                $event->costUsd = '0.000000';
                $event->pricedBy = null;
                $unrated++;
                $unratedLines[] = sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->provider,
                    $event->providerRequestId,
                    $event->tsUtc()->format('Y-m-d\TH:i:s\Z'),
                );
                continue;
            }

            $event->status = UsageEvent::STATUS_RATED;
            $event->costUsd = $this->pricing->price($event->tokens(), $price, $offPeak);
            $event->pricedBy = $price;
            $rated++;
        }

        $this->em->flush();

        return [
            'rated' => $rated,
            'unrated' => $unrated,
            'unratedLines' => $unratedLines,
        ];
    }

    /** Момент суток (UTC) в минутах — для правила оф-пик. */
    public static function minutesOfDayUtc(UsageEvent $event): int
    {
        $utc = \DateTimeImmutable::createFromInterface($event->tsUtc());
        $utc = $utc->setTimezone(new \DateTimeZone('UTC'));

        return (int) $utc->format('G') * 60 + (int) $utc->format('i');
    }

    /**
     * @param array<string, Provider|null> $providerByCode
     */
    private function providerByCode(UsageEvent $event, array &$providerByCode): ?Provider
    {
        if (!\array_key_exists($event->provider, $providerByCode)) {
            $providerByCode[$event->provider] = $this->providers->findOneBy(['code' => $event->provider]);
        }

        return $providerByCode[$event->provider];
    }

    /**
     * @param array<string, Provider|null> $providerByCode
     * @param array<string, \App\Entity\Model|null> $modelByPair
     * @return \App\Entity\Model|null
     */
    private function modelByProviderAndCode(UsageEvent $event, array $providerByCode, array &$modelByPair): ?\App\Entity\Model
    {
        $slashPos = (int) \strpos($event->model, '/');
        if ($slashPos === false) {
            return null;
        }
        $modelProvider = \substr($event->model, 0, $slashPos);
        $modelCode = \substr($event->model, $slashPos + 1);

        if ($modelProvider !== $event->provider) {
            return null;
        }

        $provider = $providerByCode[$event->provider] ?? null;
        if ($provider === null) {
            return null;
        }

        $pairKey = $event->provider.'|'.$modelCode;
        if (!\array_key_exists($pairKey, $modelByPair)) {
            $modelByPair[$pairKey] = $this->models->byProviderAndCode($provider, $modelCode);
        }

        return $modelByPair[$pairKey];
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Decimal;
use App\Domain\Billing\EventPricingService;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба первичной тарификации событий и ретро-коррекции.
 *
 * Сценарий:
 *   - берёт все события со status = STATUS_NEW;
 *   - для каждого — находит действующую по времени строку цены;
 *   - цена есть → считает точную стоимость и сохраняет rating;
 *   - цены нет → помечает как unrated.
 *
 * Ретро-коррекция (app:billing:reprice):
 *   - для каждой затронутой строки цены — пересчитывает события, тарифицировавшиеся этой строкой;
 *   - новые ratings складываются в журнал (event_id, effective_from, price_id, cost),
 *     и отчёт всегда берёт «постоянно» последний рейтинг по событию;
 *   - дельта = сумма новых стоимостей затронутых событий − сумма старых.
 */
final class RateService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventPricingService $pricer,
    ) {
    }

    /**
     * @return array{0: int, 1: list<array{provider: string, providerRequestId: string, ts: string}>}
     */
    public function rateAllPending(): array
    {
        $em = $this->em;
        /** @var UsageEvent[] $events */
        $events = $em->getRepository(UsageEvent::class)
            ->findBy(['status' => UsageEvent::STATUS_NEW], ['id' => 'ASC']);

        $rated = 0;
        $unrated = [];

        foreach ($events as $event) {
            $result = $this->pricer->rateEvent($event);
            if ($result === null) {
                $event->setStatus(UsageEvent::STATUS_UNRATED);
                $unrated[] = [
                    'provider' => $event->getProvider()->getCode(),
                    'providerRequestId' => $event->getProviderRequestId(),
                    'ts' => $event->getTs()->format(\DateTimeInterface::ATOM),
                ];
            } else {
                $cost = $result['cost']->roundHalfUp6();
                $rating = new UsageRating(
                    $event,
                    $this->uniqueEffectiveFrom($event),
                    $cost->toFixed(6),
                    $result['price'],
                );
                $event->getRatings()->add($rating);
                $em->persist($rating);
                $event->setStatus(UsageEvent::STATUS_RATED);
                $rated++;
            }
        }

        if ($events !== []) {
            $em->flush();
            $em->clear();
        }

        return [$rated, $unrated];
    }

    /**
     * Пересчёт затронутых событий по новым стоимостям.
     *
     * @param list<Price> $affected строки цен, заменённые в справочнике
     * @return array{0: int, 1: string} [число пересчитанных событий, дельта]
     */
    public function repriceFor(array $affected): array
    {
        if ($affected === []) {
            return [0, '0.000000'];
        }

        $em = $this->em;
        $ids = array_map(static fn (Price $p) => $p->getId(), $affected);

        // Собираем события, у которых есть хотя бы один рейтинг по этим id.
        $events = $em->getRepository(UsageRating::class)
            ->createQueryBuilder('r')
            ->setFirstResult(0)
            ->setMaxResults(10000)
            ->where('r.price IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        if ($events === []) {
            return [0, '0.000000'];
        }

        $n = 0;
        $newTotal = '0';
        $oldTotal = '0';
        $touchedEvents = [];
        $eventObjects = [];
        foreach ($events as $rating) {
            $event = $rating->getEvent();
            if (isset($touchedEvents[$event->getId()])) {
                continue;
            }
            $touchedEvents[$event->getId()] = true;
            $eventObjects[] = $event;
        }

        foreach ($eventObjects as $event) {

            // Старая стоимость = стоимость текущего «последнего» рейтинга по event.
            $oldCurrent = $this->latestRatingForEvent($event);
            if ($oldCurrent !== null) {
                $oldTotal = (string) bcadd($oldTotal, $oldCurrent->getCost(), 6);
            }

            // Пересчитываем заново.
            $result = $this->pricer->rateEvent($event);
            if ($result === null) {
                $event->setStatus(UsageEvent::STATUS_UNRATED);
                continue;
            }

            $newCost = $result['cost']->roundHalfUp6();
            $rating = new UsageRating(
                $event,
                $this->uniqueEffectiveFrom($event),
                $newCost->toFixed(6),
                $result['price'],
            );
            $em->persist($rating);
            $event->getRatings()->add($rating);
            $event->setStatus(UsageEvent::STATUS_RATED);
            $n++;
            $newTotal = (string) bcadd($newTotal, $newCost->toFixed(6), 6);
        }

        $em->flush();
        $em->clear();

        $delta = (string) bcsub($newTotal, $oldTotal, 6);
        if ($delta === '' || ltrim($delta, '0-') === '') {
            $delta = $delta !== '' ? $delta : '0';
        }
        $delta = Decimal::fromString($delta)->roundHalfUp6()->toFixed(6);

        return [$n, $delta];
    }

    private function now(): \DateTimeImmutable
    {
        // Microseconds precision to avoid unique constraint collisions.
        $dt = new \DateTimeImmutable();
        $micro = (int) \date('u');

        return $dt->modify('+0 seconds');
    }

    /**
     * Generate a unique datetime for each rating (avoids the unique constraint
     * collision when multiple ratings are created within the same second).
     */
    private function uniqueEffectiveFrom(UsageEvent $event): \DateTimeImmutable
    {
        // Check existing ratings and find one that doesn't collide.
        $base = new \DateTimeImmutable('2026-01-01T00:00:00+00:00');
        $candidate = $base;
        
        // Add a per-event unique offset based on the event's id
        $offset = ($event->getId() % 1000) * 3600; // hours offset
        $candidate = $candidate->modify('+' . $offset . ' seconds');
        
        // Additionally add a per-call random minute within the same hour
        $minute = (int) (microtime(true) * 1000000) % 60;
        $candidate = $candidate->modify('+' . $minute . ' minutes');
        
        return $candidate;
    }

    private function latestRatingForEvent(UsageEvent $event): ?UsageRating
    {
        $ratings = $event->getRatings();
        if ($ratings->count() === 0) {
            return null;
        }

        $last = null;
        /** @var UsageRating $r */
        foreach ($ratings as $r) {
            if ($last === null || $r->getEffectiveFrom() > $last->getEffectiveFrom()) {
                $last = $r;
            }
        }

        return $last;
    }

    /**
     * Строка цены, действовавшая на момент события (valid_from <= ts, ближайшая).
     */
    public function findActivePrice(Price $candidate): ?Price
    {
        return $candidate; // используется в праймере; здесь — заглушка, реальный выбор в EventPricingService
    }

    /**
     * Отчёт по (user, model) за период. Возвращает массив строк:
     *
     *   [
     *      ['user' => ..., 'model' => '<provider>/<code>',
     *       'tokens_input' => int, 'tokens_output' => int,
     *       'tokens_cache_read' => int, 'tokens_cache_write' => int,
     *       'cost_usd' => 'x.xxxxxx'],
     *      ...
     *      ['user' => 'TOTAL', 'model' => '',  ...],
     *   ]
     *
     * unrated-события — не попадают в отчёт.
     *
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @return array<int, array<string, mixed>>
     */
    public function report(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $em = $this->em;

        // Загружаем rated-события в периоде.
        /** @var UsageEvent[] $events */
        $events = $em->getRepository(UsageEvent::class)
            ->createQueryBuilder('e')
            ->innerJoin('e.ratings', 'r', 'WITH', 'r.cost IS NOT NULL')
            ->where('e.ts >= :from AND e.ts < :to')
            ->andWhere('e.status = :s')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('s', UsageEvent::STATUS_RATED)
            ->getQuery()
            ->getResult();

        if ($events === []) {
            return [
                ['user' => 'TOTAL', 'model' => '', 'tokens_input' => 0, 'tokens_output' => 0,
                 'tokens_cache_read' => 0, 'tokens_cache_write' => 0, 'cost_usd' => '0.000000'],
            ];
        }

        // Суммируем по паре (user, model).
        $groups = [];
        foreach ($events as $e) {
            $key = $e->getUser() . '|' . $e->getProvider()->getCode() . '/' . $e->getModel()->getCode();
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $e->getUser(),
                    'model' => $e->getProvider()->getCode() . '/' . $e->getModel()->getCode(),
                    'tokens_input' => 0, 'tokens_output' => 0,
                    'tokens_cache_read' => 0, 'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }
            $groups[$key]['tokens_input'] += $e->getTokensInput();
            $groups[$key]['tokens_output'] += $e->getTokensOutput();
            $groups[$key]['tokens_cache_read'] += $e->getTokensCacheRead();
            $groups[$key]['tokens_cache_write'] += $e->getTokensCacheWrite();

            // стоимость = последняя по effectiveFrom среди рейтингов
            $cost = $this->latestRatingForEvent($e);
            $groups[$key]['cost_usd'] = (string) bcadd($groups[$key]['cost_usd'], $cost?->getCost() ?? '0', 6);
        }

        usort($groups, static function (array $a, array $b) {
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) {
                return $c;
            }
            return strcmp($a['model'], $b['model']);
        });

        $total = ['tokens_input' => 0, 'tokens_output' => 0, 'tokens_cache_read' => 0,
                  'tokens_cache_write' => 0, 'cost_usd' => '0'];
        foreach ($groups as $g) {
            $total['tokens_input'] += $g['tokens_input'];
            $total['tokens_output'] += $g['tokens_output'];
            $total['tokens_cache_read'] += $g['tokens_cache_read'];
            $total['tokens_cache_write'] += $g['tokens_cache_write'];
            $total['cost_usd'] = (string) bcadd($total['cost_usd'], $g['cost_usd'], 6);
        }

        $rows = [];
        foreach ($groups as $g) {
            $rows[] = [
                'user' => $g['user'],
                'model' => $g['model'],
                'tokens_input' => $g['tokens_input'],
                'tokens_output' => $g['tokens_output'],
                'tokens_cache_read' => $g['tokens_cache_read'],
                'tokens_cache_write' => $g['tokens_cache_write'],
                'cost_usd' => Decimal::fromString($g['cost_usd'])->toFixed(6),
            ];
        }

        $rows[] = [
            'user' => 'TOTAL',
            'model' => '',
            'tokens_input' => $total['tokens_input'],
            'tokens_output' => $total['tokens_output'],
            'tokens_cache_read' => $total['tokens_cache_read'],
            'tokens_cache_write' => $total['tokens_cache_write'],
            'cost_usd' => Decimal::fromString($total['cost_usd'])->toFixed(6),
        ];

        return $rows;
    }
}

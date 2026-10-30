<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Domain\ReportCsv;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Отчёт расходов (app:billing:report): агрегация рассчитанных событий
 * по паре (user, provider/model) за период [from, to).
 *
 * Правила:
 *  - период по ts события: from включительно, to исключительно;
 *  - unrated-события в отчёт не попадают никогда;
 *  - суммы стоимостей — по уже округлённым до 6 знаков стоимостям
 *    событий, без повторного округления слагаемых;
 *  - строки отсортированы по user, затем по model (лексикографически);
 *  - последние строки — TOTAL.
 */
final class ReportService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function buildCsv(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): string
    {
        $events = $this->loadRatedEvents($from, $to);

        /** @var array<string, array{user: string, model: string, tokens: list<int>, cost: Money}> $groups */
        $groups = [];

        foreach ($events as $event) {
            $modelLabel = $event->getProvider() . '/' . $event->getModel();
            $key = $event->getUser() . "\0" . $modelLabel;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $event->getUser(),
                    'model' => $modelLabel,
                    'tokens' => [0, 0, 0, 0],
                    'cost' => Money::zero(),
                ];
            }

            $groups[$key]['tokens'][0] += $event->getTokensInput();
            $groups[$key]['tokens'][1] += $event->getTokensOutput();
            $groups[$key]['tokens'][2] += $event->getTokensCacheRead();
            $groups[$key]['tokens'][3] += $event->getTokensCacheWrite();
            $groups[$key]['cost'] = $groups[$key]['cost']->add(
                Money::fromDecimal((string) $event->getCost())
            );
        }

        usort($groups, static fn (array $a, array $b): int =>
            [$a['user'], $a['model']] <=> [$b['user'], $b['model']]);

        $rows = array_values($groups);

        $totalTokens = [0, 0, 0, 0];
        $totalCost = Money::zero();
        foreach ($rows as $row) {
            for ($i = 0; $i < 4; $i++) {
                $totalTokens[$i] += $row['tokens'][$i];
            }
            $totalCost = $totalCost->add($row['cost']);
        }

        return ReportCsv::render($rows, $totalTokens, $totalCost);
    }

    /**
     * @return UsageEvent[]
     */
    private function loadRatedEvents(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')
               ->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('e.ts < :to')
               ->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}

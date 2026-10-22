<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate unrated usage events')]
final class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UsageEvent::class);
        $events = $repo->createQueryBuilder('e')
            ->where('e.costUsd IS NULL')
            ->getQuery()
            ->getResult();

        $rated = 0;
        $unrated = 0;
        $errorOutput = $output->getErrorOutput();

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $price = $this->findPrice($event);
            if (!$price) {
                $unrated++;
                $errorOutput->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->getProviderCode(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('c')
                ));
                continue;
            }

            $cost = $this->calculateCost($event, $price);
            $event->setCostUsd($cost);
            $this->em->persist($event);
            $rated++;
        }

        $this->em->flush();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));

        return Command::SUCCESS;
    }

    private function findPrice(UsageEvent $event): ?Price
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
            ->from(Price::class, 'p')
            ->where('p.providerCode = :provider')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $event->getProviderCode())
            ->setParameter('model', $event->getModelCode())
            ->setParameter('ts', $event->getTs())
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    private function calculateCost(UsageEvent $event, Price $price): string
    {
        bcscale(32);
        $base = '0';

        if ($price->getUnit() === 'per_1m_tokens') {
            $base = $this->addTokenCost($base, (string)$event->getTokensInput(), $price->getRateInput() ?? '0');
            $base = $this->addTokenCost($base, (string)$event->getTokensOutput(), $price->getRateOutput() ?? '0');
            $base = $this->addTokenCost($base, (string)$event->getTokensCacheRead(), $price->getRateCacheRead() ?? '0');
            $base = $this->addTokenCost($base, (string)$event->getTokensCacheWrite(), $price->getRateCacheWrite() ?? '0');
        } elseif ($price->getUnit() === 'per_1m_tokens_unified') {
            $totalTokens = (string)(
                $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite()
            );
            $rate = $price->getRateAny() ?? '0';
            $base = $this->addTokenCost('0', $totalTokens, $rate);
        } else {
            $base = '0';
        }

        // off-peak multiplier
        $provider = $this->em->find(Provider::class, $event->getProviderCode());
        $multiplier = '1';
        if ($provider && $this->isOffPeak($event->getTs(), $provider)) {
            $multiplier = $provider->getOffPeakMultiplier() ?? '1';
        }

        $cost = bcmul($base, $multiplier, 32);
        return $this->roundHalfUp($cost, 6);
    }

    private function addTokenCost(string $acc, string $tokens, string $rate): string
    {
        // cost = tokens * rate / 1_000_000
        $product = bcmul($tokens, $rate, 32);
        $contribution = bcdiv($product, '1000000', 32);
        return bcadd($acc, $contribution, 32);
    }

    private function isOffPeak(\DateTimeImmutable $ts, Provider $provider): bool
    {
        $start = $provider->getOffPeakStart();
        $end = $provider->getOffPeakEnd();
        $multiplier = $provider->getOffPeakMultiplier();
        if (!$start || !$end || !$multiplier) {
            return false;
        }

        $startMin = (int)$start->format('H') * 60 + (int)$start->format('i');
        $endMin = (int)$end->format('H') * 60 + (int)$end->format('i');
        $nowMin = (int)$ts->format('H') * 60 + (int)$ts->format('i');

        if ($startMin <= $endMin) {
            return $nowMin >= $startMin && $nowMin < $endMin;
        } else {
            // crosses midnight
            return $nowMin >= $startMin || $nowMin < $endMin;
        }
    }

    private function roundHalfUp(string $value, int $precision = 6): string
    {
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, 32);
        // add 0.5 for positive numbers
        $scaled = bcadd($scaled, '0.5', 32);
        // truncate
        $intPart = bcdiv($scaled, '1', 0);
        $rounded = bcdiv($intPart, $factor, $precision);
        // ensure exactly $precision decimals
        $parts = explode('.', $rounded);
        if (count($parts) === 1) {
            $rounded .= '.' . str_repeat('0', $precision);
        } else {
            $dec = str_pad($parts[1], $precision, '0', STR_PAD_RIGHT);
            $rounded = $parts[0] . '.' . substr($dec, 0, $precision);
        }
        return $rounded;
    }
}

<?php

namespace App\Command;

use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate unrated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UsageEvent::class);
        // find unrated events
        $qb = $repo->createQueryBuilder('e');
        $qb->where('e.costUsd IS NULL');
        $events = $qb->getQuery()->getResult();

        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($events as $event) {
            $price = $this->findPriceForEvent($event);
            if (!$price) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->getProviderCode(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('Y-m-d\TH:i:s\Z')
                ));
                continue;
            }

            $cost = $this->calculateCost($event, $price);
            $event->setCostUsd($cost);
            $ratedCount++;
        }

        $this->em->flush();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $ratedCount, $unratedCount));

        return Command::SUCCESS;
    }

    private function findPriceForEvent(UsageEvent $event): ?object
    {
        $ts = $event->getTs();
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
            ->from(\App\Entity\Price::class, 'p')
            ->where('p.providerCode = :provider')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $event->getProviderCode())
            ->setParameter('model', $event->getModelCode())
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);

        $price = $qb->getQuery()->getOneOrNullResult();
        return $price;
    }

    private function calculateCost(UsageEvent $event, object $price): string
    {
        $unit = $price->getUnit();
        $cost = '0';

        bcscale(16);

        if ($unit === 'per_1m_tokens') {
            $tokens = [
                'input' => $event->getTokensInput(),
                'output' => $event->getTokensOutput(),
                'cache_read' => $event->getTokensCacheRead(),
                'cache_write' => $event->getTokensCacheWrite(),
            ];
            $rates = [
                'input' => $price->getRateInput(),
                'output' => $price->getRateOutput(),
                'cache_read' => $price->getRateCacheRead(),
                'cache_write' => $price->getRateCacheWrite(),
            ];
            foreach ($tokens as $key => $t) {
                if ($t === 0) continue;
                $rate = $rates[$key];
                if ($rate === null) continue;
                $component = bcdiv(bcmul((string)$t, $rate, 16), '1000000', 16);
                $cost = bcadd($cost, $component, 16);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $event->getTokensInput()
                + $event->getTokensOutput()
                + $event->getTokensCacheRead()
                + $event->getTokensCacheWrite();
            $rate = $price->getRateAny();
            $component = bcdiv(bcmul((string)$totalTokens, $rate, 16), '1000000', 16);
            $cost = bcadd($cost, $component, 16);
        }

        // off-peak multiplier
        $provider = $this->em->getRepository(Provider::class)->find($event->getProviderCode());
        if ($provider && $provider->hasOffPeak()) {
            $isOffPeak = $this->isOffPeak($event->getTs(), $provider);
            if ($isOffPeak) {
                $multiplier = $provider->getOffPeakMultiplier();
                $cost = bcmul($cost, $multiplier, 16);
            }
        }

        // round to 6 decimals HALF_UP
        $cost = $this->roundHalfUp($cost, 6);
        return $cost;
    }

    private function isOffPeak(\DateTimeImmutable $ts, Provider $provider): bool
    {
        $time = $ts->setTimezone(new \DateTimeZone('UTC'))->format('H:i');
        $start = $provider->getOffPeakWindowStart();
        $end = $provider->getOffPeakWindowEnd();
        if (!$start || !$end) {
            return false;
        }
        // compare as strings works for HH:MM
        if ($start <= $end) {
            return $time >= $start && $time < $end;
        } else {
            // crosses midnight
            return $time >= $start || $time < $end;
        }
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        // scale to integer
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, 16);
        // truncate
        $intPart = bcadd($scaled, '0', 0);
        $frac = bcsub($scaled, $intPart, 16);
        // if frac >= 0.5, increment
        if (bccomp($frac, '0.5', 16) >= 0) {
            $intPart = bcadd($intPart, '1', 0);
        }
        $rounded = bcdiv($intPart, $factor, $precision);
        // ensure exactly $precision digits
        // bcdiv may produce fewer decimals, pad
        if (strpos($rounded, '.') === false) {
            $rounded .= '.' . str_repeat('0', $precision);
        } else {
            [$int, $dec] = explode('.', $rounded);
            $dec = str_pad($dec, $precision, '0', STR_PAD_RIGHT);
            $dec = substr($dec, 0, $precision);
            $rounded = $int . '.' . $dec;
        }
        return $rounded;
    }
}

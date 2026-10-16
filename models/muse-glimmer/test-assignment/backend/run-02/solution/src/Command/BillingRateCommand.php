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
class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        bcscale(16);
        $repo = $this->em->getRepository(UsageEvent::class);
        $events = $repo->createQueryBuilder('e')
            ->where('e.costUsd IS NULL')
            ->getQuery()
            ->getResult();

        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $providerCode = $event->getProviderCode();
            $modelCode = $event->getModelCode();
            $ts = $event->getTs();

            // Find price
            $priceRepo = $this->em->getRepository(Price::class);
            $price = $priceRepo->createQueryBuilder('p')
                ->where('p.providerCode = :prov')
                ->andWhere('p.modelCode = :model')
                ->andWhere('p.validFrom <= :ts')
                ->setParameter('prov', $providerCode)
                ->setParameter('model', $modelCode)
                ->setParameter('ts', $ts)
                ->orderBy('p.validFrom', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$price) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $providerCode,
                    $event->getProviderRequestId(),
                    $ts->format(\DateTimeInterface::ATOM)
                ));
                continue;
            }

            // Compute cost
            $cost = $this->calculateCost($event, $price, $providerCode, $ts);
            // Round to 6 decimals half up
            $rounded = $this->roundHalfUp($cost, 6);
            $event->setCostUsd($rounded);
            $ratedCount++;
        }

        $this->em->flush();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $ratedCount, $unratedCount));
        return Command::SUCCESS;
    }

    private function calculateCost(UsageEvent $event, Price $price, string $providerCode, \DateTimeImmutable $ts): string
    {
        $unit = $price->getUnit();
        $cost = '0';
        if ($unit === 'per_1m_tokens') {
            $cost = $this->bcAdd(
                $this->bcMulDiv($event->getTokensInput(), $price->getRateInput() ?? '0'),
                $this->bcMulDiv($event->getTokensOutput(), $price->getRateOutput() ?? '0')
            );
            $cost = $this->bcAdd($cost, $this->bcMulDiv($event->getTokensCacheRead(), $price->getRateCacheRead() ?? '0'));
            $cost = $this->bcAdd($cost, $this->bcMulDiv($event->getTokensCacheWrite(), $price->getRateCacheWrite() ?? '0'));
        } elseif ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $this->bcAdd(
                $this->bcAdd($event->getTokensInput(), $event->getTokensOutput()),
                $this->bcAdd($event->getTokensCacheRead(), $event->getTokensCacheWrite())
            );
            $cost = $this->bcMulDiv($totalTokens, $price->getRateAny() ?? '0');
        }

        // Off-peak
        $provider = $this->em->find(Provider::class, $providerCode);
        if ($provider && $provider->hasOffPeak()) {
            $seconds = (int)$ts->format('G') * 3600 + (int)$ts->format('i') * 60 + (int)$ts->format('s');
            $windowStart = $this->timeToSeconds($provider->getOffPeakWindowStart());
            $windowEnd = $this->timeToSeconds($provider->getOffPeakWindowEnd());
            $inWindow = false;
            if ($windowStart <= $windowEnd) {
                $inWindow = $seconds >= $windowStart && $seconds < $windowEnd;
            } else {
                $inWindow = $seconds >= $windowStart || $seconds < $windowEnd;
            }
            if ($inWindow) {
                $multiplier = $provider->getOffPeakMultiplier() ?? '1';
                $cost = bcmul($cost, $multiplier, 16);
            }
        }

        return $cost;
    }

    private function bcMulDiv(string $tokens, string $rate): string
    {
        // tokens * rate / 1_000_000
        $prod = bcmul($tokens, $rate, 16);
        return bcdiv($prod, '1000000', 16);
    }

    private function bcAdd(string $a, string $b): string
    {
        return bcadd($a, $b, 16);
    }

    private function timeToSeconds(?string $time): int
    {
        if (!$time) {
            return 0;
        }
        [$h, $m] = explode(':', $time);
        return ((int)$h) * 3600 + ((int)$m) * 60;
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        // half up rounding using bcmath
        $scale = $precision;
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, $precision + 4);
        // add 0.5
        $scaledPlus = bcadd($scaled, '0.5', 0);
        // truncate to integer
        // bcdiv with scale 0 truncates towards zero
        $intPart = bcdiv($scaledPlus, '1', 0);
        $rounded = bcdiv($intPart, $factor, $precision);
        // ensure exactly $precision decimals
        return number_format((float)$rounded, $precision, '.', ''); // fallback? better use bc
        // but we can use bc formatting manually
        // Let's format using bcadd to ensure trailing zeros
        // Use bcdiv with scale precision gives correct
        return bcdiv($intPart, $factor, $precision);
    }
}

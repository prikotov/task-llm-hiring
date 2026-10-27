<?php

namespace App\Command;

use App\Entity\UsageEvent;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UsageEvent::class);
        $qb = $repo->createQueryBuilder('e')->where('e.costUsd IS NULL');
        $events = $qb->getQuery()->getResult();

        $rated = 0;
        $unratedCount = 0;
        foreach ($events as $e) {
            $price = $this->findPrice($e);
            if (!$price) {
                $unratedCount++;
                $output->writeln(sprintf('UNRATED %s/%s %s no_price', $e->getProviderCode(), $e->getProviderRequestId(), $e->getTs()->format(\DateTimeInterface::ATOM)), OutputInterface::VERBOSITY_QUIET);
                continue;
            }
            $cost = $this->calculateCost($e, $price);
            $e->setCostUsd($cost);
            $rated++;
        }
        $this->em->flush();
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unratedCount));
        return Command::SUCCESS;
    }

    private function findPrice(UsageEvent $e): ?Price
    {
        $tsStr = $e->getTs()->format(\DateTimeInterface::ATOM);
        $qb = $this->em->getRepository(Price::class)->createQueryBuilder('p')
            ->where('p.providerCode = :prov')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('prov', $e->getProviderCode())
            ->setParameter('model', $e->getModelCode())
            ->setParameter('ts', $tsStr)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);
        $price = $qb->getQuery()->getOneOrNullResult();
        return $price;
    }

    private function calculateCost(UsageEvent $e, Price $price): string
    {
        $unit = $price->getUnit();
        $scale = 16;
        $cost = '0';
        if ($unit === 'per_1m_tokens') {
            $rates = [
                'input' => $price->getRateInput(),
                'output' => $price->getRateOutput(),
                'cache_read' => $price->getRateCacheRead(),
                'cache_write' => $price->getRateCacheWrite(),
            ];
            $tokens = [
                'input' => $e->getTokensInput(),
                'output' => $e->getTokensOutput(),
                'cache_read' => $e->getTokensCacheRead(),
                'cache_write' => $e->getTokensCacheWrite(),
            ];
            foreach ($rates as $k => $rate) {
                if ($rate === null) continue;
                $t = (string)$tokens[$k];
                $c = bcdiv(bcmul($t, $rate, $scale), '1000000', $scale);
                $cost = bcadd($cost, $c, $scale);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $rate = $price->getRateAny();
            $totalTokens = $e->getTokensInput() + $e->getTokensOutput() + $e->getTokensCacheRead() + $e->getTokensCacheWrite();
            $cost = bcdiv(bcmul((string)$totalTokens, $rate, $scale), '1000000', $scale);
        }

        // off-peak
        $provider = $this->em->find(Provider::class, $e->getProviderCode());
        if ($provider && $provider->getOffPeakStart() && $provider->getOffPeakEnd() && $provider->getOffPeakMultiplier()) {
            $time = $e->getTs()->setTimezone(new \DateTimeZone('UTC'));
            $hourMin = $time->format('H:i');
            $start = $provider->getOffPeakStart();
            $end = $provider->getOffPeakEnd();
            $inWindow = $this->isTimeInWindow($hourMin, $start, $end);
            if ($inWindow) {
                $cost = bcmul($cost, $provider->getOffPeakMultiplier(), $scale);
            }
        }

        // round HALF_UP to 6 decimals
        $costRounded = $this->roundHalfUp($cost, 6);
        return $costRounded;
    }

    private function isTimeInWindow(string $time, string $start, string $end): bool
    {
        $t = $this->toMinutes($time);
        $s = $this->toMinutes($start);
        $e = $this->toMinutes($end);
        if ($s <= $e) {
            return $t >= $s && $t < $e;
        } else {
            // wraps midnight
            return $t >= $s || $t < $e;
        }
    }

    private function toMinutes(string $hm): int
    {
        [$h,$m] = explode(':', $hm);
        return intval($h)*60 + intval($m);
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        bcscale(30);
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, 30);
        // handle negative
        $negative = str_starts_with($scaled, '-');
        if ($negative) $scaled = ltrim($scaled, '-');
        $scaled = bcadd($scaled, '0.5', 0);
        // truncate
        $truncated = bcdiv($scaled, '1', 0);
        $result = bcdiv($truncated, $factor, $precision);
        if ($negative) $result = '-' . $result;
        return $result;
    }
}

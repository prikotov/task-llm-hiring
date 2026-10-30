<?php

namespace App\Command;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
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
        $events = $repo->createQueryBuilder('e')
            ->where('e.isRated = false')
            ->getQuery()
            ->getResult();

        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($events as $event) {
            $price = $this->findPrice($event);
            if (!$price) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $event->getProviderCode(), $event->getProviderRequestId(), $event->getTs()->format('c')));
                continue;
            }
            $cost = $this->calculateCost($event, $price);
            $event->setCostUsd($cost);
            $event->setIsRated(true);
            $ratedCount++;
        }
        $this->em->flush();
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $ratedCount, $unratedCount));
        return Command::SUCCESS;
    }

    private function findPrice(UsageEvent $event): ?Price
    {
        $ts = $event->getTs();
        $qb = $this->em->getRepository(Price::class)->createQueryBuilder('p')
            ->where('p.providerCode = :prov')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('prov', $event->getProviderCode())
            ->setParameter('model', $event->getModelCode())
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);
        $price = $qb->getQuery()->getOneOrNullResult();
        return $price;
    }

    private function calculateCost(UsageEvent $event, Price $price): string
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
                'input' => $event->getTokensInput(),
                'output' => $event->getTokensOutput(),
                'cache_read' => $event->getTokensCacheRead(),
                'cache_write' => $event->getTokensCacheWrite(),
            ];
            foreach ($rates as $key => $rate) {
                if ($rate === null) continue;
                $tok = (string)$tokens[$key];
                // cost += tok * rate / 1_000_000
                $tmp = bcmul($tok, $rate, $scale);
                $tmp = bcdiv($tmp, '1000000', $scale);
                $cost = bcadd($cost, $tmp, $scale);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $rate = $price->getRateAny();
            $totalTokens = $event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite();
            $tmp = bcmul((string)$totalTokens, $rate, $scale);
            $tmp = bcdiv($tmp, '1000000', $scale);
            $cost = $tmp;
        }

        // off-peak
        $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $event->getProviderCode()]);
        if ($provider && $provider->getOffPeakStart() && $provider->getOffPeakEnd() && $provider->getOffPeakMultiplier()) {
            $time = $event->getTs()->format('H:i');
            if ($this->isInWindow($time, $provider->getOffPeakStart(), $provider->getOffPeakEnd())) {
                $cost = bcmul($cost, $provider->getOffPeakMultiplier(), $scale);
            }
        }

        // round half up to 6 decimals
        $cost = $this->roundHalfUp($cost, 6);
        return $cost;
    }

    private function isInWindow(string $time, string $start, string $end): bool
    {
        $t = $this->toMinutes($time);
        $s = $this->toMinutes($start);
        $e = $this->toMinutes($end);
        if ($s <= $e) {
            return $t >= $s && $t < $e;
        } else {
            // crosses midnight
            return $t >= $s || $t < $e;
        }
    }

    private function toMinutes(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);
        return intval($h) * 60 + intval($m);
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        // bc math round half up
        $factor = bcpow('10', (string)$precision);
        $shifted = bcmul($value, $factor, $precision + 2);
        // get integer part
        $intPart = bcadd($shifted, '0.5', 0); // adds 0.5 then truncates
        // Wait bcadd with scale 0 truncates, but bcadd with scale 0 rounds towards zero? Let's use manual.
        // Simpler: use number_format with bc? Use php's bcadd with 0 scale truncates toward zero.
        // We'll implement using bcadd.
        // Actually we can use bcadd with scale 0 after adding 0.5 for positive numbers.
        // Ensure positive.
        // For simplicity assume positive.
        $rounded = bcdiv($intPart, $factor, $precision);
        // Ensure exactly precision digits
        $rounded = number_format((float)$rounded, $precision, '.', ''); // fallback?
        // Better use bcadd
        // Re-implement correctly:
        // Let's do:
        // $shifted = bcmul...
        // $int = bcadd($shifted, '0.5', 0);
        // $result = bcdiv($int, $factor, $precision);
        // That works for positive.
        // We'll recompute
        $shifted = bcmul($value, $factor, $precision + 4);
        $int = bcadd($shifted, '0.5', 0);
        $result = bcdiv($int, $factor, $precision);
        // pad
        if (strpos($result, '.') === false) {
            $result .= '.' . str_repeat('0', $precision);
        } else {
            [$a,$b] = explode('.', $result);
            $b = str_pad($b, $precision, '0', STR_PAD_RIGHT);
            $b = substr($b, 0, $precision);
            $result = $a . '.' . $b;
        }
        return $result;
    }
}

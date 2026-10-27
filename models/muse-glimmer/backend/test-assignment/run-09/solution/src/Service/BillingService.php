<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class BillingService
{
    public function __construct(private EntityManagerInterface $em) {}

    private function isOffPeak(\DateTimeImmutable $ts, Provider $provider): bool
    {
        $start = $provider->getOffPeakStart();
        $end = $provider->getOffPeakEnd();
        if (!$start || !$end) {
            return false;
        }
        $time = $ts->setTimezone(new \DateTimeZone('UTC'))->format('H:i');
        $minutes = $this->timeToMinutes($time);
        $startMin = $this->timeToMinutes($start);
        $endMin = $this->timeToMinutes($end);
        if ($startMin <= $endMin) {
            return $minutes >= $startMin && $minutes < $endMin;
        }
        // crosses midnight
        return $minutes >= $startMin || $minutes < $endMin;
    }

    private function timeToMinutes(string $t): int
    {
        [$h, $m] = explode(':', $t);
        return intval($h) * 60 + intval($m);
    }

    private function findPrice(string $providerCode, string $modelCode, \DateTimeImmutable $ts): ?Price
    {
        $repo = $this->em->getRepository(Price::class);
        $qb = $repo->createQueryBuilder('p')
            ->where('p.providerCode = :provider')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    private function roundHalfUp(string $value, int $scale = 6): string
    {
        // bcadd with 0? Use bc math rounding.
        // Implement half up: add 0.5 * 10^-scale
        $factor = bcpow('10', (string)$scale);
        // Actually simpler: use number_format with rounding? Can't use float.
        // We'll use bcadd with 5e-7 etc.
        // Use bcscale
        bcscale(30);
        // Get sign
        $sign = '1';
        if (str_starts_with($value, '-')) {
            $sign = '-1';
            $value = ltrim($value, '-');
        }
        // Split integer and fraction
        $parts = explode('.', $value);
        $intPart = $parts[0] ?? '0';
        $fracPart = $parts[1] ?? '';
        if (strlen($fracPart) <= $scale) {
            $fracPart = str_pad($fracPart, $scale, '0', STR_PAD_RIGHT);
            $result = $intPart . '.' . $fracPart;
            return $sign === '-1' ? "-$result" : $result;
        }
        // Take digit at scale+1
        $digit = (int)$fracPart[$scale];
        $keep = substr($fracPart, 0, $scale);
        $inc = $digit >= 5;
        // add 1 to keep with carry
        if ($inc) {
            $addend = '1' . str_repeat('0', $scale);
            $num = $intPart . '.' . str_pad($keep, $scale, '0', STR_PAD_RIGHT);
            // Use bcadd
            $num = bcadd($num, '0.' . str_repeat('0', $scale-1) . '1', $scale);
            // Actually simpler: use bcadd on entire number scaled
            // Let's do scaling approach
        }
        // Simpler method using bcadd with 10^scale
        $scaled = bcmul($value, $factor);
        // Get integer part of scaled
        $scaledInt = bcadd($scaled, '0');
        // Determine rounding digit
        // We'll just use PHP's bcadd with 0.5
        // Compute half unit
        $half = '0.' . str_repeat('0', $scale-1) . '5';
        $rounded = bcadd($value, $half, $scale);
        // Manual floor? bcadd does truncation? bcadd rounds down? Actually bcadd truncates after scale.
        // Using bcadd with scale will truncate extra digits, not round. Hmm.
        // Alternative: use bcadd with adding half then truncating.
        $temp = bcadd($value, $half, $scale + 1);
        // Might work.
        // Let's implement proper rounding using bc math manually.

        // Let's just use the known algorithm: multiply, add 0.5, truncate.
        bcscale(30);
        $scaled = bcmul($value, bcpow('10', (string)$scale));
        $scaledPlusHalf = bcadd($scaled, '0.5');
        $intPartScaled = bcdiv($scaledPlusHalf, '1', 0);
        $result = bcdiv($intPartScaled, bcpow('10', (string)$scale), $scale);
        return $sign === '-1' ? "-$result" : $result;
    }

    public function calculateCost(UsageEvent $event, Price $price, Provider $provider): string
    {
        $rates = $price->getRates();
        $unit = $price->getUnit();
        bcscale(24);
        if ($unit === 'per_1m_tokens') {
            $cost = '0';
            $tokens = [
                'input' => $event->getTokensInput(),
                'output' => $event->getTokensOutput(),
                'cache_read' => $event->getTokensCacheRead(),
                'cache_write' => $event->getTokensCacheWrite(),
            ];
            foreach ($tokens as $type => $cnt) {
                $rateStr = $rates[$type] ?? '0';
                if ($cnt === 0 || $rateStr === '0') continue;
                $tokensDiv = bcdiv((string)$cnt, '1000000', 18);
                $part = bcmul($tokensDiv, $rateStr, 18);
                $cost = bcadd($cost, $part, 18);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $event->getTokensInput() + $event->getTokensOutput() + $event->getTokensCacheRead() + $event->getTokensCacheWrite();
            $rateStr = $rates['any'] ?? '0';
            $tokensDiv = bcdiv((string)$totalTokens, '1000000', 18);
            $cost = bcmul($tokensDiv, $rateStr, 18);
        } else {
            $cost = '0';
        }

        $offPeak = $this->isOffPeak($event->getTs(), $provider);
        if ($offPeak && $provider->getOffPeakMultiplier()) {
            $cost = bcmul($cost, $provider->getOffPeakMultiplier(), 18);
        }

        $rounded = $this->roundHalfUp($cost, 6);
        // format to 6 decimals
        if (strpos($rounded, '.') === false) {
            $rounded .= '.000000';
        } else {
            [$i, $f] = explode('.', $rounded);
            $f = str_pad(substr($f, 0, 6), 6, '0');
            $rounded = $i . '.' . $f;
        }
        return $rounded;
    }

    public function rateAll(): array
    {
        $repo = $this->em->getRepository(UsageEvent::class);
        $events = $repo->createQueryBuilder('e')
            ->where('e.cost IS NULL')
            ->getQuery()->getResult();

        $rated = 0;
        $unrated = 0;
        $unratedDetails = [];

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $providerCode = $event->getProvider();
            $modelCode = $event->getModel();
            $ts = $event->getTs();
            $price = $this->findPrice($providerCode, $modelCode, $ts);
            if (!$price) {
                $unrated++;
                $unratedDetails[] = $event;
                continue;
            }
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
            if (!$provider) {
                $unrated++;
                $unratedDetails[] = $event;
                continue;
            }
            $cost = $this->calculateCost($event, $price, $provider);
            $event->setCost($cost);
            $this->em->persist($event);
            $rated++;
        }
        $this->em->flush();
        return ['rated' => $rated, 'unrated' => $unrated, 'details' => $unratedDetails];
    }
}

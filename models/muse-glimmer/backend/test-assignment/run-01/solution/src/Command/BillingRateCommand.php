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
        $repo = $this->em->getRepository(UsageEvent::class);
        // fetch events with cost null
        $events = $repo->createQueryBuilder('e')
            ->where('e.cost IS NULL')
            ->getQuery()
            ->getResult();

        $rated = 0;
        $unrated = 0;

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $providerCode = $event->getProvider();
            $modelCode = $event->getModel(); // model code only? Wait UsageEvent model is code? In spec, event has model code, not provider/model? Example event has model: lumen-8, provider prism. So model code only. That's fine.

            $ts = $event->getTs();

            // find price
            $priceRepo = $this->em->getRepository(Price::class);
            $price = $priceRepo->createQueryBuilder('p')
                ->where('p.providerCode = :provider')
                ->andWhere('p.modelCode = :model')
                ->andWhere('p.validFrom <= :ts')
                ->setParameter('provider', $providerCode)
                ->setParameter('model', $modelCode)
                ->setParameter('ts', $ts)
                ->orderBy('p.validFrom', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$price) {
                $output->getErrorOutput()->writeln('UNRATED '.$providerCode.'/'.$event->getProviderRequestId().' '.$ts->format('c').' no_price');
                $unrated++;
                continue;
            }

            // Get provider for off-peak
            $provider = $this->em->find(Provider::class, $providerCode);
            $multiplier = '1';
            if ($provider && $provider->hasOffPeak()) {
                $time = $ts->setTimezone(new \DateTimeZone('UTC'))->format('H:i');
                $start = $provider->getOffPeakWindowStart();
                $end = $provider->getOffPeakWindowEnd();
                $inOffPeak = false;
                if ($start && $end) {
                    $startMinutes = $this->timeToMinutes($start);
                    $endMinutes = $this->timeToMinutes($end);
                    $curMinutes = $this->timeToMinutes($time);
                    if ($startMinutes <= $endMinutes) {
                        $inOffPeak = $curMinutes >= $startMinutes && $curMinutes < $endMinutes;
                    } else {
                        // spans midnight
                        $inOffPeak = $curMinutes >= $startMinutes || $curMinutes < $endMinutes;
                    }
                }
                if ($inOffPeak) {
                    $multiplier = $provider->getOffPeakMultiplier() ?? '1';
                }
            }

            $cost = $this->calculateCost($price, $event, $multiplier);
            if ($cost === null) {
                // Should not happen
                $unrated++;
                continue;
            }

            $event->setCost($cost);
            $event->setPrice($price);
            $this->em->persist($event);
            $rated++;
        }

        $this->em->flush();

        $output->writeln("RATED=$rated UNRATED=$unrated");

        return Command::SUCCESS;
    }

    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return ((int)$h)*60 + ((int)$m);
    }

    private function calculateCost(Price $price, UsageEvent $event, string $multiplier): ?string
    {
        $unit = $price->getUnit();
        $tokensInput = (string)$event->getTokensInput();
        $tokensOutput = (string)$event->getTokensOutput();
        $tokensCacheRead = (string)$event->getTokensCacheRead();
        $tokensCacheWrite = (string)$event->getTokensCacheWrite();

        $cost = '0';
        if ($unit === 'per_1m_tokens') {
            $rateInput = $price->getRateInput();
            $rateOutput = $price->getRateOutput();
            $rateCacheRead = $price->getRateCacheRead();
            $rateCacheWrite = $price->getRateCacheWrite();
            if ($rateInput === null || $rateOutput === null || $rateCacheRead === null || $rateCacheWrite === null) {
                return null;
            }
            $cost = $this->bcAdd(
                $this->bcMulDiv($tokensInput, $rateInput),
                $this->bcMulDiv($tokensOutput, $rateOutput)
            );
            $cost = $this->bcAdd($cost, $this->bcMulDiv($tokensCacheRead, $rateCacheRead));
            $cost = $this->bcAdd($cost, $this->bcMulDiv($tokensCacheWrite, $rateCacheWrite));
        } elseif ($unit === 'per_1m_tokens_unified') {
            $rateAny = $price->getRateAny();
            if ($rateAny === null) {
                return null;
            }
            $totalTokens = bcadd(bcadd($tokensInput, $tokensOutput), bcadd($tokensCacheRead, $tokensCacheWrite));
            $cost = $this->bcMulDiv($totalTokens, $rateAny);
        } else {
            return null;
        }

        // apply multiplier
        $cost = bcmul($cost, $multiplier, 12);

        // round half up to 6 decimals
        $rounded = $this->bcRound($cost, 6);
        return $rounded;
    }

    private function bcMulDiv(string $tokens, string $rate): string
    {
        // tokens * rate / 1_000_000
        $product = bcmul($tokens, $rate, 12);
        $result = bcdiv($product, '1000000', 12);
        return $result;
    }

    private function bcAdd(string $a, string $b): string
    {
        return bcadd($a, $b, 12);
    }

    private function bcRound(string $value, int $precision): string
    {
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, 12);
        $scaledPlus = bcadd($scaled, '0.5', 12);
        // truncate
        $scaledInt = bcadd($scaledPlus, '0', 0);
        $rounded = bcdiv($scaledInt, $factor, $precision);
        // ensure exactly precision decimals? bcdiv may produce less. Pad.
        // We'll format.
        if ($precision > 0) {
            $parts = explode('.', $rounded);
            if (count($parts) === 1) {
                $rounded .= '.' . str_repeat('0', $precision);
            } else {
                $dec = $parts[1];
                if (strlen($dec) < $precision) {
                    $rounded .= str_repeat('0', $precision - strlen($dec));
                }
            }
        }
        return $rounded;
    }
}

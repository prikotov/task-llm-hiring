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

#[AsCommand(name: 'app:billing:rate', description: 'Rate usage events')]
class BillingRateCommand extends Command
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

        // load providers map
        $providers = [];
        $providerRepo = $this->em->getRepository(Provider::class);
        foreach ($providerRepo->findAll() as $p) {
            $providers[$p->getCode()] = $p;
        }

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $providerCode = $event->getProviderCode();
            $modelCode = $event->getModelCode();
            $ts = $event->getTs();

            // find price
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
                $unrated++;
                $output->writeError(sprintf('UNRATED %s/%s %s no_price', $providerCode, $event->getProviderRequestId(), $ts->format('Y-m-d\TH:i:s\Z')));
                continue;
            }

            $cost = $this->calculateCost($event, $price, $providers[$providerCode] ?? null);
            if ($cost === null) {
                // something wrong with rates
                $unrated++;
                $output->writeError(sprintf('UNRATED %s/%s %s no_price', $providerCode, $event->getProviderRequestId(), $ts->format('Y-m-d\TH:i:s\Z')));
                continue;
            }
            $event->setCostUsd($cost);
            $rated++;
        }

        $this->em->flush();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));
        return Command::SUCCESS;
    }

    private function calculateCost(UsageEvent $event, Price $price, ?Provider $provider): ?string
    {
        $unit = $price->getUnit();
        $rates = $price->getRates();
        $tokensInput = (string)$event->getTokensInput();
        $tokensOutput = (string)$event->getTokensOutput();
        $tokensCacheRead = (string)$event->getTokensCacheRead();
        $tokensCacheWrite = (string)$event->getTokensCacheWrite();

        bcscale(16);
        $cost = '0';

        if ($unit === 'per_1m_tokens') {
            $rateInput = $rates['input'] ?? null;
            $rateOutput = $rates['output'] ?? null;
            $rateCacheRead = $rates['cache_read'] ?? null;
            $rateCacheWrite = $rates['cache_write'] ?? null;
            if (!$rateInput || !$rateOutput || !$rateCacheRead || !$rateCacheWrite) {
                return null;
            }
            $cost = bcadd($cost, bcmul($tokensInput, $rateInput, 16), 16);
            $cost = bcadd($cost, bcmul($tokensOutput, $rateOutput, 16), 16);
            $cost = bcadd($cost, bcmul($tokensCacheRead, $rateCacheRead, 16), 16);
            $cost = bcadd($cost, bcmul($tokensCacheWrite, $rateCacheWrite, 16), 16);
            $cost = bcdiv($cost, '1000000', 16);
        } elseif ($unit === 'per_1m_tokens_unified') {
            $rateAny = $rates['any'] ?? null;
            if (!$rateAny) {
                return null;
            }
            $totalTokens = bcadd(bcadd(bcadd($tokensInput, $tokensOutput, 16), $tokensCacheRead, 16), $tokensCacheWrite, 16);
            $cost = bcmul($totalTokens, $rateAny, 16);
            $cost = bcdiv($cost, '1000000', 16);
        } else {
            return null;
        }

        // off-peak
        if ($provider && $provider->hasOffPeak()) {
            $ts = $event->getTs();
            $minutes = (int)$ts->format('G') * 60 + (int)$ts->format('i');
            $startStr = $provider->getOffPeakWindowStart();
            $endStr = $provider->getOffPeakWindowEnd();
            $startMin = $this->timeToMinutes($startStr);
            $endMin = $this->timeToMinutes($endStr);
            $inWindow = false;
            if ($startMin <= $endMin) {
                $inWindow = $minutes >= $startMin && $minutes < $endMin;
            } else {
                $inWindow = $minutes >= $startMin || $minutes < $endMin;
            }
            if ($inWindow) {
                $multiplier = $provider->getOffPeakMultiplier();
                $cost = bcmul($cost, $multiplier, 16);
            }
        }

        // round half up to 6 decimals
        $rounded = $this->roundHalfUp($cost, 6);
        return $rounded;
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        $h = (int)($parts[0] ?? 0);
        $m = (int)($parts[1] ?? 0);
        return $h * 60 + $m;
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        // multiply by 10^precision
        $factor = bcpow('10', (string)$precision);
        $scaled = bcmul($value, $factor, $precision + 10);
        $scaledPlus = bcadd($scaled, '0.5', 0);
        $rounded = bcdiv($scaledPlus, $factor, $precision);
        // ensure exactly $precision decimals
        if (strpos($rounded, '.') === false) {
            $rounded .= '.' . str_repeat('0', $precision);
        } else {
            [$int, $frac] = explode('.', $rounded, 2);
            $frac = str_pad($frac, $precision, '0', STR_PAD_RIGHT);
            $frac = substr($frac, 0, $precision);
            $rounded = $int . '.' . $frac;
        }
        return $rounded;
    }
}

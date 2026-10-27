<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Usage;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usages = $this->em->getRepository(Usage::class)->findBy([], ['ts' => 'ASC']);
        
        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($usages as $usage) {
            if ($usage->costUsd !== null) {
                continue;
            }

            // Ensure model is linked
            if (!$usage->model && $usage->modelCode) {
                $provider = $this->em->getRepository(Provider::class)->find($usage->providerCode);
                if ($provider) {
                    $model = $this->em->getRepository(Model::class)->findOneBy([
                        'provider' => $provider,
                        'code' => $usage->modelCode
                    ]);
                    if ($model) {
                        $usage->model = $model;
                    }
                }
            }

            if (!$usage->model) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $usage->providerCode,
                    $usage->providerRequestId,
                    $usage->ts->format('\Y-\m-\dT\H:\i:\s\Z')
                ));
                continue;
            }

            // Find applicable price
            $prices = $this->em->getRepository(Price::class)->findBy(
                ['model' => $usage->model],
                ['validFrom' => 'DESC']
            );

            $applicablePrice = null;
            foreach ($prices as $price) {
                if ($price->validFrom <= $usage->ts) {
                    $applicablePrice = $price;
                    break;
                }
            }

            if (!$applicablePrice) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $usage->providerCode,
                    $usage->providerRequestId,
                    $usage->ts->format('\Y-\m-\dT\H:\i:\s\Z')
                ));
                continue;
            }

            // Calculate cost
            $cost = $this->calculateCost($usage, $applicablePrice);
            $usage->costUsd = $cost;
            $ratedCount++;
        }

        $this->em->flush();
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $ratedCount, $unratedCount));
        return Command::SUCCESS;
    }

    private function calculateCost(Usage $usage, Price $price): string
    {
        $tokensInput = (string)$usage->tokensInput;
        $tokensOutput = (string)$usage->tokensOutput;
        $tokensCacheRead = (string)$usage->tokensCacheRead;
        $tokensCacheWrite = (string)$usage->tokensCacheWrite;
        
        $cost = '0';
        if ($price->unit === 'per_1m_tokens') {
            $cost = bcadd($cost, bcmul($tokensInput, $price->rates['input'] ?? '0', 16));
            $cost = bcadd($cost, bcmul($tokensOutput, $price->rates['output'] ?? '0', 16));
            $cost = bcadd($cost, bcmul($tokensCacheRead, $price->rates['cache_read'] ?? '0', 16));
            $cost = bcadd($cost, bcmul($tokensCacheWrite, $price->rates['cache_write'] ?? '0', 16));
        } elseif ($price->unit === 'per_1m_tokens_unified') {
            $totalTokens = bcadd(bcadd(bcadd($tokensInput, $tokensOutput, 0), $tokensCacheRead, 0), $tokensCacheWrite, 0);
            $cost = bcmul($totalTokens, $price->rates['any'] ?? '0', 16);
        }

        $cost = bcdiv($cost, '1000000', 16);

        // Off-peak
        $provider = $usage->model->provider;
        if ($provider->offPeak) {
            $ts = $usage->ts;
            $time = $ts->format('H:i');
            $start = $provider->offPeak['window_start'];
            $end = $provider->offPeak['window_end'];
            $multiplier = $provider->offPeak['multiplier'];

            if ($this->isOffPeak($time, $start, $end)) {
                $cost = bcmul($cost, $multiplier, 16);
            }
        }

        // Rounding HALF_UP to 6 decimal places
        return $this->roundHalfUp($cost, 6);
    }

    private function isOffPeak(string $time, string $start, string $end): bool
    {
        if ($start < $end) {
            return $time >= $start && $time < $end;
        } else {
            // Over midnight
            return $time >= $start || $time < $end;
        }
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        $multiplier = pow(10, $precision);
        $shifted = bcmul($value, (string)$multiplier, 16);
        
        $shifted = bcmul($value, (string)$multiplier, 16);
        
        if (strpos($shifted, '.') === false) {
            $intPart = $shifted;
            $fracPart = '0';
        } else {
            $split = explode('.', $shifted);
            $intPart = $split[0];
            $fracPart = $split[1] ?? '0';
        }
        
        if ($intPart[0] === '-') {
            $absIntPart = substr($intPart, 1);
            $firstDigit = $fracPart[0] ?? '0';
            if ((int)$firstDigit >= 5) {
                $absIntPart = bcadd($absIntPart, '1', 0);
            }
            $intPart = '-' . $absIntPart;
        } else {
            $firstDigit = $fracPart[0] ?? '0';
            if ((int)$firstDigit >= 5) {
                $intPart = bcadd($intPart, '1', 0);
            }
        }
        
        return bcdiv($intPart, (string)$multiplier, $precision);
    }
}

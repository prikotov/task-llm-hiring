<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация нерассчитанных событий'
)]
final class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private Connection $conn)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->em->beginTransaction();
        try {
            $rated = 0;
            $unrated = 0;

            $events = $this->em->getRepository(UsageEvent::class)->findBy(['costUsd' => null]);
            foreach ($events as $event) {
                $price = $this->findApplicablePrice($event);
                if ($price === null) {
                    $unrated++;
                    $output->writeln('UNRATED ' . $event->getProvider()->getCode() . '/' . $event->getProviderRequestId() . ' ' . $event->getTs()->format('c') . ' no_price', OutputInterface::VERBOSITY_VERBOSE);
                    continue;
                }

                $cost = $this->calculateEventCost($event, $price);
                $event->setCostUsd($cost);
                $rated++;
            }

            $this->em->flush();
            $this->em->commit();

            $output->writeln('RATED=' . $rated . ' UNRATED=' . $unrated);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->em->rollback();
            $output->writeln('<error>Ошибка тарификации: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function findApplicablePrice(UsageEvent $event): ?Price
    {
        $prices = $this->em->getRepository(Price::class)->findBy([
            'provider' => $event->getProvider(),
            'model' => $event->getModel(),
        ]);
        if (empty($prices)) {
            return null;
        }

        $eventTs = $event->getTs();
        $applicable = null;
        foreach ($prices as $price) {
            if ($price->getValidFrom() <= $eventTs) {
                if ($applicable === null || $price->getValidFrom() > $applicable->getValidFrom()) {
                    $applicable = $price;
                }
            }
        }
        return $applicable;
    }

    private function calculateEventCost(UsageEvent $event, Price $price): string
    {
        $bc = new \BCMath();
        $scale = 16;

        $tokensInput = (string)$event->getTokensInput();
        $tokensOutput = (string)$event->getTokensOutput();
        $tokensCacheRead = (string)$event->getTokensCacheRead();
        $tokensCacheWrite = (string)$event->getTokensCacheWrite();

        $unit = $price->getUnit();
        $cost = '0';

        if ($unit === 'per_1m_tokens') {
            $cost = $bc->add($cost, $bc->div($bc->mul($tokensInput, $price->getInputRate()), '1000000', $scale), $scale);
            $cost = $bc->add($cost, $bc->div($bc->mul($tokensOutput, $price->getOutputRate()), '1000000', $scale), $scale);
            $cost = $bc->add($cost, $bc->div($bc->mul($tokensCacheRead, $price->getCacheReadRate()), '1000000', $scale), $scale);
            $cost = $bc->add($cost, $bc->div($bc->mul($tokensCacheWrite, $price->getCacheWriteRate()), '1000000', $scale), $scale);
        } elseif ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $bc->add($bc->add($bc->add($tokensInput, $tokensOutput, $scale), $tokensCacheRead, $scale), $tokensCacheWrite, $scale);
            $cost = $bc->div($bc->mul($totalTokens, $price->getUnifiedRate()), '1000000', $scale);
        }

        // Off-peak
        $provider = $event->getProvider();
        if ($provider->getOffPeakMultiplier() !== null) {
            $ts = $event->getTs();
            $time = $ts->format('H:i');
            $start = $provider->getOffPeakWindowStart();
            $end = $provider->getOffPeakWindowEnd();
            $inWindow = $this->isTimeInWindow($time, $start, $end);
            if ($inWindow) {
                $cost = $bc->mul($cost, $provider->getOffPeakMultiplier(), $scale);
            }
        }

        // Округление до 6 знаков, HALF_UP
        $rounded = $this->bcRound($cost, 6);
        return $rounded;
    }

    private function isTimeInWindow(string $time, ?string $start, ?string $end): bool
    {
        if ($start === null || $end === null) {
            return false;
        }
        $t = strtotime($time);
        $s = strtotime($start);
        $e = strtotime($end);
        if ($s < $e) {
            return $t >= $s && $t < $e;
        } else {
            return $t >= $s || $t < $e;
        }
    }

    private function bcRound(string $value, int $scale): string
    {
        $factor = bcpow('10', (string)$scale);
        $tmp = bcadd($value, '0.' . str_repeat('0', $scale - 1) . '5', $scale + 1);
        $tmp = bcmul($tmp, $factor, 0);
        $tmp = bcadd($tmp, '0', 0); // округление
        $result = bcdiv($tmp, $factor, $scale);
        return $result;
    }
}

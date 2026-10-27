<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate usage events')]
final class BillingRateCommand extends Command
{
    public function __construct(private Connection $conn)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $events = $this->conn->fetchAllAssociative('SELECT id, provider_code, provider_request_id, model_code, user_name as user, ts, tokens_input, tokens_output, tokens_cache_read, tokens_cache_write FROM usage_events WHERE cost IS NULL');

        $rated = 0;
        $unrated = 0;

        foreach ($events as $e) {
            $price = $this->findPrice($e['provider_code'], $e['model_code'], $e['ts']);
            if ($price === null) {
                $unrated++;
                $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $e['provider_code'], $e['provider_request_id'], $e['ts']));
                continue;
            }

            $provider = $this->findProvider($e['provider_code']);
            $cost = $this->calculateCost($e, $price, $provider);
            if ($cost === null) {
                $unrated++;
                $output->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $e['provider_code'], $e['provider_request_id'], $e['ts']));
                continue;
            }

            $this->conn->executeStatement('UPDATE usage_events SET cost = :cost WHERE id = :id', [
                'cost' => $cost,
                'id' => $e['id'],
            ]);
            $rated++;
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));
        return Command::SUCCESS;
    }

    private function findPrice(string $provider, string $model, string $ts): ?array
    {
        $sql = 'SELECT valid_from, unit, rates FROM prices WHERE provider_code = :provider AND model_code = :model AND valid_from <= :ts ORDER BY valid_from DESC LIMIT 1';
        $row = $this->conn->fetchAssociative($sql, [
            'provider' => $provider,
            'model' => $model,
            'ts' => $ts,
        ]);
        return $row ?: null;
    }

    private function findProvider(string $code): ?array
    {
        $row = $this->conn->fetchAssociative('SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier FROM providers WHERE code = :code', ['code' => $code]);
        return $row ?: null;
    }

    private function calculateCost(array $event, array $price, ?array $provider): ?string
    {
        $unit = $price['unit'];
        $rates = json_decode($price['rates'], true);
        if (!is_array($rates)) {
            return null;
        }

        bcscale(16);
        $cost = '0';

        if ($unit === 'per_1m_tokens') {
            foreach (['input' => 'tokens_input', 'output' => 'tokens_output', 'cache_read' => 'tokens_cache_read', 'cache_write' => 'tokens_cache_write'] as $rateKey => $tokenKey) {
                $rate = $rates[$rateKey] ?? '0';
                $tokens = (string)$event[$tokenKey];
                $partial = bcdiv(bcmul($tokens, $rate, 16), '1000000', 16);
                $cost = bcadd($cost, $partial, 16);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $rate = $rates['any'] ?? '0';
            $totalTokens = bcadd(bcadd((string)$event['tokens_input'], (string)$event['tokens_output'], 16), bcadd((string)$event['tokens_cache_read'], (string)$event['tokens_cache_write'], 16), 16);
            $cost = bcdiv(bcmul($totalTokens, $rate, 16), '1000000', 16);
        } else {
            return null;
        }

        // off-peak
        $multiplier = '1';
        if ($provider && $provider['off_peak_window_start'] && $provider['off_peak_window_end'] && $provider['off_peak_multiplier']) {
            $ts = new \DateTimeImmutable($event['ts'], new \DateTimeZone('UTC'));
            $time = $ts->format('H:i');
            $start = $provider['off_peak_window_start'];
            $end = $provider['off_peak_window_end'];
            $inOffPeak = false;
            if ($start <= $end) {
                $inOffPeak = $time >= $start && $time < $end;
            } else {
                // crosses midnight
                $inOffPeak = $time >= $start || $time < $end;
            }
            if ($inOffPeak) {
                $multiplier = (string)$provider['off_peak_multiplier'];
                $cost = bcmul($cost, $multiplier, 16);
            }
        }

        $rounded = $this->roundHalfUp($cost, 6);
        return $rounded;
    }

    private function roundHalfUp(string $value, int $precision): string
    {
        // assume positive
        $factor = bcpow('10', (string)$precision, 0);
        $shifted = bcmul($value, $factor, $precision + 4);
        $shifted = bcadd($shifted, '0.5', 0);
        $result = bcdiv($shifted, $factor, $precision);
        // ensure exactly precision digits
        if (strpos($result, '.') === false) {
            $result .= '.' . str_repeat('0', $precision);
        } else {
            [$int, $frac] = explode('.', $result, 2);
            $frac = str_pad($frac, $precision, '0', STR_PAD_RIGHT);
            $frac = substr($frac, 0, $precision);
            $result = $int . '.' . $frac;
        }
        return $result;
    }
}

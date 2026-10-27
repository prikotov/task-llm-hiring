<?php

declare(strict_types=1);

namespace App\Command;

use App\ValueObject\Money;
use App\Entity\Rating;
use App\Entity\UsageEventRow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

final class ReportCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:report')
            ->setDescription('CSV report of usage costs (grouped by user, model)')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC timestamp (inclusive)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC timestamp (exclusive)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseTs($input->getOption('from'));
        if ($from instanceof \Throwable) {
            $output->getErrorOutput()->writeln('ERR: invalid --from ISO-8601');

            return 1;
        }
        $to = $this->parseTs($input->getOption('to'));
        if ($to instanceof \Throwable) {
            $output->getErrorOutput()->writeln('ERR: invalid --to ISO-8601');

            return 1;
        }

        $h = fopen('php://stdout', 'w');
        $stdout = new StreamOutput($h);

        $groups = [];

        $events = $this->em->getRepository(UsageEventRow::class)->findAll();
        foreach ($events as $e) {
            if ($from !== null && $e->ts < $from) {
                continue;
            }
            if ($to !== null && $e->ts >= $to) {
                continue;
            }

            $rating = $this->em->getRepository(Rating::class)->findOneBy(['eventId' => (int) $e->id]);
            if ($rating === null) {
                continue;
            }

            $model = $e->provider.'/'.$e->model;
            $key = $e->user."\x01".$model;
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = [
                    'user' => $e->user,
                    'model' => $model,
                    'ti' => '0',
                    'to' => '0',
                    'tr' => '0',
                    'tw' => '0',
                    'cost' => '0',
                ];
            }
            $groups[$key]['ti'] = bcadd($groups[$key]['ti'], (string) $e->tokensInput, 0);
            $groups[$key]['to'] = bcadd($groups[$key]['to'], (string) $e->tokensOutput, 0);
            $groups[$key]['tr'] = bcadd($groups[$key]['tr'], (string) $e->tokensCacheRead, 0);
            $groups[$key]['tw'] = bcadd($groups[$key]['tw'], (string) $e->tokensCacheWrite, 0);
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], (string) $rating->cost, 40);
        }

        $keys = array_keys($groups);
        usort($keys, static function (string $a, string $b): int {
            [$ua, $ma] = explode("\x01", $a, 2);
            [$ub, $mb] = explode("\x01", $b, 2);
            $c = strcmp($ua, $ub);

            return $c !== 0 ? $c : strcmp($ma, $mb);
        });

        $stdout->write("user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n");

        $total = ['ti' => '0', 'to' => '0', 'tr' => '0', 'tw' => '0', 'cost' => '0'];

        foreach ($keys as $key) {
            $g = $groups[$key];
            $stdout->writeln(sprintf(
                '%s,%s,%s,%s,%s,%s,%s',
                $g['user'],
                $g['model'],
                self::tokens($g['ti']),
                self::tokens($g['to']),
                self::tokens($g['tr']),
                self::tokens($g['tw']),
                Money::of($g['cost'])->toFixed6(),
            ));
            $total['ti'] = bcadd($total['ti'], $g['ti'], 0);
            $total['to'] = bcadd($total['to'], $g['to'], 0);
            $total['tr'] = bcadd($total['tr'], $g['tr'], 0);
            $total['tw'] = bcadd($total['tw'], $g['tw'], 0);
            $total['cost'] = bcadd($total['cost'], $g['cost'], 40);
        }

        $stdout->writeln(sprintf(
            'TOTAL,,%s,%s,%s,%s,%s',
            self::tokens($total['ti']),
            self::tokens($total['to']),
            self::tokens($total['tr']),
            self::tokens($total['tw']),
            Money::of($total['cost'])->toFixed6(),
        ));

        fclose($h);

        return 0;
    }

    private function parseTs(?string $v): \DateTimeImmutable|\Throwable|null
    {
        if ($v === null) {
            return null;
        }
        $v = trim($v);
        $ts = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $v, new \DateTimeZone('UTC'));
        if ($ts === false) {
            $ts = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $v, new \DateTimeZone('UTC'));
        }
        if ($ts === false) {
            $ts = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', $v, new \DateTimeZone('UTC'));
        }
        if ($ts === false) {
            return new \RuntimeException('bad timestamp');
        }

        return $ts;
    }

    private static function tokens(string $s): string
    {
        $s = ltrim($s, '0');

        return $s === '' ? '0' : $s;
    }
}

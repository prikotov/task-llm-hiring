<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Store\Store;
use App\Domain\Billing\Money;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт расходов по (user, model) за период, CSV в stdout.',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly Store $store,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC.');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fromStr = $input->getOption('from');
        $toStr = $input->getOption('to');

        try {
            $from = $fromStr === null ? null : new \DateTimeImmutable((string) $fromStr, new \DateTimeZone('UTC'));
            $to = $toStr === null ? null : new \DateTimeImmutable((string) $toStr, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            $io->getErrorStyle()->error('Некорректный период: ' . $e->getMessage());

            return Command::FAILURE;
        }

        try {
            /** @var list<\App\Billing\Entity\RatedEvent> $rows */
            $rows = $this->store->ratedEvents($from, $to);
        } catch (\Throwable $e) {
            $io->getErrorStyle()->error('Ошибка чтения отчёта: ' . $e->getMessage());

            return Command::FAILURE;
        }

        // Группировка по (user, modelRef = provider/model)
        $groups = [];
        $total = [0, 0, 0, 0, Money::zero()];

        foreach ($rows as $r) {
            $modelRef = $this->store->modelRefForEvent($r->eventId) ?? 'unknown/unknown';

            $key = $r->user . "\x01" . $modelRef;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $r->user,
                    'model' => $modelRef,
                    'tokens' => [0, 0, 0, 0],
                    'cost' => Money::zero(),
                ];
            }

            foreach ($r->tokens as $i => $t) {
                $groups[$key]['tokens'][$i] += (int) $t;
                $total[$i] = ($total[$i] ?? 0) + (int) $t;
            }            $groups[$key]['cost'] = $groups[$key]['cost']->add(Money::from($r->costUsd));
            $total[4] = $total[4]->add(Money::from($r->costUsd));
        }

        // Сортировка: user, then model.
        $sorted = array_map(static fn($v) => $v, $groups);
        uksort($sorted, static function (string $a, string $b): int {
            [$ua, $ma] = explode("\x01", $a, 2) + [1 => ''];
            [$ub, $mb] = explode("\x01", $b, 2) + [1 => ''];
            $c = strcmp($ua, $ub);
            if ($c !== 0) {
                return $c;
            }

            return strcmp($ma, $mb);
        });

        // CSV
        $ob = '';
        $ob .= 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd' . "\n";
        foreach ($sorted as $g) {
            [$ti, $to2, $tcr, $tcw] = $g['tokens'];
            $ob .= sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                (int) $ti,
                (int) $to2,
                (int) $tcr,
                (int) $tcw,
                $g['cost']->formatMicros(),
            ) . "\n";
        }

        $ob .= sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            (int) $total[0],
            (int) $total[1],
            (int) $total[2],
            (int) $total[3],
            $total[4]->formatMicros(),
        ) . "\n";

        $output->write($ob);

        return Command::SUCCESS;
    }
}

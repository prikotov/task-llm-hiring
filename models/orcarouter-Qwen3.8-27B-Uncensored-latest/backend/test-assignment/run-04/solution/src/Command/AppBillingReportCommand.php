<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportBuilder;
use App\Shared\ImportException;
use App\Shared\IsoUtc;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV report of rated usage costs by user and model (stdout)',
)]
final class AppBillingReportCommand extends Command
{
    public function __construct(private readonly ReportBuilder $reportBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Inclusive start of the period, ISO-8601 UTC');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Exclusive end of the period, ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $input->getOption('from') !== null
                ? IsoUtc::parse((string) $input->getOption('from'))
                : null;
            $to = $input->getOption('to') !== null
                ? IsoUtc::parse((string) $input->getOption('to'))
                : null;

            if ($from !== null && $to !== null && $from >= $to) {
                throw new ImportException('--from must be earlier than --to');
            }
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->write($this->reportBuilder->csv($from, $to));

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Generate a CSV billing report')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportGenerator $generator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start of period (ISO-8601 UTC, inclusive)', null)
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End of period (ISO-8601 UTC, exclusive)', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        // Validate timestamps if provided
        foreach (['from' => $from, 'to' => $to] as $name => $val) {
            if ($val !== null) {
                try {
                    new \DateTimeImmutable($val, new \DateTimeZone('UTC'));
                } catch (\Exception) {
                    $output->getErrorOutput()->writeln("Invalid timestamp for --{$name}: {$val}");
                    return Command::FAILURE;
                }
            }
        }

        $csv = $this->generator->generate($from, $to);
        $output->write($csv);

        return Command::SUCCESS;
    }
}

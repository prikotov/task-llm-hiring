<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Billing\ReportService;
use App\Infrastructure\Import\Iso8601;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Print billing report as CSV')]
final class BillingReportCommand extends AbstractCommand
{
    public function __construct(private readonly ReportService $service)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Period start, ISO-8601 UTC (inclusive)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Period end, ISO-8601 UTC (exclusive)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->optionalMoment($input->getOption('from'), 'from');
            $to = $this->optionalMoment($input->getOption('to'), 'to');
        } catch (InvalidArgumentException $exception) {
            $this->writeError($output, $exception->getMessage());

            return self::FAILURE;
        }

        $output->write($this->service->generateCsv($from, $to), false, OutputInterface::OUTPUT_RAW);

        return self::SUCCESS;
    }

    private function optionalMoment(mixed $value, string $option): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Option --%s must be an ISO-8601 string.', $option));
        }

        return Iso8601::parse($value, '--' . $option);
    }
}

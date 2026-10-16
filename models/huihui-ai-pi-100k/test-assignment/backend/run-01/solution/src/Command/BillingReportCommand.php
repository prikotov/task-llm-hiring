<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт расходов по пользователям и моделям за период [from, to)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $from = $this->parseOption($input, 'from');
            $to = $this->parseOption($input, 'to');
        } catch (\ValueError $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $output->write($this->service->build($this->em, $from, $to));

        return Command::SUCCESS;
    }

    private function parseOption(InputInterface $input, string $name): ?\DateTimeImmutable
    {
        $value = $input->getOption($name);
        if ($value === null) {
            return null;
        }

        if (!\is_string($value) || !preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(Z|[+-]\d{2}:?\d{2})$/', $value)) {
            throw new \ValueError(sprintf('Некорректное значение --%s, ожидается ISO-8601 UTC (например 2026-09-01T00:00:00Z)', $name));
        }

        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \ValueError(sprintf('Некорректное значение --%s: %s', $name, $value), 0, $e);
        }
    }
}

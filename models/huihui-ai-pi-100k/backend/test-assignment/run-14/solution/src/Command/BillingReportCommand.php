<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportBuilder;
use App\Repository\UsageEventRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Отчёт по затратам за период [from, to): user,model,tokens_*,cost_usd. CSV в stdout.')]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportBuilder $builder,
        private readonly UsageEventRepository $events,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', 'f', InputOption::VALUE_OPTIONAL, 'Начало периода (включительно), ISO-8601 UTC');
        $this->addOption('to', 't', InputOption::VALUE_OPTIONAL, 'Конец периода (эксклюзивно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseDate((string) ($input->getOption('from') ?? ''));
        $to = $this->parseDate((string) ($input->getOption('to') ?? ''));

        try {
            $events = $this->events->forReport($from, $to);
        } catch (\DomainException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return self::FAILURE;
        }

        $lines = $this->builder->build($events);
        $output->write(\implode("\n", $lines)."\n");

        return self::SUCCESS;
    }

    private function parseDate(string $s): ?\DateTimeImmutable
    {
        if ($s === '') {
            return null;
        }
        $d = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $s);
        if ($d === false) {
            throw new \DomainException(sprintf("Период: ISO-8601 не распознан: %s", $s));
        }

        return $d->setTimezone(new \DateTimeZone('UTC'));
    }
}

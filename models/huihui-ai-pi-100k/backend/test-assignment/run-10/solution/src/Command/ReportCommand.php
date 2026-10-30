<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\InputError;
use App\Service\Parsing;
use App\Service\ReportRenderer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (user, model): CSV + TOTAL',
)]
final class ReportCommand extends AppCommand
{
    public function __construct(private ReportRenderer $renderer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало диапазона (ISO-8601 UTC, включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец диапазона (ISO-8601 UTC, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseDate((string) $input->getOption('from'), 'from');
            $to = $this->parseDate((string) $input->getOption('to'), 'to');

            // Вывод заканчивается символом \n — используем write(),
            // чтобы не получить лишний пустой символ в конце.
            $output->write($this->renderer->render($from, $to));
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        return self::SUCCESS;
    }

    private function parseDate(string $raw, string $name): ?\DateTimeImmutable
    {
        if ($raw === '') {
            return null;
        }

        return Parsing::iso8601Utc($raw);
    }
}

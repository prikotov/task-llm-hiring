<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цен: применяет исправленные строки и пересчитывает связанные события')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $repriceService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл исправленных строк цен (секции providers/models необязательны)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->repriceService->reprice($file);
        } catch (ImportException $e) {
            $this->error($output)->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return Command::SUCCESS;
    }

    /** Ошибки пишутся в stderr при настоящем консольном запуске. */
    private function error(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;
    }
}

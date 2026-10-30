<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\UsageImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL)',
)]
final class AppUsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с событиями (JSONL)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        $ui = new SymfonyStyle($input, $output);

        try {
            $result = $this->service->importFile($file);
        } catch (\Throwable $e) {
            $ui->error(sprintf('Ошибка импорта событий: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        $ui->writeln(sprintf('Вставки: %d, дубли: %d', $result['inserted'], $result['duplicates']));
        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Usage\ImportUsageEvents;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузить журнал использования (JSONL)',
)]
final class ImportUsageCommand extends Command
{
    public function __construct(private readonly ImportUsageEvents $import)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');

        try {
            $result = ($this->import)($file);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>Ошибка импорта событий: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'Принято событий: %d, дублей проигнорировано: %d.',
            $result['accepted'],
            $result['duplicates'],
        ));

        return Command::SUCCESS;
    }
}

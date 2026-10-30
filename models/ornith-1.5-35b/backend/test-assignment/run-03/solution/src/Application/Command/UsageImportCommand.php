<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(name: 'app:usage:import', description: 'Загружает события использования из JSONL-файла')]
final class UsageImportCommand extends Command
{
    public function __construct(private UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий использования')
            ->setHelp(
                "Загружает события по правилу дедупликации (provider, provider_request_id).\n"
                . "Некорректная строка JSONL — код выхода 1."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = (string) $input->getArgument('file');

        try {
            $stats = $this->importer->importFromFile($file);
        } catch (Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Загружено событий: новых %d, дублей %d',
            $stats['loaded'],
            $stats['duplicate'],
        ));

        return Command::SUCCESS;
    }
}

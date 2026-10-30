<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\UsageImportService;
use App\Application\Import\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загружает события использования (JSONL). Правила: п. 5 и далее.')]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $usage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к файлу JSONL с событиями.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        try {
            $stats = $this->usage->importFile($path);
            $output->writeln(sprintf(
                'total=%d new=%d dup=%d bad=%d',
                $stats['total'],
                $stats['new'],
                $stats['dup'],
                $stats['bad'],
            ));
        } catch (ImportException $e) {
            $output->writeln($e->getMessage());

            return 1;
        }

        return 0;
    }
}

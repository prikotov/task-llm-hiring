<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageImportService;
use App\Support\ImportError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импорт событий использования (JSONL)')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл со событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        try {
            $this->service->import($path);
        } catch (ImportError $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return 1;
        }

        return 0;
    }
}

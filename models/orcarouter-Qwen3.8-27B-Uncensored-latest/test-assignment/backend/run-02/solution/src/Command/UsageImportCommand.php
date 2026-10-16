<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UsageImportService;
use App\Exception\ImportException;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL), дубли игнорируются',
)]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $res = $this->service->import($file);
        } catch (ImportException|DbalException|\JsonException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());

            return Command::FAILURE;
        }

        // Служебное сообщение — в stderr; stdout не трогаем.
        $output->getErrorOutput()->writeln(sprintf(
            'OK: imported=%d duplicates_ignored=%d',
            $res['imported'],
            $res['duplicates'],
        ));

        return Command::SUCCESS;
    }
}

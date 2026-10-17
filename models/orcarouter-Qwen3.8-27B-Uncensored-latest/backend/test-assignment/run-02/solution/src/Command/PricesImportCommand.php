<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\PriceImportService;
use App\Application\PriceFileParser;
use App\Exception\ImportException;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника: провайдеры, модели и строки цен (JSON)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceImportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $this->service->import($file, PriceFileParser::MODE_IMPORT);
        } catch (ImportException|DbalException|\JsonException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln('OK: справочник импортирован (upsert)');

        return Command::SUCCESS;
    }
}

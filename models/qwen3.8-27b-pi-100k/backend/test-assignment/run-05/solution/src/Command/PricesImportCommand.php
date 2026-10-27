<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PricesImporter;
use App\Shared\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:prices:import', description: 'Импорт справочника: провайдеры, модели, строки цен (JSON)')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        try {
            $this->importer->import($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

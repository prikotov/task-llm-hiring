<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\ImportInputException;
use App\Service\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Идемпотентно загружает провайдеров, модели и строки цен из JSON-файла',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSON-файлу со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $this->importer->import($file);
        } catch (ImportInputException $e) {
            $output->writeln('import error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln('prices: imported');

        return Command::SUCCESS;
    }
}

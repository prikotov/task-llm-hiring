<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Импорт справочника цен (providers, models, prices) из JSON-файла. Идемпотентный upsert.')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $stats = $this->importer->import($file);
        } catch (\DomainException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return self::FAILURE; // код 1
        }

        $output->getErrorOutput()->write(sprintf(
            "providers: %d, models: %d, prices: %d\n",
            $stats['providers'],
            $stats['models'],
            $stats['prices'],
        ));

        return self::SUCCESS; // код 0
    }
}

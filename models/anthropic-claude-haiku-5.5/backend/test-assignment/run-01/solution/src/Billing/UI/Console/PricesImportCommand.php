<?php

declare(strict_types=1);

namespace App\Billing\UI\Console;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Application\Import\PriceCatalogImporter;
use App\Billing\Application\Import\PriceCatalogParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Загрузить провайдеров, модели и историю цен (идемпотентно)')]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceCatalogParser $parser,
        private readonly PriceCatalogImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output->getErrorOutput();
        try {
            $data = $this->parser->parseFile((string) $input->getArgument('file'));
            $this->importer->import($data);
        } catch (InvalidInputException $e) {
            $err->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $err->writeln(sprintf(
            'Загружено: провайдеров %d, моделей %d, строк цен %d',
            count($data->providers),
            count($data->models),
            count($data->prices),
        ));

        return Command::SUCCESS;
    }
}

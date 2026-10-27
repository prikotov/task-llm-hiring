<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\DataImportException;
use App\Service\PricesFileParser;
use App\Service\PricesIngestor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт истории цен в справочник (providers/models/prices)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesFileParser $parser,
        private readonly PricesIngestor $ingestor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $doc = $this->parser->parseFile($file);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $this->ingestor->import($doc);

        $output->getErrorOutput()->writeln(
            sprintf(
                'Импортировано: %d провайдеров, %d моделей, %d строк цен',
                \count($doc->providers),
                \count($doc->models),
                \count($doc->prices)
            )
        );

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\DirectoryPersister;
use App\Import\PriceDoc;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника цен (провайдеры, модели, строки цен); идемпотентный upsert',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly DirectoryPersister $persister,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл цен (формат — как в fixtures/prices.json)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOut = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $doc = PriceDoc::fromFile((string) $input->getArgument('file'));
            $applied = $this->persister->apply($doc);

            $errorOut->writeln(sprintf(
                'Справочник импортирован: провайдеров — %d, моделей — %d, строк цен — %d (новых: %d, обновлено: %d).',
                count($applied['providers']),
                count($applied['models']),
                count($applied['rows']),
                $applied['priceRowsCreated'],
                $applied['priceRowsUpdated'],
            ));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $errorOut->writeln('Ошибка импорта справочника: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\PriceCatalogImporter;
use App\Domain\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника провайдеров, моделей и строк цен из JSON-файла (идемпотентно)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceCatalogImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $summary = $this->importer->import($file);
        } catch (BillingException $exception) {
            $output->getErrorOutput()->writeln('Ошибка: '.$exception->getMessage());
            return 1;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'Справочник импортирован: %d провайдеров, %d моделей, %d строк цен',
            $summary->providers,
            $summary->models,
            $summary->prices,
        ));

        return 0;
    }
}

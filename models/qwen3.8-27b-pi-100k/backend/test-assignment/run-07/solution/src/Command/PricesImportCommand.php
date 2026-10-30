<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\PriceCatalogImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузить историю цен: провайдеров, модели и строки цен (JSON-файл).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceCatalogImportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с историей цен (JSON)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $this->service->import($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        }

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Catalog\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника: провайдеры, модели, история цен (идемпотентный upsert)',
)]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл справочника цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->importer->import((string) $input->getArgument('file'));
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

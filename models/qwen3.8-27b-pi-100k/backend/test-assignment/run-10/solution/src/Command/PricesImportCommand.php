<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\PricesImportService;
use App\Application\Import\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загружает провайдеров, модели и строки цен (idempotent upsert).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesImportService $prices,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к файлу JSON с провайдерами, моделями и строками цен.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        try {
            $stats = $this->prices->importFile($path);
            \fwrite($output->getStream(), sprintf(
                'provs=%d models=%d price_rows=%d\n',
                $stats['providers'],
                $stats['models'],
                $stats['prices'],
            ));
        } catch (ImportException $e) {
            \fwrite($output->getStream(), $e->getMessage()."\n");

            return 1;
        }

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\CatalogService;
use App\Ingest\IngestError;
use App\Ingest\PriceFileParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:prices:import', description: 'Загружает провайдеров, модели и строки цен (идемпотентный upsert)')]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly CatalogService $catalog,
        private readonly PriceFileParser $parser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с каталогом цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $raw = FileSupport::read($file);
            $catalog = $this->parser->parse($raw);
        } catch (IngestError | \RuntimeException $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        try {
            $this->catalog->upsertCatalog($catalog);
        } catch (IngestError $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        return 0;
    }
}

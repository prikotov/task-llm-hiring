<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PriceCatalogService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:prices:import', description: 'Load providers, models and price rows (idempotent upsert).')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceCatalogService $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSON prices history file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->importer->import($this->requireFile($input));
        } catch (\App\Import\InputFormatException $e) {
            $stderr = new SymfonyStyle($input, $output->getErrorOutput());
            $stderr->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function requireFile(InputInterface $input): string
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        if (! is_file($file) || ! is_readable($file)) {
            throw new \App\Import\InputFormatException(sprintf('File not found or unreadable: %s', $file));
        }

        return $file;
    }
}

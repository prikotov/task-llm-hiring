<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Prices\PriceFileParser;
use App\Application\Prices\PricesImportService;
use App\Exception\InvalidPricesFileException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Import provider/model/price history from a JSON file (idempotent).',
)]
final class ImportPricesCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly PricesImportService $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the price history JSON file')
            ->setHelp(
                'Parses the JSON file (top-level object with sections providers/models/prices), '
                .'validates the schema and upserts the data into the database. '
                .'The command is idempotent: the same file may be re-imported safely. '
                .'Exit code: 0 on success, 1 on parse/validation errors or a failed transaction.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = $output->getErrorOutput();

        $path = (string) $input->getArgument('file');

        if (!is_file($path)) {
            $output->writeln(sprintf('<error>file not found: %s</error>', $path));

            return Command::FAILURE;
        }

        $json = @file_get_contents($path);
        if ($json === false) {
            $output->writeln(sprintf('<error>cannot read file: %s</error>', $path));

            return Command::FAILURE;
        }

        try {
            $spec = $this->parser->parse($json);
        } catch (InvalidPricesFileException $e) {
            $output->writeln(sprintf('<error>invalid prices file: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        try {
            $counts = $this->importer->import($spec);
        } catch (InvalidPricesFileException $e) {
            $output->writeln(sprintf('<error>import failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>import failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf(
            'prices imported: providers=%d models=%d price_rows=%d',
            $counts['providers'],
            $counts['models'],
            $counts['price_rows'],
        ));

        return Command::SUCCESS;
    }
}

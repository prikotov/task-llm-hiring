<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ApplicationServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import provider/model/price book from a JSON file.')]
final class PricesImportCommand extends Command
{
    protected static string $defaultName = 'app:prices:import';

    public function __construct(
        private readonly ApplicationServices $app,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $stderr = $output->getErrorOutput();

        if (!is_file($path) || !is_readable($path)) {
            $stderr->writeln(sprintf('ERROR: not found or not readable: %s', $path));
            return Command::FAILURE;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $stderr->writeln('ERROR: could not read file');
            return Command::FAILURE;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $stderr->writeln('ERROR: not a valid JSON object');
            return Command::FAILURE;
        }

        try {
            $result = $this->app->importPrices($payload);
        } catch (\RuntimeException $e) {
            $stderr->writeln('ERROR: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // print per-entry errors to stderr
        foreach ($result->errors as $err) {
            $stderr->writeln($err);
        }

        $output->writeln(sprintf('IMPORTED_PROVIDERS=%d IMPORTED_MODELS=%d IMPORTED_PRICES=%d',
            $result->providers, $result->models, $result->prices));

        return Command::SUCCESS;
    }
}

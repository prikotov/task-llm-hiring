<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Import\PricesFile;
use App\Import\PricesImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Импорт истории цен (providers, models, prices)')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу истории цен')
            ->setHelp('Идемпотентный upsert справочника цен: providers — по code, models — по (provider, code), price rows — по (provider, model, valid_from).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = PricesFile::fromFile((string) $input->getArgument('file'));
            $result = $this->importer->import($file);
        } catch (ImportException $e) {
            $this->fail($output, $e->getMessage());

            return 1;
        }

        $output->writeln($result->summary('prices'), OutputInterface::VERBOSITY_VERBOSE);

        return 0;
    }

    private function fail(OutputInterface $output, string $message): void
    {
        $output->writeln('<error>'.$message.'</error>', OutputInterface::VERBOSITY_QUIET);
    }
}

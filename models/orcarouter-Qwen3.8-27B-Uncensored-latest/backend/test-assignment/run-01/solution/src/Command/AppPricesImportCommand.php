<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\CatalogImporter;
use App\Domain\Exception\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:prices:import', description: 'Загрузить провайдеров, модели и строки цен из JSON-файла')]
final class AppPricesImportCommand extends Command
{
    public function __construct(private readonly CatalogImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        $json = $this->readFile($file, $io);
        if ($json === null) {
            return Command::FAILURE;
        }

        try {
            $this->importer->import($json, true);
        } catch (ImportException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $io->error('Не удалось импортировать цены: '.$e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function readFile(string $file, SymfonyStyle $io): ?string
    {
        $json = @file_get_contents($file);
        if ($json === false) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $file));
            return null;
        }

        return $json;
    }
}

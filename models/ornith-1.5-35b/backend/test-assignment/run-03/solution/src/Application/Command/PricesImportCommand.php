<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(name: 'app:prices:import', description: 'Загружает провайдеров, модели и строки цен из JSON-файла')]
final class PricesImportCommand extends Command
{
    public function __construct(private PriceImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу истории цен')
            ->setHelp(
                "Загружает провайдеров, модели и строки цен.\n"
                . "Идемпотентный upsert. Некорректный JSON — код выхода 1."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = (string) $input->getArgument('file');

        try {
            $stats = $this->importer->importFromFile($file);
        } catch (Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Загружено: провайдеров %d, моделей %d, строк цен %d',
            $stats['providers'],
            $stats['models'],
            $stats['prices'],
        ));

        return Command::SUCCESS;
    }
}

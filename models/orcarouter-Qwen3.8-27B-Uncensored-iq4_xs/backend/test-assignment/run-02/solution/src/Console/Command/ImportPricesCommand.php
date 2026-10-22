<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Domain\Catalog\PricesImporter;
use App\Domain\Common\ImportFormatException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * app:prices:import <file> — загрузка справочника цен (идемпотентный upsert).
 */
#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт провайдеров, моделей и строк цен (JSON)',
)]
final class ImportPricesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricesImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу со справочником цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        $real = realpath($file);
        if ($real === false) {
            $io->error('Файл не найден: '.$file);

            return Command::FAILURE;
        }

        try {
            $this->importer->importFile($real);
        } catch (ImportFormatException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->writeln('Справочник цен загружен: '.$file);

        return Command::SUCCESS;
    }
}

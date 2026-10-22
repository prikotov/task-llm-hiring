<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Service\PricesImporter;
use App\Support\ConsoleWriter;
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
    public function __construct(
        private readonly PricesImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с провайдерами, моделями и ценами');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $touched = $this->importer->import((string) $input->getArgument('file'));
        } catch (DomainException $e) {
            ConsoleWriter::note($output, 'Ошибка: '.$e->getMessage());

            return 1;
        }

        ConsoleWriter::note($output, sprintf('ОК: применено строк цен: %d', count($touched)));

        return Command::SUCCESS;
    }
}

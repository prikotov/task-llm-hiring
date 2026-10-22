<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Pricing\Application\PriceImporter;
use App\Pricing\Application\PriceList;
use App\Shared\InvalidInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Импорт истории цен (идемпотентный upsert)')]
final class ImportPricesCommand extends Command
{
    public function __construct(private readonly PriceImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл истории цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $list = PriceList::fromJson(FileReader::contents((string) $input->getArgument('file')));
            $r = $this->importer->import($list);
        } catch (InvalidInput $e) {
            $err->writeln('ERROR '.$e->getMessage());

            return Command::FAILURE;
        }
        $err->writeln(sprintf('Импортировано: провайдеров %d, моделей %d, строк цен %d', $r['providers'], $r['models'], $r['prices']));

        return Command::SUCCESS;
    }
}

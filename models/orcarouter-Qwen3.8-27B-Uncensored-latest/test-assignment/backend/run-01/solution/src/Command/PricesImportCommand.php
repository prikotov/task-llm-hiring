<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\PriceImporter;
use App\Domain\Billing\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:prices:import', description: 'Импорт истории цен (провайдеры, модели, строки цен). Идемпотентный upsert.')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceImporter $importer)
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

        try {
            $this->importer->import($file);
        } catch (BillingException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Цены загружены.');

        return Command::SUCCESS;
    }
}

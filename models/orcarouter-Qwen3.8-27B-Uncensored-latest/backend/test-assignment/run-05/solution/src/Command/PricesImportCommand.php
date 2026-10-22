<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PricesImportService;
use App\Support\ImportError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Импорт провайдеров, моделей и строк цен (JSON)')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PricesImportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        try {
            $this->service->import($path);
        } catch (ImportError $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return 1;
        }

        return 0;
    }
}

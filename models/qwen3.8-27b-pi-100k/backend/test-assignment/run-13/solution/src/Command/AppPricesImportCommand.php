<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\PriceImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт истории цен (JSON): провайдеры, модели, строки',
)]
final class AppPricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceImportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с историей цен (JSON)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        $ui = new SymfonyStyle($input, $output);

        try {
            $doc = $this->service->importFile($file);
        } catch (\Throwable $e) {
            $ui->error(sprintf('Ошибка импорта цены: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        $ui->info(sprintf(
            'Провайдеров: %d, моделей: %d, строк цен: %d',
            count($doc->providers),
            count($doc->models),
            count($doc->prices),
        ));

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Parser\PricesCatalogLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импортирует провайдеров, модели и строки цен (идемпотентный upsert).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesCatalogLoader $loader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу справочника цен.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = (string) $input->getArgument('file');
        if (!is_file($path)) {
            $io->getErrorStyle()->error(sprintf('Файл не найден: %s', $path));

            return Command::FAILURE;
        }

        try {
            $stats = $this->loader->load($path);
        } catch (\Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Импортирован справочник (providers=%d models=%d prices=%d).',
            $stats['changed_providers'],
            $stats['changed_models'],
            $stats['changed_prices'],
        ));

        return Command::SUCCESS;
    }
}

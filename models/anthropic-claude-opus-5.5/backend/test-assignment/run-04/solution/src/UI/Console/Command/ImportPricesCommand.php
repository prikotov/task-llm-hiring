<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Prices\ImportPriceList;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Загрузить историю цен (провайдеры, модели, строки цен)',
)]
final class ImportPricesCommand extends Command
{
    public function __construct(private readonly ImportPriceList $import)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу истории цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');

        try {
            $result = ($this->import)($file);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>Ошибка импорта цен: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'Импортировано: провайдеров — %d, моделей — %d, строк цен — %d.',
            $result['providers'],
            $result['models'],
            $result['prices'],
        ));

        return Command::SUCCESS;
    }
}

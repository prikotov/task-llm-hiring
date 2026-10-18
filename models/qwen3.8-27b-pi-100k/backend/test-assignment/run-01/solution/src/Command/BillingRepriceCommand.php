<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\RepriceRunner;
use App\Import\ImportException;
use App\Import\PricesParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цен: применяет исправленные строки и пересчитывает тарифицированные по ним события.',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PricesParser $pricesParser,
        private readonly RepriceRunner $repriceRunner,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        if (!is_file($path) || !is_readable($path)) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: файл не найден или не читается: %s', $path));

            return 1;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: невозможно прочитать файл: %s', $path));

            return 1;
        }

        try {
            $payload = $this->pricesParser->parseReprice($raw);
            $result = $this->repriceRunner->reprice($payload);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return 1;
        }

        // Строго две строки в stdout.
        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->eventsRepriced));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaUsd));

        return 0;
    }
}

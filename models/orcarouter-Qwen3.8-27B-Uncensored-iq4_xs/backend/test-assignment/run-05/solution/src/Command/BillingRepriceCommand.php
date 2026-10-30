<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RepriceService;
use App\Importer\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * app:billing:reprice <file> (бонус: ретро-коррекция цены)
 *
 * stdout: ровно две строки
 *   REPRICED_EVENTS=<n>
 *   REPRICE_DELTA_USD=<x.xxxxxx>
 */
#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленные строки цен и пересчитать затронутые события',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->repriceService->reprice($file);
        } catch (ImportException|\DomainException $e) {
            $output->getErrorOutput()->writeln('ОШИБКА КОРРЕКЦИИ: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln("REPRICED_EVENTS={$result['events']}");
        $output->writeln("REPRICE_DELTA_USD={$result['delta']}");

        return Command::SUCCESS;
    }
}

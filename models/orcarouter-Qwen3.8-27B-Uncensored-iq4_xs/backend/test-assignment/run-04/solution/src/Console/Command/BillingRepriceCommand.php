<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Application\Billing\Repricer;
use App\Exception\DataImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция: применить исправленные строки цен и пересчитать затронутые события.',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly Repricer $repricer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Pуть к JSON-файлу с исправленными строками цен (секции providers/models не обязательны).',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $path = (string) $input->getArgument('file');
            $result = $this->repricer->reprice($path);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка reprice: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->repricedEvents));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaFixed6()));

        return Command::SUCCESS;
    }
}

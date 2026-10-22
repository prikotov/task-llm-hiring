<?php

declare(strict_types=1);

namespace App\Billing\UI\Console;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Application\Import\PriceCatalogParser;
use App\Billing\Application\Reprice\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Применить ретро-коррекцию цен и пересчитать затронутые события')]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PriceCatalogParser $parser,
        private readonly RepriceService $reprice,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу исправленных строк цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $data = $this->parser->parseFile((string) $input->getArgument('file'));
            $result = $this->reprice->reprice($data);
        } catch (InvalidInputException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->repricedEvents));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaUsd));

        return Command::SUCCESS;
    }
}

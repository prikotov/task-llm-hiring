<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\BillingService;
use App\Billing\Money;
use App\Import\ImportException;
use App\Import\PriceCatalogParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly BillingService $billing,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:reprice')
            ->setDescription('Ретро-коррекция цен: применяет исправленные строки и пересчитывает затронутые события')
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $catalog = PriceCatalogParser::parseFile($file);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        if ($catalog['prices'] === []) {
            $output->getErrorOutput()->writeln('<error>В файле коррекции нет строк секции "prices"</error>');

            return Command::FAILURE;
        }

        $result = $this->billing->reprice($catalog['prices']);

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['count']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', Money::toSix($result['delta'])));

        return Command::SUCCESS;
    }
}

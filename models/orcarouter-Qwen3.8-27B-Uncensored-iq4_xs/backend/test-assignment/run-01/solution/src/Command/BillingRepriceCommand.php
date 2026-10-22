<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\Repricer;
use App\Exception\ImportException;
use App\Import\PricesFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цены')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly Repricer $repricer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Файл с исправленными строками цен (формат как prices.json)')
            ->setHelp('Upsert строк цен по (provider, model, valid_from) по данным загруженного справочника; пересчёт событий, тарифицированных заменёнными строками.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = PricesFile::fromFile((string) $input->getArgument('file'));
            $result = $this->repricer->reprice($file);
        } catch (ImportException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>', OutputInterface::VERBOSITY_QUIET);

            return 1;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return 0;
    }
}

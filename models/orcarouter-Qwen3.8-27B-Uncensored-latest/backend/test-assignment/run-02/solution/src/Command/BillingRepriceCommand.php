<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceService;
use App\Domain\Decimal;
use App\Exception\ImportException;
use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция: применить исправленные строки цен и пересчитать события',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $res = $this->service->reprice($file);
        } catch (ImportException|DbalException|\JsonException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->write(sprintf("REPRICED_EVENTS=%d\n", $res['events']));
        $output->write(sprintf('REPRICE_DELTA_USD=%s'.\chr(10), Decimal::format($res['delta'], 6)));

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceService;
use App\Support\ImportError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция: замена строк цен и пересчёт событий по ним')]
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
        $path = (string) $input->getArgument('file');
        try {
            $result = $this->service->reprice($path);
        } catch (ImportError $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return 1;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return 0;
    }
}

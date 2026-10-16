<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceService;
use App\Domain\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Корректировать строки цен из файла и пересчитать события, тарифицированные по ним',
)]
final class RepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $repricer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленной строкой цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->repricer->reprice($file);
        } catch (BillingException $exception) {
            $output->getErrorOutput()->writeln('Ошибка: '.$exception->getMessage());
            return 1;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->events));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->deltaUsd));

        return 0;
    }
}

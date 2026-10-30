<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceUsage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Применить исправления цен с журналом корректировок расходов.')]
final class RepriceUsageCommand extends BillingCommand
{
    public function __construct(private RepriceUsage $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл исправлений цен');
    }

    protected function perform(InputInterface $input, OutputInterface $output): void
    {
        $result = $this->reprice->run($input->getArgument('file'));
        $output->writeln('REPRICED_EVENTS=' . $result['count'], OutputInterface::OUTPUT_RAW);
        $output->writeln('REPRICE_DELTA_USD=' . $result['delta'], OutputInterface::OUTPUT_RAW);
    }
}

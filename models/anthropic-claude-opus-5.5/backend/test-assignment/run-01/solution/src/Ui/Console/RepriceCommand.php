<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Billing\Application\RepricingService;
use App\Pricing\Application\PriceList;
use App\Shared\InvalidInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цен с пересчётом рассчитанных событий')]
final class RepriceCommand extends Command
{
    public function __construct(private readonly RepricingService $repricing)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $list = PriceList::fromJson(FileReader::contents((string) $input->getArgument('file')));
            $r = $this->repricing->reprice($list);
        } catch (InvalidInput $e) {
            $err->writeln('ERROR '.$e->getMessage());

            return Command::FAILURE;
        }
        $output->write(sprintf("REPRICED_EVENTS=%d\nREPRICE_DELTA_USD=%s\n", $r['events'], $r['delta']->toFixed(6)), false, OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
    }
}

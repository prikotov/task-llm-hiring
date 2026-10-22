<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Prices\RepriceEvents;
use App\Domain\Billing\CostCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Применить ретро-коррекцию цен и пересчитать затронутые события',
)]
final class RepriceCommand extends Command
{
    public function __construct(private readonly RepriceEvents $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stderr = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        /** @var string $file */
        $file = $input->getArgument('file');

        try {
            $result = ($this->reprice)($file);
        } catch (\Throwable $e) {
            $stderr->writeln(sprintf('<error>Ошибка пересчёта: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['repriced']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']->fixed(CostCalculator::COST_SCALE)));

        return Command::SUCCESS;
    }
}

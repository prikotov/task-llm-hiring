<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленную строку и пересчитать затронутые события',
)]
final class RepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->reprice->reprice((string) $input->getArgument('file'));
            $output->writeln('REPRICED_EVENTS=' . $result['repriced']);
            $output->writeln('REPRICE_DELTA_USD=' . $result['delta']);
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

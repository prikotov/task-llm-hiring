<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use App\Domain\Billing\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цены: пересчёт затронутых событий.')]
final class RepriceCommand extends Command
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
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->service->reprice($file);
        } catch (BillingException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['n']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return Command::SUCCESS;
    }
}

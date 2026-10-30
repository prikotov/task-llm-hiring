<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\Repricer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция: применение исправленных строк цен; пересчёт событий, тарифицированных заменёнными строками.')]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly Repricer $repricer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с исправленными строками цен (схема как в app:prices:import)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $res = $this->repricer->reprice($file);
        } catch (\DomainException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return self::FAILURE;
        }

        $output->writeln('REPRICED_EVENTS='.$res['count']);
        $output->writeln('REPRICE_DELTA_USD='.$res['delta']);

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Reprice;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RepriceCommand extends Command
{
    public function __construct(private readonly Reprice $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:reprice')
            ->setDescription('Apply corrected price rows and re-evaluate rated events (bonus)')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the corrected prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        $result = $this->reprice->run($file, $output);

        $output->writeln("REPRICED_EVENTS={$result['n']}");
        $output->writeln('REPRICE_DELTA_USD='.$result['delta']);

        return 0;
    }
}

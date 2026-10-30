<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateService;
use App\Application\Billing\RepriceService;
use App\Application\Import\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция: применение строки цены из файла и пересчёт затронутых событий.',
)]
final class RepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $reprice,
        private readonly RateService $rater,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Файл коррекции (формат как prices.json; sections providers/models may be empty/missing).'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $affected = $this->reprice->repriceFile($file);
        } catch (ImportException $e) {
            \fwrite($output->getStream(), $e->getMessage() . PHP_EOL);

            return 1;
        }

        [$n, $delta] = $this->rater->repriceFor($affected);

        \fwrite($output->getStream(), sprintf("REPRICED_EVENTS=%d\n", $n));
        \fwrite($output->getStream(), sprintf("REPRICE_DELTA_USD=%s\n", $delta));

        return 0;
    }
}

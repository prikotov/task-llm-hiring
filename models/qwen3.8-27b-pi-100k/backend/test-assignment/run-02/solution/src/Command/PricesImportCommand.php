<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\PricesImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импортировать справочник (провайдеры, модели, строки цен) из JSON — idempotentно',
)]
final class PricesImportCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'JSON-файл со справочником (providers, models, prices)')
            ->setHelp('Импортирует провайдеров, модели и строки цен. Повторный запуск с теми же данными не меняет таблицы.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        /** @var string $path */
        $path = $input->getArgument('file');

        $summary = (new PricesImportService($this->em))->import($path);

        $this->emit(
            $errorOut,
            sprintf(
                '%s: OK providers created %d updated %d; models created %d; price rows created %d updated %d',
                $this->getName(),
                $summary->providers->created,
                $summary->providers->updated,
                $summary->models->created,
                $summary->priceRows->created,
                $summary->priceRows->updated,
            ),
        );

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ImportUsage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загрузить журнал использования без повторного учёта дублей.')]
final class ImportUsageCommand extends BillingCommand
{
    public function __construct(private ImportUsage $import)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий');
    }

    protected function perform(InputInterface $input, OutputInterface $output): void
    {
        $this->import->run($input->getArgument('file'));
    }
}

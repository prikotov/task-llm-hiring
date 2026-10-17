<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UsageEventImporter;
use App\Domain\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования из JSONL-файла (повторные события игнорируются)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageEventImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу со списком событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $summary = $this->importer->import($file);
        } catch (BillingException $exception) {
            $output->getErrorOutput()->writeln('Ошибка: '.$exception->getMessage());
            return 1;
        }

        $output->getErrorOutput()->writeln(sprintf(
            'События импортированы: всего строк %d, создано %d, проигнорировано как дубликаты %d',
            $summary->lines,
            $summary->created,
            $summary->duplicates,
        ));

        return 0;
    }
}

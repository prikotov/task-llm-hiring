<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Usage\UsageImporter;
use App\Shared\ConsoleStreams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузка событий использования из JSONL-файла (дубликаты игнорируются)',
)]
final class ImportUsageCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'путь к JSONL-файлу событий');
        $this->setHelp(<<<'HELP'
Загружает события использования (одна строка JSON — одно событие).

События дедуплицируются по (provider, provider_request_id): повторная
доставка уже известного события игнорируется целиком. Код выхода 0
даже если все строки — дубли; некорректная строка — ошибка, код 1
(и ни одна строка не сохраняется).
HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $summary = $this->importer->import($file);
        } catch (\Throwable $e) {
            ConsoleStreams::stderr($output)->writeln('app:usage:import: '.$e->getMessage());

            return 1;
        }

        ConsoleStreams::stderr($output)->writeln($summary->line());

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Import\UsageFileParser;
use App\Service\UsageImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Приём журнала событий использования (JSONL, одно событие на строку).
 * Дубли по (provider, provider_request_id) игнорируются; код возврата 0.
 */
#[AsCommand(
    name: 'app:usage:import',
    description: 'Загружает события использования из JSONL-файла (дубли игнорируются)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $usageImportService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий использования')
            ->setHelp(<<<'HELP'
                Пример: app:usage:import fixtures/usage.jsonl

                События дедуплицируются по (provider, provider_request_id):
                повторная доставка уже известного события игнорируется.
                Код возврата 0, даже если все строки — дубли.
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $errorOutput = $output->getErrorOutput();

        try {
            $events = UsageFileParser::parseFile($path);
        } catch (ImportException $e) {
            $errorOutput->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        try {
            $result = $this->usageImportService->import($events);
        } catch (\RuntimeException $e) {
            $errorOutput->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput->writeln(sprintf(
            'Импортировано событий: %d (дубли пропущены: %d, всего строк: %d).',
            $result['imported'],
            $result['duplicates'],
            \count($events),
        ));

        return Command::SUCCESS;
    }
}

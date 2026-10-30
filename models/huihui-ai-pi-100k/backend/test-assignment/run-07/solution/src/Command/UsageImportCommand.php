<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\DataImportException;
use App\Service\UsageFileParser;
use App\Service\UsageIngestor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL), с дедупликацией по (provider, provider_request_id)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageFileParser $parser,
        private readonly UsageIngestor $ingestor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $rows = $this->parser->parseFile($file);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $result = $this->ingestor->import($rows);

        $output->getErrorOutput()->writeln(
            sprintf(
                'Импортировано событий: %d, пропущено дублей: %d',
                $result['inserted'],
                $result['duplicates']
            )
        );

        return Command::SUCCESS;
    }
}

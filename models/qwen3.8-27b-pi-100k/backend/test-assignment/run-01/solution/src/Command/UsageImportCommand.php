<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\ImportException;
use App\Import\UsageFileParser;
use App\Import\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Загрузить события использования из JSONL-файла (дубли игнорируются)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageFileParser $parser,
        private readonly UsageImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий использования');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $specs = $this->parser->parseFile($file);
            $inserted = $this->importer->import($specs);
        } catch (ImportException|\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка импорта событий: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(
            sprintf('События загружены: всего %d, новых %d, повторно доставленных (игнор) %d (файл: %s)',
                count($specs),
                $inserted,
                count($specs) - $inserted,
                $file,
            ),
        );

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageFileParser;
use App\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий (JSONL) с дедупликацией',
)]
final class UsageImportCommand extends AppCommand
{
    public function __construct(
        private UsageImporter $importer,
        private UsageFileParser $parser,
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        try {
            $events = $this->parser->parse($path);
            $result = $this->importer->import($events);
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        $this->err($output, 'Events imported: ' . $result['inserted'] . ' new, '
            . $result['skipped'] . ' skipped (duplicates).');

        return self::SUCCESS;
    }
}

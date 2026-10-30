<?php

declare(strict_types=1);

namespace App\Billing\Command;

use App\Billing\Import\ImportException;
use App\Billing\Import\UsageFileParser;
use App\Billing\Service\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импортировать события использования из JSONL-файла (дубли игнорируются)',
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
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл событий (одно событие на строку)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = (string) $input->getArgument('file');

        if (!\is_file($path) || !\is_readable($path)) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return 1;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return 1;
        }

        try {
            $events = $this->parser->parse($content);
            $result = $this->importer->import($events);
        } catch (ImportException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        $io->success(sprintf(
            'Принято событий: %d, проигнорировано дублей: %d.',
            $result->accepted,
            $result->duplicatesIgnored,
        ));

        return 0;
    }
}

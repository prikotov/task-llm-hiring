<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Usage\UsageFileParser;
use App\Application\Usage\UsageImportService;
use App\Exception\InvalidUsageFileException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Append usage events from a JSONL file (dedup by provider + request id).',
)]
final class ImportUsageCommand extends Command
{
    public function __construct(
        private readonly UsageFileParser $parser,
        private readonly UsageImportService $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the usage events JSONL file')
            ->setHelp(
                'Reads one JSON event per line. A line that is malformed (JSON shape, required fields, '
                .'unknown provider/model) fails the whole command with exit code 1. '
                .'Duplicates by (provider, provider_request_id) are ignored and reported. '
                .'Inserted events are unrated until app:billing:rate picks applicable prices.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = $output->getErrorOutput();

        $path = (string) $input->getArgument('file');

        if (!is_file($path)) {
            $output->writeln(sprintf('<error>file not found: %s</error>', $path));

            return Command::FAILURE;
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            $output->writeln(sprintf('<error>cannot read file: %s</error>', $path));

            return Command::FAILURE;
        }

        try {
            $specs = $this->parser->parse($content);
        } catch (InvalidUsageFileException $e) {
            $output->writeln(sprintf('<error>invalid usage file: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        try {
            $result = $this->importer->import($specs);
        } catch (InvalidUsageFileException $e) {
            $output->writeln(sprintf('<error>import failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>import failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('events: %d', count($specs)));
        $output->writeln(sprintf('inserted: %d', $result['inserted']));
        $output->writeln(sprintf('duplicates ignored: %d', $result['duplicated']));

        return Command::SUCCESS;
    }
}

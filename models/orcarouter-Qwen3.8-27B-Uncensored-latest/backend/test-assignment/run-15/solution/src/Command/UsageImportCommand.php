<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\UsageImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from a JSONL file.')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImportService $import)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to a JSONL file (one event per line).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln("error: file not found: {$file}");
            return 1;
        }
        try {
            $r = $this->import->importJsonl($file);
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            $output->getErrorOutput()->writeln('error: '.$e->getMessage());
            return 1;
        }
        $output->writeln(sprintf('ok: imported %d, skipped %d duplicate(s)', $r['imported'], $r['duplicates']));
        return 0;
    }
}

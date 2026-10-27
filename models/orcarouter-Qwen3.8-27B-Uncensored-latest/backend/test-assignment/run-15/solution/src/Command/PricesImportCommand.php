<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\PriceImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import providers, models and price lines from a JSON file.')]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly PriceImportService $import)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to a prices JSON file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file)) {
            $this->err($output, "error: file not found: {$file}");
            return 1;
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            $this->err($output, 'error: cannot read input file');
            return 1;
        }
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->err($output, 'error: invalid JSON: '.$e->getMessage());
            return 1;
        }
        if (!is_array($data)) {
            $this->err($output, 'error: invalid JSON (expected object at top level)');
            return 1;
        }
        try {
            $count = $this->import->importFromPayload($data);
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            $this->err($output, 'error: '.$e->getMessage());
            return 1;
        }
        $this->out($output, sprintf('ok: imported %d price line(s)', $count));
        return 0;
    }

    private function out(OutputInterface $o, string $msg): void
    {
        $o->write($msg."\n");
    }

    private function err(OutputInterface $o, string $msg): void
    {
        $o->getErrorOutput()->write($msg."\n");
    }
}

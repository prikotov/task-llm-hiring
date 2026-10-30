<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UsageImportService;
use App\Exception\InvalidInputException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Load usage events (JSONL).',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private UsageImportService $svc,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to usage.jsonl (one event per line).');
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $style = new SymfonyStyle($in, $out);
        $file = (string) $in->getArgument('file');
        try {
            $new = $this->svc->importFile($file);
        } catch (InvalidInputException $e) {
            $style->error('usage import failed: ' . $e->getMessage());
            return 1;
        } catch (\Throwable $e) {
            $style->error('usage import failed: ' . $e->getMessage());
            return 1;
        }

        if ($new) {
            $style->success('Usage events imported.');
        } else {
            $style->success('No new events (all duplicates).');
        }
        return self::SUCCESS;
    }
}

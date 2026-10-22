<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Usage\UsageImportService;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events (idempotent by provider request id)')]
final class ImportUsageCommand extends AbstractCommand
{
    public function __construct(private readonly UsageImportService $service)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the JSONL usage file');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->service->importFile((string) $input->getArgument('file'));
        } catch (InvalidArgumentException $exception) {
            $this->writeError($output, $exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

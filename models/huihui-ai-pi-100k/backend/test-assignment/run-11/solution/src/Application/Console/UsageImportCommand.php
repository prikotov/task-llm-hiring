<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Ingestion\UsageImportService;
use App\Domain\DomainException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events (JSONL) into the database.')]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the JSONL file (e.g. fixtures/usage.jsonl)')
            ->setHelp('Idempotent import of usage events. Duplicates are silently ignored.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $err = $output->getErrorOutput();

        try {
            $result = $this->service->import($file, $this->em);
        } catch (DomainException $e) {
            $err->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
        $err->writeln(sprintf('Imported %d event(s), ignored %d duplicate(s).', $result['inserted'], $result['ignored']));

        return Command::SUCCESS;
    }
}

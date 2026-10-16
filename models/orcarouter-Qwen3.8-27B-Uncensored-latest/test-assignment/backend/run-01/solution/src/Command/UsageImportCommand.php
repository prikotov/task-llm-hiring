<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\UsageImporter;
use App\Domain\Billing\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:usage:import', description: 'Импорт событий использования (JSONL). Дубли игнорируются.')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями использования');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $accepted = $this->importer->import($file);
        } catch (BillingException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $output->getErrorOutput()->writeln(sprintf('Событий принято: %d.', $accepted));

        return Command::SUCCESS;
    }
}

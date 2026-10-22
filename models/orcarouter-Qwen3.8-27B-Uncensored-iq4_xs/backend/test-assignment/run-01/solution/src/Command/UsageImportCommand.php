<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Import\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импорт событий использования (JSONL)')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий')
            ->setHelp('Приём событий: дубликаты по (provider, provider_request_id) игнорируются. Код выхода 0, даже если все строки — дубли.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->importer->importFile((string) $input->getArgument('file'));
        } catch (ImportException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>', OutputInterface::VERBOSITY_QUIET);

            return 1;
        }

        $output->writeln($result->summary('usage'), OutputInterface::VERBOSITY_VERBOSE);

        return 0;
    }
}

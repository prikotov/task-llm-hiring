<?php

declare(strict_types=1);

namespace App\Billing\UI\Console;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Application\Import\UsageEventImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загрузить события использования из JSONL (дедупликация по провайдеру и provider_request_id)')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageEventImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output->getErrorOutput();
        try {
            $result = $this->importer->importFile((string) $input->getArgument('file'));
        } catch (InvalidInputException $e) {
            $err->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $err->writeln(sprintf('Новых событий: %d, дублей пропущено: %d', $result['new'], $result['duplicates']));

        return Command::SUCCESS;
    }
}

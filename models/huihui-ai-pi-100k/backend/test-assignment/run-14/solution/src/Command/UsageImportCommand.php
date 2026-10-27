<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Import\UsageImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импорт журнала событий использования из JSONL-файла. Дедупликация по (provider, provider_request_id).')]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу (одно событие на строку)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $stats = $this->importer->import($file);
        } catch (\DomainException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return self::FAILURE;
        }

        $output->getErrorOutput()->write(sprintf(
            "строок: %d, вставлено: %d, дубли: %d\n",
            $stats['total'],
            $stats['inserted'],
            $stats['duplicated'],
        ));

        return self::SUCCESS;
    }
}

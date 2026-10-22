<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UsageImporter;
use App\Domain\Exception\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:usage:import', description: 'Загрузить события использования из JSONL-файла (дубли игнорируются)')]
final class AppUsageImportCommand extends Command
{
    public function __construct(private readonly UsageImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        $content = @file_get_contents($file);
        if ($content === false) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $file));
            return Command::FAILURE;
        }

        try {
            $this->importer->import($content);
        } catch (ImportException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\JsonException $e) {
            $io->error('Некорректный JSON: '.$e->getMessage());
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $io->error('Не удалось импортировать события: '.$e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\FileFormatException;
use App\Application\Usage\EventImporter;
use App\Application\Usage\UsageFileParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования из JSONL-файла (дубли игнорируются)',
)]
final class UsageImportCommand extends Command
{
    public function __construct(private readonly EventImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий')
            ->setHelp('Загружает события использования. Повторная доставка событий по ключу (provider, provider_request_id) игнорируется.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $stderr = $output->getErrorOutput();

        try {
            $counts = $this->importer->import(UsageFileParser::fromFile($path));
        } catch (FileFormatException $e) {
            $stderr->writeln('Ошибка формата данных: '.$e->getMessage());

            return Command::FAILURE;
        } catch (Throwable $e) {
            $stderr->writeln('Ошибка импорта: '.$e->getMessage());

            return Command::FAILURE;
        }

        $stderr->writeln(sprintf(
            'Импорт завершён: новых событий — %d, дубликатов пропущено — %d.',
            $counts['imported'],
            $counts['duplicates'],
        ));

        return Command::SUCCESS;
    }
}

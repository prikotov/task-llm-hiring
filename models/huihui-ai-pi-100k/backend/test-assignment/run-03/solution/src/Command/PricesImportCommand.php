<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Pricing\CatalogImporter;
use App\Application\Pricing\PriceFileParser;
use App\Application\Exception\FileFormatException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника цен (провайдеры, модели, строки цен) из JSON-файла',
)]
final class PricesImportCommand extends Command
{
    public function __construct(private readonly CatalogImporter $catalog)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с историей цен')
            ->setHelp('Загружает или обновляет провайдеров, модели и строки цен. Идемпотентная операция (upsert).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $stderr = $output->getErrorOutput();

        $content = self::readFile($path, $stderr);
        if ($content === null) {
            return Command::FAILURE;
        }

        try {
            $counts = $this->catalog->import(PriceFileParser::fromJson($content));
        } catch (FileFormatException $e) {
            $stderr->writeln('Ошибка формата данных: '.$e->getMessage());

            return Command::FAILURE;
        } catch (Throwable $e) {
            $stderr->writeln('Ошибка импорта: '.$e->getMessage());

            return Command::FAILURE;
        }

        $stderr->writeln(sprintf(
            'Импорт завершён: провайдеров — %d, моделей — %d, строк цен — %d.',
            $counts['providers'],
            $counts['models'],
            $counts['price_rows'],
        ));

        return Command::SUCCESS;
    }

    public static function readFile(string $path, OutputInterface $output): ?string
    {
        if (!is_file($path)) {
            $output->writeln(sprintf('Файл не найден: %s', $path));

            return null;
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            $output->writeln(sprintf('Не удалось прочитать файл: %s', $path));

            return null;
        }

        return $content;
    }
}

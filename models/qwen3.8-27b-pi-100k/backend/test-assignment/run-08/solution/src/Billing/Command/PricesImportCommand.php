<?php

declare(strict_types=1);

namespace App\Billing\Command;

use App\Billing\Import\ImportException;
use App\Billing\Import\PricesFileParser;
use App\Billing\Service\PriceListImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импортировать (upsert) провайдеров, модели и строки цен из JSON-файла',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PricesFileParser $parser,
        private readonly PriceListImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл истории цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = (string) $input->getArgument('file');

        $content = $this->readFile($path, $io);
        if ($content === null) {
            return 1;
        }

        try {
            $file = $this->parser->parse($content);
            $this->importer->import($file);
        } catch (ImportException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        $io->success(sprintf(
            'Импортировано: провайдеров %d, моделей %d, строк цен %d.',
            \count($file->providers),
            \count($file->models),
            \count($file->priceRows),
        ));

        return 0;
    }

    private function readFile(string $path, SymfonyStyle $io): ?string
    {
        if (!\is_file($path) || !\is_readable($path)) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return null;
        }

        return $content;
    }
}

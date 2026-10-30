<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\InputError;
use App\Service\PriceFileParser;
use App\Service\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника цен (JSON)',
)]
final class PricesImportCommand extends AppCommand
{
    public function __construct(
        private PriceImporter $importer,
        private PriceFileParser $parser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        try {
            $json = self::readFile($path);
            $parsed = $this->parser->parse($json);
            $this->importer->import($parsed);
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        $this->err($output, 'Prices imported: '
        . \count($parsed['providers']) . ' providers, '
        . \count($parsed['models']) . ' models, '
        . \count($parsed['prices']) . ' price rows.');

        return self::SUCCESS;
    }

    private static function readFile(string $path): string
    {
        if (!is_file($path)) {
            throw InputError::because(string: 'Файл не найден: ' . $path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw InputError::because(string: 'Файл не читается: ' . $path);
        }

        return $json;
    }
}

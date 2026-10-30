<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PriceCatalogService;
use App\Support\Input;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;

#[AsCommand(name: 'app:prices:import')]
final class ImportPricesCommand extends AppCommand
{
    public function __construct(private readonly PriceCatalogService $catalog)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Загрузить провайдеров, модели и строки цен из JSON')
            ->addArgument('file', InputArgument::REQUIRED, 'путь к JSON-файлу с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $data = Input::loadJson($file);
            $this->catalog->import(
                $data['providers'] ?? [],
                $data['models'] ?? [],
                $data['prices'] ?? [],
            );
        } catch (\RuntimeException | \JsonException | TypeError $e) {
            $this->error($output, 'app:prices:import: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

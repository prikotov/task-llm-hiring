<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceService;
use App\Support\Input;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice')]
final class RepriceCommand extends AppCommand
{
    public function __construct(private readonly RepriceService $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Ретро-коррекция цены: применить исправленные строки и пересчитать события')
            ->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $data = Input::loadJson($file);
            $priceRows = $data['prices'] ?? [];
            $result = $this->reprice->reprice($priceRows);
        } catch (\RuntimeException | \JsonException | \TypeError $e) {
            $this->error($output, 'app:billing:reprice: ' . $e->getMessage());

            return self::FAILURE;
        }

        $output->writeln('REPRICED_EVENTS=' . $result['events']);
        $output->writeln('REPRICE_DELTA_USD=' . $result['deltaUsd']);

        return self::SUCCESS;
    }
}

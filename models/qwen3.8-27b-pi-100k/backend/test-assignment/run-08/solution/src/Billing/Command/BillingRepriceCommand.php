<?php

declare(strict_types=1);

namespace App\Billing\Command;

use App\Billing\Import\ImportException;
use App\Billing\Import\PricesFileParser;
use App\Billing\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция: применить исправленные строки цен и пересчитать события, считанные ими',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PricesFileParser $parser,
        private readonly RepriceService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен (формат как у истории цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = (string) $input->getArgument('file');

        if (!\is_file($path) || !\is_readable($path)) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return 1;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            $io->error(sprintf('Не удалось прочитать файл "%s".', $path));

            return 1;
        }

        try {
            $file = $this->parser->parse($content);
            $result = $this->service->reprice($file);
        } catch (ImportException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        // Строго две строки в stdout.
        $output->writeln('REPRICED_EVENTS='.$result->repricedEvents);
        $output->writeln('REPRICE_DELTA_USD='.$result->deltaUsd);

        return 0;
    }
}

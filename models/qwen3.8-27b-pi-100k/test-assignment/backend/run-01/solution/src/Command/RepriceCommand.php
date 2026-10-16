<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RepriceService;
use App\Import\ImportException;
use App\Import\PriceFileParser;
use App\Support\Decimals;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Бонус (ТЗ): ретро-коррекция цен — применить исправленные строки и пересчитать затрагивающие их события',
)]
final class RepriceCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен (формат как при импорте цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        $result = null;
        try {
            $priceFile = $this->parser->parseFile($file);
            $result = $this->repriceService->reprice($priceFile);
        } catch (ImportException|\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка reprice: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->write(sprintf("REPRICED_EVENTS=%d\n", $result['events']));
        $output->write(sprintf("REPRICE_DELTA_USD=%s\n", Decimals::format6($result['delta'])));

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Money;
use App\DTO\PricesDocument;
use App\Exception\DataImportException;
use App\Service\PricesFileParser;
use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: upsert исправленной строки цен и пересчёт событий, тарифицированных ею',
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
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $doc = $this->parser->parseFile($file);
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        try {
            $result = $this->service->reprice($doc);
        } catch (DataImportException | \LogicException $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->write('REPRICED_EVENTS=' . $result['count'] . "\n");
        $output->write('REPRICE_DELTA_USD=' . Money::to6($result['delta']) . "\n");

        return Command::SUCCESS;
    }
}

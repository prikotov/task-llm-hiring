<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\PriceImporter;
use App\Application\PricingService;
use App\Domain\Entity\PriceRow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Ретро- коррекция цены (бонус).
 *
 * upsert исправленных строк по (provider, model, valid_from) — модель и
 * провайдер должны существовать в уже загруженном справочнике; секции
 * providers/models в файле могут отсутствовать; затем пересчитывает Rated
 * события, тарифицировавшиеся этими строками, и выводит дельту стоимости.
 */
#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цены и пересчёт событий')]
final class RepriceCommand extends Command
{
    public function __construct(
        private PriceImporter $importer,
        private PricingService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED,
            'Путь к JSON-файлу с исправленной строкой цены');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        /** @var PriceRow[] $replaced */
        try {
            $replaced = $this->importer->reimportPricesFromFile($file);
        } catch (Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());
            return Command::FAILURE;
        }

        $result = $this->service->reprice($replaced);

        $io->writeln('REPRICED_EVENTS=' . \count($result['repricedEvents']));
        $io->writeln('REPRICE_DELTA_USD=' . $result['delta']);

        return Command::SUCCESS;
    }
}

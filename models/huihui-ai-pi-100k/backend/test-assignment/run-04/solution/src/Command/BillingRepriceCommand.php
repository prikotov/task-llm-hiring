<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\RepriceService;
use App\Ingest\IngestError;
use App\Ingest\PriceFileParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция: применяет исправленные строки цен и пересчитывает затрагиваемые события')]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly RepriceService $reprice,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен (секции providers/models опциональны)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $catalog = $this->parser->parse(FileSupport::read($file));
        } catch (IngestError | \RuntimeException $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        try {
            $result = $this->reprice->apply($catalog['prices']);
        } catch (IngestError | \Throwable $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return 0;
    }
}

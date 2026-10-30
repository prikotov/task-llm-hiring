<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Domain\MoneyMath;
use App\Service\RatingService;
use App\Service\RepriceService;
use App\Support\ConsoleWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленные строки цен и пересчитать события, тарифицированные ими',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $reprice,
        private readonly RatingService $rating,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с исправленными строками цен (формат истории цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $touched = $this->reprice->applyCorrections((string) $input->getArgument('file'));
        } catch (DomainException $e) {
            ConsoleWriter::note($output, 'Ошибка: '.$e->getMessage());

            return 1;
        }

        $ids = array_map(static fn ($p): int => $p->id, $touched);
        $result = $this->rating->repriceByPriceIds($ids);

        // В stdout — ровно две строки.
        $output->writeln(sprintf(
            "REPRICED_EVENTS=%d\nREPRICE_DELTA_USD=%s",
            $result['recomputed'],
            MoneyMath::formatMicros($result['deltaMicros']),
        ));

        return Command::SUCCESS;
    }
}

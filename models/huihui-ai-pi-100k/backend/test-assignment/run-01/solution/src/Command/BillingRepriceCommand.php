<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Ingest\PriceFileParser;
use App\Service\RepriceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция: применение исправленных строк цен и пересчёт событий, рассчитанных ими',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly RepriceService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с исправленными строками цен (формат истории цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->service->reprice($this->em, $this->parser->parse($file));
        } catch (DomainException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        // stdout — ровно две строки
        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->repriced));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->delta));

        return Command::SUCCESS;
    }
}

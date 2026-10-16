<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Ingest\PriceFile;
use App\Ingest\PriceFileParser;
use App\Service\PriceImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт (идемпотентный upsert) провайдеров, моделей и строк цен из JSON-файла',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly PriceImportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл истории цен (JSON)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $priceFile = $this->parser->parse($file);
            $this->service->import($this->em, $priceFile);
        } catch (DomainException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        // служебные сообщения — в stderr
        $output->getErrorOutput()->writeln(sprintf('[OK] Импортировано: %d провайдеров, %d моделей, %d строк цен',
            \count($priceFile->providers), \count($priceFile->models), \count($priceFile->prices)
        ));

        return Command::SUCCESS;
    }
}

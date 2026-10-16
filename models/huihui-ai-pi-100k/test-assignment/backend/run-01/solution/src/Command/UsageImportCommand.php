<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\DomainException;
use App\Service\UsageImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт журнала использования (JSONL); дубли по (provider, provider_request_id) игнорируются',
)]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл журнала использования (JSONL)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $result = $this->service->importFile($this->em, $file);
        } catch (DomainException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        // служебные сообщения — в stderr
        $output->getErrorOutput()->writeln(sprintf('[OK] Принято событий: %d; пропущено дублей: %d', $result->imported, $result->duplicates));

        return Command::SUCCESS;
    }
}

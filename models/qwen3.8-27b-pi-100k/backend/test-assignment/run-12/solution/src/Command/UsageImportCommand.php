<?php

declare(strict_types=1);

namespace App\Command;

use App\Infra\Input\FileReader;
use App\Infra\Input\UsageEventParser;
use App\Repository\UsageEventRowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRowRepository $usage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:usage:import')
            ->setDescription('Append usage events from a JSONL file (dedupe by provider + provider_request_id)')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $lines = FileReader::readJsonl($file);
            $events = UsageEventParser::parse($lines);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln("ERR: {$e->getMessage()}");
            return 1;
        }

        $inserted = 0;
        $dupes = 0;
        foreach ($events as $e) {
            $ok = $this->usage->insertIfNew(
                $e->provider,
                $e->providerRequestId,
                $e->model,
                $e->user,
                $e->ts,
                $e->tokens['input'],
                $e->tokens['output'],
                $e->tokens['cache_read'],
                $e->tokens['cache_write'],
            );
            if ($ok) {
                $inserted++;
            } else {
                $dupes++;
            }
        }

        $this->em->flush();

        $output->writeln("IMPORTED inserted={$inserted} duplicates={$dupes}");
        return 0;
    }
}

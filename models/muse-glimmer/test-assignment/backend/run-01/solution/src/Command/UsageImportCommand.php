<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from JSONL file')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln("<error>File not found</error>");
            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $output->writeln("<error>Cannot open file</error>");
            return Command::FAILURE;
        }

        $lineNumber = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $output->writeln("<error>Invalid JSON on line $lineNumber</error>", OutputInterface::VERBOSITY_NORMAL);
                fclose($handle);
                return Command::FAILURE;
            }

            $provider = $data['provider'] ?? null;
            $providerRequestId = $data['provider_request_id'] ?? null;
            if (!$provider || !$providerRequestId) {
                $output->writeln("<error>Missing keys on line $lineNumber</error>");
                fclose($handle);
                return Command::FAILURE;
            }

            // deduplicate
            $repo = $this->em->getRepository(UsageEvent::class);
            $existing = $repo->findOneBy(['provider' => $provider, 'providerRequestId' => $providerRequestId]);
            if ($existing) {
                continue; // ignore duplicate
            }

            $model = $data['model'] ?? '';
            $user = $data['user'] ?? '';
            $ts = new \DateTimeImmutable($data['ts'] ?? 'now');

            $event = new UsageEvent(
                $provider,
                $providerRequestId,
                $model,
                $user,
                $ts,
                (int)($data['tokens_input'] ?? 0),
                (int)($data['tokens_output'] ?? 0),
                (int)($data['tokens_cache_read'] ?? 0),
                (int)($data['tokens_cache_write'] ?? 0)
            );

            $this->em->persist($event);
        }

        fclose($handle);
        $this->em->flush();

        return Command::SUCCESS;
    }
}

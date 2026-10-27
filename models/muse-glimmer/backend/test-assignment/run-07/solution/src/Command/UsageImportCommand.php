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

#[AsCommand(name: 'app:usage:import', description: 'Import usage JSONL')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to usage JSONL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_readable($file)) {
            $output->writeError("File not readable");
            return Command::FAILURE;
        }
        $handle = fopen($file, 'r');
        if (!$handle) {
            $output->writeError("Cannot open");
            return Command::FAILURE;
        }
        $lineNumber = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->writeError("Invalid JSON at line $lineNumber");
                fclose($handle);
                return Command::FAILURE;
            }
            // minimal validation
            $provider = $data['provider'] ?? null;
            $requestId = $data['provider_request_id'] ?? null;
            if (!$provider || !$requestId) {
                // skip? treat as invalid
                $output->writeError("Missing fields at line $lineNumber");
                fclose($handle);
                return Command::FAILURE;
            }

            $repo = $this->em->getRepository(UsageEvent::class);
            $existing = $repo->findOneBy(['providerCode' => $provider, 'providerRequestId' => $requestId]);
            if ($existing) {
                continue; // dedupe
            }

            try {
                $ts = new \DateTimeImmutable($data['ts']);
            } catch (\Throwable) {
                $output->writeError("Invalid ts at line $lineNumber");
                fclose($handle);
                return Command::FAILURE;
            }

            $event = new UsageEvent(
                $provider,
                $requestId,
                $data['model'] ?? '',
                $data['user'] ?? '',
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

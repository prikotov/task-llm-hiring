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

#[AsCommand(name: 'app:usage:import', description: 'Import usage JSONL file')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln("File not found: $file");
            return Command::FAILURE;
        }
        $handle = fopen($file, 'r');
        if ($handle === false) {
            $output->getErrorOutput()->writeln('Cannot open file');
            return Command::FAILURE;
        }
        $repo = $this->em->getRepository(UsageEvent::class);
        $lineNum = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                $output->getErrorOutput()->writeln("Invalid JSON on line $lineNum");
                fclose($handle);
                return Command::FAILURE;
            }
            $provider = $data['provider'] ?? null;
            $requestId = $data['provider_request_id'] ?? null;
            if (!$provider || !$requestId) {
                $output->getErrorOutput()->writeln("Missing key fields on line $lineNum");
                fclose($handle);
                return Command::FAILURE;
            }
            // idempotent: skip if exists
            $existing = $repo->findOneBy(['providerCode' => $provider, 'providerRequestId' => $requestId]);
            if ($existing) {
                continue;
            }
            $event = new UsageEvent();
            $event->setProviderCode($provider);
            $event->setProviderRequestId($requestId);
            $event->setModelCode($data['model'] ?? '');
            $event->setUser($data['user'] ?? '');
            $event->setTs(new \DateTimeImmutable($data['ts']));
            $event->setTokensInput((int)($data['tokens_input'] ?? 0));
            $event->setTokensOutput((int)($data['tokens_output'] ?? 0));
            $event->setTokensCacheRead((int)($data['tokens_cache_read'] ?? 0));
            $event->setTokensCacheWrite((int)($data['tokens_cache_write'] ?? 0));
            $this->em->persist($event);
        }
        fclose($handle);
        $this->em->flush();
        return Command::SUCCESS;
    }
}

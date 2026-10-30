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
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to usage JSONL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln('File not found');
            return Command::FAILURE;
        }
        $handle = fopen($file, 'r');
        if (!$handle) {
            $output->getErrorOutput()->writeln('Cannot open file');
            return Command::FAILURE;
        }
        $lineNum = 0;
        $repo = $this->em->getRepository(UsageEvent::class);
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $output->getErrorOutput()->writeln("Invalid JSON at line $lineNum");
                fclose($handle);
                return Command::FAILURE;
            }
            // Required fields
            $provider = $data['provider'] ?? null;
            $requestId = $data['provider_request_id'] ?? null;
            if (!$provider || !$requestId) {
                $output->getErrorOutput()->writeln("Missing fields at line $lineNum");
                fclose($handle);
                return Command::FAILURE;
            }
            $existing = $repo->findOneBy(['provider' => $provider, 'providerRequestId' => $requestId]);
            if ($existing) {
                continue; // idempotent
            }
            $event = new UsageEvent();
            $event->setProvider($provider);
            $event->setProviderRequestId($requestId);
            $event->setModel($data['model'] ?? '');
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

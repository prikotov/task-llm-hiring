<?php

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
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln("File not found: $file");
            return Command::FAILURE;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            $output->getErrorOutput()->writeln("Cannot read file");
            return Command::FAILURE;
        }

        $repo = $this->em->getRepository(UsageEvent::class);

        foreach ($lines as $lineNumber => $line) {
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $output->getErrorOutput()->writeln(sprintf('Line %d: Invalid JSON: %s', $lineNumber + 1, $e->getMessage()));
                return Command::FAILURE;
            }

            $provider = $data['provider'] ?? null;
            $providerRequestId = $data['provider_request_id'] ?? null;
            if (!$provider || !$providerRequestId) {
                $output->getErrorOutput()->writeln(sprintf('Line %d: Missing provider or provider_request_id', $lineNumber + 1));
                return Command::FAILURE;
            }

            $existing = $repo->findOneBy([
                'providerCode' => $provider,
                'providerRequestId' => $providerRequestId,
            ]);
            if ($existing) {
                continue; // deduplicate
            }

            $event = new UsageEvent();
            $event->setProviderCode($provider);
            $event->setProviderRequestId($providerRequestId);
            $event->setModelCode($data['model'] ?? '');
            $event->setUser($data['user'] ?? '');
            $event->setTs(new \DateTimeImmutable($data['ts']));
            $event->setTokensInput((int)($data['tokens_input'] ?? 0));
            $event->setTokensOutput((int)($data['tokens_output'] ?? 0));
            $event->setTokensCacheRead((int)($data['tokens_cache_read'] ?? 0));
            $event->setTokensCacheWrite((int)($data['tokens_cache_write'] ?? 0));
            $this->em->persist($event);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}

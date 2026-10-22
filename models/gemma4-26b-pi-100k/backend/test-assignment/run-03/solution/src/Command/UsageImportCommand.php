<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Imports usage events from a JSONL file')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $output->writeln("<error>Could not open file: $filePath</error>");
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
                $output->writeln("<error>Invalid JSON on line $lineNumber: " . json_last_error_msg() . "</error>");
                fclose($handle);
                return Command::FAILURE;
            }

            $provider = $data['provider'];
            $providerRequestId = $data['provider_request_id'];

            $existingEvent = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
                'provider' => $provider,
                'providerRequestId' => $providerRequestId
            ]);

            if ($existingEvent) {
                // Skip duplicate
                continue;
            }

            $event = new UsageEvent(
                $provider,
                $providerRequestId,
                $data['model'],
                $data['user'],
                new \DateTime($data['ts']),
                (int) $data['tokens_input'],
                (int) $data['tokens_output'],
                (int) ($data['tokens_cache_read'] ?? 0),
                (int) ($data['tokens_cache_write'] ?? 0)
            );

            $this->entityManager->persist($event);

            // To avoid memory issues and for performance, flush in batches
            if ($lineNumber % 100 === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();
        fclose($handle);

        return Command::SUCCESS;
    }
}

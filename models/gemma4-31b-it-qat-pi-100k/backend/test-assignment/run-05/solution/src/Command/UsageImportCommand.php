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

#[AsCommand(name: 'app:usage:import')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Imports usage events from JSONL file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            $output->writeln("File not found: $filePath", OutputInterface::VERBOSITY_VERBOSE);
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return Command::FAILURE;
        }

        $count = 0;
        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $data = json_decode($line, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    fclose($handle);
                    return Command::FAILURE;
                }

                $existing = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
                    'provider' => $data['provider'],
                    'providerRequestId' => $data['provider_request_id']
                ]);

                if ($existing) {
                    continue;
                }

                $event = new UsageEvent(
                    $data['provider'],
                    $data['provider_request_id'],
                    $data['model'],
                    $data['user'],
                    new \DateTimeImmutable($data['ts']),
                    $data['tokens_input'] ?? 0,
                    $data['tokens_output'] ?? 0,
                    $data['tokens_cache_read'] ?? 0,
                    $data['tokens_cache_write'] ?? 0
                );

                $this->entityManager->persist($event);
                $count++;
            }
            $this->entityManager->flush();
            $output->writeln("Imported $count events.");
        } catch (\Exception $e) {
            fclose($handle);
            $output->writeln("Error importing usage: " . $e->getMessage(), OutputInterface::VERBOSITY_VERBOSE);
            return Command::FAILURE;
        }

        fclose($handle);
        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from JSONL file')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error("File not found: $filePath");
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $io->error("Could not open file: $filePath");
            return Command::FAILURE;
        }

        $lineNum = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') continue;

            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $io->error("Invalid JSON on line $lineNum: " . json_last_error_msg());
                fclose($handle);
                return Command::FAILURE;
            }

            try {
                $this->importEvent($data);
            } catch (\Exception $e) {
                $io->error("Error importing event on line $lineNum: " . $e->getMessage());
                fclose($handle);
                return Command::FAILURE;
            }
        }
        fclose($handle);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    private function importEvent(array $data): void
    {
        $providerCode = $data['provider'];
        $requestId = $data['provider_request_id'];

        $event = $this->entityManager->getRepository(UsageEvent::class)->findOneBy([
            'providerCode' => $providerCode,
            'providerRequestId' => $requestId,
        ]);

        if ($event) {
            return; // Idempotency: ignore duplicates
        }

        $model = $this->entityManager->getRepository(Model::class)->findOneBy([
            'provider' => $this->entityManager->getRepository(Provider::class)->find($providerCode),
            'code' => $data['model'],
        ]);

        if (!$model) {
            throw new \RuntimeException("Model {$providerCode}/{$data['model']} not found");
        }

        $event = new UsageEvent();
        $event->providerCode = $providerCode;
        $event->providerRequestId = $requestId;
        $event->model = $model;
        $event->username = $data['user'];
        $event->ts = new \DateTimeImmutable($data['ts']);
        $event->tokensInput = $data['tokens_input'] ?? 0;
        $event->tokensOutput = $data['tokens_output'] ?? 0;
        $event->tokensCacheRead = $data['tokens_cache_read'] ?? 0;
        $event->tokensCacheWrite = $data['tokens_cache_write'] ?? 0;
        $event->isRated = false;

        $this->entityManager->persist($event);
    }
}

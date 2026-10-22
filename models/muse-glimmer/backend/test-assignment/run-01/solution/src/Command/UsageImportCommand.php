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

#[AsCommand(name: 'app:usage:import', description: 'Import usage events from JSONL')]
final class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL file with usage events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            return Command::FAILURE;
        }

        $lineNum = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                fclose($handle);
                return Command::FAILURE;
            }

            $provider = $data['provider'];
            $requestId = $data['provider_request_id'];
            $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
                'providerCode' => $provider,
                'providerRequestId' => $requestId,
            ]);
            if ($existing) {
                continue; // idempotent
            }

            $event = new UsageEvent(
                $provider,
                $requestId,
                $data['model'],
                $data['user'],
                new \DateTimeImmutable($data['ts']),
                (int)$data['tokens_input'],
                (int)$data['tokens_output'],
                (int)$data['tokens_cache_read'],
                (int)$data['tokens_cache_write']
            );
            $this->em->persist($event);
        }
        fclose($handle);
        $this->em->flush();

        return Command::SUCCESS;
    }
}

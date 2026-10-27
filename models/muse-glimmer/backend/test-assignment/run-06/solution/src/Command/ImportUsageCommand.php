<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import')]
class ImportUsageCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            return Command::FAILURE;
        }
        $handle = fopen($file, 'r');
        if (!$handle) return Command::FAILURE;
        $lineNum = 0;
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') continue;
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return Command::FAILURE;
            }
            $provider = $data['provider'];
            $reqId = $data['provider_request_id'];
            $existing = $this->em->find(UsageEvent::class, ['providerCode'=>$provider, 'providerRequestId'=>$reqId]);
            if ($existing) {
                continue; // idempotent
            }
            $event = new UsageEvent();
            $event->setProviderCode($provider);
            $event->setProviderRequestId($reqId);
            $event->setModelCode($data['model']);
            $event->setUser($data['user']);
            $event->setTs(new \DateTimeImmutable($data['ts']));
            $event->setTokensInput((int)$data['tokens_input']);
            $event->setTokensOutput((int)$data['tokens_output']);
            $event->setTokensCacheRead((int)$data['tokens_cache_read']);
            $event->setTokensCacheWrite((int)$data['tokens_cache_write']);
            $event->setCostUsd(null);
            $this->em->persist($event);
        }
        fclose($handle);
        $this->em->flush();
        return Command::SUCCESS;
    }
}

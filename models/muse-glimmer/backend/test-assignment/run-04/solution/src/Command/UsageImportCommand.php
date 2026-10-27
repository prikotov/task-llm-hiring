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
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('file');
        if (!is_file($path)) {
            $output->getErrorOutput()->writeln('File not found');
            return Command::FAILURE;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->getErrorOutput()->writeln('Invalid JSON line');
                return Command::FAILURE;
            }
            $provider = $data['provider'];
            $requestId = $data['provider_request_id'];
            $event = $this->em->getRepository(UsageEvent::class)->findOneBy([
                'providerCode' => $provider,
                'providerRequestId' => $requestId,
            ]);
            if ($event) {
                // deduplicate: ignore
                continue;
            }
            $event = new UsageEvent();
            $event->setProviderCode($provider);
            $event->setProviderRequestId($requestId);
            $event->setModelCode($data['model']);
            $event->setUser($data['user']);
            $event->setTs(new \DateTimeImmutable($data['ts']));
            $event->setTokensInput((int)$data['tokens_input']);
            $event->setTokensOutput((int)$data['tokens_output']);
            $event->setTokensCacheRead((int)$data['tokens_cache_read']);
            $event->setTokensCacheWrite((int)$data['tokens_cache_write']);
            $event->setIsRated(false);
            $event->setCostUsd(null);
            $this->em->persist($event);
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}

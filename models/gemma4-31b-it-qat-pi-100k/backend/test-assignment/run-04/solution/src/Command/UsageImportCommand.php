<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Usage;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Usage JSONL file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $handle = fopen($file, 'r');
        if (!$handle) {
            return Command::FAILURE;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') continue;

            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                fclose($handle);
                return Command::FAILURE;
            }

            $existing = $this->em->getRepository(Usage::class)->findOneBy([
                'providerCode' => $data['provider'],
                'providerRequestId' => $data['provider_request_id']
            ]);
            if ($existing) {
                continue;
            }

            $modelCode = $data['model'] ?? null;
            $model = null;
            if ($modelCode) {
                $provider = $this->em->getRepository(\App\Entity\Provider::class)->find($data['provider']);
                if ($provider) {
                    $model = $this->em->getRepository(Model::class)->findOneBy([
                        'provider' => $provider,
                        'code' => $modelCode
                    ]);
                }
            }

            $usage = new Usage();
            $usage->providerCode = $data['provider'];
            $usage->providerRequestId = $data['provider_request_id'];
            $usage->ts = new \DateTimeImmutable($data['ts']);
            $usage->userName = $data['user'];
            $usage->modelCode = $modelCode;
            if ($model) {
                $usage->model = $model;
            }
            $usage->tokensInput = $data['tokens_input'] ?? 0;
            $usage->tokensOutput = $data['tokens_output'] ?? 0;
            $usage->tokensCacheRead = $data['tokens_cache_read'] ?? 0;
            $usage->tokensCacheWrite = $data['tokens_cache_write'] ?? 0;
            
            $this->em->persist($usage);
        }

        fclose($handle);
        $this->em->flush();
        return Command::SUCCESS;
    }
}

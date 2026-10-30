<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Import usage events')]
final class UsageImportCommand extends Command
{
    public function __construct(private Connection $conn)
    {
        parent::__construct();
    }

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
        if ($handle === false) {
            return Command::FAILURE;
        }
        $this->conn->beginTransaction();
        try {
            $lineNum = 0;
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
                    $this->conn->rollBack();
                    fclose($handle);
                    return Command::FAILURE;
                }
                $this->upsertEvent($data);
            }
            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            $output->getErrorOutput()->writeln('Error: '.$e->getMessage());
            fclose($handle);
            return Command::FAILURE;
        }
        fclose($handle);
        return Command::SUCCESS;
    }

    private function upsertEvent(array $e): void
    {
        $sql = 'INSERT INTO usage_events (provider_code, provider_request_id, model_code, user_name, ts, tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost)
                VALUES (:provider, :request_id, :model, :user, :ts, :in, :out, :cr, :cw, NULL)
                ON CONFLICT (provider_code, provider_request_id) DO NOTHING';
        $this->conn->executeStatement($sql, [
            'provider' => $e['provider'],
            'request_id' => $e['provider_request_id'],
            'model' => $e['model'],
            'user' => $e['user'],
            'ts' => $e['ts'],
            'in' => $e['tokens_input'],
            'out' => $e['tokens_output'],
            'cr' => $e['tokens_cache_read'],
            'cw' => $e['tokens_cache_write'],
        ]);
    }
}

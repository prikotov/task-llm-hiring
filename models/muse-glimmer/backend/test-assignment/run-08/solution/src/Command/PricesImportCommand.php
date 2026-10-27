<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import prices')]
final class PricesImportCommand extends Command
{
    public function __construct(private Connection $conn)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('<error>File not found</error>', OutputInterface::VERBOSITY_NORMAL);
            return Command::FAILURE;
        }
        $content = file_get_contents($file);
        if ($content === false) {
            return Command::FAILURE;
        }
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $output->getErrorOutput()->writeln('Invalid JSON');
            return Command::FAILURE;
        }

        $this->conn->beginTransaction();
        try {
            foreach ($data['providers'] ?? [] as $p) {
                $this->upsertProvider($p);
            }
            foreach ($data['models'] ?? [] as $m) {
                $this->upsertModel($m);
            }
            foreach ($data['prices'] ?? [] as $pr) {
                $this->upsertPrice($pr);
            }
            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            $output->getErrorOutput()->writeln('Error: '.$e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function upsertProvider(array $p): void
    {
        $offPeak = $p['off_peak'] ?? null;
        $sql = 'INSERT INTO providers (code, name, off_peak_window_start, off_peak_window_end, off_peak_multiplier)
                VALUES (:code, :name, :ws, :we, :mult)
                ON CONFLICT (code) DO UPDATE SET
                    name = EXCLUDED.name,
                    off_peak_window_start = EXCLUDED.off_peak_window_start,
                    off_peak_window_end = EXCLUDED.off_peak_window_end,
                    off_peak_multiplier = EXCLUDED.off_peak_multiplier';
        $this->conn->executeStatement($sql, [
            'code' => $p['code'],
            'name' => $p['name'],
            'ws' => $offPeak['window_start'] ?? null,
            'we' => $offPeak['window_end'] ?? null,
            'mult' => $offPeak['multiplier'] ?? null,
        ]);
    }

    private function upsertModel(array $m): void
    {
        $sql = 'INSERT INTO models (provider_code, code)
                VALUES (:provider, :code)
                ON CONFLICT (provider_code, code) DO NOTHING';
        $this->conn->executeStatement($sql, [
            'provider' => $m['provider'],
            'code' => $m['code'],
        ]);
    }

    private function upsertPrice(array $pr): void
    {
        $sql = 'INSERT INTO prices (provider_code, model_code, valid_from, currency, unit, rates)
                VALUES (:provider, :model, :valid_from, :currency, :unit, :rates::jsonb)
                ON CONFLICT (provider_code, model_code, valid_from) DO NOTHING';
        $this->conn->executeStatement($sql, [
            'provider' => $pr['provider'],
            'model' => $pr['model'],
            'valid_from' => $pr['valid_from'],
            'currency' => $pr['currency'],
            'unit' => $pr['unit'],
            'rates' => json_encode($pr['rates']),
        ]);
    }
}

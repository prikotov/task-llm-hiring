<?php

declare(strict_types=1);

namespace App\Console;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import')]
final class ImportPricesCommand extends Command
{
    public function __construct(private Connection $db) { parent::__construct(); }
    protected function configure(): void { $this->addArgument('file', InputArgument::REQUIRED); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try { $data = json_decode((string) file_get_contents($input->getArgument('file')), true, 512, JSON_THROW_ON_ERROR); } catch (\Throwable $e) { $output->writeln('<error>'.$e->getMessage().'</error>'); return 1; }
        try {
            $this->db->beginTransaction();
            foreach ($data['providers'] ?? [] as $p) {
                $o = $p['off_peak'] ?? null;
                $this->db->executeStatement('INSERT INTO providers(code,name,window_start,window_end,multiplier) VALUES(?,?,?,?,?) ON CONFLICT(code) DO UPDATE SET name=EXCLUDED.name,window_start=EXCLUDED.window_start,window_end=EXCLUDED.window_end,multiplier=EXCLUDED.multiplier', [$p['code'],$p['name'],$o['window_start'] ?? null,$o['window_end'] ?? null,$o['multiplier'] ?? null]);
            }
            foreach ($data['models'] ?? [] as $m) $this->db->executeStatement('INSERT INTO models(provider_code,code) VALUES(?,?) ON CONFLICT(provider_code,code) DO NOTHING', [$m['provider'],$m['code']]);
            foreach ($data['prices'] ?? [] as $p) {
                $id=$this->db->fetchOne('SELECT id FROM models WHERE provider_code=? AND code=?',[$p['provider'],$p['model']]);
                if (!$id) throw new \RuntimeException('Unknown model '.$p['provider'].'/'.$p['model']);
                $this->db->executeStatement('INSERT INTO prices(model_id,valid_from,currency,unit,rates) VALUES(?,?,?,?,?::jsonb) ON CONFLICT(model_id,valid_from) DO UPDATE SET currency=EXCLUDED.currency,unit=EXCLUDED.unit,rates=EXCLUDED.rates',[$id,$p['valid_from'],$p['currency'],$p['unit'],json_encode($p['rates'],JSON_THROW_ON_ERROR)]);
            }
            $this->db->commit(); return 0;
        } catch (\Throwable $e) { if($this->db->isTransactionActive()) $this->db->rollBack(); $output->writeln('<error>'.$e->getMessage().'</error>'); return 1; }
    }
}

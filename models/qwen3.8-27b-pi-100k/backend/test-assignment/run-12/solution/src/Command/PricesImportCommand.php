<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\OffPeak;
use App\Infra\Input\CatalogParser;
use App\Infra\Input\FileReader;
use App\Infra\Input\PriceSheetParser;
use App\Repository\ModelRepository;
use App\Repository\ProviderRepository;
use App\Repository\PriceRowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProviderRepository $providers,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $priceRows,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:prices:import')
            ->setDescription('Import providers, models and price rows from a JSON file (idempotent upsert)')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $data = FileReader::readJson($file);
            $providers = CatalogParser::parseProviders($data['providers'] ?? []);
            $priceSheets = PriceSheetParser::parse($data['prices'] ?? []);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln("ERR: {$e->getMessage()}");
            return 1;
        }

        foreach ($providers as $code => $p) {
            $start = $p->offPeak !== null ? self::hmFromMin($p->offPeak->startMinutes) : null;
            $end = $p->offPeak !== null ? self::hmFromMin($p->offPeak->endMinutes) : null;
            $multiplier = $p->offPeak !== null ? $p->offPeak->multiplier : null;
            $this->providers->upsert($code, $p->name, $start, $end, $multiplier);
        }

        // модели — из секции `models` (п. `models` в prices.json) либо из `prices`
        $modelSet = [];
        foreach ($data['models'] ?? [] as $m) {
            $modelSet[$m['provider'].'|'.$m['code']] = [$m['provider'], $m['code']];
        }
        foreach ($priceSheets as $s) {
            $key = $s->provider.'|'.$s->model;
            if (!isset($modelSet[$key])) {
                $modelSet[$key] = [$s->provider, $s->model];
            }
        }
        foreach ($modelSet as [$provider, $code]) {
            $this->models->upsert($provider, $code);
        }

        foreach ($priceSheets as $s) {
            $this->priceRows->upsert($s->provider, $s->model, $s->validFrom, $s->unit, $s->rates);
        }

        $this->em->flush();

        $output->writeln("IMPORTED providers=".count($providers)." models=".count($modelSet)." prices=".count($priceSheets));
        return 0;
    }

    private static function hmFromMin(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}

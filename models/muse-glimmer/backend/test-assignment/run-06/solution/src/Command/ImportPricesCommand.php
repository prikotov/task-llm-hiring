<?php

namespace App\Command;

use App\Entity\Provider;
use App\Entity\Model;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import')]
class ImportPricesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('File not found', OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return Command::FAILURE;
        }
        foreach ($data['providers'] ?? [] as $p) {
            $prov = $this->em->find(Provider::class, $p['code']) ?? new Provider();
            $prov->setCode($p['code']);
            $prov->setName($p['name'] ?? '');
            $off = $p['off_peak'] ?? null;
            if ($off) {
                $prov->setOffPeakStart($off['window_start']);
                $prov->setOffPeakEnd($off['window_end']);
                $prov->setOffPeakMultiplier($off['multiplier'] ?? null);
            } else {
                $prov->setOffPeakStart(null);
                $prov->setOffPeakEnd(null);
                $prov->setOffPeakMultiplier(null);
            }
            $this->em->persist($prov);
        }
        foreach ($data['models'] ?? [] as $m) {
            $model = $this->em->find(Model::class, ['providerCode'=>$m['provider'],'code'=>$m['code']]) ?? new Model();
            $model->setProviderCode($m['provider']);
            $model->setCode($m['code']);
            $this->em->persist($model);
        }
        foreach ($data['prices'] ?? [] as $pr) {
            $validFrom = (new \DateTimeImmutable($pr['valid_from']))->format(\DateTimeInterface::ATOM);
            $price = $this->em->find(Price::class, ['providerCode'=>$pr['provider'],'modelCode'=>$pr['model'],'validFrom'=>$validFrom]) ?? new Price();
            $price->setProviderCode($pr['provider']);
            $price->setModelCode($pr['model']);
            $price->setValidFrom($validFrom);
            $price->setCurrency($pr['currency']);
            $price->setUnit($pr['unit']);
            $rates = $pr['rates'] ?? [];
            $price->setRateInput(isset($rates['input']) ? $rates['input'] : null);
            $price->setRateOutput(isset($rates['output']) ? $rates['output'] : null);
            $price->setRateCacheRead(isset($rates['cache_read']) ? $rates['cache_read'] : null);
            $price->setRateCacheWrite(isset($rates['cache_write']) ? $rates['cache_write'] : null);
            $price->setRateAny(isset($rates['any']) ? $rates['any'] : null);
            $this->em->persist($price);
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}

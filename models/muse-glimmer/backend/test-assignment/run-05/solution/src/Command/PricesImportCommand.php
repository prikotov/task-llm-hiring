<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Provider;
use App\Entity\Model;
use App\Entity\Price;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name:'app:prices:import', description:'Import prices')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em){ parent::__construct(); }
    protected function configure(): void { $this->addArgument('file', InputArgument::REQUIRED); }
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $file=$in->getArgument('file');
        if(!is_file($file)){ $out->getErrorOutput()->writeln('File not found'); return Command::FAILURE; }
        $data=json_decode(file_get_contents($file), true);
        if(json_last_error()!==JSON_ERROR_NONE){ return Command::FAILURE; }

        foreach($data['providers'] ?? [] as $p){
            $repo=$this->em->getRepository(Provider::class);
            $prov=$repo->find($p['code']);
            if(!$prov){ $prov=new Provider($p['code']); }
            $prov->setName($p['name']??'');
            $off=$p['off_peak']??null;
            if($off){
                $prov->setOffPeakWindowStart($off['window_start']);
                $prov->setOffPeakWindowEnd($off['window_end']);
                $prov->setOffPeakMultiplier($off['multiplier']);
            } else {
                $prov->setOffPeakWindowStart(null);
                $prov->setOffPeakWindowEnd(null);
                $prov->setOffPeakMultiplier(null);
            }
            $this->em->persist($prov);
        }
        foreach($data['models'] ?? [] as $m){
            $repo=$this->em->getRepository(Model::class);
            $model=$repo->findOneBy(['providerCode'=>$m['provider'],'code'=>$m['code']]);
            if(!$model){ $model=new Model($m['provider'],$m['code']); $this->em->persist($model); }
        }
        foreach($data['prices'] ?? [] as $pr){
            $valid=new \DateTimeImmutable($pr['valid_from']);
            $repo=$this->em->getRepository(Price::class);
            $price=$repo->findOneBy(['providerCode'=>$pr['provider'],'modelCode'=>$pr['model'],'validFrom'=>$valid]);
            if(!$price){ $price=new Price($pr['provider'],$pr['model'],$valid); }
            $price->setCurrency($pr['currency']);
            $price->setUnit($pr['unit']);
            $rates=$pr['rates'];
            $price->setRateInput($rates['input']??null);
            $price->setRateOutput($rates['output']??null);
            $price->setRateCacheRead($rates['cache_read']??null);
            $price->setRateCacheWrite($rates['cache_write']??null);
            $price->setRateAny($rates['any']??null);
            $this->em->persist($price);
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}

<?php
namespace App\Command;

use App\Entity\UsageEvent;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name:'app:billing:rate', description:'Rate events')]
class BillingRateCommand extends Command
{
    public function __construct(private EntityManagerInterface $em){ parent::__construct(); }
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $repo=$this->em->getRepository(UsageEvent::class);
        $events=$repo->createQueryBuilder('e')->where('e.rated = false')->getQuery()->getResult();
        $ratedCount=0;
        $unratedCount=0;
        foreach($events as $e){
            $price=$this->findPrice($e);
            if(!$price){
                $unratedCount++;
                $out->getErrorOutput()->writeln(sprintf('UNRATED %s/%s %s no_price', $e->getProviderCode(), $e->getProviderRequestId(), $e->getTs()->format('c')));
                continue;
            }
            $cost=$this->calcCost($e,$price);
            $provider=$this->em->getRepository(Provider::class)->find($e->getProviderCode());
            if($provider && $this->inOffPeak($provider,$e->getTs())){
                $multiplier=(string)$provider->getOffPeakMultiplier();
                $cost=$this->multiply($cost,$multiplier);
            }
            $costRounded=$this->roundHalfUp($cost,6);
            $e->setCostUsd($costRounded);
            $e->setRated(true);
            $ratedCount++;
        }
        $this->em->flush();
        $out->writeln(sprintf('RATED=%d UNRATED=%d',$ratedCount,$unratedCount));
        return Command::SUCCESS;
    }

    private function findPrice(UsageEvent $e): ?Price
    {
        $qb=$this->em->getRepository(Price::class)->createQueryBuilder('p')
            ->where('p.providerCode = :prov')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('prov',$e->getProviderCode())
            ->setParameter('model',$e->getModelCode())
            ->setParameter('ts',$e->getTs())
            ->orderBy('p.validFrom','DESC')
            ->setMaxResults(1);
        $price=$qb->getQuery()->getOneOrNullResult();
        return $price;
    }

    private function calcCost(UsageEvent $e, Price $p): string
    {
        $unit=$p->getUnit();
        $scale=16;
        if($unit==='per_1m_tokens_unified'){
            $total=$e->getTokensInput()+$e->getTokensOutput()+$e->getTokensCacheRead()+$e->getTokensCacheWrite();
            $rate=$p->getRateAny()??'0';
            $cost=bcmul((string)$total,$rate,$scale);
            $cost=bcdiv($cost,'1000000',$scale);
            return $cost;
        } else {
            $cost='0';
            $cost=bcadd($cost, bcdiv(bcmul((string)$e->getTokensInput(), $p->getRateInput()??'0',$scale),'1000000',$scale), $scale);
            $cost=bcadd($cost, bcdiv(bcmul((string)$e->getTokensOutput(), $p->getRateOutput()??'0',$scale),'1000000',$scale), $scale);
            $cost=bcadd($cost, bcdiv(bcmul((string)$e->getTokensCacheRead(), $p->getRateCacheRead()??'0',$scale),'1000000',$scale), $scale);
            $cost=bcadd($cost, bcdiv(bcmul((string)$e->getTokensCacheWrite(), $p->getRateCacheWrite()??'0',$scale),'1000000',$scale), $scale);
            return $cost;
        }
    }

    private function inOffPeak(Provider $p, \DateTimeImmutable $ts): bool
    {
        $start=$p->getOffPeakWindowStart();
        $end=$p->getOffPeakWindowEnd();
        if(!$start||!$end) return false;
        $time=$ts->setTimezone(new \DateTimeZone('UTC'))->format('H:i');
        $startMin=$this->toMinutes($start);
        $endMin=$this->toMinutes($end);
        $curMin=$this->toMinutes($time);
        if($startMin <= $endMin){
            return $curMin >= $startMin && $curMin < $endMin;
        } else {
            return $curMin >= $startMin || $curMin < $endMin;
        }
    }

    private function toMinutes(string $t): int
    {
        [$h,$m]=explode(':',$t);
        return intval($h)*60+intval($m);
    }

    private function multiply(string $cost, string $mult): string
    {
        return bcmul($cost,$mult,16);
    }

    private function roundHalfUp(string $num, int $precision): string
    {
        $factor=bcpow('10',$precision);
        $scaled=bcmul($num,$factor,18);
        // add 0.5
        $scaledPlus=bcadd($scaled,'0.5',0);
        // truncate
        $int=bcdiv($scaledPlus,'1',0);
        $rounded=bcdiv($int,$factor,$precision);
        return $rounded;
    }
}

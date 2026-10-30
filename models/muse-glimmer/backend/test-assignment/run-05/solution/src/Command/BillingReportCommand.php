<?php
namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name:'app:billing:report', description:'Report')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em){ parent::__construct(); }
    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED);
        $this->addOption('to', null, InputOption::VALUE_REQUIRED);
    }
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $from=$in->getOption('from');
        $to=$in->getOption('to');
        $qb=$this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->where('e.rated = true')
            ->andWhere('e.costUsd IS NOT NULL');
        if($from){
            $qb->andWhere('e.ts >= :from')
               ->setParameter('from', new \DateTimeImmutable($from));
        }
        if($to){
            $qb->andWhere('e.ts < :to')
               ->setParameter('to', new \DateTimeImmutable($to));
        }
        $events=$qb->getQuery()->getResult();
        $agg=[];
        $totalTokensIn=0;$totalTokensOut=0;$totalCacheRead=0;$totalCacheWrite=0;$totalCost='0';
        foreach($events as $e){
            $user=$e->getUser();
            $model=sprintf('%s/%s',$e->getProviderCode(), $e->getModelCode());
            $key=$user.'|'.$model;
            if(!isset($agg[$key])){
                $agg[$key]=[
                    'user'=>$user,
                    'model'=>$model,
                    'tokens_input'=>0,
                    'tokens_output'=>0,
                    'tokens_cache_read'=>0,
                    'tokens_cache_write'=>0,
                    'cost'=>'0',
                ];
            }
            $agg[$key]['tokens_input']+=$e->getTokensInput();
            $agg[$key]['tokens_output']+=$e->getTokensOutput();
            $agg[$key]['tokens_cache_read']+=$e->getTokensCacheRead();
            $agg[$key]['tokens_cache_write']+=$e->getTokensCacheWrite();
            $agg[$key]['cost']=bcadd($agg[$key]['cost'],$e->getCostUsd()??'0',6);
            $totalTokensIn+=$e->getTokensInput();
            $totalTokensOut+=$e->getTokensOutput();
            $totalCacheRead+=$e->getTokensCacheRead();
            $totalCacheWrite+=$e->getTokensCacheWrite();
            $totalCost=bcadd($totalCost,$e->getCostUsd()??'0',6);
        }
        $out->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        $rows=array_values($agg);
        usort($rows, fn($a,$b)=> $a['user']<=>$b['user'] ?: $a['model']<=>$b['model']);
        foreach($rows as $r){
            $cost=number_format((float)$r['cost'],6,'.','');
            $out->writeln(sprintf('%s,%s,%d,%d,%d,%d,%s',$r['user'],$r['model'],$r['tokens_input'],$r['tokens_output'],$r['tokens_cache_read'],$r['tokens_cache_write'],$cost));
        }
        $totalCostFmt=number_format((float)$totalCost,6,'.','');
        $out->writeln(sprintf('TOTAL,,%d,%d,%d,%d,%s',$totalTokensIn,$totalTokensOut,$totalCacheRead,$totalCacheWrite,$totalCostFmt));
        return Command::SUCCESS;
    }
}

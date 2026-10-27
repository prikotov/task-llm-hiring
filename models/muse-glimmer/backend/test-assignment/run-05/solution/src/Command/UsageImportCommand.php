<?php
namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name:'app:usage:import', description:'Import usage')]
class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em){ parent::__construct(); }
    protected function configure(): void { $this->addArgument('file', InputArgument::REQUIRED); }
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $file=$in->getArgument('file');
        if(!is_file($file)){ $out->getErrorOutput()->writeln('File not found'); return Command::FAILURE; }
        $handle=fopen($file,'r');
        if(!$handle){ return Command::FAILURE; }
        $repo=$this->em->getRepository(UsageEvent::class);
        while(($line=fgets($handle))!==false){
            $line=trim($line);
            if($line==='') continue;
            $data=json_decode($line, true);
            if(json_last_error()!==JSON_ERROR_NONE){ return Command::FAILURE; }
            $event=$repo->findOneBy(['providerCode'=>$data['provider'],'providerRequestId'=>$data['provider_request_id']]);
            if($event){ continue; } // idempotent
            $event=new UsageEvent($data['provider'],$data['provider_request_id']);
            $event->setModelCode($data['model']);
            $event->setUser($data['user']);
            $event->setTs(new \DateTimeImmutable($data['ts']));
            $event->setTokensInput((int)$data['tokens_input']);
            $event->setTokensOutput((int)$data['tokens_output']);
            $event->setTokensCacheRead((int)$data['tokens_cache_read']);
            $event->setTokensCacheWrite((int)$data['tokens_cache_write']);
            $event->setRated(false);
            $this->em->persist($event);
        }
        fclose($handle);
        $this->em->flush();
        return Command::SUCCESS;
    }
}

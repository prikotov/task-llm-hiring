<?php

declare(strict_types=1);
namespace App\Console;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
#[AsCommand(name: 'app:usage:import')]
final class ImportUsageCommand extends Command
{
 public function __construct(private Connection $db){parent::__construct();}
 protected function configure():void{$this->addArgument('file',InputArgument::REQUIRED);}
 protected function execute(InputInterface $in,OutputInterface $out):int
 {
  $f=@fopen($in->getArgument('file'),'r'); if(!$f){$out->writeln('<error>Cannot read file</error>');return 1;}
  try{$this->db->beginTransaction();$line=0;while(($raw=fgets($f))!==false){$line++; if(trim($raw)==='')continue;
   try{$e=json_decode($raw,true,512,JSON_THROW_ON_ERROR);$ts=new \DateTimeImmutable($e['ts']); if($ts->getOffset()!==0 || !str_ends_with($e['ts'],'Z'))throw new \RuntimeException('ts must be UTC Z');
    $tokens=[];foreach(['tokens_input','tokens_output','tokens_cache_read','tokens_cache_write'] as $k){$v=$e[$k];if(!is_int($v)||$v<0)throw new \RuntimeException("Invalid $k");$tokens[]=$v;}
    $this->db->executeStatement('INSERT INTO usage_events(provider_code,request_id,model_code,user_id,event_ts,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write) VALUES(?,?,?,?,?,?,?,?,?) ON CONFLICT(provider_code,request_id) DO NOTHING',[$e['provider'],$e['provider_request_id'],$e['model'],$e['user'],$ts->format('Y-m-d H:i:sP'),...$tokens]);
   }catch(\Throwable $x){throw new \RuntimeException("Invalid JSONL line $line: ".$x->getMessage(),0,$x);}}
   $this->db->commit();fclose($f);return 0;
  }catch(\Throwable $e){if($this->db->isTransactionActive())$this->db->rollBack();fclose($f);$out->writeln('<error>'.$e->getMessage().'</error>');return 1;}
 }
}

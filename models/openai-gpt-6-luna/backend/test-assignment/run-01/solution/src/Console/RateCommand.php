<?php
declare(strict_types=1);
namespace App\Console;
use App\Billing\Billing;use Doctrine\DBAL\Connection;use Symfony\Component\Console\Attribute\AsCommand;use Symfony\Component\Console\Command\Command;use Symfony\Component\Console\Input\InputInterface;use Symfony\Component\Console\Output\OutputInterface;
#[AsCommand(name:'app:billing:rate')]
final class RateCommand extends Command{public function __construct(private Connection $db,private Billing $billing){parent::__construct();}protected function execute(InputInterface $i,OutputInterface $o):int{$rated=0;$unrated=0;foreach($this->db->fetchAllAssociative('SELECT * FROM usage_events WHERE rated=FALSE ORDER BY id') as $e){$p=$this->billing->price($e);if(!$p){$unrated++;$o->getErrorOutput()->writeln('UNRATED '.$e['provider_code'].'/'.$e['request_id'].' '.$e['event_ts'].' no_price');continue;}$cost=$this->billing->calculate($e,$p,$p);$this->db->executeStatement('UPDATE usage_events SET rated=TRUE,cost=? WHERE id=?',[$cost,$e['id']]);$rated++;}$o->writeln("RATED=$rated UNRATED=$unrated");return 0;}}

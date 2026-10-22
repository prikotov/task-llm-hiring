<?php
declare(strict_types=1);
namespace App\Billing;
use Doctrine\DBAL\Connection;
final class Billing
{
 public function __construct(private Connection $db){}
 public function calculate(array $e,array $price,array $provider):string
 {
  $rates=json_decode($price['rates'],true);$sum='0';$keys=['input'=>'tokens_input','output'=>'tokens_output','cache_read'=>'tokens_cache_read','cache_write'=>'tokens_cache_write'];
  if($price['unit']==='per_1m_tokens_unified'){$tokens='0';foreach($keys as $key)$tokens=bcadd($tokens,(string)$e[$key],0);$sum=bcdiv(bcmul($tokens,$rates['any'],20),'1000000',20);}
  else foreach($keys as $key=>$col)$sum=bcadd($sum,bcdiv(bcmul((string)$e[$col],$rates[$key],20),'1000000',20),20);
  if($provider['window_start']!==null){$tod=(new \DateTimeImmutable($e['event_ts']))->format('H:i');$s=$provider['window_start'];$end=$provider['window_end'];$inside=$s<$end?($tod>=$s&&$tod<$end):($tod>=$s||$tod<$end);if($inside)$sum=bcmul($sum,$provider['multiplier'],20);}
  return bcadd($sum,'0.0000005',6);
 }
 public function price(array $e):?array
 {return $this->db->fetchAssociative('SELECT p.id,p.unit,p.rates::text AS rates,pr.window_start,pr.window_end,pr.multiplier FROM models m JOIN providers pr ON pr.code=m.provider_code JOIN prices p ON p.model_id=m.id WHERE m.provider_code=? AND m.code=? AND p.valid_from<=? ORDER BY p.valid_from DESC LIMIT 1',[$e['provider_code'],$e['model_code'],$e['event_ts']])?:null;}
}

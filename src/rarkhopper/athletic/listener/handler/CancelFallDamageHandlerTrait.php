<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait CancelFallDamageHandlerTrait{
	public function onDamage(EntityDamageEvent $ev):void{
		if($ev->getCause() !== EntityDamageEvent::CAUSE_FALL) return;
		$entity = $ev->getEntity();
		
		if(!$entity instanceof Player) return;
		if(AthleticPlayerMap::getInstance()->get($entity)->hasJumpedFlags()){
			$ev->cancel();
		}
	}
}
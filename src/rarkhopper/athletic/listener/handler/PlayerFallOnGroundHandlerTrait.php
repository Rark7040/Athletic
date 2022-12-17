<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use rarkhopper\athletic\event\PlayerFallOnGroundEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait PlayerFallOnGroundHandlerTrait{
	public function onFall(PlayerFallOnGroundEvent $ev):void{
		$pure_player = $ev->getPlayer();
		AthleticPlayerMap::getInstance()->get($pure_player)->resetJumpAttributes();
	}
}
<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use rarkhopper\athletic\event\AthleticPlayerFallEvent;

trait PlayerFallOnGroundHandlerTrait{
	public function onFall(AthleticPlayerFallEvent $ev):void{
		$ev->getAthleticPlayer()->resetJumpAttributes();
	}
}
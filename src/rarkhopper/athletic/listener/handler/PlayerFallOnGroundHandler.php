<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use rarkhopper\athletic\attribute\AttributesMap;
use rarkhopper\athletic\event\PlayerFallOnGroundEvent;

trait PlayerFallOnGroundHandler{
	public function onFall(PlayerFallOnGroundEvent $ev):void{
		$player = $ev->getPlayer();
		$attr = AttributesMap::getInstance()->get($player);
		$attr->isJumping = false;
		$attr->isBlockJumping = false;
	}
}
<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use rarkhopper\athletic\event\PlayerFallOnGroundEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait PlayerFallOnGroundHandlerTrait{
	public function onFall(PlayerFallOnGroundEvent $ev):void{
		$pure_player = $ev->getPlayer();
		$player = AthleticPlayerMap::getInstance()->get($pure_player);
		$attr = $player->getAttribute();
		$attr->isJumping = false;
		$attr->isBlockJumping = false;
		$attr->isDoubleJumped = false;
		$attr->isBlockJumped = false;
	}
}
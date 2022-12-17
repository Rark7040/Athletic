<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;


trait DoubleJumpHandlerTrait{
	public function onJump(PlayerJumpEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		if($player->getAttribute()->isDoubleJumped) return;
		$player->setCanDoubleJump();
	}
	
	public function onFly(PlayerToggleFlightEvent $ev):void{
		$pure_player = $ev->getPlayer();
		$player = AthleticPlayerMap::getInstance()->get($pure_player);
		$attr = $player->getAttribute();
		
		if(!$player->canAthleticAction()) return;
		$pure_player->setAllowFlight(false);
		
		if(!$ev->isFlying()) return;
		if(!$attr->isJumping and !$attr->isBlockJumping) return;
		$player->doubleJump($attr->isBlockJumping);
		
		if(!$attr->isDoubleJumped and $attr->isBlockJumped){
			$player->setCanDoubleJump();
		}
	}
}
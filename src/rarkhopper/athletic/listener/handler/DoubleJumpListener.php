<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;

/**
 * @internal
 */
class DoubleJumpListener implements Listener{
	public function onJump(PlayerJumpEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		if($player->getAttribute()->isDoubleJumped) return;
		$player->setAbleDoubleJump();
	}
	
	public function onFly(PlayerToggleFlightEvent $ev):void{
		$athleticPlayer = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		$attr = $athleticPlayer->getAttribute();
		
		if(!$athleticPlayer->canAthleticAction()) return;
		$athleticPlayer->getPlayer()->setAllowFlight(false);
		$athleticPlayer->getPlayer()->setFlying(false);
		$ev->cancel();
		
		if(!$ev->isFlying()) return;
		if(!$attr->isJumping and !$attr->isBlockJumping) return;
		if($attr->isBlockJumping){
			$athleticPlayer->blockJump();
			
		}else{
			$athleticPlayer->doubleJump();
		}
		
		if(!$attr->isDoubleJumped and $attr->isBlockJumped){
			$athleticPlayer->setAbleDoubleJump();
		}
	}
}
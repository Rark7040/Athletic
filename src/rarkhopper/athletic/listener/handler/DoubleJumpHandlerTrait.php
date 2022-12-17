<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\event\PlayerDoubleJumpEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;


trait DoubleJumpHandlerTrait{
	public function onJump(PlayerJumpEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask( //ブロック引っかかり対策
			new ClosureTask(fn() => $player->setCanDoubleJump()), 3
		);
	}
	
	public function onFly(PlayerToggleFlightEvent $ev):void{
		$pure_player = $ev->getPlayer();
		$player = AthleticPlayerMap::getInstance()->get($pure_player);
		$attr = $player->getAttribute();
		
		if(!$player->canAthleticAction()) return;
		if(!$ev->isFlying()) return;
		if(!$attr->allowAthleticAction or (!$attr->isJumping and !$attr->isBlockJumping)) return;
		$pure_player->setAllowFlight(false);
		(new PlayerDoubleJumpEvent($pure_player, $attr->isBlockJumping))->call();
		$player->doubleJump();
		
		if($attr->isBlockJumping){
			$attr->isBlockJumped = true;
			
			if($attr->isDoubleJumped) return;
			$player->setCanDoubleJump();
			
		}else{
			$attr->isDoubleJumped = true;
		}
	}
}
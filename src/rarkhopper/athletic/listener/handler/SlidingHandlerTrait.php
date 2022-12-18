<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\player\AthleticPlayer;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait SlidingHandlerTrait{
	public function onSneak(PlayerToggleSneakEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		$before = $player->getPure()->isSneaking();
		$after = $ev->isSneaking();
		
		if(!$before and $after){
			$this->onToggleOn($player);
		}
	}
	
	private function onToggleOn(AthleticPlayer $player):void{
		$pure = $player->getPure();
		
		if(!$pure->isSprinting() or $pure->isOnGround() or $player->getAttribute()->isDoubleJumped) return;
		$player->sliding();
	}
	
	public function onToggleSwim(PlayerToggleSwimEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		$attr = $player->getAttribute();
		
		if($attr->keepSliding){
			//頭上のブロックがsolidではないかつ、水中ではないのでkeep終了
			if($ev->isSwimming()) return;
			$attr->keepSliding = false;
			
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $player->cancelSneak()), 1
			);
			return;
		}
		
		if($attr->isSliding){
			$ev->cancel();
		}
	}
}
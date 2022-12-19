<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait SlidingHandlerTrait{
	public function onToggleSneak(PlayerToggleSneakEvent $ev):void{
		$athleticPlayer = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$athleticPlayer->canAthleticAction()) return;
		$before = $athleticPlayer->getPlayer()->isSneaking();
		$after = $ev->isSneaking();
		
		if(!$before and $after){
			$athleticPlayer->onSneak();
		}
	}
	
	public function onToggleSwim(PlayerToggleSwimEvent $ev):void{
		$athleticPlayer = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		$player = $athleticPlayer->getPlayer();
		$attr = $athleticPlayer->getAttribute();
		$topBlock = $player->getWorld()->getBlock($player->getPosition()->add(0, 1, 0));
		
		if($attr->keepSliding and !$topBlock->isSolid()){
			//頭上のブロックがsolidではないかつ、水中ではないのでkeep終了
			if($ev->isSwimming()) return;
			$attr->keepSliding = false;
			
			//数ティック送らせないと水を設置する座標がずれる
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $athleticPlayer->cancelSneak()), 2
			);
			return;
		}
		
		if($attr->isSliding){
			$ev->cancel();
		}
	}
}
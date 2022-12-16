<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\action\AthleticActions;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\attribute\AttributesMap;


trait DoubleJumpHandler{
	public function onJump(PlayerJumpEvent $ev):void{
		$player = $ev->getPlayer();
		$attr = AttributesMap::getInstance()->get($player);
		$gameMode = $player->getGamemode();
		
		if(!AthleticActions::validateGameMode($gameMode)) return;
		if(!$attr->canDoubleJump) return;
		$attr->isJumping = true;
		$player->setAllowFlight(true);
	}
	
	public function onFly(PlayerToggleFlightEvent $ev):void{
		$player = $ev->getPlayer();
		$attr = AttributesMap::getInstance()->get($player);
		
		if(!AthleticActions::validateGameMode($player->getGamemode())) return;
		if(!$ev->isFlying()) return;
		if(!$attr->canDoubleJump or (!$attr->isJumping and !$attr->isBlockJumping)) return;
		$player->setAllowFlight(false);
		$attr->isJumping = false;
		AthleticActions::doubleJump($player);
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(function() use($player){
				$player->setFlying(false);
				$player->setAllowFlight(false);
			}),
			1
		);
	}
}
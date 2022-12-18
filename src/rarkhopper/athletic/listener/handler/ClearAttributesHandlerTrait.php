<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerQuitEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait ClearAttributesHandlerTrait{
	public function onQuit(PlayerQuitEvent $ev):void{
		AthleticPlayerMap::getInstance()->remove($ev->getPlayer());
	}
	
	public function onToggleGameMode(PlayerGameModeChangeEvent $ev):void{
		AthleticPlayerMap::getInstance()->remove($ev->getPlayer());
	}
}
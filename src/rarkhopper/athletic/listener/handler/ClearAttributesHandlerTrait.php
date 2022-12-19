<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait ClearAttributesHandlerTrait{
	public function onQuit(PlayerQuitEvent $ev):void{
		$this->removeAthleticPlayer($ev->getPlayer());
	}
	
	public function onToggleGameMode(PlayerGameModeChangeEvent $ev):void{
		$this->removeAthleticPlayer($ev->getPlayer());
	}
	
	/**
	 * @param Player $player
	 * @return void
	 *
	 * not handler
	 */
	private function removeAthleticPlayer(Player $player):void{
		$gameMode = $player->getGameMode();
		AthleticPlayerMap::getInstance()->remove($player);
		
		if(GameMode::SURVIVAL()->equals($gameMode) or GameMode::ADVENTURE()->equals($gameMode)){
			$player->setAllowFlight(false);
			$player->setFlying(false);
		}
	}
}
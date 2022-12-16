<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use rarkhopper\athletic\action\AthleticActions;

class UnsetFlyingTask extends Task{
	public function onRun():void{
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if(!AthleticActions::validateGameMode($player->getGamemode())) return;
			$player->setFlying(false);
		}
	}
}
<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use rarkhopper\athletic\player\AthleticPlayerMap;

class EachPlayersTask extends Task{
	use BlockJumpTrait;
	use UpdateOnGroundAttributeTrait;

	public function onRun():void{
		foreach(Server::getInstance()->getOnlinePlayers() as $pure){
			$player = AthleticPlayerMap::getInstance()->get($pure);
			$this->checkBlockJump($player);
			$this->updateOnGroundAttr($player);
		}
	}
}
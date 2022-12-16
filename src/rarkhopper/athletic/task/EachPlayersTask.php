<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use rarkhopper\athletic\player\AthleticPlayerMap;

class EachPlayersTask extends Task{
	use BlockJumpTrait;
	use UpdateOnGroundAttributeTrait;
	use UnsetFlyingTrait;


	public function onRun():void{
		foreach(Server::getInstance()->getOnlinePlayers() as $pure){
			$player = AthleticPlayerMap::getInstance()->get($pure);
			
			$this->updateOnGroundAttr($player);
			$this->checkBlockJump($player);
			$this->unsetFlying($player);
		}
	}
}
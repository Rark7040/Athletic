<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use rarkhopper\athletic\player\AthleticPlayer;

trait UnsetFlyingTrait{
	private function unsetFlying(AthleticPlayer $player):void{
		if(!$player->validateGameMode()) return;
		if(!$player->getPure()->isFlying()) return;
		$player->getPure()->setFlying(false);
	}
}
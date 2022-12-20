<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\event\Listener;
use rarkhopper\athletic\event\AthleticPlayerHitGroundEvent;

/**
 * @internal
 */
class PlayerFallOnGroundListener implements Listener{
	public function onHitGround(AthleticPlayerHitGroundEvent $ev):void{
		$ev->getAthleticPlayer()->resetJumpAttributes();
	}
}
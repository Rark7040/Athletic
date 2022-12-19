<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use rarkhopper\athletic\event\AthleticPlayerHitGroundEvent;
use rarkhopper\athletic\player\AthleticPlayer;

/**
 * @internal
 */
trait UpdateOnGroundAttributeTrait{
	private function updateOnGroundAttr(AthleticPlayer $athleticPlayer):void{
		$attr = $athleticPlayer->getAttribute();
		
		if($athleticPlayer->getPlayer()->isOnGround()){
			if(!$attr->isOnGround){
				(new AthleticPlayerHitGroundEvent($athleticPlayer))->call();
			}
			$attr->isOnGround = true;
			
		}else{
			$attr->isOnGround = false;
		}
	}
}
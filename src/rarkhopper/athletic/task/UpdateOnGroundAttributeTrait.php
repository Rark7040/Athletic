<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use rarkhopper\athletic\event\AthleticPlayerFallEvent;
use rarkhopper\athletic\player\AthleticPlayer;

trait UpdateOnGroundAttributeTrait{
	private function updateOnGroundAttr(AthleticPlayer $player):void{
		$attr = $player->getAttribute();
		
		if($player->getPure()->isOnGround()){
			if(!$attr->isOnGround){
				(new AthleticPlayerFallEvent($player))->call();
			}
			$attr->isOnGround = true;
			
		}else{
			$attr->isOnGround = false;
		}
	}
}
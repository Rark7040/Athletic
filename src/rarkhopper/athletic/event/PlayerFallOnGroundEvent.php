<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerFallOnGroundEvent extends PlayerEvent{
	public function __construct(Player $player){
		$this->player = $player;
	}
}
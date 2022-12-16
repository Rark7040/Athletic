<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerDoubleJumpEvent extends PlayerEvent{
	protected bool $isBlockJump;
	
	public function __construct(Player $player, bool $isBlockJump){
		$this->player = $player;
		$this->isBlockJump = $isBlockJump;
	}
	
	public function isBlockJump():bool{
		return $this->isBlockJump;
	}
}
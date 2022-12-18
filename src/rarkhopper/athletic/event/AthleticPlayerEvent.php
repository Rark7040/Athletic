<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\Event;
use rarkhopper\athletic\player\AthleticPlayer;

abstract class AthleticPlayerEvent extends Event{
	protected AthleticPlayer $player;
	
	public function __construct(AthleticPlayer $player){
		$this->player = $player;
	}
	
	/**
	 * @return AthleticPlayer
	 */
	public function getAthleticPlayer():AthleticPlayer{
		return $this->player;
	}
}
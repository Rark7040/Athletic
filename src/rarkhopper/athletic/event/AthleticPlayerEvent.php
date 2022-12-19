<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\Event;
use rarkhopper\athletic\player\AthleticPlayer;

abstract class AthleticPlayerEvent extends Event{
	protected AthleticPlayer $athleticPlayer;
	
	public function __construct(AthleticPlayer $athleticPlayer){
		$this->athleticPlayer = $athleticPlayer;
	}
	
	/**
	 * @return AthleticPlayer
	 */
	public function getAthleticPlayer():AthleticPlayer{
		return $this->athleticPlayer;
	}
}
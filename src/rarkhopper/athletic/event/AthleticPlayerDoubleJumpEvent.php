<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use rarkhopper\athletic\player\AthleticPlayer;

class AthleticPlayerDoubleJumpEvent extends AthleticPlayerEvent implements Cancellable{
	use CancellableTrait;
	protected bool $isBlockJump;
	
	public function __construct(AthleticPlayer $player, bool $isBlockJump){
		parent::__construct($player);
		$this->isBlockJump = $isBlockJump;
	}
	
	/**
	 * @return bool
	 */
	public function isBlockJump():bool{
		return $this->isBlockJump;
	}
}
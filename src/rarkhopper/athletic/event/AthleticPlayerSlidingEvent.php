<?php
declare(strict_types=1);

namespace rarkhopper\athletic\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class AthleticPlayerSlidingEvent extends AthleticPlayerEvent implements Cancellable{
	use CancellableTrait;
}
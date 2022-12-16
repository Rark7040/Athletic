<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener;

use pocketmine\event\Listener;
use rarkhopper\athletic\listener\handler\BlockJumpHandlerTrait;
use rarkhopper\athletic\listener\handler\DoubleJumpHandlerTrait;
use rarkhopper\athletic\listener\handler\PlayerFallOnGroundHandlerTrait;

class EventListener implements Listener{
	use DoubleJumpHandlerTrait;
	use PlayerFallOnGroundHandlerTrait;
	use BlockJumpHandlerTrait;
}
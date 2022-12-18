<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener;

use pocketmine\event\Listener;
use rarkhopper\athletic\listener\handler\DoubleJumpHandlerTrait;
use rarkhopper\athletic\listener\handler\PlayerFallOnGroundHandlerTrait;
use rarkhopper\athletic\listener\handler\SlidingHandlerTrait;

class EventListener implements Listener{
	use DoubleJumpHandlerTrait;
	use PlayerFallOnGroundHandlerTrait;
	use SlidingHandlerTrait;
}
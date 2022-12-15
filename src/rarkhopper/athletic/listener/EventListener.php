<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener;

use pocketmine\event\Listener;
use rarkhopper\athletic\listener\handler\DoubleJumpHandler;

class EventListener implements Listener{
	use DoubleJumpHandler;
}
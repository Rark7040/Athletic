<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener;

use pocketmine\event\Listener;
use rarkhopper\athletic\listener\handler\CancelFallDamageHandler;
use rarkhopper\athletic\listener\handler\ClearAttributesHandler;
use rarkhopper\athletic\listener\handler\DoubleJumpHandler;
use rarkhopper\athletic\listener\handler\PlayerFallOnGroundHandler;
use rarkhopper\athletic\listener\handler\SlidingHandler;

/**
 * @internal
 */
class AthleticEventHandlers{
	/**
	 * @var array<Listener>
	 */
	private array $handlers = [];
	
	public function __construct(){
		$this->init();
	}
	
	private function init():void{
		$this->registerHandler(new CancelFallDamageHandler);
		$this->registerHandler(new ClearAttributesHandler);
		$this->registerHandler(new DoubleJumpHandler);
		$this->registerHandler(new PlayerFallOnGroundHandler);
		$this->registerHandler(new SlidingHandler);
	}
	
	private function registerHandler(Listener $handler):void{
		$this->handlers[] = $handler;
	}
	
	/**
	 * @return array<Listener>
	 */
	public function getHandlers():array{
		return $this->handlers;
	}
}
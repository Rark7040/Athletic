<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener;

use pocketmine\event\Listener;
use rarkhopper\athletic\listener\handler\CancelFallDamageListener;
use rarkhopper\athletic\listener\handler\ClearAttributesListener;
use rarkhopper\athletic\listener\handler\DoubleJumpListener;
use rarkhopper\athletic\listener\handler\PlayerFallOnGroundListener;
use rarkhopper\athletic\listener\handler\SlidingListener;

/**
 * @internal
 */
class AthleticEventListeners{
	/**
	 * @var array<Listener>
	 */
	private array $listeners = [];
	
	public function __construct(){
		$this->init();
	}
	
	private function init():void{
		$this->registerListener(new CancelFallDamageListener);
		$this->registerListener(new ClearAttributesListener);
		$this->registerListener(new DoubleJumpListener);
		$this->registerListener(new PlayerFallOnGroundListener);
		$this->registerListener(new SlidingListener);
	}
	
	private function registerListener(Listener $handler):void{
		$this->listeners[] = $handler;
	}
	
	/**
	 * @return array<Listener>
	 */
	public function getListeners():array{
		return $this->listeners;
	}
}
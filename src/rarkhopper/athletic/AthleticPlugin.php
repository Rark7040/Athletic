<?php
declare(strict_types=1);

namespace rarkhopper\athletic;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rarkhopper\athletic\listener\EventListener;
use rarkhopper\athletic\task\BlockJumpTask;
use rarkhopper\athletic\task\UnsetFlyingTask;

class AthleticPlugin extends PluginBase{
	private static TaskScheduler $taskScheduler;
	
	protected function onEnable():void{
		self::$taskScheduler = $this->getScheduler();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);
		$this->getScheduler()->scheduleRepeatingTask(new BlockJumpTask, 1);
		$this->getScheduler()->scheduleRepeatingTask(new UnsetFlyingTask, 1);
	}
	
	public static function getTaskScheduler():TaskScheduler{
		return self::$taskScheduler;
	}
}
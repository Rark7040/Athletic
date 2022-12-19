<?php
declare(strict_types=1);

namespace rarkhopper\athletic;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rarkhopper\athletic\listener\AthleticEventHandlers;
use rarkhopper\athletic\task\EachPlayersTask;

class AthleticPlugin extends PluginBase{
	private static TaskScheduler $taskScheduler;
	
	protected function onEnable():void{
		self::$taskScheduler = $this->getScheduler();
		$this->getScheduler()->scheduleRepeatingTask(new EachPlayersTask, 1);
		
		foreach((new AthleticEventHandlers())->getHandlers() as $handler){
			$this->getServer()->getPluginManager()->registerEvents($handler, $this);
		}
	}
	
	public static function getTaskScheduler():TaskScheduler{
		return self::$taskScheduler;
	}
}
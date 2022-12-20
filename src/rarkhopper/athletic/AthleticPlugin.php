<?php
declare(strict_types=1);

namespace rarkhopper\athletic;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rarkhopper\athletic\listener\AthleticEventListeners;
use rarkhopper\athletic\task\EachPlayersTask;

class AthleticPlugin extends PluginBase{
	private static TaskScheduler $taskScheduler;
	
	protected function onEnable():void{
		self::$taskScheduler = $this->getScheduler();
		$this->getScheduler()->scheduleRepeatingTask(new EachPlayersTask, 1);
		
		foreach((new AthleticEventListeners())->getListeners() as $listener){
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}
	
	public static function getTaskScheduler():TaskScheduler{
		return self::$taskScheduler;
	}
}
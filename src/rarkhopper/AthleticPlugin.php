<?php
declare(strict_types=1);

use pocketmine\plugin\PluginBase;

class AthleticPlugin extends PluginBase{
	protected function onEnable():void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);
	}
}
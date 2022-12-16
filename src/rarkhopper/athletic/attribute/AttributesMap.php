<?php
declare(strict_types=1);

namespace rarkhopper\athletic\attribute;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class AttributesMap{
	use SingletonTrait;
	
	/**
	 * @var array<string, PlayerAthleticAttribute>
	 */
	private array $attrMap = [];
	
	public function get(Player $player):PlayerAthleticAttribute{
		$this->attrMap[$player->getName()] ??= new PlayerAthleticAttribute;
		return $this->attrMap[$player->getName()];
	}
	
	public function clear(Player $player):void{
		unset($this->attrMap[$player->getName()]);
	}
}
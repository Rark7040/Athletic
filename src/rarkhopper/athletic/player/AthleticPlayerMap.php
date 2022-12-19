<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class AthleticPlayerMap{
	use SingletonTrait;
	
	/**
	 * @var array<string, AthleticPlayer>
	 */
	private array $playerMap = [];
	
	public function get(Player $player):AthleticPlayer{
		$this->playerMap[$player->getName()] ??= new AthleticPlayer($player);
		return $this->playerMap[$player->getName()];
	}
	
	public function remove(Player $player):void{
		unset($this->playerMap[$player->getName()]);
	}
}
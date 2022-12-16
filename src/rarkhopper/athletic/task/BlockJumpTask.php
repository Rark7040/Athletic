<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use rarkhopper\athletic\action\AthleticActions;
use rarkhopper\athletic\attribute\AttributesMap;
use rarkhopper\athletic\event\PlayerFallOnGroundEvent;

class BlockJumpTask extends Task{
	public function onRun():void{
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$attr = AttributesMap::getInstance()->get($player);
			$gameMode = $player->getGamemode();
			
			if(!AthleticActions::validateGameMode($gameMode)) continue;
			$this->updateOnGroundAttr($player);
			
			if(!$attr->allowAthleticAction or $attr->isBlockJumping) continue;
			if(!$this->isCollidedBlock($player) or $attr->isOnGround) continue;
			AthleticActions::setCanBlockJump($player);
		}
	}
	
	private function updateOnGroundAttr(Player $player):void{
		$attr = AttributesMap::getInstance()->get($player);
		
		if($player->isOnGround()){
			if(!$attr->isOnGround){
				(new PlayerFallOnGroundEvent($player))->call();
			}
			$attr->isOnGround = true;
			
		}else{
			$attr->isOnGround = false;
		}
	}
	
	private function isCollidedBlock(Entity $entity):bool{
		$world = $entity->getWorld();
		$v = $entity->getPosition()->asVector3()->floor();
		/**
		 * @var array<Block> $list
		 */
		$list = [
			$world->getBlock($v->add(1, 0, 1)),
			$world->getBlock($v->add(-1, 0, 1)),
			$world->getBlock($v->add(1, 0, -1)),
			$world->getBlock($v->add(-1, 0, -1))
		];
		foreach($list as $block){
			foreach($block->getCollisionBoxes() as $bb){
				if($bb->expandedCopy(0.3, 0.0, 0.3)->intersectsWith($entity->getBoundingBox())){
					return true;
				}
			}
		}
		return false;
	}
}
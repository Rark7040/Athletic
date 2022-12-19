<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use rarkhopper\athletic\player\AthleticPlayer;

trait BlockJumpTrait{
	private function checkBlockJump(AthleticPlayer $athleticPlayer):void{
		$attr = $athleticPlayer->getAttribute();
		
		if(!$athleticPlayer->canAthleticAction()) return;
		if($attr->isBlockJumped) return;
		if(!$this->isMidAir($athleticPlayer->getPlayer()) or $attr->isOnGround) return;
		if(!$this->isCollidedBlockOutside($athleticPlayer->getPlayer())) return;
		$athleticPlayer->setAbleBlockJump();
	}
	
	private function isMidAir(Entity $entity):bool{
		$world = $entity->getWorld();
		$v = $entity->getPosition()->asVector3()->floor();
		return !$world->getBlock($v->add(0, 1, 0))->isSolid();
	}
	
	private function isCollidedBlockOutside(Entity $entity):bool{
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
				if(!$bb->expandedCopy(0.5, 0.0, 0.5)->intersectsWith($entity->getBoundingBox())) continue;
				return true;
			}
		}
		return false;
	}
}
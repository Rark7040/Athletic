<?php
declare(strict_types=1);

namespace rarkhopper\athletic\task;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use rarkhopper\athletic\player\AthleticPlayer;

trait BlockJumpTrait{
	private function checkBlockJump(AthleticPlayer $player):void{
		$attr = $player->getAttribute();
		
		if(!$player->validateGameMode()) return;
		if(!$attr->allowAthleticAction or $attr->isBlockJumping) return;
		if(!$this->isCollidedBlock($player->getPure()) or $attr->isOnGround) return;
		if(!$this->isOnAir($player->getPure())) return;
		$player->setCanBlockJump();
	}
	
	private function isOnAir(Entity $entity):bool{
		$world = $entity->getWorld();
		$v = $entity->getPosition()->asVector3()->floor();
		$block = $world->getBlock($v->add(0, 1, 0));
		
		return !$block->isSolid();
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
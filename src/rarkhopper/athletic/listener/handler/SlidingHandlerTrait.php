<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\player\AthleticPlayer;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait SlidingHandlerTrait{
	public function onSneak(PlayerToggleSneakEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		$before = $player->getPure()->isSneaking();
		$after = $ev->isSneaking();
		
		if(!$before and $after){
			$this->onToggleOn($player);
		}
	}
	
	private function onToggleOn(AthleticPlayer $player):void{
		$pure = $player->getPure();
		
		if(!$pure->isSprinting() or $pure->isOnGround() or $player->getAttribute()->isDoubleJumped) return;
		$direction = $pure->getDirectionPlane()->multiply(0.7);
		$motion = new Vector3($direction->x, -0.4, $direction->y);
		$pure->setMotion($motion);
		$player->getAttribute()->isSliding = true;
		$sound_pk = LevelSoundEventPacket::nonActorSound(
			LevelSoundEvent::ARMOR_EQUIP_LEATHER,
			$pure->getPosition(),
			false,
			0
		);
		$pure->getWorld()->broadcastPacketToViewers($pure->getPosition(), $sound_pk);
		$pure->setSwimming();
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $this->stopSliding($player)), 15
		);
	}
	
	private function stopSliding(AthleticPlayer $player):void{
		if(!$player->getAttribute()->isSliding) return;
		$pure = $player->getPure();
		$topBlock = $pure->getWorld()->getBlock($pure->getPosition()->add(0, 1, 0));
		$player->getAttribute()->isSliding = false;
		
		if($topBlock->isSolid()){
			$player->getAttribute()->keepSliding = true;
			
		}else{
			$pure->setSwimming(false);
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $this->cancelSneak($pure)), 1
			);
		}
	}
	
	public function onToggleSwim(PlayerToggleSwimEvent $ev):void{
		$pure = $ev->getPlayer();
		$attr = AthleticPlayerMap::getInstance()->get($pure)->getAttribute();
		
		if($attr->keepSliding){
			if($ev->isSwimming()) return;
			$attr->keepSliding = false;
			
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $this->cancelSneak($pure)), 1
			);
			return;
		}
		
		if(!$attr->isSliding) return;
		$pure->setSwimming();
		$ev->cancel();
	}
	
	private function cancelSneak(Player $player):void{
		if(!$player->isOnline()) return;
		$player->toggleSneak(false);
		$vec = BlockPosition::fromVector3($player->getPosition()->add(0, 0, 0));
		$player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
			$vec,
			RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::WATER()->getFullId()),
			UpdateBlockPacket::FLAG_NETWORK,
			UpdateBlockPacket::DATA_LAYER_LIQUID
		));
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
				$vec,
				RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::AIR()->getFullId()),
				UpdateBlockPacket::FLAG_NETWORK,
				UpdateBlockPacket::DATA_LAYER_LIQUID
			))), 1
		);
	}
}
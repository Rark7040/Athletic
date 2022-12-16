<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\player\AthleticPlayer;
use rarkhopper\athletic\player\AthleticPlayerMap;
use ReflectionClass;

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
		$metadata = clone $pure->getNetworkProperties();
		$metadata->setGenericFlag(EntityMetadataFlags::SWIMMING, true);
		$actor_data_pk = SetActorDataPacket::create(
			$pure->getId(),
			$metadata->getAll(),
			new PropertySyncData([], []),
			1
		);
		$pure->getWorld()->broadcastPacketToViewers($pure->getPosition(), $actor_data_pk);
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
		
		$ref = new ReflectionClass($pure);
		$setSize = $ref->getMethod('setSize');
		$getInitialSizeInfo = $ref->getMethod('getInitialSizeInfo');
		$networkPropertiesDirty = $ref->getProperty('networkPropertiesDirty');
		
		$setSize->setAccessible(true);
		$getInitialSizeInfo->setAccessible(true);
		$networkPropertiesDirty->setAccessible(true);
		
		$size = $getInitialSizeInfo->invoke($pure);
		$width = $size->getWidth();
		$setSize->invoke($pure, (new EntitySizeInfo($width, $width, $width * 0.9))->scale($pure->getScale()));
		$networkPropertiesDirty->setValue($pure,true);

		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $this->stopSliding($player)), 15
		);
	}
	
	private function stopSliding(AthleticPlayer $player):void{
		if(!$player->getAttribute()->isSliding) return;
		$pure = $player->getPure();
		$player->getAttribute()->isSliding = false;
		$this->cancelSneak($pure);
		$metadata = clone $pure->getNetworkProperties();
		$metadata->setGenericFlag(EntityMetadataFlags::SNEAKING, false);
		$metadata->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
		$actor_data_pk = SetActorDataPacket::create(
			$pure->getId(),
			$metadata->getAll(),
			new PropertySyncData([], []),
			1
		);
		$pure->getWorld()->broadcastPacketToViewers($pure->getPosition(), $actor_data_pk);
		
		$ref = new ReflectionClass($pure);
		$setSize = $ref->getMethod('setSize');
		$getInitialSizeInfo = $ref->getMethod('getInitialSizeInfo');
		$networkPropertiesDirty = $ref->getProperty('networkPropertiesDirty');
		
		$setSize->setAccessible(true);
		$getInitialSizeInfo->setAccessible(true);
		$networkPropertiesDirty->setAccessible(true);
		
		$size = $getInitialSizeInfo->invoke($pure);
		$setSize->invoke($pure, $size->scale($pure->getScale()));
		$networkPropertiesDirty->setValue($pure, true);
	}
	
	protected function cancelSneak(Player $player):void{
		if(!$player->isOnline()) return;
		$position = BlockPosition::fromVector3($player->getPosition()->add(0, 0, 0));
		$player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
			$position,
			RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::WATER()->getFullId()),
			UpdateBlockPacket::FLAG_NETWORK,
			UpdateBlockPacket::DATA_LAYER_LIQUID
		));
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(new ClosureTask(
			function() use ($player, $position): void{
				$player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
					$position,
					RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::AIR()->getFullId()),
					UpdateBlockPacket::FLAG_NETWORK,
					UpdateBlockPacket::DATA_LAYER_LIQUID
				));
			}
		), 1);
	}
}
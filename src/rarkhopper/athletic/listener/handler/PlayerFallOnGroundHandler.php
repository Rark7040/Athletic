<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use rarkhopper\athletic\attribute\AttributesMap;
use rarkhopper\athletic\event\PlayerFallOnGroundEvent;

trait PlayerFallOnGroundHandler{
	public function onFall(PlayerFallOnGroundEvent $ev):void{
		$player = $ev->getPlayer();
		$attr = AttributesMap::getInstance()->get($player);
		$attr->isJumping = false;
		$attr->isBlockJumping = false;
		
		$metadata = clone $player->getNetworkProperties();
		$metadata->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
		$actor_data_pk = SetActorDataPacket::create(
			$player->getId(),
			$metadata->getAll(),
			new PropertySyncData([], []),
			1
		);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $actor_data_pk);
	}
}
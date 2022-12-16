<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use rarkhopper\athletic\event\PlayerFallOnGroundEvent;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait PlayerFallOnGroundHandlerTrait{
	public function onFall(PlayerFallOnGroundEvent $ev):void{
		$pure_player = $ev->getPlayer();
		$player = AthleticPlayerMap::getInstance()->get($pure_player);
		$attr = $player->getAttribute();
		$attr->isJumping = false;
		$attr->isBlockJumping = false;
		
		$metadata = clone $pure_player->getNetworkProperties();
		$metadata->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
		$actor_data_pk = SetActorDataPacket::create(
			$pure_player->getId(),
			$metadata->getAll(),
			new PropertySyncData([], []),
			1
		);
		$pure_player->getWorld()->broadcastPacketToViewers($pure_player->getPosition(), $actor_data_pk);
	}
}
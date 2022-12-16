<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use rarkhopper\athletic\event\PlayerDoubleJumpEvent;

trait BlockJumpHandler{
	public function onDoubleJump(PlayerDoubleJumpEvent $ev):void{
		if(!$ev->isBlockJump()) return;
		$player = $ev->getPlayer();
		$sound_pk = LevelSoundEventPacket::nonActorSound(
			LevelSoundEvent::MOB_ARMOR_STAND_PLACE,
			$player->getPosition(),
			false,
			0
		);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $sound_pk);
	}
}
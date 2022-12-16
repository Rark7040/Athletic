<?php
declare(strict_types=1);

namespace rarkhopper\athletic\action;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\world\sound\ArrowHitSound;

class AthleticActions{
	public static function doubleJump(Player $player):void{
		$motion = $player->getDirectionVector()->multiply(0.4);
		$motion->y = 0.6;
		$player->setMotion($motion);
		$sound_pk = LevelSoundEventPacket::nonActorSound(
			LevelSoundEvent::ARMOR_EQUIP_LEATHER,
			$player->getPosition(),
			false,
			0
		);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $sound_pk);
	}
}
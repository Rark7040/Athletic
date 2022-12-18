<?php
declare(strict_types=1);

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\world\sound\Sound;

class BlockJumpSound implements Sound{
	/**
	 * @return ClientboundPacket[]
	 */
	public function encode(Vector3 $pos):array{
		return [
			LevelSoundEventPacket::nonActorSound(
				LevelSoundEvent::BLOCK_SCAFFOLDING_CLIMB,
				$pos,
				false,
				0
			),
			LevelSoundEventPacket::nonActorSound(
				LevelSoundEvent::ARMOR_EQUIP_LEATHER,
				$pos,
				false,
				0
			)
		];
	}
}
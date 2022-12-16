<?php
declare(strict_types=1);

namespace rarkhopper\athletic\action;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\sound\ArrowHitSound;
use rarkhopper\athletic\attribute\AttributesMap;

class AthleticActions{
	public static function validateGameMode(GameMode $gameMode):bool{
		return $gameMode->equals(GameMode::SURVIVAL()) or $gameMode->equals(GameMode::ADVENTURE());
	}
	
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
	
	public static function setCanDoubleJump(Player $player):void{
		$attr = AttributesMap::getInstance()->get($player);
		$attr->isJumping = true;
		$player->setAllowFlight(true);
	}
	
	public static function setCanBlockJump(Player $player):void{
		$attr = AttributesMap::getInstance()->get($player);
		$attr->isBlockJumping = true;
		$player->setAllowFlight(true);
	}
}
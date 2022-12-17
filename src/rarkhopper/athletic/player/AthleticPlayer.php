<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use rarkhopper\athletic\event\PlayerDoubleJumpEvent;

class AthleticPlayer{
	private Player $player;
	private PlayerAthleticAttribute $attr;
	
	public function __construct(Player $player){
		$this->player = $player;
		$this->attr = new PlayerAthleticAttribute;
	}
	
	public function getPure():Player{
		return $this->player;
	}
	
	public function getAttribute():PlayerAthleticAttribute{
		return $this->attr;
	}
	
	public function canAthleticAction():bool{
		return $this->attr->allowAthleticAction and $this->validateGameMode();
	}
	
	public function validateGameMode():bool{
		$gameMode = $this->player->getGamemode();
		return GameMode::SURVIVAL()->equals($gameMode) or GameMode::ADVENTURE()->equals($gameMode);
	}
	
	public function doubleJump(bool $isBlockJump = false):void{
		if($isBlockJump){
			$this->attr->isBlockJumped = true;
			$this->attr->isBlockJumping = false;
			
		}else{
			$this->attr->isDoubleJumped = true;
			$this->attr->isJumping = false;
		}
		(new PlayerDoubleJumpEvent($this->player, $isBlockJump))->call();
		$direction = $this->player->getDirectionPlane()->multiply(0.6);
		$motion = new Vector3($direction->x, 0.7, $direction->y);
		$this->player->setMotion($motion);
		$sound_pk = LevelSoundEventPacket::nonActorSound(
			LevelSoundEvent::ARMOR_EQUIP_LEATHER,
			$this->player->getPosition(),
			false,
			0
		);
		$this->player->getWorld()->broadcastPacketToViewers($this->player->getPosition(), $sound_pk);
	}
	
	public function setCanDoubleJump():void{
		$this->attr->isJumping = true;
		$this->player->setAllowFlight(true);
	}
	
	public function setCanBlockJump():void{
		$this->attr->isBlockJumping = true;
		$this->player->setAllowFlight(true);
	}
	
	public function resetJumpAttributes():void{
		$this->attr->isJumping = false;
		$this->attr->isDoubleJumped = false;
		$this->attr->isBlockJumping = false;
		$this->attr->isBlockJumped = false;
	}
}
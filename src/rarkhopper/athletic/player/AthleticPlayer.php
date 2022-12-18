<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

use BlockJumpSound;
use DoubleJumpSound;
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
	
	public function doubleJump():void{
		(new PlayerDoubleJumpEvent($this->player, false))->call();
		$this->attr->isDoubleJumped = true;
		$this->attr->isJumping = false;
		$this->player->getWorld()->addSound($this->player->getPosition(), new DoubleJumpSound);
		$this->addJumpMotion();
	}
	
	public function blockJump():void{
		(new PlayerDoubleJumpEvent($this->player, true))->call();
		$this->attr->isBlockJumped = true;
		$this->attr->isBlockJumping = false;
		$this->player->getWorld()->addSound($this->player->getPosition(), new BlockJumpSound);
		$this->addJumpMotion();
	}
	
	public function addJumpMotion():void{
		$direction = $this->player->getDirectionPlane()->multiply(0.6);
		$motion = new Vector3($direction->x, 0.7, $direction->y);
		$this->player->setMotion($motion);
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
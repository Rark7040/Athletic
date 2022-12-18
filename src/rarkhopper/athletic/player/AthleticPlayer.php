<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\event\PlayerDoubleJumpEvent;
use rarkhopper\athletic\sound\BlockJumpSound;
use rarkhopper\athletic\sound\DoubleJumpSound;
use rarkhopper\athletic\sound\SlidingSound;

class AthleticPlayer{
	private Player $player;
	private PlayerAthleticAttribute $attr;
	
	public function __construct(Player $player){
		$this->player = $player;
		$this->attr = new PlayerAthleticAttribute;
	}
	
	/**
	 * @return Player
	 */
	public function getPure():Player{
		return $this->player;
	}
	
	/**
	 * @return PlayerAthleticAttribute
	 */
	public function getAttribute():PlayerAthleticAttribute{
		return $this->attr;
	}
	
	/**
	 * @return bool
	 * if returned false, then can not perform the actions
	 */
	public function canAthleticAction():bool{
		return $this->attr->allowAthleticAction and $this->validateGameMode();
	}
	
	/**
	 * @return bool
	 */
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
	
	public function sliding():void{
		$pure = $this->getPure();
		$direction = $pure->getDirectionPlane()->multiply(0.7);
		$motion = new Vector3($direction->x, -0.4, $direction->y);
		$pure->setMotion($motion);
		$this->attr->isSliding = true;
		$this->player->getWorld()->addSound($this->player->getPosition(), new SlidingSound);
		$pure->setSwimming();
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $this->stopSliding($this)), 15
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
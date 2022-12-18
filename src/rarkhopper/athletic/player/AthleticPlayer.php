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
use rarkhopper\athletic\event\AthleticPlayerDoubleJumpEvent;
use rarkhopper\athletic\event\AthleticPlayerSlidingEvent;
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
	
	/**
	 * @return void
	 * player perform air jumping
	 * {@link AthleticPlayer::setAbleDoubleJump()}
	 */
	public function doubleJump():void{
		$ev = new AthleticPlayerDoubleJumpEvent($this, false);
		$ev->call();
		
		if($ev->isCancelled()) return;
		$this->attr->isDoubleJumped = true;
		$this->attr->isJumping = false;
		$this->player->getWorld()->addSound($this->player->getPosition(), new DoubleJumpSound);
		$this->addJumpMotion();
	}
	
	/**
	 * @return void
	 * player perform air jumping
	 * if also in the case of not collided the block then able jump
	 * {@link AthleticPlayer::setAbleBlockJump()}
	 */
	public function blockJump():void{
		$ev = new AthleticPlayerDoubleJumpEvent($this, true);
		$ev->call();
		
		if($ev->isCancelled()) return;
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
	
	/**
	 * @return void
	 * to be able {@link AthleticPlayer::doubleJump()}
	 */
	public function setAbleDoubleJump():void{
		$this->attr->isJumping = true;
		$this->player->setAllowFlight(true);
	}
	
	/**
	 * @return void
	 * to be able {@link AthleticPlayer::blockJump()}
	 */
	public function setAbleBlockJump():void{
		$this->attr->isBlockJumping = true;
		$this->player->setAllowFlight(true);
	}
	
	/**
	 * @return void
	 * calling this function will allow the player to perform a double jump again
	 */
	public function resetJumpAttributes():void{
		$this->attr->isJumping = false;
		$this->attr->isDoubleJumped = false;
		$this->attr->isBlockJumping = false;
		$this->attr->isBlockJumped = false;
	}
	
	/**
	 * @return void
	 * player perform sliding
	 */
	public function sliding():void{
		$ev = new AthleticPlayerSlidingEvent($this);
		$ev->call();
		
		if($ev->isCancelled()) return;
		$this->attr->isSliding = true;
		$this->addSlidingMotion();
		$this->player->getWorld()->addSound($this->player->getPosition(), new SlidingSound);
		$this->player->setSwimming();
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $this->stopSliding($this)), 15
		);
	}
	
	public function addSlidingMotion():void{
		$direction = $this->player->getDirectionPlane()->multiply(0.7);
		$motion = new Vector3($direction->x, -0.4, $direction->y);
		$this->player->setMotion($motion);
	}
	
	private function stopSliding(AthleticPlayer $player):void{
		$topBlock = $this->player->getWorld()->getBlock($this->player->getPosition()->add(0, 1, 0));
		$player->getAttribute()->isSliding = false;
		
		if($topBlock->isSolid()){
			$player->getAttribute()->keepSliding = true;
			
		}else{
			$this->player->setSwimming(false);
			
			//数ティック送らせないと水を設置する座標がずれる
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $this->cancelSneak()), 2
			);
		}
	}
	
	/**
	 * @return void
	 * placing water at the player's feet will cancel the sneaking.
	 * this is a glitch that use client-side behavior.
	 */
	public function cancelSneak():void{
		$pure = $this->player;
		
		if(!$pure->isOnline()) return;
		$pure->toggleSneak(false);
		$vec = BlockPosition::fromVector3($pure->getPosition()->add(0, 0, 0));
		$pure->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
			$vec,
			RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::WATER()->getFullId()),
			UpdateBlockPacket::FLAG_NETWORK,
			UpdateBlockPacket::DATA_LAYER_LIQUID
		));
		
		AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(fn() => $pure->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
				$vec,
				RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::AIR()->getFullId()),
				UpdateBlockPacket::FLAG_NETWORK,
				UpdateBlockPacket::DATA_LAYER_LIQUID
			))), 1
		);
	}
	
	/**
	 * @return bool
	 * if returned true then cancelled fall damage
	 */
	public function hasJumpedFlags():bool{
		return $this->attr->isDoubleJumped or $this->attr->isBlockJumped;
	}
}
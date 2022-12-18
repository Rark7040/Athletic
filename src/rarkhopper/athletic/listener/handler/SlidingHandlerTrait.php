<?php
declare(strict_types=1);

namespace rarkhopper\athletic\listener\handler;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\athletic\AthleticPlugin;
use rarkhopper\athletic\player\AthleticPlayer;
use rarkhopper\athletic\player\AthleticPlayerMap;

trait SlidingHandlerTrait{
	public function onSneak(PlayerToggleSneakEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		
		if(!$player->canAthleticAction()) return;
		$before = $player->getPure()->isSneaking();
		$after = $ev->isSneaking();
		
		if(!$before and $after){
			$this->onToggleOn($player);
		}
	}
	
	private function onToggleOn(AthleticPlayer $player):void{
		$pure = $player->getPure();
		
		if(!$pure->isSprinting() or $pure->isOnGround() or $player->getAttribute()->isDoubleJumped) return;
		$player->sliding();
	}
	
	public function onToggleSwim(PlayerToggleSwimEvent $ev):void{
		$player = AthleticPlayerMap::getInstance()->get($ev->getPlayer());
		$attr = $player->getAttribute();
		
		if($attr->keepSliding){
			//頭上のブロックがsolidではないかつ、水中ではないのでkeep終了
			if($ev->isSwimming()) return;
			$attr->keepSliding = false;
			
			//数ティック送らせないと水を設置する座標がずれる
			AthleticPlugin::getTaskScheduler()->scheduleDelayedTask(
				new ClosureTask(fn() => $player->cancelSneak()), 1
			);
			return;
		}
		
		if($attr->isSliding){
			$ev->cancel();
		}
	}
	
	public function onDeath(PlayerDeathEvent $ev):void{
		$pos = $ev->getPlayer()->getPosition();
		$pos->getWorld()->broadcastPacketToViewers($pos, LevelEventPacket::create(LevelEvent::PARTICLE_DESTROY,RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::RED_GLAZED_TERRACOTTA()->getFullId()), $pos->add(0, 1, 0)));
		$pos->getWorld()->broadcastPacketToViewers($pos,LevelEventPacket::create(LevelEvent::PARTICLE_DESTROY, RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::REDSTONE()->getFullId()), $pos->add(0, 1, 0)));
	}
}
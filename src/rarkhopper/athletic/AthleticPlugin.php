<?php
declare(strict_types=1);

namespace rarkhopper\athletic;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use rarkhopper\athletic\attribute\AttributesMap;
use rarkhopper\athletic\listener\EventListener;

class AthleticPlugin extends PluginBase{
	private static TaskScheduler $taskScheduler;
	
	protected function onEnable():void{
		self::$taskScheduler = $this->getScheduler();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);
		
		AthleticPlugin::getTaskScheduler()->scheduleRepeatingTask(
			new ClosureTask(
				function(){
					foreach(Server::getInstance()->getOnlinePlayers() as $player){
						$attr = AttributesMap::getInstance()->get($player);
						$gameMode = $player->getGamemode();
						
						if(!$gameMode->equals(GameMode::SURVIVAL()) and !$gameMode->equals(GameMode::ADVENTURE())) continue;
						var_dump($this->isCollidedBlock($player));
						
						if($player->isOnGround()){
							$attr->isJumping = true;
						}
						if(!$attr->canDoubleJump or $attr->isJumping) continue;
						if(!$this->isCollidedBlock($player)) continue;
						$attr->isJumping = true;
						$player->setAllowFlight(true);
						$sound_pk = LevelSoundEventPacket::nonActorSound(
							LevelSoundEvent::MOB_ARMOR_STAND_PLACE,
							$player->getPosition(),
							false,
							0
						);
						$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $sound_pk);
					}
				}
			),
			1
		);
	}
	
	private function isCollidedBlock(Entity $entity):bool{
		$world = $entity->getWorld();
		$v = $entity->getPosition()->asVector3()->floor();
		/**
		 * @var array<Block> $list
		 */
		$list = [
			$world->getBlock($v->add(1, 0, 1)),
			$world->getBlock($v->add(-1, 0, 1)),
			$world->getBlock($v->add(1, 0, -1)),
			$world->getBlock($v->add(-1, 0, -1))
		];
		foreach($list as $block){
			foreach($block->getCollisionBoxes() as $bb){
				if($bb->expandedCopy(0.3, 0.0, 0.3)->intersectsWith($entity->getBoundingBox())){
					return true;
				}
			}
		}
		return false;
	}
	
	
	public static function getTaskScheduler():TaskScheduler{
		return self::$taskScheduler;
	}
}
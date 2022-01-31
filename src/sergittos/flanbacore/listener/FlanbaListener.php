<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use sergittos\flanbacore\utils\ConfigGetter;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\FlanbaCore;

class FlanbaListener implements Listener {

	public function __construct(FlanbaCore $plugin){
		$this->plugin = $plugin;
	}

	public function onFight(EntityDamageByEntityEvent $event): void {
		$event->setKnockBack(ConfigGetter::getKnockback());
	}

	public function onBlockBreak(BlockBreakEvent $event) {
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($player->isCreative() or $player->isSpectator()) return;
		if ($event->isCancelled()) {
			$x = $player->getPosition()->getX();
			$y = $player->getPosition()->getY();
			$z = $player->getPosition()->getZ();
			$playerX = $player->getPosition()->getX();
			$playerZ = $player->getPosition()->getZ();
			if($playerX < 0) $playerX = $playerX - 1;
			if($playerZ < 0) $playerZ = $playerZ - 1;
			if (($block->getPosition()->getX() == (int)$playerX) AND ($block->getPosition()->getZ() == (int)$playerZ) AND ($player->getPosition()->getY() > $block->getPosition()->getY())) { #If block is under the player
				foreach ($block->getCollisionBoxes() as $blockHitBox) {
					$y = max([$y, $blockHitBox->maxY]);
				}
				$player->teleport(new Vector3($x, $y, $z));
			} else { #If block is on the side of the player
				$xb = 0;
				$zb = 0;
				foreach ($block->getCollisionBoxes() as $blockHitBox) {
					if (abs($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2) > abs($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) {
						$xb = (5 / ($x - ($blockHitBox->minX + $blockHitBox->maxX) / 2)) / 24;
					} else {
						$zb = (5 / ($z - ($blockHitBox->minZ + $blockHitBox->maxZ) / 2)) / 24;
					}
				}
				$player->setMotion(new Vector3($xb, 0, $zb));
			}
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) {
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($player->isCreative() or $player->isSpectator()) return;
		if ($event->isCancelled()) {
			$playerX = $player->getPosition()->getX();
			$playerZ = $player->getPosition()->getZ();
			if($playerX < 0) $playerX = $playerX - 1;
			if($playerZ < 0) $playerZ = $playerZ - 1;
			if (($block->getPosition()->getX() == (int)$playerX) AND ($block->getPosition()->getZ() == (int)$playerZ) AND ($player->getPosition()->getY() > $block->getPosition()->getY())) { #If block is under the player
				$playerMotion = $player->getMotion();
				$this->plugin->getScheduler()->scheduleDelayedTask(new MotionTask($player, new Vector3($playerMotion->getX(), -0.1, $playerMotion->getZ())), 2);
			}
		}
	}

	public function onJoin(PlayerJoinEvent $ev){
		$ev->setJoinMessage(" §gWelcome, §a{$ev->getPlayer()->getDisplayName()}!");
		$player = $ev->getPlayer();
		$player->sendMessage(" §f__________________\n     §e§lFLANBA§6MC     \n§l§eSTORE: §r§fflanba.com/store\n§l§eDISCORD: §r§fdiscord.gg/flanba\n§l§eYOUTUBE: §r§fyoutube.com/c/flanba\n§r§f §f__________________");
		$player->sendTitle(TextFormat::YELLOW . TextFormat::BOLD . "Flanba " . TextFormat::GOLD . "Network");
		$player->sendSubTitle(TextFormat::YELLOW . TextFormat::BOLD . "Welcome to Flanba Network,\nPlease join our discord server!\n" . TextFormat::GREEN . "discord.gg/flanba");
	}

	public function onLeave(PlayerQuitEvent $ev){
		$ev->setQuitMessage(" §gGoodbye, §c{$ev->getPlayer()->getDisplayName()}.");
	}
}
class MotionTask extends Task {

	private Entity $entity;

	private Vector3 $vector3;

	public function __construct(Entity $entity, Vector3 $vector3) {
		$this->entity = $entity;
		$this->vector3 = $vector3;
	}

	public function onRun() : void{
		$this->entity->setMotion($this->vector3);
	}
}

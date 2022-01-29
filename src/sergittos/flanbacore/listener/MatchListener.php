<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\entity\projectile\Arrow;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\GoldenApple;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\event\SessionDeathEvent;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\cooldown\BowCooldown;
use sergittos\flanbacore\utils\cooldown\Cooldown;
use sergittos\flanbacore\utils\cooldown\GappleCooldown;

class MatchListener implements Listener {

	public function onDrop(PlayerDropItemEvent $event){
		$event->cancel();
	}

	public function onDeath(SessionDeathEvent $event): void {
        $session = $event->getSession();
        $cause = $session->getPlayer()->getLastDamageCause();
        $session->teleportToTeamSpawnPoint();
        if($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if(!$damager instanceof Player) {
                return;
            }
            $damager_session = SessionFactory::getSession($damager);
            if(!$damager_session->hasMatch()) {
                return;
            }
            if($damager_session->getMatch()->getId() === $session->getMatch()->getId()) {
                $damager_session->getTeam()->addKill();
            }
        }
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }
        $session = SessionFactory::getSession($entity);
        $session->updateNameTag();
        if($session->hasMatch() and $session->getMatch()->getStage() === FlanbaMatch::WAITING_STAGE) {
            $event->cancel();
            return;
        }
        if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
            return;
        }
		if($event instanceof EntityDamageByEntityEvent){
			if($session->hasMatch() and $entity->getHealth() - $event->getFinalDamage() <= 0) {
				foreach($entity->getWorld()->getPlayers() as $players){
					if($session->getTeam()->getColor() == "{RED}"){
						$players->sendMessage(TextFormat::RED . "{$entity->getName()}" . TextFormat::GRAY .  " was killed by " . TextFormat::BLUE . "{$event->getDamager()->getName()}.");
					}
					if($session->getTeam()->getColor() == "{BLUE}"){
						$players->sendMessage(TextFormat::BLUE . "{$entity->getName()}" . TextFormat::GRAY .  " was killed by " . TextFormat::RED . "{$event->getDamager()->getName()}.");
					}
				}
				$death_event = new SessionDeathEvent($session);
				$death_event->call();
				$event->cancel();
			}
		}
    }

    public function onRegainHealth(EntityRegainHealthEvent $event): void {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            SessionFactory::getSession($entity)->updateNameTag();
        }
    }

    public function onConsume(PlayerItemConsumeEvent $event): void {
        $session = SessionFactory::getSession($player = $event->getPlayer());
        if(!$session->hasMatch()) {
            return;
        }
        if($event->getItem() instanceof GoldenApple) {
            $player->setHealth($player->getMaxHealth());
            $player->getEffects()->clear();
            $session->updateNameTag();
        }
    }

    public function onShoot(EntityShootBowEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }
        $session = SessionFactory::getSession($entity);
        if(!$session->hasMatch()) {
            return;
        }
        if($session->hasCooldown(Cooldown::BOW)) {
            $event->cancel();
            return;
        }
        $session->addCooldown(new BowCooldown());
    }

    public function onHitEntity(ProjectileHitEntityEvent $event): void {
        $owning_entity = $event->getEntity()->getOwningEntity();
        if($owning_entity instanceof Player) {
            SessionFactory::getSession($owning_entity)->sendOrbSound();
			$owning_entity->sendMessage("§b{$event->getEntityHit()->getName()} §ais on §c{$event->getEntityHit()->getHealth()}!");
        }
    }

    public function onHitBlock(ProjectileHitBlockEvent $event): void {
        $entity = $event->getEntity();
        if($entity instanceof Arrow) {
            $entity->kill();
        }
    }

    public function onMove(PlayerMoveEvent $event): void {
        $session = SessionFactory::getSession($player = $event->getPlayer());
		$position = $player->getPosition();
        if(!$session->hasMatch()) {
			if($position->getY() <= 10){
				$player->teleport(FlanbaCore::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
			}
            return;
        }
        $match = $session->getMatch();
        $stage = $match->getStage();
        $session_team = $session->getTeam();
        if($position->getY() <= $match->getArena()->getVoidLimit()) {
            if($stage === FlanbaMatch::WAITING_STAGE or $stage === FlanbaMatch::COUNTDOWN_STAGE) {
                $session->teleportToTeamSpawnPoint(false);
            } else {
                $session->teleportToTeamSpawnPoint(true);
                $match->broadcastMessage($session_team->getColor() . $session->getUsername() . " {GRAY}fell into the void.");
            }
            return;
        }

        if($stage !== $match::PLAYING_STAGE) {
            return;
        }

        $players = $match->getPlayers();
        foreach($match->getTeams() as $team) {
            if(!$team->getGoalArea()->isInside($position, true)) {
                continue;
            }
            $color = $session_team->getColor();
            if($color === $team->getColor()) {
                $session->teleportToTeamSpawnPoint();
                return;
            }
            $session_team->addScore();
            if($session_team->getScoreNumber() >= 5) {
                $match->finish($session_team, $team);
                return;
            } else {
                $match->setSessionScored($session);
                $match->setStage($match::OPENING_CAGES_STAGE);
                foreach($players as $player) {
                    $player->getPlayer()->setGamemode(GameMode::ADVENTURE());
                }
            }

            $countdown = $match->getCountdown();
            $countdown--;
            $match->setCountdown($countdown);
            foreach($players as $player) {
                $player->setImmobile();
                $player->teleportToTeamSpawnPoint();
                $player->updateScoreboard();
                $player->title(
                    $color . $session->getUsername() . "§7 scored!",
                    "{GRAY}Cages open in {GREEN}{$countdown}s{GRAY}..."
                );
                $player->message($color . $session->getUsername() . " §6scored!");
            }
            // TODO: Clean this
        }
    }

    public function onPlace(BlockPlaceEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if(!$session->hasMatch()) {
            return;
        }
        if($event->getBlock()->getPosition()->getY() >= $session->getMatch()->getArena()->getHeightLimit()) {
            $session->message("§8» §cHeight Limit");
            $event->cancel();
        }
        $block = $event->getBlock();
        if (in_array($block->getId(), [205, 459, 58, 145, 154])) {
          $event->cancel();
        } 
    }

	public function onBreak(BlockBreakEvent $event){
		$session = SessionFactory::getSession($event->getPlayer());
		if(!$session->hasMatch()) {
			return;
		}
		if(!($event->getBlock()->getId() == 159 and $event->getBlock()->getMeta() == 11 or $event->getBlock()->getMeta() == 14 or $event->getBlock()->getMeta() == 0)){
			$event->cancel();
		}
	}

    public function onTouch(PlayerInteractEvent $event) {
		$session = SessionFactory::getSession($event->getPlayer());
		if(!$session->hasMatch()) {
			return;
		}
		$block = $event->getBlock();
		if (in_array($block->getId(), [205, 459, 58, 145, 154])) {
			$event->cancel();
		}
    }
    
    public function onQuit(PlayerQuitEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if($session->hasMatch()) {
            $session->setMatch(null);
        }
    }

}

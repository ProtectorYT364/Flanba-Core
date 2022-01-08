<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\entity\projectile\Arrow;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\GoldenApple;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use sergittos\flanbacore\event\SessionDeathEvent;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\cooldown\BowCooldown;
use sergittos\flanbacore\utils\cooldown\Cooldown;
use sergittos\flanbacore\utils\cooldown\GappleCooldown;

class MatchListener implements Listener {

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
        if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
            return;
        }
        if($session->hasMatch() and $entity->getHealth() - $event->getFinalDamage() <= 0) {
            $death_event = new SessionDeathEvent($session);
            $death_event->call();
            $event->cancel();
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
            if($session->hasCooldown(Cooldown::GAPPLE)) {
                $event->cancel();
                return;
            }
            $player->setHealth($player->getMaxHealth());
            $player->getEffects()->clear();
            $session->updateNameTag();
            $session->addCooldown(new GappleCooldown());
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

    public function onHitBlock(ProjectileHitBlockEvent $event): void {
        $entity = $event->getEntity();
        if($entity instanceof Arrow) {
            $entity->kill();
        }
    }

    public function onMove(PlayerMoveEvent $event): void {
        $session = SessionFactory::getSession($player = $event->getPlayer());
        if(!$session->hasMatch()) {
            return;
        }
        $match = $session->getMatch();
        $stage = $match->getStage();
        $position = $player->getPosition();
        if($position->getY() <= $match->getArena()->getVoidLimit()) {
            if($stage === FlanbaMatch::WAITING_STAGE or $stage === FlanbaMatch::COUNTDOWN_STAGE) {
                $session->teleportToTeamSpawnPoint(false);
            } else {
                $session->teleportToTeamSpawnPoint(true);
            }
            return;
        }

        if($stage !== $match::PLAYING_STAGE) {
            return;
        }
        $players = $match->getPlayers();
        $session_team = $session->getTeam();
        foreach($match->getTeams() as $team) {
            if(!$team->getGoalArea()->isInside($position, true)) {
                continue;
            }
            if($session_team->getColor() === $team->getColor()) {
                $session->teleportToTeamSpawnPoint();
                return;
            }
            $session_team->addScore();
            $color = $session_team->getColor();
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
                $match->broadcastTitle(
                    $color . $session->getUsername() . " scored!",
                    "{GRAY}Cages open in {GREEN}{$countdown}s{GRAY}..."
                );

                // TODO: Clean this
            }
            $match->updatePlayersScoreboard();
        }
    }

    public function onPlace(BlockPlaceEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if(!$session->hasMatch()) {
            return;
        }
        if($event->getBlock()->getPosition()->getY() >= $session->getMatch()->getArena()->getHeightLimit()) {
            $event->cancel();
        }
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if($session->hasMatch()) {
            $finish = false;
            if($session->getMatch()->getStage() !== FlanbaMatch::WAITING_STAGE) {
                $finish = true;
            }
            $session->setMatch(null, $finish);
        }
    }

}
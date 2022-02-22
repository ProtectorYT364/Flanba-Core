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
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\GoldenApple;
use pocketmine\item\ItemIds;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\event\SessionDeathEvent;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\item\presets\match\LeaveSpectatorItem;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\cooldown\BowCooldown;
use sergittos\flanbacore\utils\cooldown\Cooldown;
use sergittos\flanbacore\utils\cooldown\GappleCooldown;

class MatchListener implements Listener
{

    public array $position_before_break = [];

    public array $block_position_when_place = [];

    public function onDrop(PlayerDropItemEvent $event)
    {
		if($event->getItem()->getId() !== ItemIds::GOLDEN_APPLE) $event->cancel();
    }

    public function onDeath(SessionDeathEvent $event): void
    {
        $session = $event->getSession();
        $cause = $session->getPlayer()->getLastDamageCause();
        $session->teleportToTeamSpawnPoint();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if (!$damager instanceof Player) {
                return;
            }
            $damager_session = SessionFactory::getSession($damager);
            if (!$damager_session->hasMatch()) {
                return;
            }
            if ($damager_session->getMatch()->getId() === $session->getMatch()->getId()) {
                $damage_event = new EntityDamageEvent($damager_session->getPlayer(), EntityDamageEvent::CAUSE_VOID, 0);
                $damager_session->getPlayer()->setLastDamageCause($damage_event);
                $damager_session->getTeam()->addKill();
            }
        }
    }

    public function onDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) {
            return;
        }
        $session = SessionFactory::getSession($entity);
        $session->updateNameTag();
        if ($session->hasMatch()) {
            $stage = $session->getMatch()->getStage();
            if($stage === FlanbaMatch::WAITING_STAGE or $stage === FlanbaMatch::COUNTDOWN_STAGE) {
                $event->cancel();
                return;
            }
        }
        if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
            return;
        }


        if ($event instanceof EntityDamageByEntityEvent) {
            if ($session->hasMatch() and $entity->getHealth() - $event->getFinalDamage() <= 0) {
                foreach ($entity->getWorld()->getPlayers() as $players) {
                    if ($session->getTeam()->getColor() == "{RED}") {
                        $players->sendMessage(TextFormat::RED . " {$entity->getName()}" . TextFormat::GRAY . " was killed by " . TextFormat::BLUE . "{$event->getDamager()->getName()}.");
                    }
                    if ($session->getTeam()->getColor() == "{BLUE}") {
                        $players->sendMessage(TextFormat::BLUE . " {$entity->getName()}" . TextFormat::GRAY . " was killed by " . TextFormat::RED . "{$event->getDamager()->getName()}.");
                    }
                }
                $death_event = new SessionDeathEvent($session);
                $death_event->call();
                $event->cancel();
            }
        }
    }

    public function onRegainHealth(EntityRegainHealthEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            SessionFactory::getSession($entity)->updateNameTag();
        }

		$reason = $event->getRegainReason();

		if($reason === EntityRegainHealthEvent::CAUSE_REGEN) {
			$event->cancel();
		}
		if($reason === EntityRegainHealthEvent::CAUSE_SATURATION) {
			$event->cancel();
		}

    }

    public function onConsume(PlayerItemConsumeEvent $event): void
    {
        $session = SessionFactory::getSession($player = $event->getPlayer());
        if (!$session->hasMatch()) {
            return;
        }
        if ($event->getItem() instanceof GoldenApple) {
            $player->setHealth($player->getMaxHealth());
            $player->getEffects()->clear();
            $session->updateNameTag();
        }
    }

    public function onShoot(EntityShootBowEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) {
            return;
        }
        $session = SessionFactory::getSession($entity);
        if (!$session->hasMatch()) {
            return;
        }
        if ($session->hasCooldown(Cooldown::BOW)) {
            $event->cancel();
            return;
        }
        $session->addCooldown(new BowCooldown());
    }

    public function onHitEntity(ProjectileHitEntityEvent $event): void
    {
        $owning_entity = $event->getEntity()->getOwningEntity();
        if ($owning_entity instanceof Player) {
            SessionFactory::getSession($owning_entity)->sendOrbSound();
            $owning_entity->sendMessage("§b{$event->getEntityHit()->getName()} §ais on §c{$event->getEntityHit()->getHealth()}!");
        }
    }

    public function onHitBlock(ProjectileHitBlockEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Arrow) {
            $entity->kill();
        }
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $session = SessionFactory::getSession($player = $event->getPlayer());
        $position = $player->getPosition();
        if (!$session->hasMatch()) {
            if ($position->getY() <= 10) {
                $player->teleport(FlanbaCore::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
            }
            return;
        }
        $match = $session->getMatch();
        $stage = $match->getStage();
        $session_team = $session->getTeam();
        if ($position->getY() <= $match->getArena()->getVoidLimit()) {
            if ($stage === FlanbaMatch::WAITING_STAGE or $stage === FlanbaMatch::COUNTDOWN_STAGE or $stage === FlanbaMatch::ENDING_STAGE) {
                $session->teleportToTeamSpawnPoint(false);
            } else {

                if ($player->getLastDamageCause() instanceof EntityDamageByEntityEvent && $player->getLastDamageCause()->getDamager() !== null) {
                    $damager = $player->getLastDamageCause()->getDamager();
                    $damage_session = SessionFactory::getSession($damager);
                    $damage_team = $damage_session->getTeam();
                    $match->broadcastMessage($session_team->getColor() . " " . $session->getUsername() . " {GRAY}fell into the void fighting " . $damage_team->getColor() . $damage_session->getUsername());

                    $session->teleportToTeamSpawnPoint(true);
                    $death_event = new SessionDeathEvent($session);
                    $death_event->call();
                } else {

                    $session->teleportToTeamSpawnPoint(true);
                    $match->broadcastMessage($session_team->getColor() . " " . $session->getUsername() . " {GRAY}fell into the void.");

                }
            }
            return;
        }

        if ($stage !== $match::PLAYING_STAGE) {
            return;
        }

        $players = $match->getPlayers();
        foreach ($match->getTeams() as $team) {
            if (!$team->getGoalArea()->isInside($position, true)) {
                continue;
            }
            $color = $session_team->getColor();
            if ($color === $team->getColor()) {
                $session->teleportToTeamSpawnPoint();
                return;
            }
            $session_team->addScore();
            if ($session_team->getScoreNumber() >= 5) {
                $match->finish($session_team, $team);
                return;
            } else {
                $match->setSessionScored($session);
                $match->setStage($match::OPENING_CAGES_STAGE);
                foreach ($players as $player) {
                    $player->getPlayer()->setGamemode(GameMode::ADVENTURE());
                }
            }

            $countdown = $match->getCountdown();
            $countdown--;
            $match->setCountdown($countdown);
            foreach ($players as $player) {
                $player->setImmobile();
                $player->teleportToTeamSpawnPoint();
                $player->updateScoreboard();
                $player->title(
                    $color . $session->getUsername() . " scored!",
                    "{GRAY}Cages open in {GREEN}{$countdown}s{GRAY}..."
                );
                $player->message($color . $session->getUsername() . " §6scored!");
            }
            // TODO: Clean this
        }
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        $session = SessionFactory::getSession($event->getPlayer());
        if (!$session->hasMatch()) {
            return;
        }
        if ($event->getBlock()->getPosition()->getY() >= $session->getMatch()->getArena()->getHeightLimit()) {
            $session->message("§8» §cHeight Limit");
            $event->cancel();
        }
        $block = $event->getBlock();
        if (in_array($block->getId(), [205, 459, 58, 145, 154])) {
            $event->cancel();
        }
    }

    public function onHeld(PlayerItemHeldEvent $event)
    {

        if ($event->getPlayer() !== null and $event->getPlayer()->isSpectator() and $event->getItem() instanceof LeaveSpectatorItem) {

            $event->getItem()->onClickAir($event->getPlayer(), $event->getPlayer()->getDirectionVector());

        }

    }


    public function onBreak(BlockBreakEvent $event)
    {
        $session = SessionFactory::getSession($event->getPlayer());
        if (!$session->hasMatch()) {
            return;
        }
        $block = $event->getBlock();


        if ($block->getId() !== 159 or  $block->getId() == 159 and !in_array($block->getMeta(), [11, 0, 14])) {
            $event->cancel();
        }

        /* if(!isset($this->block_position_when_place[$player->getUniqueId()->toString()])){
             if($block->getId() !== 159 && !in_array($block->getMeta(), [11, 0, 14])){
                $player->teleport($this->position_before_break[$player->getUniqueId()->toString()]);
             }
        }else{
            if(!$blockp == $this->block_position_when_place[$player->getUniqueId()->toString()]){
                if($block->getId() !== 159 && !in_array($block->getMeta(), [11, 0, 14])){
                    $player->teleport($this->position_before_break[$player->getUniqueId()->toString()]);
                }
            }
        }
        */
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $session = SessionFactory::getSession($event->getPlayer());
        $player = $event->getPlayer();
        if (!$session->hasMatch()) return;
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
        if($player->isSneaking()) return;
        $block = $event->getBlock();
        switch($block->getId()){
            case 58:
            case 61:
            case 116:
            case 145:
            case 379:
            case 459:
                $event->cancel();
                break;
            case 23:
                if($block->getMeta() === 3) $event->cancel();
                break;
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $recipients = $event->getRecipients();
        foreach($recipients as $key => $recipient){
            if($recipient instanceof Player){
                if($recipient->getWorld() !== $player->getWorld()){
                    unset($recipients[$key]);
                }
            }
        }
        $event->setRecipients($recipients);
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if($session->hasMatch()) {
            $session->setMatch(null);
        }
    }

}

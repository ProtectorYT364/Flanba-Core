<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;

class LobbyListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        SessionFactory::getSession($player = $event->getPlayer())->teleportToLobby();
        $hunger_manager = $player->getHungerManager();
        $hunger_manager->setFood($hunger_manager->getMaxFood());
        $hunger_manager->setEnabled(false);
    }

    public function onDamage(EntityDamageEvent $event): void {
        if($this->checkLobby($event->getEntity())) {
            $event->cancel();
        }
    }

    public function onBreak(BlockBreakEvent $event): void {
        if($this->checkLobby($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event): void {
        if($this->checkLobby($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK and $this->checkLobby($event->getPlayer())) {
            $event->cancel();
        }
    }

    private function checkLobby(Entity $entity): bool {
        if($entity->getWorld()->getFolderName() === ConfigGetter::getLobbyWorldName()) {
            return true;
        }
        return false;
    }

}
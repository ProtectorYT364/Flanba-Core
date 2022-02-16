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
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;

class LobbyListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        $session->updateNameTag();
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

    public function onMove(PlayerMoveEvent $event): void {
        $hunger_manager = $event->getPlayer()->getHungerManager();
        $hunger_manager->setFood($hunger_manager->getMaxFood());
    }

    private function checkLobby(Entity $entity): bool {
        if($entity->getWorld()->getFolderName() === ConfigGetter::getLobbyWorldName()) {
            return true;
        }
        return false;
    }

}
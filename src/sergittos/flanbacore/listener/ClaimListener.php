<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\world\Position;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\session\SessionFactory;

class ClaimListener implements Listener {

    public function onPlace(BlockPlaceEvent $event): void {
        if($this->checkLand(SessionFactory::getSession($event->getPlayer()), $event->getBlock()->getPosition())) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK and
            $this->checkLand(SessionFactory::getSession($event->getPlayer()), $event->getBlock()->getPosition())) {
            $event->cancel();
        }
    }

    private function checkLand(Session $session, Position $position): bool {
        return $session->hasTeam() and $session->getTeam()->getArea()->isInside($position);
    }

}
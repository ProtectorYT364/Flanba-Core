<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use sergittos\flanbacore\session\SessionFactory;

class FlanbaListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        SessionFactory::getSession($event->getPlayer())->teleportToLobby();
    }

}
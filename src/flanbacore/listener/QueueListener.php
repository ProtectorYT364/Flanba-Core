<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\listener;


use flanbacore\session\SessionFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class QueueListener implements Listener {

    public function onQuit(PlayerQuitEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if($session->hasQueue()) {
            $session->setQueue(null);
        }
    }

}
<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use sergittos\flanbacore\session\SessionFactory;

class MatchListener implements Listener {

    public function onQuit(PlayerQuitEvent $event): void {
        $session = SessionFactory::getSession($event->getPlayer());
        if($session->hasMatch()) {
            $session->setMatch(null);
        }
    }

}
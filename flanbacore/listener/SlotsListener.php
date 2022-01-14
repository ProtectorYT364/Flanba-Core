<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\Server;

class SlotsListener implements Listener {

    public function onQueryRegenerate(QueryRegenerateEvent $event): void {
        $event->getQueryInfo()->setMaxPlayerCount(count(Server::getInstance()->getOnlinePlayers()) + 1);
    }

}
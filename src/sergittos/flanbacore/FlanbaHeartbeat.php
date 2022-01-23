<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore;


use pocketmine\scheduler\Task;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;

class FlanbaHeartbeat extends Task {

    public function onRun(): void {
        foreach(FlanbaCore::getInstance()->getMatchManager()->getMatches() as $match) {
            $match->tick();
        }
        foreach(SessionFactory::getSessions() as $session) {
			foreach($session->getCooldowns() as $cooldown) {
				$cooldown->onRun();
			}
			if($session->getScoreboard() instanceof LobbyScoreboard and $session->checkPing()) {
				$session->updateScoreboard();
			}
        }
    }

}
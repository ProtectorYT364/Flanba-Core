<?php

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;

class ScoreboardListener implements Listener {

	public function onJoin(PlayerJoinEvent $event): void {
		foreach(SessionFactory::getSessions() as $session) {
			if($session->getScoreboard() instanceof LobbyScoreboard) {
				$session->updateScoreboard();
			}
		}
	}

}
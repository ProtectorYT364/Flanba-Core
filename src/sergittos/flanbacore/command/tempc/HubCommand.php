<?php

namespace sergittos\flanbacore\command\tempc;

use sergittos\flanbacore\session\SessionFactory;

class HubCommand extends \pocketmine\command\Command{

	public function __construct(){
		parent::__construct("hub", "Teleports you to the lobby!", null, ["lobby", "l", "spawn"]);
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args){
		$session = SessionFactory::getSession($sender);
		if($session->hasMatch()){
			$session->setMatch(null, true);
		}
		$session->teleportToLobby();
	}
}

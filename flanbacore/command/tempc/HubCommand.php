<?php

namespace sergittos\flanbacore\command\tempc;

use pocketmine\player\Player;
use sergittos\flanbacore\session\SessionFactory;

class HubCommand extends \pocketmine\command\Command{

	public function __construct(){
		parent::__construct("hub", "Teleports you to the lobby!", null, ["lobby", "l", "spawn"]);
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args){
		if(!$sender instanceof Player){
			$sender->sendMessage("You cant use this command in console!");
			return;
		}
		$session = SessionFactory::getSession($sender);
		if($session->hasMatch()){
			$session->setMatch(null, true);
			$sender->teleport($sender->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
		}
	}
}
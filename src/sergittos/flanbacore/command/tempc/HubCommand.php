<?php

declare(strict_types=1);

namespace sergittos\flanbacore\command\tempc;

use pocketmine\command\CommandSender;
use sergittos\flanbacore\session\SessionFactory;
use pocketmine\command\Command;

class HubCommand extends Command {

	public function __construct() {
		parent::__construct("hub", "Teleports you to the lobby!", null, ["lobby", "l", "spawn"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		$session = SessionFactory::getSession($sender);
		if($session->hasMatch()){
			$session->setMatch(null, true);
		}
	}

}
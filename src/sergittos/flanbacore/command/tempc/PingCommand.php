<?php

namespace sergittos\flanbacore\command\tempc;

use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PingCommand extends \pocketmine\command\Command{

	public function __construct() {
		parent::__construct("ping", "Sends you your latency with the server!", null, ["lobby", "l", "spawn"]);
	}

	/**
	 * @inheritDoc
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$sender instanceof Player){
			return false;
		}

		$sender->sendMessage(TextFormat::GREEN . "Your latency to the server is §b{$sender->getNetworkSession()->getPing()}");
		return true;

	}
}
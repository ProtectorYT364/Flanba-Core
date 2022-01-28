<?php

declare(strict_types=1);

namespace sergittos\flanbacore\command\tempc;

use pocketmine\block\utils\DyeColor;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use pocketmine\command\Command;

class HubCommand extends Command {

	public function __construct() {
		parent::__construct("hub", "Teleports you to the lobby!", null, ["lobby", "l", "spawn"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in-game");
            return;
        }
		$session = SessionFactory::getSession($sender);
		if($session->hasMatch()) {
            $session->setMatch(null);
		}
		$session->teleportToLobby();
	}

}
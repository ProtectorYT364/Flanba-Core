<?php

namespace sergittos\flanbacore\command\party\presets;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sergittos\flanbacore\session\SessionFactory;
use thebarii\partyengine\PartyEngine;

class PartyListCommand extends Command
{
    public function __construct()
    {
        parent::__construct("party list", "Gets the list of parties available.", "Usage: /p list", ['p list', 'plist']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in-game");
            return;
        }

        $session = SessionFactory::getSession($sender);
        $parties = PartyEngine::getInstance()->getPartyManager()->getParties();

        $parties = implode(", ", $parties);

        $session->message("List of current parties: " . $parties);
    }
}
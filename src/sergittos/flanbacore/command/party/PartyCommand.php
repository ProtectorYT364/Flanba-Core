<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\command\party;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sergittos\flanbacore\command\party\presets\PartyListCommand;

class PartyCommand extends Command {

    public function __construct()
    {
        parent::__construct("party", "Main party command.", null, ['p']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof  Player) {
            $sender->sendMessage("Please, run this command in-game");
            return;
        }
        
        if(!$args[0]) return false;

        if($args[0] === 'list') {

            $listCommand = new PartyListCommand();
            $listCommand->execute($sender, "p list", []);

        } elseif($args[0] === 'duel'){

            return;

        }
    }

}
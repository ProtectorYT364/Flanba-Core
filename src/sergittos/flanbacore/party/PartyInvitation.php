<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\party;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PartyInvitation {

    public function __construct(Player $owner, Player $player){
        $player->sendMessage(TextFormat::AQUA . "{$owner->getName()} " . TextFormat::GREEN . "invited you too a party!");
        $owner->sendMessage(TextFormat::AQUA . "{$player->getName()} " . TextFormat::GREEN . "was invited to the party!");
    }

}
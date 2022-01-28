<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form\party;



use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\player\Player;
use sergittos\flanbacore\party\Party;

class InviteForm extends \jojoe77777\FormAPI\CustomForm
{

    public function __construct(){
        parent::__construct(function(Player $player, $data = null){
            if($data == null) return;
            $party = new Party($player);
            $party->getPartyFactory()->InvitePlayer($player, $data[0]);
        });
        $this->setTitle("Invite");
        $this->addInput("Enter the player name", "Player name");
    }

}
<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form\party;


use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use sergittos\flanbacore\party\Party;

class PartyForm extends SimpleForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data = null) : void{
            switch($data){
                case 0:
                    $party = new Party($player);
                    $party->getPartyFactory()->createParty($player);
                    new YourPartyForm();
                break;
                case 1:
                    //soon
                    $player->sendMessage("Coming soon!");
                break;
                case 2:
                    //soon
                    $player->sendMessage("Coming soon!");
                break;
            }
        });
        $this->setTitle("Party");
        $this->addButton("Create");
        $this->addButton("Invites");
        $this->addButton("List");
    }

}
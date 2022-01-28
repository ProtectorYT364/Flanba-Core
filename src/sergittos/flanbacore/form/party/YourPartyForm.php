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

class YourPartyForm extends SimpleForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data = null){
            switch($data) {
                case 0:
                    $player->sendForm(new InviteForm());
                break;
            }
        });
        $this->setTitle("My party");
        $this->addButton("Invite");
        $this->addButton("Disband");
    }
}
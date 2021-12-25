<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\session;


use flanbacore\FlanbaCore;
use flanbacore\utils\ColorUtils;
use pocketmine\player\Player;

class Session {

    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function popup(string $text): void {
        $this->player->sendPopup(ColorUtils::translate($text));
    }

    public function message(string $text): void {
        $this->player->sendMessage(ColorUtils::translate($text));
    }

    public function save(): void {
        FlanbaCore::getInstance()->getProvider()->saveSession($this);
    }

}
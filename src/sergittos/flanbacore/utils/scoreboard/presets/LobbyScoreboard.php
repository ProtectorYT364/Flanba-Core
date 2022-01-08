<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\scoreboard\presets;


use pocketmine\Server;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class LobbyScoreboard extends Scoreboard {

    public function getLines(): array {
        return [
            "{DARK_GRAY}---------------",
            "{LIGHT_PURPLE}Online: {WHITE}" . count(Server::getInstance()->getOnlinePlayers()),
            " ",
            "{LIGHT_PURPLE}Your ping: {WHITE}" . $this->session->getPing(),
            "  ",
            "{LIGHT_PURPLE}K: {WHITE}3 {LIGHT_PURPLE}D: {WHITE}1", // {kills} {deaths}
            "{LIGHT_PURPLE}KDR: {WHITE}3.8 {LIGHT_PURPLE}Elo: {WHITE}1000", // {kdr} {elo}
            "{LIGHT_PURPLE}Killstreak: {WHITE}4 {GRAY}9", // {current_kill_streak} {best_kill_streak}
            "{DARK_GRAY}---------------{DARK_GRAY}"
        ];
    }

}
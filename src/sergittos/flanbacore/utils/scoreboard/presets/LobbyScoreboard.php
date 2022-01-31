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
            "flanba.sb.logo",
            "{DARK_GRAY}---------------",
            " §eOnline: {WHITE}" . count(Server::getInstance()->getOnlinePlayers()),
            " ",
            " §eYour ping: {WHITE}" . $this->session->getPing(),
            "  ",
            " §eK: {WHITE}3 §eD: {WHITE}1", // {kills} {deaths}
            " §eKDR: {WHITE}3.8 {LIGHT_PURPLE}Elo: {WHITE}1000", // {kdr} {elo}
            " §eKillstreak: {WHITE}4 {GRAY}9", // {current_kill_streak} {best_kill_streak}
            "{DARK_GRAY}---------------{DARK_GRAY}"
        ];
    }

}

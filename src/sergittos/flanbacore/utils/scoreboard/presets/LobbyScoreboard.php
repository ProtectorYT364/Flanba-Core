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
            //Rank | Level | Coins | Wins
            " §eRank: {WHITE}N/A",
            " §eLevel: {WHITE}N/A",
            " §eCoins: {WHITE}N/A",
            " §eWins: {WHITE}N/A"
        ];
    }

}

<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\scoreboard\presets\match;


use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;
use sergittos\flanbacore\utils\StoresMatch;

class WaitingPlayersScoreboard extends Scoreboard {
    use StoresMatch;

    public function __construct(?Session $session, FlanbaMatch $match) {
        $this->match = $match;
        parent::__construct($session);
    }

    public function getLines(): array {
        return [
            " {GREEN} " . $this->match->getArena()->getWorld()->getDisplayName(),
            " {GREEN} " . $this->match->getPlayersCount(),
            "  Waiting..."
        ];
    }

}
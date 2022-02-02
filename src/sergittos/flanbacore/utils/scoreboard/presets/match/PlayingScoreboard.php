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

class PlayingScoreboard extends Scoreboard {
    use StoresMatch;

    public function __construct(?Session $session, FlanbaMatch $match) {
        $this->match = $match;
        parent::__construct($session);
    }

    public function getLines(): array {
        $team = $this->session->getTeam();
        return [
            " {GREEN} " . gmdate("i:s", $this->match->getTimeLeft()),
            " {RED}[R] {BOLD}" . $this->match->getRedTeam()->getScore(),
            " {BLUE}[B] {BOLD}" . $this->match->getBlueTeam()->getScore(),
            " {GREEN} The Bridge Duel",
            " {GREEN} " . $team->getKills(),
            " {GREEN} " . $team->getScoreNumber()
        ];
    }

}
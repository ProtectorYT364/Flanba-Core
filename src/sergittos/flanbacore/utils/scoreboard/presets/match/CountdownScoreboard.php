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

class CountdownScoreboard extends Scoreboard {
    use StoresMatch;

    public function __construct(?Session $session, FlanbaMatch $match) {
        $this->match = $match;
        parent::__construct($session);
    }

    public function getLines(): array {
        return [
            "{GRAY}" . date("d/m/y") . "   ",
            " ",
            "{WHITE}Map: {GREEN}" . $this->match->getArena()->getWorld()->getDisplayName() . "   ",
            "{WHITE}Players: {GREEN}" . $this->match->getPlayersCount() . "/2   ",
            "  ",
            "{WHITE}Starting in {GREEN}{$this->match->getCountdown()}s   ",
            "   ",
            "{YELLOW}play.flanba.com   "
        ];
    }

}
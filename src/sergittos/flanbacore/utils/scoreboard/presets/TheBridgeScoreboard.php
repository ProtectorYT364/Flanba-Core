<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\scoreboard\presets;


use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class TheBridgeScoreboard extends Scoreboard {

    private FlanbaMatch $match;

    public function __construct(?Session $session, FlanbaMatch $match) {
        $this->match = $match;
        parent::__construct("thebridge", $session);
    }

    public function getLines(): array {
        return [
            " {BOLD}{YELLOW}The Bridge",
            " {GRAY}{date}",
            " ",
            " {WHITE}Time left: {GREEN}{time_left}",
            "  ",
            " {RED}[R] {red_score}",
            " {BLUE}[B] {blue_score}",
            "   ",
            " {WHITE}Mode: {GREEN}The Bridge Duel",
            " {WHITE}Kills: {GREEN}{kills}",
            " {WHITE}Goals: {GREEN}{goals}",
            "    ",
            " {YELLOW}flanba.com"
        ];
    }

}
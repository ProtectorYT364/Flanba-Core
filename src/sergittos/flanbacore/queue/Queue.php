<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\queue;

use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\map\Map;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\Session;

class Queue {

    private int $player_team_capacity;

    private Map $map;
    private FlanbaMatch $match;

    public function __construct(Map $map, int $player_team_capacity) {
        $this->map = $map;
        $this->player_team_capacity = $player_team_capacity;
        $this->reset();
    }

    public function getMap(): Map {
        return $this->map;
    }

    public function getPlayerTeamCapacity(): int {
        return $this->player_team_capacity;
    }

    public function addSession(Session $session): void {
        $this->match->addSession($session);

        if($this->match->getPlayersCount() >= ($this->player_team_capacity * 2)) {
            $this->reset();
        }
    }

    public function getMatch(): FlanbaMatch {

        return $this->match;

    }

    private function reset(): void {
        $this->match = FlanbaCore::getInstance()->getMatchManager()->getRandomMatch($this);
    }

}
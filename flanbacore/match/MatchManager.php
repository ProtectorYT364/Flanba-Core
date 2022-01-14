<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;



use sergittos\flanbacore\arena\ArenaFactory;

class MatchManager {

    /** @var FlanbaMatch[] */
    private array $matches = [];

    public function __construct() {
        foreach(ArenaFactory::getArenas() as $arena) {
            $this->addMatch(new FlanbaMatch($arena));
        }
    }

    /**
     * @return FlanbaMatch[]
     */
    public function getMatches(): array {
        return $this->matches;
    }

    private function addMatch(FlanbaMatch $match): void {
        $this->matches[$match->getId()] = $match;
    }

}
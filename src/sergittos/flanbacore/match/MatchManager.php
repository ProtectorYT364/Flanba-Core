<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;


use sergittos\flanbacore\arena\ArenaFactory;
use sergittos\flanbacore\queue\Queue;
use sergittos\flanbacore\utils\ArenaUtils;

class MatchManager {

    /** @var FlanbaMatch[] */
    public array $matches = [];

    public function __construct() {
        /*
        foreach(ArenaFactory::getArenas() as $arena) {
            $this->addMatch(new FlanbaMatch($arena));
        }
        */
    }

    /**
     * @return FlanbaMatch[]
     */
    public function getMatches(): array {
        return $this->matches;
    }

    public function getRandomMatch(Queue $queue): FlanbaMatch {
        shuffle($this->matches);
        $map = $queue->getMap();
        $capacity = $queue->getPlayerTeamCapacity();
        foreach($this->matches as $match) {
            if($match->getStage() === FlanbaMatch::WAITING_STAGE and
                $match->getArena()->getMap()->getName() === $map->getName() and
                $match->getPlayerTeamCapacity() === $queue->getPlayerTeamCapacity()) {
                return $match;
            }
        }
        $this->addMatch(new FlanbaMatch(ArenaUtils::generateArena($map), $capacity));
        return $this->getRandomMatch($queue);
    }

    public function addMatch(FlanbaMatch $match): void {
        $this->matches[$match->getId()] = $match;
    }

}
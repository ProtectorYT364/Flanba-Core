<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\queue;


use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\Session;

abstract class Queue {

    public const THE_BRIDGE = 0;

    private const MAX_PLAYERS_QUEUED = 2;

    private int $id;
    private string $name;

    private FlanbaMatch|null $match = null;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function addSession(Session $session): void {
        if($this->match === null) {
            $match = $this->getRandomMatch();
            if($match === null) {
                $session->message("{RED}Seems look like there isn't any matches available! Try joining in five minutes.");
                return;
            }
            $this->match = $match;
        }
        $this->match->addSession($session);
        if($this->match->getPlayersCount() >= self::MAX_PLAYERS_QUEUED) {
            $this->match = null;
        }
    }

    private function getRandomMatch(): ?FlanbaMatch {
        foreach(FlanbaCore::getInstance()->getMatchManager()->getMatches() as $match) {
            if($match->getStage() === $match::WAITING_STAGE) {
                return $match;
            }
        }
        return null;
    }

}
<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\queue;


use flanbacore\FlanbaCore;
use flanbacore\match\FlanbaMatch;
use flanbacore\session\Session;

abstract class Queue {

    public const THE_BRIDGE = 0;

    private const MAX_PLAYERS_QUEUED = 2;

    private int $id;
    private string $name;

    private FlanbaMatch|null $match = null;

    /** @var Session[] */
    private array $players;

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

    public function getPlayersCount(): int {
        return count($this->players);
    }

    private function hasSession(Session $session): bool {
        return in_array($session, $this->players, true);
    }

    public function addSession(Session $session): void {
        if($this->hasSession($session)) {
            $session->message("{RED}You already are in the queue!");
            return;
        }
        if($this->match === null) {
            $match = $this->getRandomMatch();
            if($match === null) {
                $this->sendErrorMessage($session);
                return;
            }
            $this->match = $match;
        }
        $this->players[] = $session;
        $session->setQueue($this);
        $this->attemptToMatchup();
    }

    public function removeSession(Session $session): void {
        if(!$this->hasSession($session)) {
            unset($this->players[array_search($session, $this->players, true)]);
        } else {
            $session->message("{RED}You cannot leave this queue because you are not in it!");
        }
    }

    private function attemptToMatchup(): void {
        if($this->getPlayersCount() >= self::MAX_PLAYERS_QUEUED) {
            $this->matchup();
        }
    }

    private function matchup(): void {
        foreach($this->players as $session) {
            if($this->match !== null) {
                $this->match->addSession($session);
                $session->setQueue(null);
            } else {
                $this->sendErrorMessage($session);
            }
        }
    }

    private function sendErrorMessage(Session $session): void {
        $session->message("{RED}Seems look like there isn't any matches available! Try joining in five minutes.");
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
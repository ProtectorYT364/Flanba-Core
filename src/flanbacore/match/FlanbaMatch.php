<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\match;


use flanbacore\arena\Arena;
use flanbacore\session\Session;
use flanbacore\utils\ConfigGetter;

class FlanbaMatch {

    private const WAITING_STAGE = 0;
    private const STARTING_STAGE = 1;
    private const PLAYING_STAGE = 2;
    private const ENDING_STAGE = 3;

    private string $id;
    private int $stage = self::WAITING_STAGE;
    private int $countdown;

    private Arena $arena;

    /** @var Session[] */
    private array $players = [];
    /** @var Session[] */
    private array $spectators = [];

    public function __construct(string $id, Arena $arena) {
        $this->id = $id;
        $this->arena = $arena;

        $this->countdown = ConfigGetter::getStartingSeconds() + 1;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getArena(): Arena {
        return $this->arena;
    }

    /**
     * @return Session[]
     */
    public function getPlayers(): array {
        return $this->players;
    }

    /**
     * @return Session[]
     */
    public function getSpectators(): array {
        return $this->spectators;
    }

    /**
     * @return Session[]
     */
    public function getPlayersAndSpectators(): array {
        return array_merge($this->players, $this->spectators);
    }

    public function tick(): void {
        switch($this->stage) {
            case self::STARTING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    foreach($this->players as $session) {
                        // Give kit and more stuff
                    }
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getEndingSeconds() + 6;
                } else {
                    foreach($this->players as $session) {
                        $session->popup("{YELLOW}Starting in {WHITE}$this->countdown {YELLOW}seconds...");
                    }
                }
                break;

            case self::ENDING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->reset();
                } elseif($this->countdown === 6) {
                    foreach($this->getPlayersAndSpectators() as $session) {
                        // Remove session from the match
                    }
                }
        }
    }

    private function reset(): void {
        $this->players = [];
        $this->spectators = [];
        $this->arena->reset();

        $this->countdown = ConfigGetter::getStartingSeconds();
        $this->stage = self::WAITING_STAGE;
    }

}
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

    public const WAITING_STAGE = 0;
    private const COUNTDOWN_STAGE = 1;
    private const STARTING_STAGE = 2;
    private const PLAYING_STAGE = 3;
    public const ENDING_STAGE = 4;

    private string $id;
    private int $stage = self::WAITING_STAGE;
    private int $countdown;

    private Arena $arena;

    private Team $first_team;
    private Team $second_team;

    /** @var Session[] */
    private array $spectators = [];

    public function __construct(string $id, Arena $arena) {
        $this->id = $id;
        $this->arena = $arena;

        $this->countdown = ConfigGetter::getCountdownSecconds();
    }

    public function getId(): string {
        return $this->id;
    }

    public function getStage(): int {
        return $this->stage;
    }

    public function getArena(): Arena {
        return $this->arena;
    }

    public function getFirstTeam(): Team {
        return $this->first_team;
    }

    public function getSecondTeam(): Team {
        return $this->second_team;
    }

    /**
     * @return Session[]
     */
    public function getPlayers(): array {
        return array_merge($this->first_team->getMembers(), $this->second_team->getMembers());
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
        return array_merge($this->getPlayers(), $this->spectators);
    }

    public function addSession(Session $session): void {

    }

    public function tick(): void {
        switch($this->stage) {
            case self::COUNTDOWN_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    foreach($this->getPlayers() as $session) {
                        // Teleport to waiting lobby
                    }
                    $this->stage = self::STARTING_STAGE;
                    $this->countdown = ConfigGetter::getStartingSeconds();
                } else {
                    $this->broadcastPopup("{YELLOW}The match will start in {WHITE}$this->countdown {YELLOW}seconds...");
                }
                break;
            case self::STARTING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    foreach($this->getPlayers() as $session) {
                        // Give kit and more stuff
                    }
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getEndingSeconds();
                } else {
                    $this->broadcastPopup("{YELLOW}Starting in {WHITE}$this->countdown {YELLOW}seconds...");
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
                break;
        }
    }

    private function broadcastPopup(string $popup): void {
        foreach($this->getPlayers() as $session) {
            $session->popup($popup);
        }
    }

    private function broadcastMessage(string $message): void {
        foreach($this->getPlayers() as $session) {
            $session->message($message);
        }
    }

    private function reset(): void {
        $this->arena->reset();
        $this->countdown = ConfigGetter::getStartingSeconds();
        $this->stage = self::WAITING_STAGE;
    }

}
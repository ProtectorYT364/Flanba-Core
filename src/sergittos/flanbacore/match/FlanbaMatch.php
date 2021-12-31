<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;


use sergittos\flanbacore\arena\Arena;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\ConfigGetter;

class FlanbaMatch {

    public const WAITING_STAGE = 0;
    private const COUNTDOWN_STAGE = 1;
    private const OPENING_CAGES_STAGE= 2;
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

        $this->countdown = ConfigGetter::getCountdownSeconds();
        $this->first_team = new Team($this, $arena->getRedPosition(), "{RED}");
        $this->second_team = new Team($this, $arena->getBluePosition(), "{BLUE}");
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

    public function getPlayersCount(): int {
        return count($this->getPlayers());
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

    public function isPlaying(Session $session): bool {
        return in_array($session, $this->getPlayers(), true);
    }

    public function addSession(Session $session): void {
        if(!$this->isPlaying($session)) {
            /** @var Team[] $teams */
            $teams = [$this->first_team, $this->second_team];
            shuffle($teams);

            $team = array_shift($teams);
            if($team->hasMember($session)) {
                $team = array_shift($teams);
            }
            $team->addMember($session);
            $session->setTeam($team);
            $session->setMatch($this);

            $session->getPlayer()->teleport($session->getTeam()->getSpawnPoint());
            $session->setImmobile(); // TODO: Change this to a cage

            if($this->getPlayersCount() >= 2) { // TODO: Change 2 to the max players of the arena
                $this->stage = self::COUNTDOWN_STAGE;
            }
        }
    }

    public function removeSession(Session $session): void {
        if($this->isPlaying($session)) {
            $session->getTeam()->removeMember($session);
        }
    }

    public function addSpectator(Session $spectator): void {
        // TODO
    }

    public function removeSpectator(Session $spectator): void {
        // TODO
    }

    public function tick(): void {
        switch($this->stage) {
            case self::WAITING_STAGE:
                // TODO: Update scoreboard
                break;
            case self::COUNTDOWN_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->stage = self::OPENING_CAGES_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
                } else {
                    $this->broadcastPopup("{YELLOW}The match will start in {WHITE}$this->countdown {YELLOW}seconds...");
                }
                break;
            case self::OPENING_CAGES_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    foreach($this->getPlayers() as $session) {
                        $session->setImmobile(false);
                    }
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
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
        $this->stage = self::WAITING_STAGE;
        $this->countdown = ConfigGetter::getCountdownSeconds();
    }

}
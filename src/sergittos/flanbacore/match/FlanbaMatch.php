<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;


use sergittos\flanbacore\arena\Arena;
use sergittos\flanbacore\kit\Kit;
use sergittos\flanbacore\kit\KitFactory;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\scoreboard\presets\match\CountdownScoreboard;
use sergittos\flanbacore\utils\scoreboard\presets\match\PlayingScoreboard;
use sergittos\flanbacore\utils\scoreboard\presets\match\WaitingPlayersScoreboard;

class FlanbaMatch {

    public const WAITING_STAGE = 0;
    private const COUNTDOWN_STAGE = 1;
    private const STARTING_STAGE = 2;
    public const OPENING_CAGES_STAGE= 3;
    public const PLAYING_STAGE = 4;
    public const ENDING_STAGE = 5;

    private string $id;
    private int $stage = self::WAITING_STAGE;
    private int $countdown;
    private int $time_left;
    private Arena $arena;

    private Team $red_team;
    private Team $blue_team;

    private Session $session_scored;

    /** @var Session[] */
    private array $spectators = [];

    public function __construct(Arena $arena) {
        $this->id = $arena->getId();
        $this->arena = $arena;

        $this->countdown = ConfigGetter::getCountdownSeconds();
        $this->time_left = $arena->getTimeLeft() * 60;
        $this->red_team = new Team($arena->getRedTeamSettings(), "{RED}");
        $this->blue_team = new Team($arena->getBlueTeamSettings(), "{BLUE}");
    }

    public function getId(): string {
        return $this->id;
    }

    public function getStage(): int {
        return $this->stage;
    }

    public function getCountdown(): int {
        return $this->countdown;
    }

    public function getTimeLeft(): float|int {
        return $this->time_left;
    }

    public function getArena(): Arena {
        return $this->arena;
    }

    public function getRedTeam(): Team {
        return $this->red_team;
    }

    public function getBlueTeam(): Team {
        return $this->blue_team;
    }

    public function setStage(int $stage): void {
        $this->stage = $stage;
    }

    public function setSessionScored(Session $session_scored): void {
        $this->session_scored = $session_scored;
    }

    /**
     * @return Team[]
     */
    public function getTeams(): array {
        return [$this->red_team, $this->blue_team];
    }

    /**
     * @return Session[]
     */
    public function getPlayers(): array {
        return array_merge($this->red_team->getMembers(), $this->blue_team->getMembers());
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

    public function setCountdown(int $countdown): void {
        $this->countdown = $countdown;
    }

    public function addSession(Session $session): void {
        if(!$this->isPlaying($session)) {
            $teams = $this->getTeams();
            shuffle($teams);

            $team = $teams[0];
            if(!empty($team->getMembers())) {
                $team = $teams[1];
            }
            $team->addMember($session);
            $session->setMatch($this);
            $session->setTeam($team);
            $session->addLeaveMatchItem();
            $session->getPlayer()->teleport($team->getWaitingPoint());

            if($this->getPlayersCount() >= 2) {
                $this->stage = self::COUNTDOWN_STAGE;
                foreach($this->getPlayers() as $session) {
                    $session->setScoreboard(new CountdownScoreboard($session, $this));
                }
            } else {
                $session->setScoreboard(new WaitingPlayersScoreboard($session, $this));
            }
            $this->broadcastMessage("{GRAY}{$session->getUsername()} {YELLOW}has joined ({AQUA}{$this->getPlayersCount()}{YELLOW}/{AQUA}2{YELLOW})!");
        }

        // TODO: Clean this
    }

    public function removeSession(Session $session): void {
        if($this->isPlaying($session)) {
            $this->finish($this->red_team->hasMember($session) ? $this->blue_team : $this->red_team, $session->getTeam());
            $session->setTeam(null);
        }
    }

    public function addSpectator(Session $spectator): void {
        // TODO
    }

    public function removeSpectator(Session $spectator): void {
        // TODO
    }

    public function tick(): void {
        // TODO: Clean this
        if($this->stage !== self::WAITING_STAGE and $this->stage !== self::COUNTDOWN_STAGE and $this->stage !== self::ENDING_STAGE) {
            $this->time_left--;
            if($this->time_left <= 0) {
                $this->countdown = ConfigGetter::getEndingSeconds();
                $this->stage = self::ENDING_STAGE;
            }
        }
        $players = $this->getPlayers();
        switch($this->stage) {
            case self::COUNTDOWN_STAGE:
                if($this->countdown <= 1) {
                    foreach($players as $session) {
                        $session->teleportToTeamSpawnPoint();
                        $session->setImmobile(); // TODO: Change this to a cage
                        $session->setScoreboard(new PlayingScoreboard($session, $this));
                        $session->setTheBridgeKit(ColorUtils::colorToDyeColor($session->getTeam()->getColor()));
                        $session->title(" ", "{GRAY}Cages open in {GREEN}5s{GRAY}...");
                    }
                    $this->stage = self::STARTING_STAGE;
                    $this->countdown = ConfigGetter::getStartingSeconds();
                } else {
                    $this->countdown--;
                    $color = "{YELLOW}";
                    if($this->countdown <= 3) {
                        $color = "{RED}";
                    }
                    $this->broadcastTitle("{$color}{$this->countdown}");
                    $this->broadcastMessage("{YELLOW}The game starts in {RED}$this->countdown {YELLOW}seconds!");
                }
                $this->updatePlayersScoreboard();
                break;

            case self::STARTING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->start();
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
                } else {
                    $this->broadcastSubTitle("{GRAY}Cages open in {GREEN}{$this->countdown}s{GRAY}...");
                }
                $this->updatePlayersScoreboard();
                break;

            case self::PLAYING_STAGE:
                $this->updatePlayersScoreboard();
                break;

            case self::OPENING_CAGES_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->start();
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
                } else {
                    $this->broadcastTitle(
                        $this->session_scored->getTeam()->getColor() . $this->session_scored->getUsername() . " scored!",
                        "{GRAY}Cages open in {GREEN}{$this->countdown}s{GRAY}..."
                    );
                }
                $this->updatePlayersScoreboard();
                break;

            case self::ENDING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->reset();
                } elseif($this->countdown === 6) {
                    foreach($this->getPlayersAndSpectators() as $session) {
                        $session->setMatch(null);
                        $session->teleportToLobby();
                    }
                }
                break;
        }
    }

    public function updatePlayersScoreboard(): void {
        foreach($this->getPlayers() as $session) {
            $session->updateScoreboard();
        }
    }

    public function broadcastTitle(string $title, string $subtitle = ""): void {
        foreach($this->getPlayers() as $session) {
            $session->title($title, $subtitle);
        }
    }

    private function broadcastSubTitle(string $subtitle): void {
        $this->broadcastTitle(" ", $subtitle);
    }

    private function broadcastMessage(string $message): void {
        foreach($this->getPlayers() as $session) {
            $session->message($message);
        }
    }

    private function start(): void {
        foreach($this->getPlayers() as $session) {
            $session->title(" ", "{GREEN}Fight!");
            $session->setImmobile(false); // TODO: Change this to a cage
            // TODO: Set kit

            $player = $session->getPlayer();
            $player->removeTitles();
            $player->resetTitles();
        }
    }

    private function reset(): void {
        $this->arena->reset();
        $this->stage = self::WAITING_STAGE;
        $this->countdown = ConfigGetter::getCountdownSeconds();
    }

    public function finish(Team $winner_team, Team $loser_team): void {
        $color = $winner_team->getColor();
        foreach($this->getPlayers() as $player) {
            $player->title(
                $color . strtoupper($winner_team->getName()) . " WINS!",
                $color . $winner_team->getScoreNumber() . " {WHITE}- " .
                $loser_team->getColor() . $loser_team->getScoreNumber()
            );
            $player->updateScoreboard();
            $player->teleportToTeamSpawnPoint();
        }
        $this->stage = self::ENDING_STAGE;
    }

}
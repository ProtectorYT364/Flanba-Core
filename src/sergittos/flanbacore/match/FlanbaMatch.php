<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;


use pocketmine\player\GameMode;
use alemiz\sga\StarGateAtlantis;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\arena\Arena;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\form\queue\PlayForm;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\scoreboard\presets\match\CountdownScoreboard;
use sergittos\flanbacore\utils\scoreboard\presets\match\PlayingScoreboard;
use sergittos\flanbacore\utils\scoreboard\presets\match\WaitingPlayersScoreboard;

class FlanbaMatch {

    public const WAITING_STAGE = 0;
    public const COUNTDOWN_STAGE = 1;
	public const STARTING_STAGE = 2;
    public const OPENING_CAGES_STAGE = 3;
    public const PLAYING_STAGE = 4;
    public const ENDING_STAGE = 5;

    private string $id;
    private int $stage = self::WAITING_STAGE;
    private int $countdown;
    private int $time_left;
    private int $player_team_capacity;
    private Arena $arena;

    private Team $red_team;
    private Team $blue_team;

    private Session $session_scored;

    /** @var Session[] */
    public array $spectators = [];

    public function __construct(Arena $arena, int $player_team_capacity) {
        $this->id = $arena->getId();
        $this->arena = $arena;

        $this->countdown = ConfigGetter::getCountdownSeconds();
        $this->time_left = $arena->getTimeLeft() * 60;
        $this->player_team_capacity = $player_team_capacity;
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

    public function getPlayerTeamCapacity(): int {
        return $this->player_team_capacity;
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
            if(count($team->getMembers()) >= $this->player_team_capacity) {
                $team = $teams[1];
            }
            $team->addMember($session);
            $session->getPlayer()->setGamemode(Gamemode::ADVENTURE());
            $session->setMatch($this);
            $session->setTeam($team);
            $session->setMatchItems();
            $session->getPlayer()->teleport($team->getWaitingPoint());
            $players_count = $this->getPlayersCount();
            $max_players = $this->player_team_capacity * 2;
            if($players_count >= $max_players) {
                $this->stage = self::COUNTDOWN_STAGE;
                foreach($this->getPlayers() as $session) {
                    $session->setScoreboard(new CountdownScoreboard($session, $this));
                }
            } else {
                foreach($this->getPlayers() as $session) {
                    $session->setScoreboard(new WaitingPlayersScoreboard($session, $this));
                }
            }
            $this->broadcastMessage("§l§a» §r§a§k{$session->getUsername()} §r§7 joined §8[{$players_count}/{$max_players}]!");
        }

        // TODO: Clean this
    }

    public function removeSession(Session $session, bool $finish = true): void {
        if($this->isPlaying($session)) {
            if($finish) {
                $this->finish($this->red_team->hasMember($session) ? $this->blue_team : $this->red_team, $session->getTeam());
            }
            $session->setTeam(null);
            $this->broadcastMessage("{BOLD}{RED}» §r§a§k{$session->getUsername()} §r§7 left!");
        }
    }

    public function addSpectator(Session $spectator): void {
        $this->spectators[$spectator->getPlayer()->getPlayerInfo()->getUuid()->toString()];
    }

    public function removeSpectator(Session $spectator): void {
		unset($this->spectators[$spectator->getPlayer()->getPlayerInfo()->getUuid()->toString()]);
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
                        $session->updateNameTag();
                        $session->setImmobile(); // TODO: Change this to a cage
                        $session->setScoreboard(new PlayingScoreboard($session, $this));
                        $session->giveTheBridgeKit();
                        $session->getPlayer()->setGamemode(GameMode::ADVENTURE());
                        $session->title(" ", "{GRAY}Cages open in {GREEN}5s{GRAY}...");
                    }
                    $this->stage = self::STARTING_STAGE;
                    $this->countdown = ConfigGetter::getStartingSeconds();
                } else {
                    $color = "{YELLOW}";
                    if($this->countdown <= 4) {
                        $color = "{RED}";
                    }
			        if($this->countdown < 4) {
				        foreach($players as $session) {
                            $player = $session->getPlayer();
					        if($player->getCurrentWindow() !== null) {
						        $player->removeCurrentWindow();
					        }
				        }
			        }
                    $this->countdown--;
                    $this->broadcastTitle($color . $this->countdown);
                    $this->broadcastMessage("{YELLOW}The game starts in {RED}" . $this->countdown  . " {YELLOW}seconds!");
                }
                $this->updatePlayersScoreboard();
                break;

            case self::STARTING_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->start();
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
                    foreach($players as $session) {
                        $session->getPlayer()->setGamemode(GameMode::ADVENTURE()); // TODO: Make a function for this
                    }
                } else {
                    $this->broadcastSubTitle("{GRAY}Cages open in {GREEN}{$this->countdown}s{GRAY}...");
                }
                $this->updatePlayersScoreboard();
                break;

		case self::PLAYING_STAGE:         
                $this->updatePlayersScoreboard();	
                foreach($players as $session) {
                    $session->getPlayer()->setGamemode(GameMode::SURVIVAL());
		        }
                break;

            case self::OPENING_CAGES_STAGE:
                $this->countdown--;
                if($this->countdown <= 0) {
                    $this->start();
                    $this->stage = self::PLAYING_STAGE;
                    $this->countdown = ConfigGetter::getOpeningCagesSeconds();
                    foreach($players as $session) {
                        $session->getPlayer()->setGamemode(GameMode::SURVIVAL());
                    }
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
                if($this->countdown <= 1) {
					foreach($this->getPlayersAndSpectators() as $session) {						
						$session->teleportToLobby();
					}
                    $this->reset();
                } elseif($this->countdown === 6) {
                    foreach($this->getPlayersAndSpectators() as $session) {
                        $session->setMatch(null);              
						$session->getPlayer()->setGamemode(Gamemode::ADVENTURE());
                        $session->message(TextFormat::GREEN . "If you want to play a different mode for this gamemode, please go back to hub using the bed or /hub.");
                    }
                }

				if($this->countdown === 10){
					foreach($this->getPlayersAndSpectators() as $session){
						$session->setLobbyItems();
						$session->getPlayer()->setGamemode(Gamemode::ADVENTURE());
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

    public function broadcastMessage(string $message): void {
        foreach($this->getArena()->getWorld()->getPlayers() as $session) {
            SessionFactory::getSession($session)->message($message);
        }
    }

    private function start(): void {
        foreach($this->getPlayers() as $session) {
            $player = $session->getPlayer();
            $player->removeTitles();
            $player->resetTitles();

            $session->title(" ", "{GREEN}Fight!");
            $session->setImmobile(false); // TODO: Change this to a cage
        }
    }

    private function reset(): void {
        $this->arena->reset();
        $this->stage = self::WAITING_STAGE;
        $this->countdown = ConfigGetter::getCountdownSeconds();
        $this->time_left = $this->arena->getTimeLeft() * 60;

        $this->red_team->reset();
        $this->blue_team->reset();
    }

    public function finish(Team $winner_team, Team $loser_team): void {
        $color = $winner_team->getColor();
        foreach($this->getPlayers() as $player) {
            $player->title(
                $color . strtoupper($winner_team->getName()) . " WINS!",
                $color . $winner_team->getScoreNumber() . " {WHITE}- " .
                $loser_team->getColor() . $loser_team->getScoreNumber()
            );
            $player->teleportToTeamSpawnPoint();
            $player->updateScoreboard();
        }
        $this->countdown = ConfigGetter::getEndingSeconds();
        $this->stage = self::ENDING_STAGE;
    }

}

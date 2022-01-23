<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\queue;


use FilesystemIterator;
use pocketmine\Server;
use pocketmine\world\World;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use sergittos\flanbacore\arena\Arena;
use sergittos\flanbacore\arena\ArenaFactory;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\match\MatchManager;
use sergittos\flanbacore\match\team\TeamSettings;
use sergittos\flanbacore\session\Session;
use SplFileInfo;

abstract class Queue {

    public const THE_BRIDGE = 0;

    private const MAX_PLAYERS_QUEUED = 2;

    private int $id;
    private string $name;

    private FlanbaMatch|null $match = null;
	private int $j = 10;

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
				$j = $this->j;
				mkdir(Server::getInstance()->getDataPath() . "/worlds/Ruins-" . $j);

				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Server::getInstance()->getDataPath() . "/worlds/Ruins", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
				/** @var SplFileInfo $fileInfo */
				foreach($files as $fileInfo) {
					if($filePath = $fileInfo->getRealPath()) {
						if($fileInfo->isFile()) {
							copy($filePath, str_replace("Ruins", "Ruins-" . $j, $filePath));
						} else {
							mkdir(str_replace("Ruins", "Ruins-" . $j, $filePath));
						}
					}
				}
				$world_name = "Ruins-" . $j;
				$world_manager = Server::getInstance()->getWorldManager();
				$world_manager->loadWorld($world_name, true);

				$world = $world_manager->getWorldByName($world_name);
				$world->setAutoSave(false);
				$world->setTime(World::TIME_DAY);
				$world->stopTime();
				foreach(json_decode(file_get_contents(FlanbaCore::getInstance()->getDataFolder() . "dupedarena.json"), true) as $arena_data){
					ArenaFactory::addArena(new Arena(
						"tb" . $j, $arena_data["time_left"], $arena_data["height_limit"], $arena_data["void_limit"], $world,
						TeamSettings::fromData($arena_data["red_settings"], $world), TeamSettings::fromData($arena_data["blue_settings"], $world)
					));
				}
				$match = new MatchManager();
				foreach(ArenaFactory::getArenas() as $arena) {
					$match->addMatch(new FlanbaMatch($arena));
				}
				$this->j++;
                return;
            }
            $this->match = $match;
        }
        $this->match->addSession($session);
        if($this->match->getPlayersCount() >= self::MAX_PLAYERS_QUEUED) {
            $this->match = null;
        }
    }

	public function addDupedMatch(Session $session): void {
		if($this->match === null){
			$match = $this->getRandomMatch();
			if($match === null) {
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
        $matches = FlanbaCore::getInstance()->getMatchManager()->getMatches();
        shuffle($matches);

        foreach($matches as $match) {
            if($match->getStage() === FlanbaMatch::WAITING_STAGE) {
                return $match;
            }
        }
        return null;
    }

}
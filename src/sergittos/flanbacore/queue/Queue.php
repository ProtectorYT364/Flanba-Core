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
	private int $j = 0;
	private string $worldname;

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
            /*
            $match = $this->getRandomMatch();
            if($match === null) {
				$this->createNewArena();
                $this->addSession($session);
                return;
            }
            */
            $this->match = $this->getRandomMatch();
        }
        $this->match->addSession($session);
        if($this->match->getPlayersCount() >= self::MAX_PLAYERS_QUEUED) {
            $this->match = null;
        }
    }

    private function createNewArena(): void {

        $j = $this->j;
		$random = mt_rand(0, 3);
		switch($random){
			case 0:
				$server = Server::getInstance();
				$data_path = $server->getDataPath();
				$dir = $data_path . "/worlds/Ruins-" . $j;
				if(!file_exists($dir)) {
					mkdir($dir);
					$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data_path . "/worlds/Ruins", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
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
				}
				$this->worldname = "Ruins-" . $j;
				break;
			case 1:
				$server = Server::getInstance();
				$data_path = $server->getDataPath();
				$dir = $data_path . "/worlds/SpaceX-" . $j;
				if(!file_exists($dir)) {
					mkdir($dir);
					$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data_path . "/worlds/SpaceX", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
					/** @var SplFileInfo $fileInfo */
					foreach($files as $fileInfo) {
						if($filePath = $fileInfo->getRealPath()) {
							if($fileInfo->isFile()) {
								copy($filePath, str_replace("SpaceX", "SpaceX-" . $j, $filePath));
							} else {
								mkdir(str_replace("SpaceX", "SpaceX-" . $j, $filePath));
							}
						}
					}
				}
				$this->worldname = "SpaceX-" . $j;
				break;
			case 2:
				$server = Server::getInstance();
				$data_path = $server->getDataPath();
				$dir = $data_path . "/worlds/Shrine-" . $j;
				if(!file_exists($dir)) {
					mkdir($dir);
					$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data_path . "/worlds/Shrine", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
					/** @var SplFileInfo $fileInfo */
					foreach($files as $fileInfo) {
						if($filePath = $fileInfo->getRealPath()) {
							if($fileInfo->isFile()) {
								copy($filePath, str_replace("Shrine", "Shrine-" . $j, $filePath));
							} else {
								mkdir(str_replace("Shrine", "Shrine-" . $j, $filePath));
							}
						}
					}
				}
				$this->worldname = "Shrine-" . $j;
				break;
			case 3:
				$server = Server::getInstance();
				$data_path = $server->getDataPath();
				$dir = $data_path . "/worlds/1945-" . $j;
				if(!file_exists($dir)) {
					mkdir($dir);
					$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data_path . "/worlds/1945", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
					/** @var SplFileInfo $fileInfo */
					foreach($files as $fileInfo) {
						if($filePath = $fileInfo->getRealPath()) {
							if($fileInfo->isFile()) {
								copy($filePath, str_replace("1945", "1945-" . $j, $filePath));
							} else {
								mkdir(str_replace("1945", "1945-" . $j, $filePath));
							}
						}
					}
				}
				$this->worldname = "1945-" . $j;
				break;
		}
		
        $world_manager = Server::getInstance()->getWorldManager();
        $world_manager->loadWorld($this->worldname, true);

        $world = $world_manager->getWorldByName($this->worldname);
        $world->setAutoSave(false);
        $world->setTime(World::TIME_DAY);
        $world->stopTime();
        foreach(json_decode(file_get_contents(FlanbaCore::getInstance()->getDataFolder() . "dupedarena.json"), true) as $arena_data){
            $arena = new Arena(
                "tb" . $j, $arena_data["time_left"], $arena_data["height_limit"], $arena_data["void_limit"], $world,
                TeamSettings::fromData($arena_data["red_settings"], $world), TeamSettings::fromData($arena_data["blue_settings"], $world)
            );
            ArenaFactory::addArena($arena);
            FlanbaCore::getInstance()->getMatchManager()->addMatch(new FlanbaMatch($arena));
        }
        $this->j++;
    }

    private function getRandomMatch(): FlanbaMatch {
        $matches = FlanbaCore::getInstance()->getMatchManager()->getMatches();
        shuffle($matches);

        foreach($matches as $match) {
            if($match->getStage() === FlanbaMatch::WAITING_STAGE) {
                return $match;
            }
        }
        $this->createNewArena();
        return $this->getRandomMatch();
    }

}
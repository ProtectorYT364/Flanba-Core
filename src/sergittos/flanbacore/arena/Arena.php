<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\arena;


use pocketmine\Server;
use pocketmine\world\World;
use sergittos\flanbacore\map\Map;
use sergittos\flanbacore\match\team\TeamSettings;

class Arena {

    private string $id;
    private int $time_left;
    private int $height_limit;
    private int $void_limit;

    private Map $map;
    private World $world;

    private TeamSettings $red_team_settings;
    private TeamSettings $blue_team_settings;

    public function __construct(string $id, int $time_left, int $height_limit, int $void_limit, Map $map, World $world, TeamSettings $red_team_settings, TeamSettings $blue_team_settings) {
        $this->id = $id;
        $this->time_left = $time_left;
        $this->height_limit = $height_limit;
        $this->void_limit = $void_limit;
        $this->map = $map;
        $this->world = $world;
        $this->red_team_settings = $red_team_settings;
        $this->blue_team_settings = $blue_team_settings;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getTimeLeft(): int {
        return $this->time_left;
    }

    public function getHeightLimit(): int {
        return $this->height_limit;
    }

    public function getVoidLimit(): int {
        return $this->void_limit;
    }

    public function getMap(): Map {
        return $this->map;
    }

    public function getWorld(): World {
        return $this->world;
    }

    public function getRedTeamSettings(): TeamSettings {
        return $this->red_team_settings;
    }

    public function getBlueTeamSettings(): TeamSettings {
        return $this->blue_team_settings;
    }

    public function reset(): void {
        $world_name = $this->world->getFolderName();
        $world_manager = Server::getInstance()->getWorldManager();
        $world_manager->unloadWorld($this->world);
        $world_manager->loadWorld($world_name, true);

        $this->world = $world_manager->getWorldByName($world_name);
        $this->world->setAutoSave(false);
        $this->world->setTime(World::TIME_DAY);
        $this->world->stopTime();

        $this->red_team_settings->updatePositionsWorld($this->world);
        $this->blue_team_settings->updatePositionsWorld($this->world);
    }

}
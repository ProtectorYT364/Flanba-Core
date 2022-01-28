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
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\map\MapFactory;
use sergittos\flanbacore\match\team\TeamSettings;

class ArenaFactory {

    /** @var Arena[] */
    static private array $arenas = [];

    static public function init(): void {
        /*
        foreach(MapFactory::getMaps() as $map) {
            foreach(json_decode(file_get_contents(FlanbaCore::getInstance()->getDataFolder() . "maps/{$map->getName()}.json"), true) as $arena_data) {
                $world_name = $arena_data["world_name"];
                $world_manager = Server::getInstance()->getWorldManager();
                $world_manager->loadWorld($world_name, true);

                $world = $world_manager->getWorldByName($world_name);
                $world->setAutoSave(false);
                $world->setTime(World::TIME_DAY);
                $world->stopTime();

                self::addArena(new Arena(
                    $arena_data["id"], $arena_data["time_left"], $arena_data["height_limit"], $arena_data["void_limit"],
                    $map, $world, TeamSettings::fromData($arena_data["red_settings"], $world),
                    TeamSettings::fromData($arena_data["blue_settings"], $world)
                ));
            }
        }
        */
    }

    /**
     * @return Arena[]
     */
    static public function getArenas(): array {
        return self::$arenas;
    }

    static public function addArena(Arena $arena): void {
        self::$arenas[$arena->getId()] = $arena;
    }

}
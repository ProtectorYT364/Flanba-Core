<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\map;


use sergittos\flanbacore\FlanbaCore;

class MapFactory {

    /** @var Map[] */
    static private array $maps = [];

    static public function init(): void {
        foreach(json_decode(file_get_contents(FlanbaCore::getInstance()->getDataFolder() . "maps.json"), true) as $map_data) {
            self::addMap(new Map($map_data["name"]));
        }
    }

    /**
     * @return Map[]
     */
    static public function getMaps(): array {
        return self::$maps;
    }

    static public function getMapByName(string $name): ?Map {
        return self::$maps[$name] ?? null;
    }

    static private function addMap(Map $map): void {
        self::$maps[$map->getName()] = $map;
    }

}
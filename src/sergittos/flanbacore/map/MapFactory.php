<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\map;


use sergittos\flanbacore\FlanbaCore;
use DirectoryIterator;
use SplFileInfo;

class MapFactory {

    /** @var Map[] */
    static private array $maps = [];

    static public function init(): void {
        foreach(glob(FlanbaCore::getInstance()->getDataFolder() . "maps/*.json") as $map_name) {
            self::addMap(new Map(basename(substr($map_name, 0, strrpos($map_name, ".")))));
        }
    }

    /**
     * @return Map[]
     */
    static public function getMaps(): array {
        return self::$maps;
    }

    static private function addMap(Map $map): void {
        self::$maps[$map->getName()] = $map;
    }

}
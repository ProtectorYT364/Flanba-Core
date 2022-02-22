<?php

declare(strict_types=1);


namespace sergittos\flanbacore\utils;


use FilesystemIterator;
use pocketmine\Server;
use pocketmine\world\World;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use sergittos\flanbacore\arena\Arena;
use sergittos\flanbacore\arena\ArenaFactory;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\map\Map;
use sergittos\flanbacore\match\team\TeamSettings;
use SplFileInfo;

class ArenaUtils {

    static private int $j = 0;

    static public function generateArena(Map $map): ?Arena {
        $name = ucfirst($map->getName());
        if($name === "Spacex") {
            $name = "SpaceX";
        } elseif ($name === "Flanbainc") {

            $name = "flanbainc";

        }
        $server = Server::getInstance();
         $data_path = $server->getDataPath();
        $dir = $data_path . "worlds/$name-" . self::$j;
        if(!file_exists($dir)) {
            mkdir($dir);
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data_path . "worlds/$name", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
            /** @var SplFileInfo $fileInfo */
            foreach($files as $fileInfo) {
                if($filePath = $fileInfo->getRealPath()) {
                    if($fileInfo->isFile()) {
                        copy($filePath, str_replace($name, "$name-" . self::$j, $filePath));
                    } else {
                        mkdir(str_replace($name, "$name-" . self::$j, $filePath));
                    }
                }
            }
        }

        $world_name = "$name-" . self::$j;
        $world_manager = Server::getInstance()->getWorldManager();
        $world_manager->loadWorld($world_name, true);


        $world = $world_manager->getWorldByName($world_name);
        if(is_null($world)) echo "\n\n\n\n Ayo this world is naughty {$world_name}\n\n\n\n\n";
        $world->setAutoSave(false);
        $world->setTime(World::TIME_DAY);
        $world->stopTime();


        $plugin = FlanbaCore::getInstance();
        $arena = null;
        foreach(json_decode(file_get_contents($plugin->getDataFolder()  . "maps/" . strtolower($name) . ".json"), true) as $arena_data) {
            $arena = new Arena(
                "tb" . self::$j, $arena_data["time_left"], $arena_data["height_limit"], $arena_data["void_limit"], $map, $world,
                TeamSettings::fromData($arena_data["red_settings"], $world), TeamSettings::fromData($arena_data["blue_settings"], $world)
            );
            ArenaFactory::addArena($arena);
        }
        self::$j++;
        return $arena;
    }

}
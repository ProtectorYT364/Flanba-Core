<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\utils;


use flanbacore\FlanbaCore;
use pocketmine\utils\Config;

class ConfigGetter {

    static private array $config_data;
    static private array $scoreboard_data;

    static public function init(): void {
        $plugin = FlanbaCore::getInstance();
        self::$config_data = $plugin->getConfig()->getAll();
        self::$scoreboard_data = (new Config($plugin->getDataFolder() . "scoreboard.yml", Config::YAML))->getAll();
    }

    static private function get(string $key): mixed {
        return self::$config_data[$key] ?? null;
    }

    static private function getScoreboardConfig(string $key): mixed {
        return self::$scoreboard_data[$key] ?? null;
    }

    static public function getCountdownSeconds(): int {
        return self::get("countdown-seconds") + 1;
    }

    static public function getOpeningCagesSeconds(): int {
        return self::get("opening-cages-seconds") + 1;
    }

    static public function getEndingSeconds(): int {
        return self::get("ending-seconds") + 6;
    }

    static public function getScoreboardTitle(): string {
        return ColorUtils::translate(self::getScoreboardConfig("scoreboard-title"));
    }

    static public function getLobbyScoreboardLines(): array {
        return self::getScoreboardConfig("lobby");
    }

    static public function getTheBridgeScoreboardLines(): array {
        return self::getScoreboardConfig("the-bridge");
    }

}
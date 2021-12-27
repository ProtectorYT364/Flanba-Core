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

    static private function get(string $key) {
        return FlanbaCore::getInstance()->getConfig()->get($key);
    }

    static private function getScoreboardConfig(string $key) {
        return (new Config(FlanbaCore::getInstance()->getDataFolder() . "scoreboard.yml"))->get($key);
    }

    static public function getCountdownSecconds(): int {
        return self::get("countdown-seconds") + 1;
    }

    static public function getStartingSeconds(): int {
        return self::get("starting-seconds") + 1;
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
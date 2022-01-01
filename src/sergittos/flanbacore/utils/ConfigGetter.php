<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils;


use pocketmine\utils\Config;
use sergittos\flanbacore\FlanbaCore;

class ConfigGetter {

    static private array $config_data;

    static public function init(): void {
        $plugin = FlanbaCore::getInstance();
        self::$config_data = $plugin->getConfig()->getAll();
    }

    static private function get(string $key): mixed {
        return self::$config_data[$key] ?? null;
    }

    static public function getLobbyWorldName(): string {
        return self::get("lobby-world");
    }

    static public function getKnockback(): float {
        return (float) self::get("knockback");
    }

    static public function getAttackCooldown(): int {
        return self::get("attack-cooldown");
    }

    static public function getCountdownSeconds(): int {
        return self::get("countdown-seconds");
    }

    static public function getStartingSeconds(): int {
        return self::get("starting-seconds");
    }

    static public function getOpeningCagesSeconds(): int {
        return self::get("opening-cages-seconds") + 1;
    }

    static public function getEndingSeconds(): int {
        return self::get("ending-seconds") + 6;
    }

    static public function getScoreboardTitle(): string {
        return self::get("scoreboard-title");
    }

}
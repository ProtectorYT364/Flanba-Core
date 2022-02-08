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
        self::$config_data = FlanbaCore::getInstance()->getConfig()->getAll();
    }

    static private function get(string $key): mixed {
        return self::$config_data[$key] ?? null;
    }

    static public function getLobbyWorldName(): string {
        return self::get("lobby-world");
    }

    static public function getScoreboardTitle(): string {
        return self::get("scoreboard-title");
    }

    static public function getKnockback(): float {
        return (float) self::get("knockback");
    }

    static public function getProvider(): string {
        return self::get("provider");
    }

    static public function getAttackCooldown(): int {
        return self::get("attack-cooldown");
    }

    static public function getCountdownSeconds(): int {
        return self::get("countdown-seconds") + 1;
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

    static public function getBowCooldownSeconds(): int {
        return self::get("bow-cooldown");
    }

    static public function getGappleCooldownSeconds(): int {
        return self::get("gapple-cooldown");
    }

    static public function getGamemodeMax(): int {
        return self::get("gamemode-max");
    }

}
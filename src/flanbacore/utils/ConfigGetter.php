<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\utils;


use flanbacore\FlanbaCore;

class ConfigGetter {

    static private function get(string $key) {
        return FlanbaCore::getInstance()->getConfig()->get($key);
    }

    static public function getStartingSeconds(): int {
        return self::get("starting-seconds");
    }

    static public function getEndingSeconds(): int {
        return self::get("ending-seconds");
    }

}
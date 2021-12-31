<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\arena;


class ArenaFactory {

    /** @var Arena[] */
    static private array $arenas = [];

    static public function init(): void {
        // TODO
    }

    /**
     * @return Arena[]
     */
    static public function getArenas(): array {
        return self::$arenas;
    }

    static private function addArena(Arena $arena): void {

    }

}
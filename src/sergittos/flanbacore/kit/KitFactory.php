<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\kit;


class KitFactory {

    /** @var Kit[] */
    static private array $kits = [];

    static public function init(): void {
        self::registerKit(new TheBridgeKit());
    }

    static public function getKitById(int $id): ?Kit {
        return self::$kits[$id] ?? null;
    }

    static private function registerKit(Kit $kit): void {
        self::$kits[$kit->getId()] = $kit;
    }

}
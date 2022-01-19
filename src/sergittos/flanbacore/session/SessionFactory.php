<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\session;


use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;

class SessionFactory {

    /** @var Session[] */
    static private array $sessions = [];

    /**
     * @return Session[]
     */
    static public function getSessions(): array {
        return self::$sessions;
    }

    static public function getSession(Player $player): ?Session {
        return self::$sessions[$player->getName()] ?? null;
    }

    static public function createSession(Player $player): void {
        $session = new Session($player);
        FlanbaCore::getInstance()->getProvider()->loadSession($session);
        self::$sessions[$player->getName()] = $session;
    }

    static public function removeSession(Player $player): void {
        $session = self::$sessions[$player->getName()];
        $session->save();
        unset($session);
    }

}
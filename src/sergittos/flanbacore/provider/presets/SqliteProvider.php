<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\provider\presets;


use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\provider\Provider;
use sergittos\flanbacore\session\Session;
use Sqlite3;

class SqliteProvider extends Provider {

    private Sqlite3 $sqlite;

    public function __construct() {
        $this->sqlite = new Sqlite3(FlanbaCore::getInstance()->getDataFolder() . "database.db");
        $this->sqlite->exec(
            "CREATE TABLE IF NOT EXISTS layouts (
                user_xuid VARCHAR(255) PRIMARY KEY,
                
                sword_slot TINYINT,
                bow_slot TINYINT,
                pickaxe_slot TINYINT,
                arrow_slot TINYINT,
    
                blocks_slot TINYINT,
                gapples_slot TINYINT
            )"
        );
    }

    public function loadSession(Session $session): void {
        // $statement = $this->sqlite->prepare(""); TODO
    }

    public function saveSession(Session $session): void {
        // TODO: Implement saveSession() method.
    }

}

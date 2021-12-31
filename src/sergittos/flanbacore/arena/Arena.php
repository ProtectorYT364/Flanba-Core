<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\arena;


use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use sergittos\flanbacore\utils\claim\Claim;

class Arena {

    private string $id;

    private World $world;
    private Claim $claim;

    private Position $red_position;
    private Position $blue_position;

    public function __construct(string $id, World $world, Claim $claim, Position $red_position, Position $blue_position) {
        $this->id = $id;
        $this->world = $world;
        $this->claim = $claim;
        $this->red_position = $red_position;
        $this->blue_position = $blue_position;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getWorld(): World {
        return $this->world;
    }

    public function getClaim(): Claim {
        return $this->claim;
    }

    public function getRedPosition(): Position {
        return $this->red_position;
    }

    public function getBluePosition(): Position {
        return $this->blue_position;
    }

    public function reset(): void {
        $world_name = $this->world->getFolderName();
        $world_manager = Server::getInstance()->getWorldManager();
        $world_manager->unloadWorld($this->world);
        $world_manager->loadWorld($world_name, true);

        $this->world = $world_manager->getWorldByName($world_name);
        $this->world->setTime(World::TIME_DAY);
        $this->world->stopTime();
        $this->world->setAutoSave(false);

        $this->red_position->world = $this->world;
        $this->blue_position->world = $this->world;
    }

}